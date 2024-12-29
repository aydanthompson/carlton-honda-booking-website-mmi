function initLoginForm() {
  $("#login-form").on("submit", function (event) {
    // Prevent the default form submission behavior.
    event.preventDefault();
    // Keeps to drop-down open.
    event.stopPropagation();

    $.ajax({
      type: "POST",
      url: "/login",
      data: $(this).serialize(),

      success: function (response) {
        if (response.success) {
          location.reload();
        } else {
          $("#login-error-message").text(response.message).show();
        }
      },

      error: function () {
        $("#login-error-message").text("An unexpected error occurred. Please try again.").show();
      },
    });
  });
}

$(document).ready(initLoginForm);
