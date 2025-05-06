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

    // Gestione click su link con data-access senza usare inline JS
    document.querySelectorAll('[data-access]').forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const destination = link.getAttribute('data-access');
            controllaAccesso(destination);
        });
    });

    // Logout separato (non inline)
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (event) => {
            event.preventDefault();
            logout();
        });
    }

    // Gestione dei link con data-access (Hero, CTA, Footer, ecc.)
    document.querySelectorAll('[data-access]').forEach(link => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const destination = link.getAttribute('data-access');
            
            controllaAccesso(destination);
        });
    });

    // Inizializza l'immagine profilo
    getUserPicProfile();
});

function controllaAccesso(destination) {
    const baseName = destination.split('.')[0];
    const redirectPath = localStorage.getItem("user_id") 
        ? `../${baseName}/${destination}`
        : `../login-signup/login.html`;

    window.location.href = redirectPath;
}

function logout() {
    const conferma = confirm("⚠️ Stai per disconnetterti dal tuo account. Sei sicuro?");
    
    if (conferma) {
        // Se l'utente conferma, fai il logout
        alert("Logout effettuato!");
        localStorage.clear();
        window.location.href = "../login-signup/login.html";
    } else {
      // Se annulla, non succede nulla
    }
}

function getUserPicProfile(txt) {
    accountPic = document.getElementById("profile-pic");

    if (!txt || txt === "null") {
        if(localStorage.getItem("imagePic")){
            accountPic.src = localStorage.getItem("imagePic");
        }else{
            accountPic.src = "../img/utente.png";
        }
        return;
    }

    localStorage.setItem("imagePic", txt);
    accountPic.src = txt;
}

function getUserPicProfileAccount(){
    // Questa funzione verrà chiamata solo nelle sottocartelle dentro account
    accountPic = document.getElementById("profile-pic");

    if(localStorage.getItem("imagePic")){
        accountPic.src = "../" + localStorage.getItem("imagePic");
    }else{
        accountPic.src = "../img/utente.png";
    }
}