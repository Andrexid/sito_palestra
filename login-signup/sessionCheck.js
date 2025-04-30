// Questo script controlla se c’è l’ID nel localStorage e lo invia al PHP:

window.addEventListener('DOMContentLoaded', async () => {
    const userId = localStorage.getItem('user_id');

    if (userId) {
        const formData = new FormData();
        formData.append('user_id', userId);

        try {
            const response = await fetch('checkLocalStorage.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                console.error("Errore sessione:", result.message);
            }
        } catch (error) {
            console.error("Errore nella chiamata:", error);
        }
    } else {
        console.log("Nessun utente nel localStorage.");
    }
});
