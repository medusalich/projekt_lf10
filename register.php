<?php
//Verbindungdsaufbau an die datenbank 
$host = 'localhost';
$dbname = 'projekt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

// Benutzereingaben aus dem Formular abrufen und validieren
$user = $_POST['username'];
$passwort = $_POST['password'];
$vorname = $_POST['vorname'];
$nachname = $_POST['nachname'];
$strasse = $_POST['strasse'];
$postleitzahl = $_POST['postleitzahl'];
$ort = $_POST['ort'];
$geburtsdatum = $_POST['geburtsdatum'];
$email = $_POST['email'];

// Sicherstellung das keine doppelter Username oder Email vergeben werden kann 
$sql = "SELECT * FROM Userlogin WHERE User = :user OR EXISTS (SELECT 1 FROM Mitarbeiter WHERE Email = :email)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user', $user);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    die("Benutzername oder E-Mail-Adresse ist bereits vergeben.");
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
        }, 5000);
      </script>';
      // 5000 Millisekunden = 5 Sekunden
?>
