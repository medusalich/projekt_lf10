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

    $startzeit = isset($_SESSION['startzeit']) ? $_SESSION['startzeit'] : null;
    $endzeit = isset($_SESSION['endzeit']) ? $_SESSION['endzeit'] : null; ////////////////////////

    // Wenn nicht eingeloggt wird hier direkt zur Login-Seite gesprungen.
    if (!isset($_SESSION["isLoggedIn"]) or $_SESSION["isLoggedIn"] == false) {
        header("Location: login-form.php");
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

    $MitarbeiterID = $_SESSION['UserID'];

    // Aufbau Zeiten nach Jahren und Monaten und Monatsweise aufsummiert
    // Zuerst Datenbankabfrage, Monatsweise aufsummiert
    $sql = "SELECT DATE(startzeit) AS datum, SUM(dauer) AS gesamtzeit FROM Zeiterfassung WHERE MitarbeiterID = :MitarbeiterID GROUP BY MONTH(DATE(startzeit)) ORDER BY datum ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Übertragung der Daten in Assoziatives Array
    // etwas unnötig!
    $time_sheet = array();
    foreach ($logs as $log) {
        $year = substr($log["datum"], 0, 4);
        $month = substr($log["datum"], 5, 2);
        $time_sheet[$year][$month] = (int)$log["gesamtzeit"];
    }
    // Mitarbeiterdaten holen
    $sql = "SELECT * FROM Mitarbeiter WHERE MitarbeiterID = :MitarbeiterID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmt->execute();
    $daten_mitarbeiter = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $year = $_POST["year"];
    $month = $_POST["month"];
    $month_exists = true;

    if (array_key_exists($month, $time_sheet[$year])) {
        $hours = $time_sheet[$year][$month]/3600;
    } else {
        $month_exists = false;
    }
    $h_rate = 15.2; //Stundenlohn
    $steuersatz = 0.15;
?>

<!-- Prüfung ob im gewählten Monat Arbeitszeit erfasst wurde
     Wenn ja: Tabelle aufbauen, sonst fehlermeldung-->
<?php if ($month_exists) : ?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lohntabelle X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
        <style>
            table, th, td {
                border-collapse: collapse;
                background-color: #a5c3ba;
                border: 0px solid black;
            }

            tr {
                border-bottom: 1px solid #ddd;
            }

            table {
                border: 2px solid black;
            }
        </style>
    </head>
    <body class="<?php echo $modeClass; ?>">
        <header>
            <nav class="nav-box">
                <button onclick="window.location.href='zeiterfassung.php'">Zeiterfassung</button>
                <button onclick="window.location.href='lohnabrechnung.php'">Abrechnungen</button>
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
            <!-- Aufbau Tabelle Lohnabrechnung --> 
            <table style="width:60%">
                <tr style="height:75px">
                    <th style="text-align:center" colspan="5", rowspan="1"><?php echo "Lohnabrechnung $month $year" ?></th>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?php echo $daten_mitarbeiter[0]["Nachname"] ?></td>
                    <th></th>
                    <th>Geburtsdatum</th>
                    <td><?php echo $daten_mitarbeiter[0]["Geburtsdatum"] ?></td>
                </tr>
                <tr>
                    <th>Vorname</th>
                    <td><?php echo $daten_mitarbeiter[0]["Vorname"] ?></td>
                    <td></td>
                    <th>Anspruch Urlaubstage</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Adresse</th>
                    <td><?php echo $daten_mitarbeiter[0]["Straße"] ?></td>
                    <td></td>
                    <th>Urlaub genommen</th>
                    <td></td>
                </tr>
                <tr>
                    <th>PLZ, Ort</th>
                    <td><?php echo $daten_mitarbeiter[0]["Postleitzahl"] . ", " . $daten_mitarbeiter[0]["Ort"] ?></td>
                    <td></td>
                    <th>Resturlaub</th>
                    <td></td>
                </tr>
                <tr style="height:75px">
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <th>Arbeitsstunden</th>
                    <th>Stundenlohn</th>
                    <th colspan="2"></th>
                    <th>Betrag</th>
                </tr>
                <tr style="text-align: right;">
                    <!-- NICHT VALIDE tr align="right" -->
                    <td><?php echo number_format($hours, 2, '.', '') ?></td>
                    <td><?php echo number_format($h_rate, 2, '.', '') ." EUR/h" ?></td>
                    <td colspan="2"></td>
                    <td><?php echo number_format($h_rate * $hours,  2, '.', '') . " EUR" ?></td>
                </tr>
                <tr style="height:30px">
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <th>Steuer Brutto</th>
                    <th>Lohnsteuer</th>
                    <th>RV-Beitrag</th>
                    <th>AV-Beitrag</th>
                    <th>KV-Beitrag</th>
                </tr>
                <tr style="text-align: right;">
                    <td><?php echo number_format($h_rate * $hours, 2, '.', '') . "  EUR"?></td>
                    <td><?php echo number_format($h_rate * $hours * $steuersatz, 2, '.', '') . " EUR"?></td>
                    <td><?php echo number_format($h_rate * $hours * 0.08, 2, '.', '') . " EUR"?></td>
                    <td><?php echo number_format($h_rate * $hours * 0.07, 2, '.', '') . " EUR"?></td>
                    <td><?php echo number_format($h_rate * $hours * 0.12, 2, '.', '') . " EUR"?></td>
                </tr>
                <tr style="height:30px">
                    <td colspan="5"></td>
                </tr>
                <tr>
                    <td colspan="4"></td>
                    <th>Auszahlungsbetrag</th>
                </tr>
                <tr style="text-align: right;">
                    <td colspan="4"></td>
                    <td><?php echo number_format($h_rate * $hours * (1 - ($steuersatz+0.08+0.07+0.12)), 2, '.', '') . " EUR" ?></td>
                </tr>
            </table>
        </div>
    </body>
</html>
<!-- Fehlermeldung wenn es im gewählten Monat keine Zeiterfassung gab -->
<?php 
else : 
    header("Location: tabelle_lohn_fehler.php");
endif;
?>