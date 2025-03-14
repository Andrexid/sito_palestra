document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");
    const emailInput = document.getElementById("email");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Blocca l'invio tradizionale del form

        if (!validateForm()) {
            return;
        }

        const formData = new FormData(form);
        fetch("../php/registrazione.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem("email", emailInput.value);
                window.location.href = "login.html";
            } else {
                addErrorMessage("server", data.message);
            }
        })
        .catch(error => {
            console.error("Errore di rete: ", error);
            addErrorMessage("server", "Errore di connessione al server. Riprova più tardi.");
        });
    });

    if (sessionStorage.getItem("email")) {
        emailInput.value = sessionStorage.getItem("email");
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
            case "name": whereAddError = document.getElementById("nome").parentElement; break;
            case "surname": whereAddError = document.getElementById("cognome").parentElement; break;
            case "password": whereAddError = document.getElementById("password").parentElement; break;
            case "equalPassword": whereAddError = document.getElementById("password-v").parentElement; break;
            case "server": whereAddError = form; break;
        }
        whereAddError.appendChild(errorParagraph);
    }
});
