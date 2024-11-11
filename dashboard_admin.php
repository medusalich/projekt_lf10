<?php
    require "db.php";
    // Session Start nach oben verschoben
    session_start();


    $sqlMitarbeiter = "SELECT Mitarbeiter.vorname, Mitarbeiter.nachname, Userlogin.Status FROM Mitarbeiter JOIN Userlogin On Mitarbeiter.MitarbeiterID = Userlogin.UserID";
    $stmtMitarbeiter = $pdo->prepare($sqlMitarbeiter);
    $stmtMitarbeiter->execute();

    if ($stmtMitarbeiter->rowCount() > 0) {
        while ($row = $stmtMitarbeiter->fetch(PDO::FETCH_ASSOC)) {
            echo "Nachname: " . $row["nachname"] . " Vorname: " . $row["vorname"] . " Status: " . $row["Status"] . "<br>";
        }
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
    <title>Admin - Mitarbeiterverwaltung</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .btn { padding: 5px 10px; margin: 0 2px; cursor: pointer; }
    </style>
</head>
<body>
    
    <!-- Button um vom Admin Board zur Zeiterfassung zu gelangen -->
    <button onclick="window.location.href='dashboard_test.php'" class="btn">Zeiterfassung</button>

    <!-- Button um sich vom Admin Board auszuloggen -->
    <form method="post" style="display: inline;">
    <button type="submit" name="logout" class="btn">Logout</button>
    </form>

    <h1>Mitarbeiterübersicht</h1>
    <table>
        <thead>
            <tr>
                <th>Nachname</th>
                <th>Vorname</th>
                <th>Status</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $stmtMitarbeiter->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row["nachname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["vorname"]); ?></td>
                    <td><?php echo htmlspecialchars($row["Status"]); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $row['MitarbeiterID']; ?>">
                            <button class="btn" type="submit" name="new_status" value="Member">Member</button>
                            <button class="btn" type="submit" name="new_status" value="Admin">Admin</button>
                            <button class="btn" type="submit" name="new_status" value="Gesperrt">Gesperrt</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>


