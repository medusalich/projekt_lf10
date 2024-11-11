<?php
    require "db.php";

    $sqlMitarbeiter = "SELECT vorname, nachname FROM Mitarbeiter WHERE MitarbeiterID = :MitarbeiterID";
    $stmtMitarbeiter = $pdo->prepare($sqlMitarbeiter);
    $stmtMitarbeiter->bindParam(':MitarbeiterID', $mitarbeiterID);
    $stmtMitarbeiter->execute();

    if ($stmtMitarbeiter->rowCount() > 0) {
        while ($row = $stmtMitarbeiter->fetch(PDO::FETCH_ASSOC)) {
            echo "Nachname: " . $row["nachname"] . "Vorname: " . $row["vorname"] . "<br>";
        }
    }
?>

