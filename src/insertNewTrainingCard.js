const todayInput = document.querySelector('[name="duration_start"]');
const tomorrowInput = document.querySelector('[name="duration_end"]');

const today = new Date();
const nextMonth = new Date(today);
nextMonth.setMonth(today.getMonth() + 1); // Imposta al mese successivo

function formatDateToInput(date) {
    return date.toISOString().split('T')[0]; // Formato yyyy-mm-dd
}

document.addEventListener("DOMContentLoaded", function () {
    todayInput.value = formatDateToInput(today);
    tomorrowInput.value = formatDateToInput(nextMonth);
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
        div.className = "exercise-day";
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

                        // creazione del bottone per eliminare un elemento sigolo
                        var deleteButton = document.createElement("button");
                        deleteButton.textContent = "X";
                        deleteButton.className = "delete-button";
                        deleteButton.onclick = function () {
                            deleteExercise(exercise, listItem)
                        }

                        // Aggiungo il bottone al listItem
                        optgroup.appendChild(deleteButton);
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

function checkExercise() {
    const selectedExercise = document.querySelector("#insert-exercise").value.trim();
    const selectedDay = document.querySelector("#muscle-group").value;

    if (!selectedExercise || selectedDay === "default") {
        alert("Seleziona un esercizio e un giorno valido!");
        return;
    }

    const dayDivId = "divExercises" + (selectedDay - 1);
    const dayDiv = document.getElementById(dayDivId);

    if (!dayDiv) {
        alert("Il giorno selezionato non è valido.");
        return;
    }

    // Controlla se l'esercizio è già presente in quel giorno
    const existingExercises = dayDiv.querySelectorAll("li");
    for (let li of existingExercises) {
        if (li.textContent.trim().toLowerCase() === selectedExercise.toLowerCase()) {
            alert("Questo esercizio è già stato inserito in questo giorno!");
            return;
        }
    }

    // Crea l'elemento <li> per l'esercizio
    const li = document.createElement("li");
    li.textContent = selectedExercise;

    // creazione del bottone per eliminare un elemento singolo
    const deleteButton = document.createElement("button");
    deleteButton.textContent = "";
    deleteButton.className = "delete-button";
    deleteButton.onclick = function () {
        li.remove();
    };

    // Aggiungi il bottone all'interno dell'elemento <li>
    li.appendChild(deleteButton);

    // Aggiungi il <li> al giorno selezionato
    dayDiv.appendChild(li);
}

document.querySelector("form").addEventListener("submit", function (event) {
    event.preventDefault();

    const workoutsPerWeek = document.querySelector("#week-workout").value;
    console.log(todayInput.value);
    console.log(tomorrowInput.value);
    console.log(workoutsPerWeek);

    if (!todayInput || !tomorrowInput || !workoutsPerWeek) {
        alert("Compila tutti i campi prima di salvare!");
        return;
    }

    // Recupera tutti gli esercizi divisi per giorno
    let exercises = [];
    const daysContainer = document.querySelectorAll("[id^='divExercises']");

    daysContainer.forEach(dayDiv => {
        const exerciseItems = dayDiv.querySelectorAll("li");
        exerciseItems.forEach(li => {
            const exerciseName = li.textContent.trim();
            if (exerciseName !== "") {
                exercises.push({
                    name: exerciseName,
                    // opzionale: potresti includere anche il giorno o il gruppo muscolare
                    // day: parseInt(dayDiv.id.replace("divExercises", ""))
                });
            }
        });
    });

    if (exercises.length === 0) {
        alert("Devi aggiungere almeno un esercizio!");
        return;
    }

    const dataToSend = {
        start_date: todayInput.value,
        end_date: tomorrowInput.value,
        workouts_per_week: parseInt(workoutsPerWeek),
        exercise_list: JSON.stringify(exercises) // stringa JSON degli esercizi
    };
    console.log("cc" + JSON.stringify(exercises));

    // Invia i dati al file PHP
    fetch("../php/save_workout_plan.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams(dataToSend)
    })
    .then(response => {
        response.text();
        console.log(response);
    })
    .then(data => {
        console.log(data);
        alert("Piano salvato con successo!");
        // Puoi fare un reset del form o redirect
    })
    .catch(error => {
        console.error("Errore:", error);
        alert("Errore durante il salvataggio. Riprova.");
    });
});