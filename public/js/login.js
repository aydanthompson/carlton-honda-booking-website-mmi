function initLoginForm() {
  const loginForm = document.getElementById("login-form");
  loginForm.addEventListener("submit", function (event) {
    // Prevent the default form submission behavior.
    event.preventDefault();
    // Keeps the drop-down open.
    event.stopPropagation();

    const formData = new FormData(loginForm);
    const data = new URLSearchParams(formData);

    fetch("/login", {
      method: "POST",
      body: data,
    })
      .then((response) => response.json())
      .then((response) => {
        if (response.success) {
          location.reload();
        } else {
          const errorMessage = document.getElementById("login-error-message");
          errorMessage.textContent = response.message;
          errorMessage.style.display = "block";
        }
      })
      .catch(() => {
        const errorMessage = document.getElementById("login-error-message");
        errorMessage.textContent = "An unexpected error occurred. Please try again.";
        errorMessage.style.display = "block";
      });
  });
}

document.addEventListener("DOMContentLoaded", initLoginForm);
