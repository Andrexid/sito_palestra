<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="../css/account.css">
</head>

<body>

    <div class="cover">
        <div class="hamburger" id="hamburger">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" id="ham" viewBox="0 -960 960 960" width="24px"
                fill="#e8eaed">
                <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
            </svg>
        </div>
        <div class="overlay" id="overlay"></div>
        <div class="sidebar" id="sidebar">
            <button class="close-btn" id="close-btn">&times;</button>

        </div>
        <div id="title">Benvenuto User</div>
    </div>


    <div class="container">
        <div class="container-main">
            <div class="box info">
                <h2>Notifiche e scadenze</h2>
                <div class="notification-buttons">
                    <button>Notifica 1</button>
                    <p>scade il ??/??/???</p>
                    <button>Notifica 2</button>
                    <p>scade il ??/??/???</p>
                    <button>Notifica 3</button>
                    <p>scade il ??/??/???</p>
                </div>
            </div>
            <div class="box">
                <h2>Obiettivi</h2>
                <br>
                <div class="temporary-graphic"></div>
            </div>
            <div class="double-box">
                <div class="box">Allenamenti Mensili
                    <div class="temporary-graphic"></div>
                </div>    

                <div class="box">Bedge</div>
            </div>

            <div class="box large-box tall-box"> <strong>Andamento settimanale</strong> <br>
                <div class="temporary-graphic"></div>
            </div>
            <div class="box"> <strong>Statistiche</strong> <br> (Grafico vuoto) </div>
            <div class="box tall-box info"> <strong>Obiettivi</strong> <br> (Grafico vuoto) </div>
        </div>
    </div>



    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const hamburger = document.getElementById('hamburger');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            const closeBtn = document.getElementById('close-btn');
            hamburger.addEventListener('click', () => {
                sidebar.style.left = '0';
                overlay.style.display = 'block';
            });

            overlay.addEventListener('click', () => {
                sidebar.style.left = '-350px';
                overlay.style.display = 'none';
            });

            closeBtn.addEventListener('click', () => {
                sidebar.style.left = '-350px';
                overlay.style.display = 'none';
            });
        });
    </script>

</body>

</html>