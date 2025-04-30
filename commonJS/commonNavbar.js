let accountPic;

document.addEventListener("DOMContentLoaded", function () {
    accountPic = document.getElementById("profile-pic");

    const profileBtn = document.getElementById("profile-pic");
    const profileMenu = document.getElementById("profile-menu");

    const userId = localStorage.getItem("user_id");
    const userImage = localStorage.getItem("imagePic");

    if (userId) {
        // Utente loggato, mostra la sua immagine se esiste
        getUserPicProfile(userImage || "../img/utente.png");
    } else {
        // Nessun utente loggato: immagine di default
        accountPic.src = "../img/utente.png";
        // Se vuoi disabilitare il menu o fare redirect:
        profileBtn.style.pointerEvents = "none";
        profileBtn.title = "Accedi per usare il profilo";
        profileMenu.style.display = "none";
    }

    // Mostra/nasconde il menu al click sull'immagine profilo
    profileBtn.addEventListener("click", function (event) {
        event.stopPropagation();
        profileMenu.style.display = (profileMenu.style.display === "flex") ? "none" : "flex";
    });

    document.addEventListener("click", function () {
        profileMenu.style.display = "none";
    });

    profileMenu.addEventListener("click", function (event) {
        event.stopPropagation();
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
