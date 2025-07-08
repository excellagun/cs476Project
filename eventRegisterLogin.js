var form = document.getElementById("login-page");
form.addEventListener("submit", function (event) {
  validateLogin(event);

  // If validation passed, simulate "successful login"
  if (!event.defaultPrevented) {
    alert("Login successful! Redirecting...");
    window.location.href = "mainpage.html"; // Or whatever page you want
  }
});
