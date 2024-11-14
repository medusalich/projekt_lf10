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

    //$year = "2024"; //$_POST["year"];
    //$month = 11;    //$_POST["month"];
    //$hours = 120;   //$time_sheet[$year][$month]/3600;
    $year = $_POST["year"];
    $month = $_POST["month"];
    $hours = $time_sheet[$year][$month]/3600;
    $h_rate = 15.2;
    $steuersatz = 0.15;
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="css/styles.css">





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
    <body>
        <!-- Aufbau Tabelle Lohnabrechnung --> 
        <table>
            <tr style="height:75px">
                <th colspan="5", rowspan="1"><?php echo "Lohnabrechnung $month $year" ?></th>
            </tr>
            <tr>
                <th style="width:150px">Name</th>
                <td style="width:200px"><?php echo $daten_mitarbeiter[0]["Nachname"] ?></td>
                <th style="width:200px"></th>
                <th style="width:250px">Geburtsdatum</th>
                <td style="width:220px"><?php echo $daten_mitarbeiter[0]["Geburtsdatum"] ?></td>
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
    </body>
</html>