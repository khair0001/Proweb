// script.js
document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.getElementById("registerForm");
  const loginForm = document.getElementById("loginForm");

  if (registerForm) {
    registerForm.addEventListener("submit", (e) => {
      const password = document.getElementById("password").value;
      const confirmPassword = document.getElementById("confirmPassword").value;

      if (password !== confirmPassword) {
        alert("Passwords do not match.");
        e.preventDefault(); // Mencegah form dikirim
      }
    });
  }

  if (loginForm) {
    loginForm.addEventListener("submit", (e) => {
      const checkbox = document.getElementById("privasi");
      if (!checkbox.checked) {
        alert("You must agree to the Terms and Privacy Policy.");
        e.preventDefault();
      }
    });
  }
});


function togglePasswordVisibility(inputId, icon) {
  const input = document.getElementById(inputId);
  const isHidden = input.type === "password";

  input.type = isHidden ? "text" : "password";

  // Ganti ikon tergantung kondisi
  icon.src = isHidden
    ? "https://i.imgur.com/03EOylj.png" // eye-slash
    : "https://cdn-icons-png.flaticon.com/512/709/709612.png"; // eye
}

