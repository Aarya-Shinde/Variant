
// dark mode toggle to automatic set theme fetching from the local storage
document.addEventListener("DOMContentLoaded", () => {
  const darkModeBtn = document.querySelector(".dark-mode-btn");

  if (darkModeBtn) {
    const icon = darkModeBtn.querySelector("i");

    // Load saved theme
    const savedTheme = localStorage.getItem("theme");
    const isDarkMode = savedTheme === "dark";

    document.body.classList.toggle("dark-mode", isDarkMode);

    if (icon) {
      icon.classList.replace(
        isDarkMode ? "fa-moon" : "fa-sun",
        isDarkMode ? "fa-sun" : "fa-moon"
      );
    }

    // Toggle handler
    darkModeBtn.addEventListener("click", () => {
      const isDark = document.body.classList.toggle("dark-mode");
      localStorage.setItem("theme", isDark ? "dark" : "light");

      if (icon) {
        icon.classList.replace(
          isDark ? "fa-moon" : "fa-sun",
          isDark ? "fa-sun" : "fa-moon"
        );
      }
    });
  }
});

  



//   How to use it on your pages:
// 1. Add this to your HTML <head>:

//     <!-- ---Dark mode icon import--- -->
/* <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" /> */
// 2. Add the toggle button inside <body>:

// <button class="dark-mode-btn" title="Toggle Dark Mode">
//   <i class="fas fa-moon"></i>
// </button>
// 3. Link the script just before </body>:

// <script src="/js/darkmode.js"></script>
// Replace path/to/ with the actual path you store the JS file in.

// Make sure the css is in body.dark-mode not dark theme or nothing else