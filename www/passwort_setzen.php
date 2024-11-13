<?php
ini_set("session.gc_maxlifetime", 900);
ini_set("session.cookie_lifetime", 900);
session_start();

require "db.php";

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
        <title>Neues Passwort-setzen</title>
        <title>Neues Passwort-setzen</title>
    </head>
    <body>
        <?php
        // Überprüfen, ob ein gültiger Token übergeben wurde
        if (isset($_GET['token'])) {
            $token = $_GET['token'];

            // Token in der Datenbank suchen
            $sql = "SELECT email FROM mitarbeiter WHERE reset_token = :reset_token";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":reset_token", $token);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Formular zum Passwort-Zurücksetzen anzeigen
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $newPassword = $_POST["new_password"];
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Neues Passwort in der Datenbank speichern und reset_token entfernen
                    $sql = "UPDATE userlogin 
                            SET passwort = :passwort
                            WHERE userid = (SELECT MitarbeiterID FROM mitarbeiter WHERE email = :email)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":passwort", $hashedPassword);
                    $stmt->bindParam(":email", $result["email"]);
                    $stmt->execute();

                    // Reset-Token entfernen
                    $sql = "UPDATE mitarbeiter SET reset_token = NULL WHERE reset_token = :reset_token";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":reset_token", $token);
                    $stmt->execute();

                    echo "Passwort erfolgreich zurückgesetzt.";
                } else {
                    echo "<h1>Passwort zurücksetzen</h1>";
                    echo "<form method='post'>";
                    echo "Neues Passwort: <input type='password' name='new_password'><br><br>";
                    echo "<input type='submit' value='Passwort ändern'>";
                    echo "</form>";
                }
            } else {
                echo "Ungültiger Reset-Token.";
            }
        } else {
            echo "Kein Reset-Token übergeben.";
        }

        ?>


    </body>
</html>