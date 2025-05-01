let accountPic;

document.addEventListener("DOMContentLoaded", () => {
    const menu = document.querySelector(".nav-links");
    const profileBtn = document.getElementById("profile-pic");
    const profileMenu = document.getElementById("profile-menu");
    const hamburger = document.querySelector(".hamburger-menu");

    // Toggle menu quando si clicca sull'hamburger
    hamburger.addEventListener("click", (event) => {
        event.stopPropagation(); // Evita che il click si propaghi e chiuda subito il menu
        menu.classList.toggle("show");
        document.body.classList.toggle("menu-open");
    });

    // Chiudi il menu quando si clicca su un link interno
    document.querySelectorAll(".nav-links li").forEach(link => {
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

    // Mostra/nasconde il menu al click sull'immagine profilo
    profileBtn.addEventListener("click", function (event) {
        event.stopPropagation();
        profileMenu.style.display = (profileMenu.style.display === "flex") ? "none" : "flex";
    });
});

function controllaAccesso(destination) {
    const baseName = destination.split('.')[0];
    const redirectPath = localStorage.getItem("user_id") 
        ? `../${baseName}/${destination}`
        : `../login-signup/login.html`;

    window.location.href = redirectPath;
}

function logout() {
    alert("Logout effettuato!");
    localStorage.clear(); // Elimina anche user_id e imagePic
    window.location.href = "../login-signup/login.html";
}

function getUserPicProfile(txt) {
    if (!txt || txt === "null") {
        accountPic.src = "../img/utente.png";
        return;
    }

    localStorage.setItem("imagePic", txt);
    accountPic.src = txt;
}
