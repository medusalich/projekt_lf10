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

    // Wenn nicht eingeloggt wird hier direkt zur Login-Seite gesprungen.
    if (!isset($_SESSION["isLoggedIn"]) or $_SESSION["isLoggedIn"] == false){
        header("Location: login-form.php");
    }

    // Funktion zum Session-Logout und Session-Destroy.
    function logout_action() {
        if (isset($_SESSION["isLoggedIn"])){
            if ($_SESSION["isLoggedIn"] == true) {
                $_SESSION["isLoggedIn"] = false;
                session_destroy();
                header("Location: login-form.php");
            }
        }
    }

    // POST Logout-Abfrage.
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
        logout_action();
    }
?>

<?php
    // Wenn eine Statusänderung angefordert wurde
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['new_status'])) {
        $userId = $_POST['user_id'];
        $newStatus = $_POST['new_status'];
        
        // Status in der Datenbank aktualisieren
        $sqlUpdate = "UPDATE Userlogin SET Status = :newStatus WHERE UserID = :userId";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':newStatus', $newStatus);
        $stmtUpdate->bindParam(':userId', $userId);
        $stmtUpdate->execute();
    }

    // Mitarbeiterdaten abfragen
    $sqlMitarbeiter = "SELECT Mitarbeiter.MitarbeiterID, Mitarbeiter.vorname, Mitarbeiter.nachname, Userlogin.Status FROM Mitarbeiter JOIN Userlogin ON Mitarbeiter.MitarbeiterID = Userlogin.UserID";
    $stmtMitarbeiter = $pdo->prepare($sqlMitarbeiter);
    $stmtMitarbeiter->execute();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminbereich X Logistics</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>

<body class="<?php echo $modeClass; ?>">
    <header>
        <nav>
            <button onclick="window.location.href='lohnabrechnung.php'">Abrechnungen</button>
            <button onclick="window.location.href='zeiterfassung.php'">Zeiterfassung</button>
            <button onclick="window.location.href='zeitanpassung.php'">Zeitanpassung</button>

            <form method="post">
                <button type="submit" name="logout">Logout</button>
            </form>
            <form method="post">
                <button id="auge-button" type="submit" name="farbwechsel"></button>
            </form>
        </nav>
    </header>

    <div class="tabelle">
        <div class="xlogo">
            <?php
                echo $_SESSION['farbenblind_modus'] ? '<img src="images/xlogo_bg_auge.png">' : '<img src="images/xlogo_bg.png">'; 
            ?>                    
        </div> 

        <h1>Mitarbeiterübersicht</h1>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Nachname</th>
                    <th style="width: 20%;">Vorname</th>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 40%;">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmtMitarbeiter->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td style="width: 20%;"><?php echo htmlspecialchars($row["nachname"]); ?></td>
                    <td style="width: 20%;"><?php echo htmlspecialchars($row["vorname"]); ?></td>
                    <td style="width: 20%;"><?php echo htmlspecialchars($row["Status"]); ?></td>
                    <td style="width: 40%;">
                        <form method="post">
                            <input type="hidden" name="user_id" value="<?php echo $row['MitarbeiterID']; ?>">
                            <button type="submit" name="new_status" value="Member">Member</button>
                            <button type="submit" name="new_status" value="Admin">Admin</button>
                            <button type="submit" name="new_status" value="Gesperrt">Gesperrt</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
      
</body>
</html>


