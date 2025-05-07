const inputs = document.querySelectorAll(".input-text-dis");
const unlockButton = document.querySelector(".unlock-button");
const submitButton = document.querySelector("input[type='submit']");

let isDisabled = true;

function unLockInputs() {
    inputs.forEach(input => {
        input.disabled = !input.disabled;
    });

    isDisabled = !isDisabled;

    // Attiva/disattiva il bottone submit
    submitButton.disabled = !submitButton.disabled;

    // Cambia testo del bottone di sblocco
    unlockButton.textContent = isDisabled ? "Sblocca Modifica" : "Blocca Modifica";
}