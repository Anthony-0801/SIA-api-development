document.addEventListener("DOMContentLoaded", function () {
    // Define the correct credentials
    const correctUsername = "admin";
    const correctPassword = "admin";
  
    // Get the form and form elements
    const form = document.querySelector("form");
    const usernameInput = document.querySelector('input[type="text"]');
    const passwordInput = document.querySelector('input[type="password"]');
  
    // Event listener for form submission
    form.addEventListener("submit", function (event) {
      event.preventDefault(); // Prevent the default form submission
  
      // Check if the entered credentials are correct
      if (
        usernameInput.value === correctUsername &&
        passwordInput.value === correctPassword
      ) {
        // Redirect to success.html
        window.location.href = "success.html";
      } else {
        // Display an error message or handle incorrect credentials as needed
        alert("Incorrect username or password. Please try again.");
      }
    });
  });
  