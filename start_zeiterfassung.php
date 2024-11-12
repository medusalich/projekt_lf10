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

// Only set the start time if it has not been set yet
if (!isset($_SESSION['startzeit'])) {
    $_SESSION['startzeit'] = date('Y-m-d H:i:s'); // Set start time in session
    
} 

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
