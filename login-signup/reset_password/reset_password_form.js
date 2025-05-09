document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("new-password-form");
  const tokenInput = document.getElementById("token");

  // Estrae il token dall'URL
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get("token");

  if (!token) {
    showMessage("error", "Token non valido o mancante.");
    form.style.display = "none";
    return;
  }

  tokenInput.value = token;

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch("../reset_password.php", {
      method: "POST",
      body: formData
    })
      .then(res => res.text())
      .then(response => {
        showMessage("success", response);

        // Aspetta 2 secondi prima del redirect
        setTimeout(() => {
            window.location.href = "../login.html";
        }, 2500);
      })
      .catch(() => {
        showMessage("error", "Errore nella connessione al server.");
      });
  });

  function showMessage(type, message) {
    const area = document.getElementById("feedback-area");
    area.textContent = message;
    area.style.color = type === "success" ? "green" : "red";
  }
});
