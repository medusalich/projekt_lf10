<?php
ini_set("session.gc_maxlifetime", 900);
ini_set("session.cookie_lifetime", 900);
session_start();

ini_set("SMTP", "localhost");       // SMTP-Server (z. B. localhost)
ini_set("smtp_port", "1025");  
// Datenbankverbindung herstellen
require "db.php";
require "farbenblind_modus.php";
    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farbwechsel'])) {
    farbwechsel();
}
$modeClass = farbModus();

// Tabelle für Nutzer-Anmeldedaten
$table_name = "Mitarbeiter";

// Formular verarbeiten, wenn es abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['farbwechsel'])) {
    $email = $_POST["email"];

    // Überprüfen, ob der Nutzer existiert
    $sql = "SELECT * FROM Mitarbeiter WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindparam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result !== false) {
        // Zufälliges Token für den zurücksetzen-Link generieren
        $resetToken = bin2hex(random_bytes(32));

        // Token in der Datenbank speichern
        $sql = "UPDATE $table_name SET reset_token = :reset_token WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindparam(":reset_token", $resetToken);
        $stmt->bindparam(":email", $email);
        $stmt->execute();

        // zurücksetzen-Link erstellen und E-Mail senden
        $resetLink = "http://localhost/projekt_lf10/www/passwort_setzen.php?token=" . $resetToken;
        sendPasswordResetEmail($email, $resetLink);

        header("Location: after-resetmail.php");
    } else {
        echo "Kein Nutzer mit der E-Mail-Adresse " . $email . " gefunden.";
    }

    $stmt->closeCursor();
}

function sendPasswordResetEmail($email, $resetLink) {
    // E-Mail senden
    $to = $email;
    $subject = "Passwort zurücksetzen";
    $message = "Zum zurücksetzen Ihres Passworts klicken Sie bitte auf den folgenden Link:" . $resetLink;
    $headers = "From: support@xlogistics.com";

    if (mail($to, $subject, $message, $headers)) {
        echo "Passwort-zurücksetzen-E-Mail wurde gesendet an " . $email . ".\n";
    } else {
        echo "Fehler beim Senden der Passwort-zurücksetzen-E-Mail an " . $email . ".\n";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <title>Passwort zurücksetzen</title>
</head>

<body class="<?php echo $modeClass; ?>">
    <header>
        <form method="post">
            <button id="auge-button" type="submit" name="farbwechsel"></button>
        </form>
    </header>

    <main>
        <div class="xlogo">
            <h1>Passwort<br>
                zurücksetzen</h1>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="email">E-Mail-Adresse:</label>
                <input type="email" name="email" required>
                <button type="passwort" name="submit" value="Passwort zuzurücksetzen">Passwort zurücksetzen</button>
            </form>
        </div>
        <div class="xlogo">
            <?php
                echo $_SESSION['farbenblind_modus'] ? '<img src="images/xlogo_bg_auge.png">' : '<img src="images/xlogo_bg.png">'; 
            ?>                    
        </div>
    </main>
</body>
</html>