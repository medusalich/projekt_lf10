<?php
session_start();
require "db.php";

// BenutzerID aus der Session   
$benutzerID = $_SESSION['UserID']; 

// Überprüfen, ob bereits eine aktive Zeiterfassung existiert
$sql = "SELECT * FROM Zeiterfassung WHERE benutzerID = :benutzerID AND status = 'aktiv' AND endzeit IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':benutzerID', $benutzerID);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "Sie haben bereits eine laufende Zeiterfassung.";
} else {
    // Startzeit im DATETIME-Format einfügen
    $sqlStart = "INSERT INTO Zeiterfassung (benutzerID, startzeit, status) VALUES (:benutzerID, NOW(), 'aktiv')";
    $stmtStart = $pdo->prepare($sqlStart);
    $stmtStart->bindParam(':benutzerID', $benutzerID);
    $stmtStart->execute();

    echo "Zeiterfassung gestartet!";
}
?>
