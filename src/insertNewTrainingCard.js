document.addEventListener("DOMContentLoaded", function () {

});

//Aggiunge dinamicamente i giorni possibili in cui un utente potrebbe mettere l'esercizio creato
function modifyDaysTraining(val){
    if(val === "default") return;

    var muscleGroup = document.querySelector("#muscle-group");
    var exercisesList = document.querySelector("#exercisesList");

    //Prima elimino tutti i dati già presenti
    while(muscleGroup.options.length > 0){
        muscleGroup.remove(0);
    }
    while (exercisesList.firstChild) {
        exercisesList.removeChild(exercisesList.firstChild);
    }

    for(var i = 0; i < val; i++){
        //aggiungo i campi options
        var option = document.createElement("option");
        option.value = i + 1;
        option.text = i + 1;

        muscleGroup.add(option, muscleGroup[i]);

        //aggiungo i div
        var div = document.createElement("div");
        div.id = "divExercises"+i;
        div.className = "exercisesList";
        div.textContent = "Giorno " + (i + 1); // Testo per evitare collasso

        exercisesList.appendChild(div);
    }

    enableExercises();
}

function blurExercises() {
        let div = document.getElementById("box");
        let elements = div.querySelectorAll("*"); // Select all elements inside the div

        elements.forEach(el => {
            if (el.tagName !== "P" && el.tagName !== "DIV" && el.tagName !== "SPAN") {
                el.disabled = true; // Disable only interactive elements
            }
            el.style.pointerEvents = "none"; // Prevent clicks on everything
            el.style.opacity = "0.5"; // Visual effect
        });
    }

function enableExercises() {
    let div = document.getElementById("box");
    let elements = div.querySelectorAll("*");

    elements.forEach(el => {
        if (el.tagName !== "P" && el.tagName !== "DIV" && el.tagName !== "SPAN") {
            el.disabled = false;
        }
        el.style.pointerEvents = "auto";
        el.style.opacity = "1";
    });
}

//Applica dinamicamente le option dentro il select degli esercizi
async function addExercises() {
    fetch("../php/addExercises.php")
        .then(response => response.json())
        .then(data => {
            console.log("Risposta dal server:", data); // Debug

            if (data.success) {
                let select = document.getElementById("insert-exercise");

                // Pulisce le opzioni esistenti
                select.innerHTML = "";

                // Itera sugli esercizi raggruppati per muscle_group
                for (let muscleGroup in data.data) {
                    let optgroup = document.createElement("optgroup");
                    optgroup.label = muscleGroup;

                    data.data[muscleGroup].forEach(exercise => {
                        let option = document.createElement("option");
                        option.value = exercise;
                        option.textContent = exercise;
                        optgroup.appendChild(option);
                    });

                    select.appendChild(optgroup);
                }
            } else {
                console.error("Errore:", data.message);
            }
        })
        .catch(error => {
            console.error("Errore di rete:", error);
        });
}

function filterExercises() {
    let input = document.getElementById("exercise-search").value.toLowerCase();
    let select = document.getElementById("insert-exercise");
    let optgroups = select.getElementsByTagName("optgroup");
    let found = false; // Controlla se ci sono risultati validi

    for (let optgroup of optgroups) {
        let options = optgroup.getElementsByTagName("option");
        let hasVisibleOption = false;

        for (let option of options) {
            if (option.textContent.toLowerCase().includes(input)) {
                option.style.display = "block";
                hasVisibleOption = true;
                found = true;
            } else {
                option.style.display = "none";
            }
        }

        // Nasconde il gruppo se non ha opzioni visibili
        optgroup.style.display = hasVisibleOption ? "block" : "none";
    }

    // Se ci sono risultati, cambia la dimensione del select
    select.size = found ? Math.min(7, select.options.length) : 1;
}
function openSelect() {
    let select = document.getElementById("insert-exercise");
    select.size = Math.min(7, select.options.length); // Mostra max 7 risultati
}


// Array per definire gli esercizi inseriti
var objExercises = {};

function getInputValue() {
    // Prende il valore dell'input e lo mette maiuscolo
    var insertExercise = document.getElementById('insert-exercise').value.trim().toUpperCase();
    var muscleGroup = document.getElementById('muscle-group').value;

    if (insertExercise !== "") { // Controlla che non sia vuoto
        checkExerciseAndMuscleGroup(insertExercise, muscleGroup)
        document.getElementById('insert-exercise').value = ""; // Pulisce
    }
}

// Verifica se l'esercizio è già presente
function checkExerciseAndMuscleGroup(insertExercise, muscleGroup) {
    if (objExercises.hasOwnProperty(insertExercise)) {
        alert("Esercizio già inserito");
    } else if (muscleGroup === 'default') {
        alert("Gruppo muscolare non inserito")
    } else {
        addExercise(insertExercise, muscleGroup)
    }
}

function addExercise(exercise, muscleGroup) {
    muscleGroup -= 1;
    // Aggiunge l'esercizio all'array back-end
    objExercises[exercise] = muscleGroup;
    console.log(objExercises);
    console.log(exercise);

    // Crea un nuovo elemento front-end
    var listItem = document.createElement("li");
    listItem.textContent = exercise;

    // creazione del bottone per eliminare un elemento sigolo
    var deleteButton = document.createElement("button");
    deleteButton.textContent = "X";
    deleteButton.className = "delete-button";
    deleteButton.onclick = function () {
        deleteExercise(exercise, listItem)
    }

    // Aggiungo il bottone al listItem
    listItem.appendChild(deleteButton)
    // document.getElementById("exercise").appendChild(listItem)
    console.log(`divExercises${muscleGroup}`);
    document.querySelector(`#divExercises${muscleGroup}`).appendChild(listItem);
}

function deleteExercise(exercise, listItem) {
    // Rimuove l'elemento dall'array
    listItem.remove(exercise)

    // RImuove l'elemento dal front-end
    delete objExercises[exercise];
    console.log(objExercises);
}

document.querySelector("form").addEventListener("submit", function (event) {
    console.log("Funzione di invio del modulo chiamata!"); // Aggiungi questa linea
    let exercises = [];
    for (let exercise in objExercises) {
        exercises.push({ name: exercise, muscleGroup: objExercises[exercise] });
    }

    console.log("Esercizi inviati:", exercises);
    console.log("JSON inviato:", JSON.stringify(exercises));
    document.getElementById("exercise_list").value = JSON.stringify(exercises);
});