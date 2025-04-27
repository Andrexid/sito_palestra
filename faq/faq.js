// function searchFAQ() {
//     let input = document.getElementById("search").value.toLowerCase();
//     let questions = document.querySelectorAll(".faq");

//     questions.forEach((faq) => {
//         let questionText = faq.querySelector(".question").innerText.toLowerCase();
//         faq.style.display = questionText.includes(input) ? "block" : "none";
//     });
// }

// function scrollToTop() {
//     window.scrollTo({ top: 0, behavior: 'smooth' });
// }

document.addEventListener("DOMContentLoaded", function () {
    const faqs = document.querySelectorAll(".faq");
    faqs.forEach((faq) => {
      faq.addEventListener("click", function () {
        faqs.forEach((el) => {
          if (el !== this) {
            el.classList.remove("open");
            el.querySelector(".answer").style.display = "none";
          }
        });
        const answer = this.querySelector(".answer");
        const isOpen = this.classList.contains("open");
        if (!isOpen) {
          this.classList.add("open");
          answer.style.display = "block";
          this.scrollIntoView({ behavior: "smooth", block: "center" });
        } else {
          this.classList.remove("open");
          answer.style.display = "none";
        }
      });
    });
  });

  function searchFAQ() {
    const input = document.getElementById("search").value.toLowerCase();
    const faqs = document.querySelectorAll(".faq");
    faqs.forEach((faq) => {
      const question = faq.querySelector(".question").innerText.toLowerCase();
      if (question.includes(input)) {
        faq.style.display = "block";
      } else {
        faq.style.display = "none";
      }
    });
  }

// Mostra il pulsante "Torna su" solo quando l'utente è sceso
// window.onscroll = function() {
//     document.getElementById("topBtn").style.display = (window.scrollY > 300) ? "block" : "none";
// };