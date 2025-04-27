function getUserDataProfile() {
    fetch("../profile/get_user_data_profile.php")
        .then(response => response.text())
        .then(text => {
            const data = JSON.parse(text); // 👈 parsing corretto

            console.log("✅ JSON parsato:", data);

            if (data.error) {
                console.error(data.error);
                return;
            }

            // Salva i dati in sessionStorage
            sessionStorage.setItem("EXP", data.puntiEXP);

            // Aggiorna il DOM con i dati
            drawBadges();
        })
        .catch(error => console.error("❌ Errore nel recupero dati:", error));
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

  secondImg.src = `../img/badge-${(positionThresholds)}.jpg`;
  secondImg.alt = `${badges[(positionThresholds)]}`;
  secondImg.hidden = false;
  secondP.innerHTML = badges[(positionThresholds)];

  thirdImg.src = `../img/badge-${(positionThresholds + 1)}.jpg`;
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