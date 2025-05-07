let accountPic;

document.addEventListener("DOMContentLoaded", () => {
    // Inizializza l'immagine profilo
    getUserPicProfile();

    if (!localStorage.getItem('theme')) {
      // Se non c'è un tema salvato, imposta 'dark' di default
      localStorage.setItem('theme', 'dark');
    }
    applyTheme();

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

    //scriviamo l'anno corrente nel footer
    document.getElementById('currentYear').textContent = new Date().getFullYear();
});

function controllaAccesso(destination) {
    const baseName = destination.split('.')[0]; // "account"
    const redirectPath = localStorage.getItem("email") 
        ? `${baseName}/${destination}`
        : `login-signup/login.html`;

    window.location.href = redirectPath;
}

function logout() {
  const conferma = confirm("⚠️ Stai per disconnetterti dal tuo account. Sei sicuro?");
    
  if (conferma) {
      // Se l'utente conferma, fai il logout
      alert("Logout effettuato!");
      localStorage.clear();
      window.location.href = "login-signup/login.html";
  } else {
    // Se annulla, non succede nulla
  }
}

function getUserPicProfile(txt) {
  accountPic = document.getElementById("profile-pic");

  localStorage.removeItem("imagePic");
  localStorage.setItem("imagePic", txt);
  accountPic.src = txt;
}

function getUserPicProfile(){
  accountPic = document.getElementById("profile-pic");

  if(localStorage.getItem("imagePic")){
    accountPic.src = localStorage.getItem("imagePic").replace("../", "");
  }else{
    accountPic.src = "img/utente.png";
  }
}

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
      const target = this.getAttribute('href');
      
      if (target === "#") return; // evita errore su href="#"

      e.preventDefault();
      const element = document.querySelector(target);

      if (element) {
          element.scrollIntoView({ behavior: 'smooth' });
      }
  });
});

function applyTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      document.body.classList.add('dark-mode');
    } else {
      document.body.classList.remove('dark-mode');
    }
  }

  function toggleTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      localStorage.setItem('theme', 'light');
    } else {
      localStorage.setItem('theme', 'dark');
    }
    applyTheme();
  }