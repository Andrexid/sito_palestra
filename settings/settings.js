function vaiAlProfilo() {
    window.location.href = "profile.html"; // Cambia con il percorso corretto
}

function vaiAiProgressi() {
    window.location.href = "progress.html"; // Cambia con il percorso corretto
}

document.getElementById("notifications").addEventListener("change", function() {
    if (this.checked) {
        alert("Notifiche attivate!");
    } else {
        alert("Notifiche disattivate!");
    }
});

// Gestione dark mode
const themeToggle = document.getElementById("theme");

// Controlla lo stato della dark mode al caricamento della pagina
if (localStorage.getItem("darkMode") === "enabled") {
    document.body.classList.add("darkMode");
    themeToggle.checked = true;
}

// Aggiungi un listener per cambiare lo stato della dark mode
themeToggle.addEventListener("change", function() {
    if (this.checked) {
        document.body.classList.add("darkMode");
        localStorage.setItem("darkMode", "enabled");
        alert
    } else {
        document.body.classList.remove("darkMode");
        localStorage.setItem("darkMode", "disabled");
    }
});

const toggle = document.getElementById('theme');

toggle.addEventListener('change', () => {
  const root = document.documentElement;
  if (toggle.checked) {
    root.classList.add('dark-mode');
    root.classList.remove('light-mode');
  } else {
    root.classList.add('light-mode');
    root.classList.remove('dark-mode');
  }
});