document.addEventListener("DOMContentLoaded", () => {
    const toggleButton = document.getElementById("dark-mode-toggle");
    
    // Check if button exists
    if (toggleButton) {
        const currentTheme = localStorage.getItem("theme");

        // 1. Check if a theme is saved in memory
        if (currentTheme === "dark") {
            document.body.classList.add("dark-mode");
        }

        // 2. Listen for the button click
        toggleButton.addEventListener("click", () => {
            // Toggle the .dark-mode class on the <body>
            document.body.classList.toggle("dark-mode");

            // 3. Save the choice in memory
            let theme = "light";
            if (document.body.classList.contains("dark-mode")) {
                theme = "dark";
            }
            localStorage.setItem("theme", theme);
        });
    }
});