document.addEventListener("DOMContentLoaded", function () {
    let profilePic = document.getElementById("profile-pic");

    const profileBtn = document.getElementById("profile-pic");
    const profileMenu = document.getElementById("profile-menu");

    if (localStorage.getItem("email")) {
        profilePic.src = "../img/utente_without_bg.png";  // Immagine normale se l'utente è loggato
    } else {
        profilePic.src = "../img/utente.png";  // Immagine grigia se l'utente non è loggato
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

function controllaAccesso(destination){
    if(localStorage.getItem("email")){
        alert("cc")
    }else{
        alert(destination);
    }
}

function logout() {
    alert("Logout effettuato!");
    localStorage.removeItem("email");
    window.location.href = "../html/login.html";
}