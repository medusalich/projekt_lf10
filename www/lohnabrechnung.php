<?php
    require "db.php";
    // Session Start 
    session_start();
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
?>


<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">




    </head>
    <body>
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
            <input type="submit">
        </form>
    </body>
</html>