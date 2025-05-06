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

    document.getElementById("backToProfile").addEventListener('click', (event) => {
      controllaAccesso('profile.html');
    });

    document.getElementById("backToAccount").addEventListener('click', (event) => {
      controllaAccesso('account.php');
    });

    document.getElementById("backToResetPassword").addEventListener('click', (event) => {
      window.location.href='../login-signup/reset_password_request.html';
    });

    document.getElementById("delete-account").addEventListener('click', (event) => {
      logout();
    });
  });
  