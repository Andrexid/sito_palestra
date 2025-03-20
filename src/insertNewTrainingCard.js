// Array per definire gli esercizi inseriti
var objExercises = {};

function getInputValue() {
    // Prende il valore dell'input e lo mette maiuscolo
    var insertExercise = document.getElementById('insert-exercise').value.trim().toUpperCase();
    var muscleGroup = document.getElementById('muscle-group').value;

    if (insertExercise !== "") { // Controlla che non sia vuoto
        checkExerciseAndMuscleGroup(insertExercise, muscleGroup)
        document.getElementById('insert-exercise').value = ""; // Pulisce
        document.getElementById('muscle-group').value = "default";
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
    // Aggiunge l'esercizio all'array back-end
    objExercises[exercise] = muscleGroup;
    console.log(objExercises);
    console.log(exercise);

    // Crea un nuovo elemento front-end
    var listItem = document.createElement("li");
    listItem.textContent = exercise + " | " + muscleGroup + " ";

    // creazione del bottone per eliminare un elemento sigolo
    var deleteButton = document.createElement("button");
    deleteButton.textContent = "X";
    deleteButton.style.marginLeft = "10px"; // Distanza dal testo
    deleteButton.style.cursor = "pointer";
    deleteButton.onclick = function () {
        deleteExercise(exercise, listItem)
    }

    // Aggiungo il bottone al listItem
    listItem.appendChild(deleteButton)
    document.getElementById("exercise").appendChild(listItem)
}

function deleteExercise(exercise, listItem) {
    // Rimuove l'elemento dall'array
    listItem.remove(exercise)

    // RImuove l'elemento dal front-end
    delete objExercises[exercise];
    console.log(objExercises);
}