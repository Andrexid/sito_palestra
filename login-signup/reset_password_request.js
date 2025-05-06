document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("reset-form");
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        document.getElementById("feedback-area").innerHTML = "";
    
        const formData = new FormData(form);
        fetch("send_reset_link.php", {
        method: "POST",
        body: formData
        })
        .then(response => response.json())
        .then(data => {
        addMessage(data.success ? "success" : "error", data.message);
        })
        .catch(error => {
        console.error("Errore di rete:", error);
        addMessage("error", "Errore di connessione al server. Riprova più tardi.");
        });
    });
    
    function addMessage(type, message) {
        const feedback = document.createElement("p");
        feedback.textContent = message;
        feedback.style.color = type === "success" ? "green" : "red";
        document.getElementById("feedback-area").appendChild(feedback);
    }
});