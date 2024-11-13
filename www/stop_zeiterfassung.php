<?php
session_start();

// MitarbeiterID aus der Session
require "db.php";
$MitarbeiterID = $_SESSION['UserID']; 
unset($_SESSION['startzeit']);

// Überprüfen, ob eine aktive Zeiterfassung vorhanden ist
$sql = "SELECT * FROM Zeiterfassung WHERE MitarbeiterID = :MitarbeiterID AND status = 'aktiv' AND endzeit IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':MitarbeiterID', $MitarbeiterID);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Endzeit setzen
    $sqlStop = "UPDATE Zeiterfassung SET endzeit = NOW(), status = 'Abgeschlossen', dauer = TIME_TO_SEC(TIMEDIFF(NOW(), startzeit)) WHERE MitarbeiterID = :MitarbeiterID AND endzeit IS NULL";
    $stmtStop = $pdo->prepare($sqlStop);
    $stmtStop->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmtStop->execute();

    //Dauer der Sitzung erfassen und umrechnen
    $sqlDauer = "SELECT dauer FROM Zeiterfassung WHERE MitarbeiterID = :MitarbeiterID ORDER BY id DESC LIMIT 1";
    $stmtDauer = $pdo->prepare($sqlDauer);
    $stmtDauer->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmtDauer->execute();
    $result = $stmtDauer->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['dauer'])) {
        $seconds = (int)$result['dauer'];

        // Umrechnung in Stunden, Minuten, Sekunden
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        echo "Die Arbeitszeit betrug $hours Stunden, $minutes Minuten und $seconds Sekunden. ";
    } else {
        echo "Die Arbeitszeit konnte nicht berechnet werden.";
    }


    echo "Zeiterfassung gestoppt!";
} else {
    echo "Keine laufende Zeiterfassung gefunden.";
}
?>
