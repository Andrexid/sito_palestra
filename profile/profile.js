document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btnModify").addEventListener('click', (event) => {
        modifyProfile();
    });

    getUserDataProfile();
    fetchMotivationalQuote();
    calcolateBMI();
});

function fetchMotivationalQuote() {
    fetch("motivational_quotes.json")
    .then(response => response.json())
    .then(quotes => {
        let randomQuote = quotes[Math.floor(Math.random() * quotes.length)];
        document.getElementById("motivational-quote").textContent = `"${randomQuote.quote}"`;
        document.getElementById("motivational-author").textContent = `- ${randomQuote.author}`;
    });
}

function getUserDataProfile() {
    fetch("get_user_data_profile.php")
        .then(response => response.text())
        .then(text => {
            const data = JSON.parse(text); // 👈 parsing corretto

            if (data.error) {
                console.error(data.error);
                return;
            }

            // Salva i dati in sessionStorage
            sessionStorage.setItem("username", `${data.nome} ${data.cognome}`);
            sessionStorage.setItem("email", data.email);
            sessionStorage.setItem("dateBirth", data.data_nascita);
            sessionStorage.setItem("sex", data.sesso);
            sessionStorage.setItem("weight", data.peso);
            sessionStorage.setItem("height", data.altezza);
            sessionStorage.setItem("EXP", data.puntiEXP);
            sessionStorage.setItem("nTrainings", data.nAllenamenti);

            // Nuovi dati
            sessionStorage.setItem("xpTotale", data.xpTotale);
            sessionStorage.setItem("numeroObiettiviAttivi", data.numeroObiettiviAttivi);
            sessionStorage.setItem("totaleSetCompletati", data.totaleSetCompletati);
            sessionStorage.setItem("totaleRepsCompletate", data.totaleRepsCompletate);
            sessionStorage.setItem("esercizioPiuFrequente", data.esercizioPiuFrequente);

            // Aggiorna il DOM con i dati
            if(data.fotoProfilo){
                updateProfileUI(data.fotoProfilo);
            }else{
                updateProfileUI("../img/utente.png");
            }
            drawBadges();
            calcolateBMI();
        })
        .catch(error => console.error("❌ Errore nel recupero dati:", error));
}

function updateProfileUI(foto) {
    document.getElementById("username").textContent = sessionStorage.getItem("username") || "";
    document.getElementById("email").textContent = sessionStorage.getItem("email") || "";

    const dateBirth = sessionStorage.getItem("dateBirth");
    document.getElementById("dateBirth").textContent = dateBirth ? "Data di nascita: " + dateBirth + " (YYYY-MM-GG)" : "";

    const sex = sessionStorage.getItem("sex");
    document.getElementById("sex").textContent = sex ? "Sesso: " + sex : "";

    const weight = sessionStorage.getItem("weight");
    document.getElementById("weight").textContent = weight ? "Peso: " + weight + " kg" : "";

    const height = sessionStorage.getItem("height");
    document.getElementById("height").textContent = height ? "Altezza: " + height + " cm" : "";

    document.getElementById("profile-pic-profile").src = foto;
    getUserPicProfile(foto);

    // Aggiornamento delle statistiche
    document.getElementById("most-xp").textContent = sessionStorage.getItem("xpTotale") || 0;
    document.getElementById("goals-active").textContent = sessionStorage.getItem("numeroObiettiviAttivi") || 0;
    document.getElementById("sets-completed").textContent = sessionStorage.getItem("totaleSetCompletati") || 0;
    document.getElementById("reps-completed").textContent = sessionStorage.getItem("totaleRepsCompletate") || 0;
    document.getElementById("top-exercise").textContent = sessionStorage.getItem("esercizioPiuFrequente") || "-";
}

let isEditing = false;
let btnModify = document.getElementById("btnModify");
let originalElements = []; // Array per memorizzare gli elementi originali
let formData = new FormData();
let editableElements;

function modifyProfile() {
    //Modifica il testo del btn
    btnModify.textContent = isEditing ? "✏️ Modifica Profilo" : "✅ Conferma Modifiche";

    editableElements = document.querySelectorAll("[data-editable]");

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
                                element.id === "sex" ? "Sesso" :
                                element.id === "weight" ? "Peso (kg)" :
                                element.id === "height" ? "Altezza (cm)" : "";
            label.htmlFor = element.id; // Associa il label all'input
    
            let input;
            // Prendi il valore dalla sessionStorage invece che dal textContent
            let previousValue = sessionStorage.getItem(element.id) || "";
    
            if (element.getAttribute("tag") === "sex") {
                // Se è il campo "sex", crea un <select>
                input = document.createElement("select");
                let options = ["Uomo", "Donna", "Altro"];
                options.forEach(optionText => {
                    let option = document.createElement("option");
                    option.value = optionText;
                    option.textContent = optionText;
                    if (previousValue === optionText) {
                        option.selected = true;
                    }
                    input.appendChild(option);
                });
            } else if (element.getAttribute("tag") === "date") {
                // Se è un campo data
                input = document.createElement("input");
                input.type = "date";
                input.value = previousValue === "0000-00-00" || previousValue === "" ? 
                    new Date().toISOString().split('T')[0] : previousValue;
            } else if (element.getAttribute("tag") === "weight" || element.getAttribute("tag") === "height") {
                // Se è peso o altezza, crea un input numerico
                input = document.createElement("input");
                input.type = "number";
                input.min = "1";
                input.value = previousValue && parseInt(previousValue) > 0 ? previousValue : "";
            } else {
                // Per tutti gli altri campi (testo generico)
                input = document.createElement("input");
                input.type = "text";
                input.value = previousValue;
            }
    
            input.dataset.editable = "true";
            input.id = element.id;
    
            // Aggiungi il label e l'input al contenitore
            container.appendChild(label);
            container.appendChild(input);
    
            // Sostituisce l'elemento con il contenitore
            element.replaceWith(container);
        } else {
            // Recupera il valore dell'input e lo salva in FormData
            let input = document.getElementById(element.id);
            formData.append(element.id, input.value);
            
            // Aggiorna anche la sessionStorage con il nuovo valore
            sessionStorage.setItem(element.id, input.value);
        }
    });

    if (isEditing) {
        modifyDatabase();
    }

    // Cambia stato della modalità di modifica
    isEditing = !isEditing;
}

function modifyDatabase(){
    let isPic = false;
    // Invia i dati al server solo quando si sta uscendo dalla modalità modifica
    fetch("modify_user_data_profile.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text()) // Usa .text() per vedere esattamente la risposta
    .then(text => {
        return JSON.parse(text); // Poi converti in JSON
    })
    .then(data => {
        if (data.error) {
            console.error("Errore dal server:", data.error);
            return;
        }

        for(const key of formData.keys()){
            if(key === "profilePic"){
                isPic = true;
            }
        }

        if(!isPic){
            // Ripristina gli elementi originali
            editableElements.forEach((element, index) => {
                // Sostituisci il contenitore con l'elemento originale
                element.closest(".input-container").replaceWith(originalElements[index]);
            });
        }

        // Svuota l'array degli elementi originali
        originalElements = [];

        // Creare un array con tutte le chiavi e rimuoverle una per una
        for (let key of formData.keys()) {
            formData.delete(key);
        }

        // Aggiorna i dati nella pagina
        getUserDataProfile();
        calcolateBMI();
    })
    .catch(error => console.error("Errore nel salvataggio:", error));
}

let badges = [
    "Il viaggio inizia", "Primo Passo", "Iniziato il Viaggio", 
    "Fitness Warrior", "Resiliente", "Iron Man", 
    "Fitness Machine", "Inarrestabile", "Bestia della Palestra", 
    "Atleta d’élite", "Leggenda del Fitness"
];

// Soglie di esperienza per ogni badge
let badgeThresholds = [0, 500, 1500, 3000, 7500, 15000, 30000, 50000, 100000, 200000, 500000];

function drawBadges() {
    let nTrainings = document.querySelector("#nTrainings");
    nTrainings.innerHTML = `Hai completato <strong>${sessionStorage.getItem("nTrainings") ?? "0"}</strong> allenamenti! 🚀`;

    let secondImg = document.getElementById("secondImg");
    let thirdImg = document.getElementById("thirdImg");

    let secondP = document.querySelector("#secondP");
    let thirdP = document.querySelector("#thirdP");

    let positionThresholds = 0;

    for (let i = 0; i < badges.length && (sessionStorage.getItem("EXP") > badgeThresholds[i]); i++) {
        positionThresholds = i;
    }

    let levelUser = document.querySelector("#gamification-text");
    levelUser.innerHTML = "Sei al <strong>Livello " + (positionThresholds + 1) + "</strong> 💪";

    secondImg.src = `../img/badges/badge-${(positionThresholds)}.jpg`;
    secondImg.alt = `${badges[(positionThresholds)]}`;
    secondImg.hidden = false;
    secondP.innerHTML = badges[(positionThresholds)];

    thirdImg.src = `../img/badges/badge-${(positionThresholds + 1)}.jpg`;
    thirdImg.alt = `${badges[(positionThresholds + 1)]}`;
    thirdImg.hidden = false;
    thirdP.innerHTML = badges[(positionThresholds + 1)];
    
    if (positionThresholds == badges.length - 1){
        thirdImg.hidden = true;
    }


    let currentEXP = sessionStorage.getItem("EXP") ?? 0;

    let currentThreshold = badgeThresholds[positionThresholds];
    let nextThreshold = badgeThresholds[positionThresholds + 1] ?? currentThreshold; // Se è l'ultimo livello, nextThreshold sarà uguale a currentThreshold

    let progress = positionThresholds === (badgeThresholds.length - 1)
        ? 100
        : Math.min(100, Math.round(((currentEXP - currentThreshold) / (nextThreshold - currentThreshold)) * 100));

    let slider = document.querySelector("#progressGoals");
    slider.value = progress;
    document.querySelector("#ci").innerHTML = progress + "%";

}

document.getElementById("upload-profile-pic").addEventListener("change", function(event) {
    const file = event.target.files[0]; // Prende il file selezionato
    if(!file) return;

    // Controllo MIME type
    if (!file.type.startsWith('image/')) {
        alert("Il file selezionato non è un'immagine.");
        event.target.value = ""; // resetta input
        return;
    }

    // controllo estensione
    const validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    const fileExtension = file.name.split('.').pop().toLowerCase();

    if (!validExtensions.includes(fileExtension)) {
        alert("Estensione non valida. Seleziona un file immagine.");
        event.target.value = "";
        return;
    }

    if (file) {
        document.getElementById("profile-pic-profile").src = URL.createObjectURL(file); // Mostra l'anteprima
        formData.append("profilePic", file); // Aggiunge il file a FormData
        modifyDatabase();
    }
});

let bmiChartInstance = null; // Variabile globale per conservare l'istanza del grafico

function calcolateBMI() {
    let ctx = document.getElementById('bmiGaugeChart').getContext('2d');

    // Se esiste già un grafico, distruggilo prima di crearne uno nuovo
    if (bmiChartInstance !== null) {
        bmiChartInstance.destroy();
    }

    let weight = parseFloat(sessionStorage.getItem("weight")) || 0; // kg
    let height = parseFloat(sessionStorage.getItem("height")) || 0; // cm
    let age = 0;
    let gender = "male";
    let bmiValue = 0;

    if (sessionStorage.getItem("dateBirth")) {
        age = sessionStorage.getItem("dateBirth");
    }

    if (sessionStorage.getItem("sex") === "donna") {
        gender = "female";
    }

    if (height > 0) {
        height /= 100; // Converti cm in metri
        bmiValue = (weight / (height * height)).toFixed(1);
    }

    // Aggiustamenti in base a sesso ed età
    if (gender === "female") {
        bmiValue *= 1.02;
    }
    if (age >= 50) {
        bmiValue *= 1.05;
    }

    let minBMI = 10;
    let maxBMI = 40;
    bmiValue = Math.min(Math.max(bmiValue, minBMI), maxBMI).toFixed(1); 

    // Creazione del grafico
    bmiChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Sottopeso", "Normopeso", "Sovrappeso", "Obesità"],
            datasets: [{
                data: [18.5 - minBMI, 24.9 - 18.5, 29.9 - 24.9, maxBMI - 29.9],
                backgroundColor: ["#42a5f5", "#66bb6a", "#ffa726", "#ef5350"],
                borderWidth: 0
            }]
        },
        options: {
            rotation: -90,
            circumference: 180,
            cutout: "70%",
            responsive: true,
            plugins: {
                tooltip: { enabled: false },
                legend: { display: false }
            }
        }
    });

    // Posizionamento dinamico della freccia
    let arrow = document.getElementById("arrow");
    let angle = ((bmiValue - minBMI) / (maxBMI - minBMI)) * 180 - 90;
    arrow.style.transform = `translateX(-50%) rotate(${angle}deg)`;

    // Testo BMI
    let bmiText = document.getElementById("bmiText");
    let status = bmiValue < 18.5 ? "Sottopeso" :
                 bmiValue < 24.9 ? "Normopeso" :
                 bmiValue < 29.9 ? "Sovrappeso" : "Obesità";

    bmiText.innerHTML = `Il tuo BMI è <strong>${bmiValue}</strong> (${status})`;

    // Messaggio aggiuntivo
    let warningText = document.getElementById("bmiWarning");
    warningText.innerHTML = "<small>Nota: Il BMI è un valore indicativo e può variare in base a fattori come età, sesso e composizione corporea.</small>";
}
