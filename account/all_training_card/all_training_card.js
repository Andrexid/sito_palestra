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
        // Effettua una richiesta fetch per eliminare la scheda
        fetch(`./delete_training_card.php?id_card=${cardId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => {
                if (response.ok) {
                    alert("Scheda eliminata con successo!");
                    // Ricarica la pagina per aggiornare la lista
                    // window.location.reload();
                    // window.location.href=window.location.href
                } else {
                    throw new Error('Errore nella cancellazione'.response);
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert("Si è verificato un errore durante l'eliminazione");
            });
    } else {
        alert("Eliminazione annullata");
    }
}

// Rende la funzione disponibile globalmente
window.eliminazione = eliminazione;