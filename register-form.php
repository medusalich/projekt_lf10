<?php
    ini_set("session.gc_maxlifetime", 900);
    ini_set("session.cookie_lifetime", 900);
    session_start();
    require "db.php";


    if (!isset($_SESSION["farbenblind_mode"])){
        $_SESSION["farbenblind_mode"] = false;
    }

    // Überprüfen, ob der farbenfreundliche Modus bereits aktiviert ist oder ob der Benutzer ihn gerade auswählt
    if (isset($_POST["toggle_mode"])) {
        $_SESSION["farbenblind_mode"] = !($_SESSION["farbenblind_mode"] ?? false);
    }

    // Bestimme die Klasse basierend auf dem Modus
    $modeClass = $_SESSION["farbenblind_mode"] ?? false ? "normal" : "farbenblind";
        

    if (!isset($_SESSION["isLoggedIn"])){
        $_SESSION["isLoggedIn"] = false;
    }

    if (isset($_SESSION["isLoggedIn"])){
        if ($_SESSION["isLoggedIn"] == true) {
            header("Location: dashboard.php");
        }
    }
?>

<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registrierung X Logistics</title>
        <link rel="stylesheet" href="css/regi-styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="<?php echo $modeClass; ?>">
        <header>
            <form method="post" action="">
                <button id="auge-button" type="submit" name="toggle_mode"></button>
            </form>
        </header>
        <div class="main-content">
            <main>
                <div class="xform">
                    <h1>Registrierung</h1>
                    <form method="post">

                        <div class="form-element" id="form-user">
                            <label for="username">Benutzername:</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <div class="form-element" id="form-vorname">
                            <label for="vorname">Vorname:</label>
                            <input type="text" id="vorname" name="vorname" required>
                        </div>

                        <div class="form-element" id="form-nachname">
                            <label for="nachname">Nachname:</label>
                            <input type="text" id="nachname" name="nachname" required>
                        </div>

                        <div class="form-element" id="form-geburtstag">
                            <label for="geburtsdatum">Geburtsdatum:</label>
                            <input type="date" id="geburtsdatum" name="geburtsdatum" required>
                        </div>

                        <div class="form-element" id="form-strasse">
                            <label for="strasse">Straße, Hausnummer:</label>
                            <input type="text" id="strasse" name="strasse" required>
                        </div>

                        <div class="form-element" id="form-plz">
                            <label for="postleitzahl">Postleitzahl:</label>
                            <input type="text" id="postleitzahl" name="postleitzahl" pattern="\d{5,10}" maxlength="10" title="Bitte eine Postleitzahl eingeben. Bsp.: 70565" required>
                        </div>

                        <div class="form-element" id="form-ort">
                            <label for="ort">Ort:</label>
                            <input type="text" id="ort" name="ort" required>
                        </div>

                        <div class="form-element" id="form-email">
                            <label for="email">E-Mail:</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="form-element" id="form-pw">
                            <label for="password">Passwort:</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-element" id="form-pw-wdhl">
                            <label for="password">Passwort wiederholen:</label>
                            <input type="password" id="password_wdhl" name="password_wdhl" required>
                        </div>

                        <div class="form-element" id="form-submit">
                            <?php
                                echo $_SESSION['farbenblind_mode'] ? '<button id="form-button" type="submit">Registrieren</button>' : '<button id="form-button-auge" type="submit">Registrieren</button>';
                            ?> 
                        </div>
                        <?php
                            // Benutzereingaben aus dem Formular abrufen und validieren
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['toggle_mode'])) {
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
                                    $sqlUserlogin = "INSERT INTO Userlogin (User, Passwort, Status) VALUES (:user, :passwort, 'gesperrt')";
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

                                    header("Location: after-register.html");
                                }
                            }
                        ?>
                    </form>
                    <p>
                        Zum <a href="login-form.php">Login</a>
                    </p>
                </div>
                <div class="xlogo">
                    <?php 
                        echo $_SESSION['farbenblind_mode'] ? '<img src="images/xlogo_bg.png">' : '<img src="images/xlogo_bg_auge.png">'; 
                    ?>
                </div>
            </main>
        </div>
        <footer>

        </footer>
    </body>
</html>