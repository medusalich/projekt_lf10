<?php
session_start();
require "db.php";

// MitarbeiterID aus der Session   
$MitarbeiterID = $_SESSION['UserID']; 

// Überprüfen, ob bereits eine aktive Zeiterfassung existiert
$sql = "SELECT * FROM Zeiterfassung WHERE MitarbeiterID = :MitarbeiterID AND status = 'aktiv' AND endzeit IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':MitarbeiterID', $MitarbeiterID);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "Sie haben bereits eine laufende Zeiterfassung.";
} else {
    // Startzeit im DATETIME-Format einfügen
    $sqlStart = "INSERT INTO Zeiterfassung (MitarbeiterID, startzeit, status) VALUES (:MitarbeiterID, NOW(), 'aktiv')";
    $stmtStart = $pdo->prepare($sqlStart);
    $stmtStart->bindParam(':MitarbeiterID', $MitarbeiterID);
    $stmtStart->execute();

    echo "Zeiterfassung gestartet!";
}
?>
