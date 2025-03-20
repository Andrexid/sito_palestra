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
            document.getElementById("dateBirth").textContent = data.data_nascita;
            document.getElementById("sex").textContent = data.sesso;
        })
        .catch(error => console.error("Errore nel recupero dati:", error));
    
    fetch("../json/motivational_quotes.json")
    .then(response => response.json())
    .then(quotes => {
        let randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        document.getElementById("motivational-quote").textContent = `"${randomQuote.quote}"`;
        document.getElementById("motivational-author").textContent = `- ${randomQuote.author}`;
    });
      
});

let username = document.getElementById("username");
let userEmail = document.getElementById("email");
let dateBirth = document.getElementById("dateBirth");
let sex = document.getElementById("sex");
let btnModify = document.getElementById("btnModify");

let isEditing = false;
let originalElements = []; // Array per memorizzare gli elementi originali

function modifyProfile() {
    //Modifica il testo del btn
    if (btnModify.textContent === "✏️ Modifica Profilo") {
        btnModify.textContent = "✅ Conferma Modifiche"; // Cambia testo ed emoji
    } else {
        btnModify.textContent = "✏️ Modifica Profilo"; // Torna al testo originale
    }

    let editableElements = document.querySelectorAll("[data-editable]");
    let formData = new FormData();

    editableElements.forEach(element => {
        if (!isEditing) {
            // Memorizza l'elemento originale prima di sostituirlo
            originalElements.push(element.cloneNode(true));
        
            // Crea un contenitore per l'input e il label
            let container = document.createElement("div");
            container.className = "input-container";
        
            // Crea il label
            let label = document.createElement("label");
            label.textContent = element.id === "username" ? "Nome" :
                                element.id === "dateBirth" ? "Data di Nascita" :
                                element.id === "sex" ? "Sesso" : "";
            label.htmlFor = element.id; // Associa il label all'input
        
            // Converte <h2> e <p> in <input>
            let input = document.createElement("input");
        
            // Verifica se l'elemento ha l'attributo tag="date"
            if (element.getAttribute("tag") === "date") {
                input.type = "date"; // Imposta il tipo come "date"
            } else {
                input.type = "text"; // Altrimenti, usa il tipo "text"
            }
        
            input.value = element.textContent; // Imposta il valore iniziale
            input.dataset.editable = "true"; // Mantiene l'attributo
            input.id = element.id; // Mantiene l'ID
        
            // Aggiungi il label e l'input al contenitore
            container.appendChild(label);
            container.appendChild(input);
        
            // Sostituisce l'elemento con il contenitore
            element.replaceWith(container);
        } else {
            // Recupera il valore dell'input e lo salva in FormData
            formData.append(element.id, element.value);
        }
    });

    if (isEditing) {
        // Invia i dati al server solo quando si sta uscendo dalla modalità modifica
        fetch("../php/modify_user_data_profile.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text()) // Usa .text() per vedere esattamente la risposta
        .then(text => {
            console.log("Risposta ricevuta dal server:", text);
            return JSON.parse(text); // Poi converti in JSON
        })
        .then(data => {
            if (data.error) {
                console.error("Errore dal server:", data.error);
                return;
            }
            console.log("Dati salvati correttamente:", data);
    
            // Ripristina gli elementi originali
            editableElements.forEach((element, index) => {
                // Sostituisci il contenitore con l'elemento originale
                element.closest(".input-container").replaceWith(originalElements[index]);
            });
    
            // Svuota l'array degli elementi originali
            originalElements = [];
    
            // Aggiorna i dati nella pagina
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
                document.getElementById("dateBirth").textContent = data.data_nascita;
                document.getElementById("sex").textContent = data.sesso;
            })
            .catch(error => console.error("Errore nel recupero dati:", error));
        })
        .catch(error => console.error("Errore nel salvataggio:", error));
    }

    // Cambia stato della modalità di modifica
    isEditing = !isEditing;
}