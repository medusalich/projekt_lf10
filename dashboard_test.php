<?php
    ini_set("session.gc_maxlifetime", 900);
    ini_set("session.cookie_lifetime", 900);
    session_start();
    require "db.php";

    // Wenn nicht eingeloggt wird hier direkt zur Login-Seite gesprungen.
    if (!isset($_SESSION["isLoggedIn"]) or $_SESSION["isLoggedIn"] == false){
        header("Location: login-form.php");
    }

    // Funktion zum Session-Logout und Session-Destroy.
    function logout_action() {
        if (isset($_SESSION["isLoggedIn"])){
            if ($_SESSION["isLoggedIn"] == true) {
                $_SESSION["isLoggedIn"] = false;
                session_destroy();
                header("Location: login-form.php");
            }
        }
    }

    // POST Logout-Abfrage.
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
        logout_action();
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard X Logistics</title>
        <style>
            .time-display {
                font-size: 1.2em;
                margin-top: 10px;
            }
            .btn {
                padding: 10px 20px;
                margin: 5px;
                font-size: 1em;
                cursor: pointer;
            }
    </style>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="xlogo">
        Test Yeah
        <form method="post">
        <button type="submit" name="logout">Logout</button>
        <br><br>
    </form>
    <br><br>
    <br><br>
    <br><br>
    <h1>Zeiterfassung</h1>
    
    <!-- Nachricht / Status -->
    <div id="statusMessage"></div>
    
    <!-- Anzeige der Start- und Endzeit -->
    <div class="time-display">
        <p>Startzeit: <span id="startzeit">-</span></p>
        <p>Endzeit: <span id="endzeit">-</span></p>
    </div>

    <!-- Buttons zum Starten und Stoppen der Zeiterfassung -->
    <button class="btn" onclick="startZeiterfassung()">Zeiterfassung starten</button>
    <button class="btn" onclick="stopZeiterfassung()">Zeiterfassung stoppen</button>

    <script>
        // Funktion zum Starten der Zeiterfassung
        function startZeiterfassung() {
            fetch('start_zeiterfassung.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('statusMessage').innerText = data;
                    
                    if (data.includes("Zeiterfassung gestartet")) {
                        // Startzeit setzen, wenn erfolgreich
                        const currentDateTime = new Date().toLocaleString('de-DE'); // Datum und Uhrzeit im deutschen Format
                        document.getElementById('startzeit').innerText = currentDateTime;
                        document.getElementById('endzeit').innerText = "-";
                    }
                });
        }

        // Funktion zum Stoppen der Zeiterfassung
        function stopZeiterfassung() {
            fetch('stop_zeiterfassung.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('statusMessage').innerText = data;

                    if (data.includes("Zeiterfassung gestoppt")) {
                        // Endzeit setzen, wenn erfolgreich
                        const currentDateTime = new Date().toLocaleString('de-DE'); // Datum und Uhrzeit im deutschen Format
                        document.getElementById('endzeit').innerText = currentDateTime;
                    }
                });
        }
    </script>
    </body>
</html>