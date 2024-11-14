<?php
ini_set("session.gc_maxlifetime", 900);
ini_set("session.cookie_lifetime", 900);
session_start();

require "db.php";
require "farbenblind_modus.php";
    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farbwechsel'])) {
    farbwechsel();
}
$modeClass = farbModus();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Neues Passwort-setzen X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="<?php echo $modeClass; ?>">
        <header>
        <button onclick="window.location.href='login-form.php'">Zurück zum Login</button>
            <form method="post">
                <button id="auge-button" type="submit" name="farbwechsel"></button>
            </form>
        </header>

        <main>
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
                    if ($_SERVER["REQUEST_METHOD"] == "POST"  && !isset($_POST['farbwechsel'])) {
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

                        echo "<h1>Passwort erfolgreich zurückgesetzt.</h1>";
                    } else {
                        echo "<h1>Passwort </br> setzen</h1>";
                        echo "<form method='post'>";
                        echo "Neues Passwort: <input type='password' name='new_password'></br>";
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
            <div class="xlogo">
                <?php
                    echo $_SESSION['farbenblind_modus'] ? '<img src="images/xlogo_bg_auge.png">' : '<img src="images/xlogo_bg.png">'; 
                ?>                    
            </div>
        </main>

    </body>
</html>