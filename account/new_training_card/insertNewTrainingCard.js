document.addEventListener("DOMContentLoaded", function () {
  todayInput.value = formatDateToInput(today);
  tomorrowInput.value = formatDateToInput(nextMonth);
  initializePage();

  const workoutSelect = document.getElementById("week-workout");
  const searchInput = document.getElementById("exercise-search");
  const addBtn = document.getElementById("addExerciseBtn");

  // 1. Modifica giorni allenamento
  workoutSelect.addEventListener("change", (e) => {
    const value = e.target.value;
    modifyDaysTraining(value); // Funzione definita sotto
  });

  // 2. Filtra esercizi mentre scrivi
  searchInput.addEventListener("keyup", filterExercises);
  searchInput.addEventListener("click", openSelect);

  // 3. Aggiungi esercizio alla lista
  addBtn.addEventListener("click", checkExercise);

  // Apre la lista select degli esercizi
  window.openSelect = function () {
    document.getElementById("insert-exercise").size = 7;
  };

  // Filtra gli esercizi nella select in base all'input
  window.filterExercises = function () {
    const input = document
      .getElementById("exercise-search")
      .value.toLowerCase();
    const select = document.getElementById("insert-exercise");
    for (let option of select.options) {
      const text = option.text.toLowerCase();
      option.style.display = text.includes(input) ? "" : "none";
    }
  };

  // Aggiunge un esercizio selezionato alla lista
  window.checkExercise = function () {
    const selectedExercise = document.getElementById("insert-exercise").value;
    const selectedDay = document.getElementById("muscle-group").value;

    if (!selectedExercise || !selectedDay) {
      alert("Seleziona un esercizio e un giorno!");
      return;
    }

    const list = document.getElementById("exercise");
    const li = document.createElement("li");
    li.textContent = `${selectedExercise} - ${selectedDay}`;
    list.appendChild(li);

    // Aggiunta input nascosto per invio al server
    const hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "exercises[]";
    hiddenInput.value = `${selectedExercise}|${selectedDay}`;
    document.getElementById("exercisesList").appendChild(hiddenInput);
  };
});

async function initializePage() {
  await addExercises(); // aspetta che gli esercizi siano caricati
  blurExercises(); // poi applica il blur
}

const todayInput = document.querySelector('[name="duration_start"]');
const tomorrowInput = document.querySelector('[name="duration_end"]');

const today = new Date();
const nextMonth = new Date(today);
nextMonth.setMonth(today.getMonth() + 1); // Imposta al mese successivo

function formatDateToInput(date) {
  return date.toISOString().split("T")[0]; // Formato yyyy-mm-dd
}

//Aggiunge dinamicamente i giorni possibili in cui un utente potrebbe mettere l'esercizio creato
function modifyDaysTraining(val) {
  if (val === "default") return;

  var muscleGroup = document.querySelector("#muscle-group");
  var exercisesList = document.querySelector("#exercisesList");

  //Prima elimino tutti i dati già presenti
  while (muscleGroup.options.length > 0) {
    muscleGroup.remove(0);
  }
  while (exercisesList.firstChild) {
    exercisesList.removeChild(exercisesList.firstChild);
  }

  for (var i = 0; i < val; i++) {
    //aggiungo i campi options
    var option = document.createElement("option");
    option.value = i + 1;
    option.text = i + 1;

    muscleGroup.add(option, muscleGroup[i]);

    //aggiungo i div
    var div = document.createElement("div");
    div.id = "divExercises" + i;
    div.className = "exercise-day";
    div.textContent = "Giorno " + (i + 1); // Testo per evitare collasso

    exercisesList.appendChild(div);
  }

  enableExercises();
}

function blurExercises() {
  let div = document.getElementById("box");
  let elements = div.querySelectorAll("*"); // Select all elements inside the div

  elements.forEach((el) => {
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

  elements.forEach((el) => {
    if (el.tagName !== "P" && el.tagName !== "DIV" && el.tagName !== "SPAN") {
      el.disabled = false;
    }
    el.style.pointerEvents = "auto";
    el.style.opacity = "1";
  });
}

//Applica dinamicamente le option dentro il select degli esercizi
async function addExercises() {
  fetch("addExercises.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        let select = document.getElementById("insert-exercise");

        // Pulisce le opzioni esistenti
        select.innerHTML = "";

        // Itera sugli esercizi raggruppati per muscle_group
        for (let muscleGroup in data.data) {
          let optgroup = document.createElement("optgroup");
          optgroup.label = muscleGroup;

          data.data[muscleGroup].forEach((exercise) => {
            let option = document.createElement("option");
            option.value = exercise;
            option.textContent = exercise;
            optgroup.appendChild(option);

            // creazione del bottone per eliminare un elemento sigolo
            var deleteButton = document.createElement("button");
            deleteButton.textContent = "X";
            deleteButton.className = "delete-button";
            deleteButton.onclick = function () {
              deleteExercise(exercise, listItem);
            };

            // Aggiungo il bottone al listItem
            optgroup.appendChild(deleteButton);
          });

          select.appendChild(optgroup);
        }
      } else {
        console.error("Errore:", data.message);
      }
    })
    .catch((error) => {
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
  const selectedExercise = document
    .querySelector("#insert-exercise")
    .value.trim();
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
    const span = li.querySelector(".exercise-name");
    if (
      span &&
      span.textContent.trim().toLowerCase() === selectedExercise.toLowerCase()
    ) {
      alert("Questo esercizio è già stato inserito in questo giorno!");
      return;
    }
  }

  // Crea l'elemento <li> per l'esercizio
  const li = document.createElement("li");

  const exerciseSpan = document.createElement("span");
  exerciseSpan.className = "exercise-name";
  exerciseSpan.textContent = selectedExercise;

  // creazione del bottone per eliminare un elemento singolo
  const deleteButton = document.createElement("button");
  deleteButton.textContent = "X";
  deleteButton.className = "delete-button";
  deleteButton.onclick = function () {
    li.remove();
  };

  // Aggiungi lo span e il bottone all'interno dell'elemento <li>
  li.appendChild(exerciseSpan);
  li.appendChild(deleteButton);

  // Aggiungi il <li> al giorno selezionato
  dayDiv.appendChild(li);
}

document.querySelector("form").addEventListener("submit", function (event) {
  event.preventDefault();

  const workoutsPerWeek = document.querySelector("#week-workout").value;

  if (!todayInput || !tomorrowInput || !workoutsPerWeek) {
    alert("Compila tutti i campi prima di salvare!");
    return;
  }

  // Recupera tutti gli esercizi divisi per giorno
  let exercises = [];
  const daysContainer = document.querySelectorAll("[id^='divExercises']");

  daysContainer.forEach((dayDiv) => {
    const exerciseItems = dayDiv.querySelectorAll("li");
    exerciseItems.forEach((li) => {
      const span = li.querySelector(".exercise-name");
      const exerciseName = span?.textContent.trim();
      if (exerciseName !== "") {
        exercises.push({
          name: exerciseName,
          day: parseInt(dayDiv.id.replace("divExercises", "")) + 1,
        });
      }
    });
  });

  if (exercises.length === 0) {
    alert("Devi aggiungere almeno un esercizio!");
    return;
  }

  // Verifica che ogni giorno selezionato abbia almeno un esercizio
  let incompleteDays = [];
  daysContainer.forEach((dayDiv, index) => {
    const exerciseItems = dayDiv.querySelectorAll("li");
    if (exerciseItems.length === 0) {
      incompleteDays.push(index + 1); // Giorno 1, 2, ecc.
    }
  });

  if (incompleteDays.length > 0) {
    alert(
      `Ogni giorno selezionato deve contenere almeno un esercizio! Giorni mancanti: ${incompleteDays.join(
        ", "
      )}`
    );
    return;
  }

  const dataToSend = {
    start_date: todayInput.value,
    end_date: tomorrowInput.value,
    workouts_per_week: parseInt(workoutsPerWeek),
    exercise_list: JSON.stringify(exercises), // stringa JSON degli esercizi
  };

  // Invia i dati al file PHP
  fetch("save_workout_plan.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams(dataToSend),
  })
    .then((response) => {
      response.text();
    })
    .then((data) => {
      alert("Piano salvato con successo!");
      // Puoi fare un reset del form o redirect
      window.location.href = "../account.php";
    })
    .catch((error) => {
      console.error("Errore:", error);
      alert("Errore durante il salvataggio. Riprova.");
    });
});
