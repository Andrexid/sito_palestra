function testLogout() {
    const conferma = confirm("⚠️ Stai per disconnetterti dal tuo account. Sei sicuro?");
    
    if (conferma) {
        // Se l'utente conferma, fai il logout
        alert("Logout effettuato!");
        localStorage.removeItem("email");
        window.location.href = "../login-signup/login.html";
    } else {
      // Se annulla, non succede nulla
    }
}
  

// Gestione dark mode
function applyTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      document.body.classList.add('dark-mode');
    } else {
      document.body.classList.remove('dark-mode');
    }
  }

  function toggleTheme() {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      localStorage.setItem('theme', 'light');
    } else {
      localStorage.setItem('theme', 'dark');
    }
    applyTheme();
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Quando la pagina carica
    if (!localStorage.getItem('theme')) {
      localStorage.setItem('theme', 'light');
    }
    applyTheme();
  
    // Event listener sul checkbox switch
    const switchCheckbox = document.getElementById('switch');
    switchCheckbox.addEventListener('change', function() {
      toggleTheme();
    });
  
    // Impostare lo stato iniziale del checkbox in base al tema salvato
    if (localStorage.getItem('theme') === 'dark') {
      switchCheckbox.checked = true;
    } else {
      switchCheckbox.checked = false;
    }
  });
  