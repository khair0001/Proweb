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

function openTab(tabName) {
  // Hide all tab contents
  const tabContents = document.getElementsByClassName('tab-content');
  for (let i = 0; i < tabContents.length; i++) {
      tabContents[i].classList.remove('active');
  }
  
  // Remove active class from all tabs
  const tabs = document.getElementsByClassName('profile-tab');
  for (let i = 0; i < tabs.length; i++) {
      tabs[i].classList.remove('active');
  }
  
  // Show the selected tab content and mark the tab as active
  document.getElementById(tabName).classList.add('active');
  event.currentTarget.classList.add('active');
}

function previewImage(input) {
  const preview = document.getElementById('profile-preview');
  if (input.files && input.files[0]) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
          preview.src = e.target.result;
      }
      
      reader.readAsDataURL(input.files[0]);
  }
}

    // Simple slider functionality for testimonials
    let currentSlide = 0;
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    function showSlide(index) {
      testimonialCards.forEach((card, i) => {
        if (i === index) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    }
    
    function nextSlide() {
      currentSlide = (currentSlide + 1) % testimonialCards.length;
      showSlide(currentSlide);
    }
    
    // Initialize slider
    showSlide(currentSlide);
    
    // Auto-rotate testimonials every 5 seconds
    setInterval(nextSlide, 5000);