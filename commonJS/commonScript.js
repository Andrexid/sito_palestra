// 
// document.addEventListener("DOMContentLoaded", function () {
//     

//     const profileBtn = document.getElementById("profile-pic");
//     const profileMenu = document.getElementById("profile-menu");

//     if (localStorage.getItem("email") && localStorage.getItem("imagePic")) {
//         // getUserPicProfile(localStorage.getItem("imagePic"));
//         accountPic.src = localStorage.getItem("imagePic");
//     } else {
//         accountPic.src = "../img/utente_without_bg.png";  // Immagine grigia se l'utente non è loggato
//     }

//     // Mostra/nasconde il menu al click sull'immagine profilo
//     profileBtn.addEventListener("click", function (event) {
//         event.stopPropagation(); // Evita la chiusura immediata del menu
//         profileMenu.style.display = (profileMenu.style.display === "flex") ? "none" : "flex";
//     });

//     // Nasconde il menu se l'utente clicca fuori
//     document.addEventListener("click", function () {
//         profileMenu.style.display = "none";
//     });

//     // Evita che il click sul menu lo chiuda subito
//     profileMenu.addEventListener("click", function (event) {
//         event.stopPropagation();
//     });
// });

// function controllaAccesso(destination){
//     if(localStorage.getItem("email")){
//         window.location.href = "../html/" + destination;
//     }else{
//         window.location.href = "../login-signup/login.html"
//     }
// }

// function logout() {
//     alert("Logout effettuato!");
//     localStorage.removeItem("email");
//     window.location.href = "../login-signup/login.html";
// }

// function getUserPicProfile(){
//     accountPic.src = localStorage.getItem("imagePic").replace("../", "");
// }

// let accountPic;
// function getUserPicProfile(txt) {
//   accountPic = document.getElementById("profile-pic");

//   localStorage.removeItem("imagePic");
//   localStorage.setItem("imagePic", txt);
//   accountPic.src = txt;
// }

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