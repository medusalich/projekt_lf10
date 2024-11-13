<?php
    ini_set("session.gc_maxlifetime", 900);
    ini_set("session.cookie_lifetime", 900);
    session_start();
    require "db.php";

    // Sehschwäche check und Logik
    if (!isset($_SESSION["farbenblind_mode"])){
        $_SESSION["farbenblind_mode"] = false;
    }
    if (isset($_POST["toggle_mode"])) {
        $_SESSION["farbenblind_mode"] = !($_SESSION["farbenblind_mode"] ?? false);
    }
    $modeClass = $_SESSION["farbenblind_mode"] ?? false ? "normal" : "farbenblind";
        
    // Login check und ggf Weiterleitung
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
        <link rel="stylesheet" href="css/login-styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body class="<?php echo $modeClass; ?>">
        <header>
                <form method="post">
                    <button id="auge-button" type="submit" name="toggle_mode"></button>
                </form>
            </header>
            <div class="main-content">
            <main>
                <div class="xform">
                    <h1>Login</h1>
                    <form method="POST">
                        <label for="username">Benutzername:</label>
                        <input type="text" id="username" name="username" required>
                        <label for="password">Passwort:</label>
                        <input type="password" id="password" name="password" required>
                        <?php
                            echo $_SESSION['farbenblind_mode'] ? '<button id="form-button" type="submit">Anmelden</button>' : '<button id="form-button-auge" type="submit">Anmelden</button>';
                        ?>
                    </form>
                    <?php
                        // SQL datenabruf
                        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['toggle_mode'])) {
                            $user = $_POST['username'];
                            $passwort = $_POST['password'];
                            $sql = "SELECT * FROM Userlogin WHERE User = :user";
                            $stmt = $pdo->prepare($sql);
                            $stmt->bindParam(':user', $user);
                            $stmt->execute();
                            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($userData && password_verify($passwort, $userData['Passwort'])) {
                                // Valide eingaben
                                if ($userData['Status'] === 'member' || $userData['Status'] === 'admin') {
                                    $_SESSION['UserID'] = $userData['UserID'];
                                    if ($_SESSION["isLoggedIn"] == false) {
                                        $_SESSION["isLoggedIn"] = true;
                                    }

                                    if ($userData['Status'] === 'admin') {
                                        // Admin Logging in.
                                        header("Location: dashboard.php");
                                    } else {
                                        // Random User Logging in.
                                        header("Location: dashboard_test.php");
                                    }
                                    
                                } else {
                                    // User gesperrt
                                    echo '<p class="warning">Ihr Konto ist derzeit ' . htmlspecialchars($userData['Status']) . '!<br>Warte bitte auf die Freischaltung durch einen Administrator.</p>';
                                }
                            } else {
                                // Eingaben Falsch
                                echo '<p class="warning">Benutzername oder Passwort ist falsch!</p>';
                            }
                        }
                    ?>
                    <p>
                        Noch kein Konto? <a href="register-form.php">Registrieren</a>
                    </p>
                </div>
                <div class="xlogo">
                    <?php
                            echo $_SESSION['farbenblind_mode'] ? '<img src="images/xlogo_bg.png">' : '<img src="images/xlogo_bg_auge.png">'; 
                        ?>
                </div>
            <main>
        </div>
    </body>
</html>