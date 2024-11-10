<?php
session_start();

// BenutzerID aus der Session
require "db.php";
$benutzerID = $_SESSION['UserID']; 

// Überprüfen, ob eine aktive Zeiterfassung vorhanden ist
$sql = "SELECT * FROM Zeiterfassung WHERE benutzerID = :benutzerID AND status = 'aktiv' AND endzeit IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':benutzerID', $benutzerID);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Endzeit setzen
    $sqlStop = "UPDATE Zeiterfassung SET endzeit = NOW(), status = 'beendet' WHERE benutzerID = :benutzerID AND endzeit IS NULL";
    $stmtStop = $pdo->prepare($sqlStop);
    $stmtStop->bindParam(':benutzerID', $benutzerID);
    $stmtStop->execute();

    echo "Zeiterfassung gestoppt!";
} else {
    echo "Keine laufende Zeiterfassung gefunden.";
}
?>
