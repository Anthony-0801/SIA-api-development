document.addEventListener("DOMContentLoaded", function () {
  // Define the correct credentials
  const correctUsername = "admin";
  const correctPassword = "admin";

  // Get the form and form elements
  const form = document.querySelector("form");
  const usernameInput = document.getElementById("text");
  const passwordInput = document.getElementById("password");

  // Event listener for form submission
  form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Check if the entered credentials are correct
    if (
      usernameInput.value === correctUsername &&
      passwordInput.value === correctPassword
    ) {
      // Redirect to success.html
      window.location.href = "nav.html";
    } else {
      // Display an error message or handle incorrect credentials as needed
      alert("Incorrect username or password. Please try again.");
    }
  });
});

document.addEventListener('DOMContentLoaded', function () {
  const passwordInput = document.getElementById('password');
  const togglePasswordBtn = document.querySelector('.toggle-password');

  togglePasswordBtn.addEventListener('click', function () {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    togglePasswordBtn.textContent = type === 'password' ? 'Show' : 'Hide';
  });
});