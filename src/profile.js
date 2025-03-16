document.addEventListener("DOMContentLoaded", function () {
    fetch("../php/get_user_data_profile.php")
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Inserisce i dati nella pagina
            document.getElementById("username").textContent = data.nome + " " + data.cognome;
            document.getElementById("email").textContent = data.email;
        })
        .catch(error => console.error("Errore nel recupero dati:", error));
    
    fetch("../json/motivational_quotes.json")
    .then(response => response.json())
    .then(quotes => {
        let randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        document.getElementById("motivational-quote").innerText = `"${randomQuote.quote}" - ${randomQuote.author}`;
    });
      
});
