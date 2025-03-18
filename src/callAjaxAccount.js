// Recupero il nome per la pagina account
fetch('../php/get_user_data_profile.php')
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Errore:", data.error);
        } else {
            console.log("Nome utente:", data.nome);
            document.getElementById("nomeUtente").innerText = data.nome; // Mostra il nome sulla pagina
        }
    })
    .catch(error => console.error('Errore nella richiesta:', error));
