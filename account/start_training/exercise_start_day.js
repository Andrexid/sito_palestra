document.addEventListener("DOMContentLoaded", function () {
    // Gestione input "numero di set"
    const setInputs = document.querySelectorAll(".set-input");

    setInputs.forEach((input) => {
        input.addEventListener("change", function () {
        const esercizioId = this.dataset.id;
        const container = document.getElementById("reps-container-" + esercizioId);
        const numSet = parseInt(this.value, 10);

        container.innerHTML = ""; // Svuota i set esistenti

        for (let i = 1; i <= numSet; i++) {
            const div = document.createElement("div");
            div.className = "set-container";

            const label = document.createElement("label");
            label.className = "set-label";
            label.textContent = `Set ${i}:`;

            const repsInput = document.createElement("input");
            repsInput.type = "number";
            repsInput.name = `reps[${esercizioId}][]`;
            repsInput.placeholder = "Reps";
            repsInput.min = 1;
            repsInput.required = true;

            const weightInput = document.createElement("input");
            weightInput.type = "number";
            weightInput.name = `weights[${esercizioId}][]`;
            weightInput.placeholder = "Peso (kg)";
            weightInput.step = 0.5;
            weightInput.min = 0;

            div.appendChild(label);
            div.appendChild(repsInput);
            div.appendChild(weightInput);
            container.appendChild(div);
        }
        });
    });
});
  

function generaInputSet(id) {
    const setInput = document.getElementById(`set-input-${id}`);
    const setCount = parseInt(setInput.value);
    const container = document.getElementById(`reps-container-${id}`);
    container.innerHTML = "";
    for (let i = 1; i <= setCount; i++) {
        container.innerHTML += `
        <div class="set-container">
            <label class="set-label">Set ${i}:</label>
            <input type="number" name="reps[${id}][]" placeholder="Reps" min="1" required>
            <input type="number" name="weights[${id}][]" placeholder="Peso (kg)" step="0.5" min="0">
        </div>
    `;
    }
}

document.getElementById("training-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("save_training_ajax.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const resultBox = document.getElementById("result");
            resultBox.style.display = "block";
            resultBox.innerHTML = `
                <strong>🎉 Allenamento salvato con successo!</strong><br><br>
                Hai guadagnato <strong>${data.xp}</strong> XP<br>
                Totale XP: <strong>${data.total_xp}</strong><br>
                Verrai reindirizzato tra pochi secondi <br>
            `;
            
            setTimeout(() => {
                window.location.href = "../account.php";
            }, 2000);
        })
        .catch(err => alert("Errore nel salvataggio: " + err));
});

function redirectAfterDelay() {
    // (Opzionale) Mostra un messaggio mentre aspetti
    // Aspetta 5 secondi e poi cambia pagina
    setTimeout(function() {
        window.location.href = '../account.php'; // Cambia "nome-pagina.php" con la destinazione
    }, 5000);
}