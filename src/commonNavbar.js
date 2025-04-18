let accountPic;
document.addEventListener("DOMContentLoaded", function () {
    accountPic = document.getElementById("profile-pic");

    const profileBtn = document.getElementById("profile-pic");
    const profileMenu = document.getElementById("profile-menu");

    if (localStorage.getItem("email")) {
        getUserPicProfile(localStorage.getItem("imagePic"));
    } else {
        accountPic.src = "../img/utente.png";  // Immagine grigia se l'utente non è loggato
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

function controllaAccesso(destination, isHTML = true) {
    const basePath = isHTML ? "" : "../html/";
    const redirectPath = localStorage.getItem("email") 
        ? `${basePath}${destination}`
        : `${basePath}login.html`;
    
    window.location.href = redirectPath;
}

function logout() {
    alert("Logout effettuato!");
    localStorage.removeItem("email");
    window.location.href = "../html/login.html";
}

function getUserPicProfile(txt) {
    localStorage.removeItem("imagePic");
    localStorage.setItem("imagePic", txt);
    accountPic.src = txt;
}