document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("email");

    if (localStorage.getItem("email")) {
        emailInput.value = localStorage.getItem("email"); // Precompila il campo email
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Blocca l'invio tradizionale del form

        const formData = new FormData(form);
        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Risposta dal server:", data); // Stampa direttamente l'oggetto JSON ricevuto
        
            if (data.success) {
                console.log("Login riuscito! Reindirizzamento in corso...");
                localStorage.setItem("email", emailInput.value);

                // Aspetta 2 secondi prima del redirect
                setTimeout(() => {
                    window.location.href = "../index.html";
                }, 2500);
            } else {
                console.log("Errore:", data.message);
                addErrorMessage("server", data.message);
            }
        })        
        .catch(error => {
            console.error("Errore di rete:", error);
            addErrorMessage("server", "Errore di connessione al server. Riprova più tardi.");
        });
        
    });

    function addErrorMessage(field, message) {
        let errorParagraph = document.createElement("p");
        errorParagraph.className = "error-message";
        errorParagraph.style.color = "red";
        errorParagraph.textContent = message;

        let whereAddError;
        switch (field) {
            case "server": whereAddError = form; break;
        }
        whereAddError.appendChild(errorParagraph);
    }
});