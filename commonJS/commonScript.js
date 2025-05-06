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
      // Se non c'è un tema salvato, imposta 'dark' di default
      localStorage.setItem('theme', 'dark');
    }
    applyTheme();

    //scriviamo l'anno corrente nel footer
    document.getElementById('currentYear').textContent = new Date().getFullYear();
  });