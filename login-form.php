<?php
    ini_set("session.gc_maxlifetime", 900);
    ini_set("session.cookie_lifetime", 900);
    session_start();
    require "db.php";

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
        <title>Login X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="xlogo">
        <div class="container">
            <div id="left">
                <h1>Login</h1><br>
                <form action="" method="POST">
                    <label for="username">Benutzername:</label>
                    <input type="text" id="username" name="username" required>
                    <br><br>
                    <label for="password">Passwort:</label>
                    <input type="password" id="password" name="password" required>
                    <br><br>
                    <button type="submit">Anmelden</button>

                    <?php
                        // SQL datenabruf
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

                                    if ($_SESSION["isLoggedIn"] == false) {
                                        $_SESSION["isLoggedIn"] = true;
                                    }

                                    header("Location: dashboard.php");
                                } else {
                                    echo "Ihr Konto ist derzeit " . htmlspecialchars($userData['Status']) . ".<br>";
                                    echo "Warten Sie bitte auf die Freischaltung durch einen Administrator.";
                                }
                            } else {
                                // falsche eingaben
                                echo '<p class="warning">Benutzername oder Passwort ist falsch!</p>';
                            }
                        }
                    ?>
                </form>
                <br>
                <a href="register-form.php">Registrieren</a>
            </div>
        </div>
    </body>
</html>