let accountPic;
document.addEventListener("DOMContentLoaded", function () {
    accountPic = document.getElementById("profile-pic");

    const profileBtn = document.getElementById("profile-pic");
    const profileMenu = document.getElementById("profile-menu");

    if (localStorage.getItem("email") && localStorage.getItem("imagePic")) {
        // getUserPicProfile(localStorage.getItem("imagePic"));
        accountPic.src = localStorage.getItem("imagePic");
    } else {
        accountPic.src = "img/utente.png";  // Immagine grigia se l'utente non è loggato
    }

    // Mostra/nasconde il menu al click sull'immagine profilo
    profileBtn.addEventListener("click", function (event) {
        event.stopPropagation(); // Evita la chiusura immediata del menu
        profileMenu.style.display = (profileMenu.style.display === "flex") ? "none" : "flex";
    });

    // Nasconde il menu se l'utente clicca fuori
    document.addEventListener("click", function () {
        profileMenu.style.display = "none";
    });

    // Evita che il click sul menu lo chiuda subito
    profileMenu.addEventListener("click", function (event) {
        event.stopPropagation();
    });
});

function controllaAccesso(destination) {
    const baseName = destination.split('.')[0]; // "account"
    const redirectPath = localStorage.getItem("email") 
        ? `${baseName}/${destination}`
        : `login-signup/login.html`;

    window.location.href = redirectPath;
}

function logout() {
    alert("Logout effettuato!");
    localStorage.removeItem("email");
    window.location.href = "login-signup/login.html";
}

function getUserPicProfile(txt) {
    localStorage.removeItem("imagePic");
    localStorage.setItem("imagePic", txt);
    accountPic.src = txt;
}

function getUserPicProfile(){
    accountPic.src = localStorage.getItem("imagePic").replace("../", "");
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

  document.addEventListener('DOMContentLoaded', function() {
    // Quando la pagina carica
    if (!localStorage.getItem('theme')) {
      // Se non c'è un tema salvato, imposta 'dark' di default
      localStorage.setItem('theme', 'dark');
    }
    applyTheme();
  });