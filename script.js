/*
 * College Digital Notice Board - Combined Logic
 */

document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. Read More Logic ---
    function initReadMore() {
        const allNotices = document.querySelectorAll(".notice-content");
        const charLimit = 200;

        allNotices.forEach(notice => {
            if (notice.dataset.processed) return; 
            
            const fullText = notice.textContent;
            if (fullText.length > charLimit) {
                const shortText = fullText.substring(0, charLimit) + "...";
                notice.textContent = shortText;
                
                const link = document.createElement("a");
                link.href = "#";
                link.textContent = "Read More";
                link.className = "read-more-link";
                
                notice.parentNode.insertBefore(link, notice.nextSibling);

                link.addEventListener("click", (e) => {
                    e.preventDefault();
                    if (link.textContent === "Read More") {
                        notice.textContent = fullText;
                        link.textContent = "Show Less";
                    } else {
                        notice.textContent = shortText;
                        link.textContent = "Read More";
                    }
                });
                notice.dataset.processed = "true";
            }
        });
    }
    initReadMore();

    // --- 2. Live Search Listener ---
    const searchInput = document.getElementById("search-input");
    const noticeContainer = document.getElementById("notice-container");

    if (searchInput && noticeContainer) {
        searchInput.addEventListener("keyup", () => {
            const query = searchInput.value;
            const formData = new FormData();
            formData.append("query", query);

            fetch("search.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                noticeContainer.innerHTML = data;
                initReadMore(); 
            })
            .catch(error => console.error("Error:", error));
        });
    }
});

// --- 3. Back to Top Button ---
const topButton = document.getElementById("back-to-top-btn");
if (topButton) {
    window.onscroll = function() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            topButton.style.display = "block";
            topButton.style.opacity = "1";
        } else {
            topButton.style.opacity = "0";
            setTimeout(() => { 
                if (document.body.scrollTop < 100 && document.documentElement.scrollTop < 100) {
                    topButton.style.display = "none";
                }
            }, 300);
        }
    };
    topButton.addEventListener("click", () => {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    });
}

// --- 4. Print Individual Notice Function ---
function printNotice(button) {
    // Find the card
    const card = button.closest('.notice-card');
    // Mark it for printing
    card.classList.add('printing');
    // Print
    window.print();
    // Cleanup after print dialog closes
    setTimeout(() => {
        card.classList.remove('printing');
    }, 500);
}
// --- 4. Print Individual Notice Function ---
// This handles the click
function printNotice(button) {
    // 1. Find the specific card
    const card = button.closest('.notice-card');
    
    // 2. Add the 'printing' class (This tells CSS to hide everything else)
    card.classList.add('printing');
    
    // 3. Open the Print Dialog
    window.print();
    
    // 4. Cleanup: Remove the class after printing
    // We wait 500ms to ensure the browser captured the specific style
    setTimeout(() => {
        card.classList.remove('printing');
    }, 500);
}