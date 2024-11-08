<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen bei Logistics</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>

<body id="startseite">
    
        <?php
        require 'db.php';
        //Verbindungdsaufbau an die datenbank 
       /* $host = 'localhost';
        $dbname = 'projekt';
        $username = 'root';
        $password = ''; 

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }
        */
        // SQL datenabruf 
        $user = $_POST['username'];
        $passwort = $_POST['password'];
        $sql = "SELECT * FROM Userlogin WHERE User = :user";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($passwort, $userData['Passwort'])) {
            // richtige eingaben
            if ($userData['Status'] === 'member') {
                echo "Willkommen, " . htmlspecialchars($user) . "!";
                //hier kommt noch eine weiterleitung dazu sobald diese fertig ist
            } else {
                echo "Ihr Konto ist derzeit " . htmlspecialchars($userData['Status']) . ".<br>";
                echo "Warten Sie bitte auf die Freischaltung durch einen Administrator.";
            }
        } else {
            // falsche eingaben
            echo "Benutzername oder Passwort ist falsch.";
        }
        ?>
    
</body>

</html>