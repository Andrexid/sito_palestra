document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".nav-links");
    const hamburger = document.querySelector(".hamburger-menu");

    // Toggle menu quando si clicca sull'hamburger
    hamburger.addEventListener("click", (event) => {
        event.stopPropagation(); // Evita che il click si propaghi e chiuda subito il menu
        menu.classList.toggle("show");
        document.body.classList.toggle("menu-open");
    });

    // Chiudi il menu quando si clicca su un link interno
    document.querySelectorAll(".nav-links a").forEach(link => {
        link.addEventListener("click", () => {
            menu.classList.remove("show");
            document.body.classList.remove("menu-open");
        });
    });

    // Chiudi il menu se si clicca fuori
    window.addEventListener("click", (event) => {
        if (!menu.contains(event.target) && !hamburger.contains(event.target)) {
            menu.classList.remove("show");
            document.body.classList.remove("menu-open");
        }
    });

    // Chiudi il menu quando l'utente scrolla la pagina
    window.addEventListener("scroll", () => {
        menu.classList.remove("show");
        document.body.classList.remove("menu-open");
    });
});