document.addEventListener("DOMContentLoaded", function () {
    console.log(sessionStorage.getItem("email"))
    let profilePic = document.getElementById("profile-pic");

    if (sessionStorage.getItem("email")) {
        profilePic.src = "img/utente_without_bg.png";  // Immagine normale se l'utente è loggato
    } else {
        profilePic.src = "img/utente.png";  // Immagine grigia se l'utente non è loggato
    }
});

// crea la funzione controllaAccesso() per fare in modo che venga controllato se ha fatto l'accesso prima di farlo andare nelle varie pagine