document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("email");

    if (sessionStorage.getItem("email")) {
        emailInput.value = sessionStorage.getItem("email"); // Precompila il campo email
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Blocca l'invio tradizionale del form

        const formData = new FormData(form);
        fetch("../php/login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text()) // Usa text() invece di json()
        .then(data => {
            console.log("Risposta dal server:", data); // Stampa l'output ricevuto
            let json;
            try {
                json = JSON.parse(data); // Prova a convertire in JSON
            } catch (error) {
                console.error("Errore nel parsing JSON:", error, "Risposta ricevuta:", data);
                addErrorMessage("server", "Errore nel formato della risposta dal server.");
                return;
            }
            
            if (json.success) {
                sessionStorage.setItem("email", emailInput.value);
                window.location.href = "../index.html";
            } else {
                addErrorMessage("server", json.message);
            }
        })
        .catch(error => {
            console.error("Errore di rete:", error);
            addErrorMessage("server", "Errore di connessione al server. Riprova più tardi.");
        });
        
    });

    if (sessionStorage.getItem("email")) {
        emailInput.value = sessionStorage.getItem("email");
    }

    function removeExistingErrors() {
        document.querySelectorAll(".error-message").forEach(error => error.remove());
    }

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