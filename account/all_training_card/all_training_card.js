document.addEventListener("DOMContentLoaded", function () {
    const deleteCard = document.getElementById("deleteCard");
    const datiJson = deleteCard.dataset.id;
    const idCard = JSON.parse(datiJson);

    deleteCard.addEventListener('click', (event) => {
        eliminazione(idCard);
    });
});

var expirationText = document.querySelectorAll('.expiration');

// Ottieni la data di oggi
const today = new Date();
today.setHours(0, 0, 0, 0); // Rimuove l'orario per un confronto solo sulla data

expirationText.forEach(element => {
    // Converti expiration in un oggetto Date
    var expirationDate = new Date(element.innerHTML);

    // Controllo che la scheda sia scaduta
    if (expirationDate < today) {
        console.log("Scheda scaduta");
        element.style.color = "red"; // Evidenzia se scaduta
    } else {
        console.log("Scheda ancora valida");
    }
});

function eliminazione(cardId) {
    if (confirm("Vuoi davvero eliminare questa scheda di allenamento?")) {
        fetch(`./delete_training_card.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_card: cardId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload(); // o aggiorna la lista dinamicamente
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert("Si è verificato un errore durante l'eliminazione: " + error.message);
        });
    } else {
        alert("Eliminazione annullata");
    }
}


// Rende la funzione disponibile globalmente
window.eliminazione = eliminazione;