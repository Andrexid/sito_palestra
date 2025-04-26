function getUserDataProfile() {
  fetch("../php/get_user_data_profile.php")
    .then((response) => response.text())
    .then((text) => {
      console.log("📦 Risposta grezza dal server:", text);
      const data = JSON.parse(text); // 👈 parsing corretto

      console.log("✅ JSON parsato:", data);

      if (data.error) {
        console.error(data.error);
        return;
      }

      // Salva i dati in sessionStorage
      sessionStorage.setItem("EXP", data.puntiEXP);

      // Nuovi dati
      sessionStorage.setItem("xpTotale", data.xpTotale);

      drawBadges();
    })
    .catch((error) => console.error("❌ Errore nel recupero dati:", error));
}

let badges = [
  "Il viaggio inizia",
  "Primo Passo",
  "Iniziato il Viaggio",
  "Fitness Warrior",
  "Resiliente",
  "Iron Man",
  "Fitness Machine",
  "Inarrestabile",
  "Bestia della Palestra",
  "Atleta d’élite",
  "Leggenda del Fitness",
];

// Soglie di esperienza per ogni badge
let badgeThresholds = [
  0, 500, 1500, 3000, 7500, 15000, 30000, 50000, 100000, 200000, 500000,
];

function drawBadges() {
  let nTrainings = document.querySelector("#nTrainings");
  nTrainings.innerHTML =
    "Hai completato <strong>" +
    sessionStorage.getItem("nTrainings") +
    "</strong> allenamenti! 🚀";

  let firstImg = document.getElementById("firstImg");
  let secondImg = document.getElementById("secondImg");
  let thirdImg = document.getElementById("thirdImg");
  let textPone = document.getElementById("subOne");
  let textPtwo = document.getElementById("subTwo");
  let textPthree = document.getElementById("subThree");
  let positionThresholds = 0;

  for (
    let i = 0;
    i < badges.length && sessionStorage.getItem("EXP") > badgeThresholds[i];
    i++
  ) {
    positionThresholds = i;
  }

  firstImg.src = `../img/badge-${positionThresholds - 1}.jpg`;
  firstImg.alt = `${badges[positionThresholds - 1]}`;
  let textTitleOne = `${badges[positionThresholds - 1]}`;
  if (textTitleOne != "undefined") {
      textPone.append(textTitleOne);
  }
  firstImg.hidden = false;

  secondImg.src = `../img/badge-${positionThresholds}.jpg`;
  secondImg.alt = `${badges[positionThresholds]}`;
  let textTitleTwo = `${badges[positionThresholds]}`;
  if (textTitleTwo == "undefined") {
    textPtwo.append("");
  } else {
    textPtwo.append(textTitleTwo);
  }
  secondImg.hidden = false;

  thirdImg.src = `../img/badge-${positionThresholds + 1}.jpg`;
  thirdImg.alt = `${badges[positionThresholds + 1]}`;
  let textTitleThree = `${badges[positionThresholds + 1]}`;
  if (textTitleThree == "undefined") {
    textPthree.append("");
  } else {
    textPthree.append(textTitleThree);
  }
  thirdImg.hidden = false;

  if (positionThresholds == 0) {
    firstImg.hidden = true;
  } else if (positionThresholds == badges.length - 1) {
    thirdImg.hidden = true;
  }
}
