<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung X Logistics</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>

<?php
    require "db.php";
    // Session isLoggedIn = True überprüfen
?>

<body class="xlogo">
    <div class="container">
        <div id="left">
            <h1>Registrierung</h1>
            <form action="" method="POST">
                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username" required>
                <br><br>

                <label for="vorname">Vorname:</label>
                <input type="text" id="vorname" name="vorname" required>
                <br><br>

                <label for="nachname">Nachname:</label>
                <input type="text" id="nachname" name="nachname" required>
                <br><br>

                <label for="geburtsdatum">Geburtsdatum:</label>
                <input type="date" id="geburtsdatum" name="geburtsdatum" required>
                <br><br>

                <label for="strasse">Straße, Hausnummer:</label>
                <input type="text" id="strasse" name="strasse" required>
                <br><br>

                <label for="postleitzahl">Postleitzahl:</label>
                <input type="text" id="postleitzahl" name="postleitzahl" required>
                <br><br>

                <label for="ort">Ort:</label>
                <input type="text" id="ort" name="ort" required>
                <br><br>

                <label for="email">E-Mail:</label>
                <input type="email" id="email" name="email" required>
                <br><br>

                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password" required>
                <br><br>

                <label for="password">Passwort wiederholen:</label>
                <input type="password" id="password_wdhl" name="password_wdhl" required>
                <br><br>

                <button type="submit">Registrieren</button>
            </form>
            <?php
                // Benutzereingaben aus dem Formular abrufen und validieren
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $user = $_POST['username'];
                    $vorname = $_POST['vorname'];
                    $nachname = $_POST['nachname'];
                    $geburtsdatum = $_POST['geburtsdatum'];
                    $strasse = $_POST['strasse'];
                    $postleitzahl = $_POST['postleitzahl'];
                    $ort = $_POST['ort'];
                    $email = $_POST['email'];
                    $passwort = $_POST['password'];
                    $passwort_wdhl = $_POST['password_wdhl'];

                    if ($passwort !== $passwort_wdhl) {
                        echo '<p class="warning">Passwort stimmt nicht überein!</p>';
                    } else {
                        // Sicherstellung das keine doppelter Username oder Email vergeben werden kann 
                        $sql = "SELECT * FROM Userlogin WHERE User = :user OR EXISTS (SELECT 1 FROM Mitarbeiter WHERE Email = :email)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':user', $user);
                        $stmt->bindParam(':email', $email);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            die('<p class="warning">Benutzername oder E-Mail-Adresse ist bereits vergeben.</p>');
                        }
                        // Passwort verschlüsselung via Hash 
                        $hashedPassword = password_hash($passwort, PASSWORD_DEFAULT);

                        //Userlogin-Tabelle
                        $sqlUserlogin = "INSERT INTO Userlogin (User, Passwort, Status) VALUES (:user, :passwort, 'member')";
                        $stmtUserlogin = $pdo->prepare($sqlUserlogin);
                        $stmtUserlogin->bindParam(':user', $user);
                        $stmtUserlogin->bindParam(':passwort', $hashedPassword);
                        $stmtUserlogin->execute();

                        // UserID des neu eingefügten Benutzers abrufen
                        $userID = $pdo->lastInsertId();

                        //Mitarbeiter-Tabelle
                        $sqlMitarbeiter = "INSERT INTO Mitarbeiter (MitarbeiterID, Vorname, Nachname, Straße, Postleitzahl, Ort, Geburtsdatum, Email)
                                            VALUES (:MitarbeiterID, :vorname, :nachname, :strasse, :postleitzahl, :ort, :geburtsdatum, :email)";
                        $stmtMitarbeiter = $pdo->prepare($sqlMitarbeiter);
                        $stmtMitarbeiter->bindParam(':MitarbeiterID', $userID);
                        $stmtMitarbeiter->bindParam(':vorname', $vorname);
                        $stmtMitarbeiter->bindParam(':nachname', $nachname);
                        $stmtMitarbeiter->bindParam(':strasse', $strasse);
                        $stmtMitarbeiter->bindParam(':postleitzahl', $postleitzahl);
                        $stmtMitarbeiter->bindParam(':ort', $ort);
                        $stmtMitarbeiter->bindParam(':geburtsdatum', $geburtsdatum);
                        $stmtMitarbeiter->bindParam(':email', $email);
                        $stmtMitarbeiter->execute();

                        echo "Registrierung erfolgreich! Sie können sich jetzt anmelden.<br>";
                        echo "Sie werden in 5 Sekunden weitergeleitet";
                        echo '<script>
                                setTimeout(function() {
                                    window.location.href = "Login-Regi.php";
                                }, 0);
                            </script>';
                            // 5000 Millisekunden = 5 Sekunden                      
                    }
                }
            ?>
        </div>
    </div>
</html>

12345678


