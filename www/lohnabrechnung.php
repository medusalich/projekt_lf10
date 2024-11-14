<?php
    ini_set("session.gc_maxlifetime", 900);
    ini_set("session.cookie_lifetime", 900);
    session_start();
    require "db.php";
    require "farbenblind_modus.php";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farbwechsel'])) {
        farbwechsel();
    }
    $modeClass = farbModus();

    $MitarbeiterID = $_SESSION['UserID'];

    // Aufbau Zeiten nach Jahren und Monaten und Monatsweise aufsummiert
    // Zuerst Datenbankabfrage, Monatsweise aufsummiert
    $sql = "SELECT DATE(startzeit) AS datum, SUM(dauer) AS gesamtzeit FROM Zeiterfassung WHERE MitarbeiterID = :MitarbeiterID GROUP BY MONTH(DATE(startzeit)) ORDER BY datum ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Übertragung der Daten in Assoziatives Array
    $time_sheet = array();
    foreach ($logs as $log) {
    $year = substr($log["datum"], 0, 4);
    $month = substr($log["datum"], 5, 2);
    $time_sheet[$year][$month] = (int)$log["gesamtzeit"];
    } 

    // Funktion zum Session-Logout und Session-Destroy.
    function logout_action()
    {
        if (isset($_SESSION["isLoggedIn"])) {
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
        <title>Lohnabrechnung X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="<?php echo $modeClass; ?>">
        <header>
            <nav class="nav-box">
                <button onclick="window.location.href='zeiterfassung.php'">Zeiterfassung</button>
                <form method="post">
                    <button type="submit" name="logout">Logout</button>
                </form>
                <form method="post">
                    <button id="auge-button" type="submit" name="farbwechsel"></button>
                </form>
            </nav>
        </header>
        <div class="dashboard-main">
            <div class="xlogo">
                <?php
                    echo $_SESSION['farbenblind_modus'] ? '<img src="images/xlogo_bg_auge.png">' : '<img src="images/xlogo_bg.png">'; 
                ?>
            </div>

            <h2>Auswahl Lohnabrechnung</h2>
            <p>Bitte Jahr und Monat auswählen:</p>
            <form action="tabelle_lohn.php" method="POST">
                <label for="year">Jahr:</label>
                <select id="year" name="year"> 
                <!-- Nur Jahre mit Daten anzeigen --> 
                <?php
                    foreach (array_keys($time_sheet) as $year) {
                        echo "<option value=" . $year . ">" . $year . "</option>";
                    }
                ?>
                </select>

                <label for="month">Monat:</label>
                    <select id="month" name="month">
                        <option value="01">Januar</option>
                        <option value="02">Februar</option>
                        <option value="03">März</option>
                        <option value="04">April</option>
                        <option value="05">Mai</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Dezember</option>
                    </select>
                <button type="submit">Aufrufen</button>
            </form>            
        </div>
    </body>
</html>