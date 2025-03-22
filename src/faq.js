function searchFAQ() {
    let input = document.getElementById("search").value.toLowerCase();
    let questions = document.querySelectorAll(".faq");

    questions.forEach((faq) => {
        let questionText = faq.querySelector(".question").innerText.toLowerCase();
        faq.style.display = questionText.includes(input) ? "block" : "none";
    });
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Mostra il pulsante "Torna su" solo quando l'utente è sceso
window.onscroll = function() {
    document.getElementById("topBtn").style.display = (window.scrollY > 300) ? "block" : "none";
};

