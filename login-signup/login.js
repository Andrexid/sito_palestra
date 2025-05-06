document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");
    const emailInput = document.getElementById("email");
    const messageEl = document.getElementById("message");

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
            messageEl.style.fontSize = "20px";
            if (data.success) {
                messageEl.textContent = "Login riuscito! Reindirizzamento in corso...";
                messageEl.style.color = "green";
                localStorage.setItem("email", emailInput.value);
                localStorage.setItem('user_id', data.user_id);

                // Aspetta 2.5 secondi prima del redirect
                setTimeout(() => {
                    window.location.href = "../index.html";
                }, 2500);
            } else {
                messageEl.textContent = data.message;
                messageEl.style.color = "red";
            }
        })        
        .catch(error => {
            console.error("Errore di rete:", error);
            messageEl.textContent = "Errore di connessione al server. Riprova più tardi.";
            messageEl.style.color = "red";
            messageEl.style.fontSize = "20px";
        });
    });    
});