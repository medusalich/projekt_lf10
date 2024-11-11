<?php
    require "db.php";

    $sqlMitarbeiter = "SELECT Mitarbeiter.vorname, Mitarbeiter.nachname, Userlogin.Status FROM Mitarbeiter JOIN Userlogin On Mitarbeiter.MitarbeiterID = Userlogin.UserID";
    $stmtMitarbeiter = $pdo->prepare($sqlMitarbeiter);
    $stmtMitarbeiter->execute();

    if ($stmtMitarbeiter->rowCount() > 0) {
        while ($row = $stmtMitarbeiter->fetch(PDO::FETCH_ASSOC)) {
            echo "Nachname: " . $row["nachname"] . " Vorname: " . $row["vorname"] . " Status: " . $row["Status"] . "<br>";
        }
    }
?>

