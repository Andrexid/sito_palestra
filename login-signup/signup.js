document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");
    const emailInput = document.getElementById("email");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Blocca l'invio tradizionale del form

        if (!validateForm()) {
            return;
        }

        const formData = new FormData(form);
        fetch("signup.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {        
                // Mostra il messaggio di conferma
                const confirmMessage = document.getElementById("confirmMessage");
                confirmMessage.textContent = "Registrazione completata con successo! Reindirizzamento in corso...";
                confirmMessage.style.color = "green";
                confirmMessage.style.fontWeight = "bold";
                confirmMessage.style.textAlign = "center";
                confirmMessage.style.marginTop = "10px";
        
                // Avvia l'invio dell'email in background
                fetch("sendEmail.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text()) // Prima ottieni il testo della risposta
                .then(text => {
                    try {
                        const data = JSON.parse(text); // Prova a convertire in JSON
                        console.log(data.message);
                    } catch (error) {
                        console.error("Risposta non valida da sendEmail.php:", text);
                    }
                })
                .catch(error => console.error("Errore nell'invio dell'email:", error));                
        
                // Aspetta 2 secondi prima del redirect
                setTimeout(() => {
                    window.location.href = "login.html";
                }, 2500);
        
            } else {
                addErrorMessage("server", data.message);
            }
        })
        .catch(error => {
            console.error("Errore di rete: ", error);
            addErrorMessage("server", "Errore di connessione al server. Riprova più tardi.");
        });
        
    });

    if (localStorage.getItem("email")) {
        emailInput.value = localStorage.getItem("email");
    }

    function validateForm() {
        let isValid = true;
        removeExistingErrors();

        const nome = document.querySelector("input[name='nome']").value.trim();
        const cognome = document.querySelector("input[name='cognome']").value.trim();
        const password = document.querySelector("input[name='password']").value;
        const confirmPassword = document.querySelector("input[name='password-v']").value;

        if (/\d/.test(nome)) {
            addErrorMessage("name", "Il nome non deve contenere numeri!");
            isValid = false;
        }
        if (/\d/.test(cognome)) {
            addErrorMessage("surname", "Il cognome non deve contenere numeri!");
            isValid = false;
        }
        if (password.length < 5) {
            addErrorMessage("password", "La password deve contenere almeno 5 caratteri!");
            isValid = false;
        }
        if (password !== confirmPassword) {
            addErrorMessage("equalPassword", "Le due password devono coincidere!");
            isValid = false;
        }
        return isValid;
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
            case "name": 
                whereAddError = document.getElementById("nome"); 
                break;
            case "surname": 
                whereAddError = document.getElementById("cognome"); 
                break;
            case "password": 
                whereAddError = document.getElementById("password"); 
                break;
            case "equalPassword": 
                whereAddError = document.getElementById("password-v"); 
                break;
            case "server": 
                whereAddError = form; 
                break;
        }
        
        if(whereAddError) {
            // Aggiungi l'errore DOPO l'elemento input (non dentro)
            whereAddError.parentNode.appendChild(errorParagraph);
        } else {
            console.error("Elemento padre non trovato per:", field);
        }
    }
});
