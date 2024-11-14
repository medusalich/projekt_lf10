<?php
session_start();
require "db.php"; // Datenbankverbindung einbinden
require "farbenblind_modus.php";
    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['farbwechsel'])) {
    farbwechsel();
}
$modeClass = farbModus();

// Initialisiere die Variablen
$searchResults = [];
$errorMsg = '';
$mitarbeiterName = '';

// Mitarbeiter suchen
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !isset($_POST['farbwechsel'])) {
    $searchName = $_POST['searchName'];

    // Mitarbeiter anhand des Namens suchen
    $sql = "SELECT * FROM Mitarbeiter WHERE Vorname LIKE :leer OR Nachname LIKE :leer";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['leer' => '%' . $searchName . '%']);
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Zeiterfassung aktualisieren
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_time'])) {
    $id = $_POST['time_id'];
    $newStart = $_POST['startzeit'];
    $newEnd = $_POST['endzeit'];
    
    // Aktualisieren der Zeiterfassungsdaten
    $updateSql = "UPDATE Zeiterfassung SET startzeit = :startzeit, endzeit = :endzeit WHERE id = :id";
    $stmt = $pdo->prepare($updateSql);
    if ($stmt->execute(['startzeit' => $newStart, 'endzeit' => $newEnd, 'id' => $id])) {
        $errorMsg = "Zeiterfassung erfolgreich aktualisiert!";
    } else {
        $errorMsg = "Fehler beim Aktualisieren der Zeiterfassung!";
    }
}

// Prüfe, ob Suchergebnisse vorhanden sind
if (count($searchResults) > 0) {
    $mitarbeiterName = $searchResults[0]['Vorname'] . ' ' . $searchResults[0]['Nachname'];
}
?>
<!DOCTYPE html>
<html lang="de">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mitarbeitersuche und Zeiterfassungskorrektur X Logistics</title>
        <link rel="stylesheet" href="css/styles.css">
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    </head>

    <body class="<?php echo $modeClass; ?>">
        
        <header>
            
            <button onclick="window.location.href='lohnabrechnung.php'">Abrechnungen</button>
            <button onclick="window.location.href='zeiterfassung.php'">Zeiterfassung</button>
            <button onclick="window.location.href='dashboard_admin.php'">Dashboard</button>
            <form method="post">
                <button id="auge-button" type="submit" name="farbwechsel"></button>
            </form>
        </header>
        <div class="xlogo">
                <?php
                    echo $_SESSION['farbenblind_modus'] ? '<img src="images/xlogo_bg_auge.png">' : '<img src="images/xlogo_bg.png">'; 
                ?>                    
        </div>

        <main>
            
            <div class="zeitanpassung">
                
                <h1>Mitarbeiter suchen</h1>
                
                <!-- Suchformular -->
                <form method="POST">
                    <label for="searchName">Name des Mitarbeiters:</label>
                    <input type="text" id="searchName" name="searchName" required>
                    <button type="submit" name="search">Suchen</button>
                </form>
            
                <?php if (!empty($errorMsg)) : ?>
                    <p><?php echo htmlspecialchars($errorMsg); ?></p>
                <?php endif; ?>

                <!-- Suchergebnisse -->
                <?php if (count($searchResults) > 0) : ?>
                    <h2>Suchergebnisse:</h2>
                    <ul>
                        <?php foreach ($searchResults as $mitarbeiter) : ?>
                            <li>
                                <strong><?php echo htmlspecialchars($mitarbeiter['Vorname'] . ' ' . $mitarbeiter['Nachname']); ?></strong>
                                <form method="POST" action="">
                                    <input type="hidden" name="mitarbeiterID" value="<?php echo $mitarbeiter['MitarbeiterID']; ?>">
                                    <button type="submit" name="view_times">Arbeitszeiten anzeigen</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) : ?>
                    <p>Kein Mitarbeiter mit diesem Namen gefunden.</p>
                <?php endif; ?>

                <!-- Zeiterfassungsdaten anzeigen und korrigieren -->
                <?php if (isset($_POST['view_times'])) : ?>
                    <?php
                    $mitarbeiterID = $_POST['mitarbeiterID'];
                    
                    // Arbeitszeiten des Mitarbeiters abrufen
                    $sql = "SELECT * FROM Zeiterfassung WHERE MitarbeiterID = :mitarbeiterID";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['mitarbeiterID' => $mitarbeiterID]);
                    $timeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    
                    <?php if (count($timeRecords) > 0) : ?>
                        
                        <table>
                            <tr>
                                <th>Original Startzeit</th>
                                <th>Original Endzeit</th>
                                <th>Neue Startzeit</th>
                                <th>Neue Endzeit</th>
                                <th>Aktionen</th>
                            </tr>
                            <?php foreach ($timeRecords as $record) : ?>
                                <tr>
                                    <!-- Originale Zeiten anzeigen -->   
                                    <td><?php echo date("d.m.Y H:i:s", strtotime($record['startzeit'])); ?></td>                                  
                                    <td><?php echo date("d.m.Y H:i:s", strtotime($record['endzeit'])); ?></td> 
                                    
                                    <!-- Bearbeitbare Felder für neue Start- und Endzeit -->
                                    <!-- Bearbeitbare Felder für neue Start- und Endzeit -->   
                                    <form method="POST">
                                        <td><input type="datetime-local" name="startzeit" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($record['startzeit']))); ?>"></td>
                                        <td><input type="datetime-local" name="endzeit" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($record['endzeit']))); ?>"></td>
                                        <td>
                                            <input type="hidden" name="time_id" value="<?php echo $record['id']; ?>">
                                            <button type="submit" name="update_time">Speichern</button>
                                        </td>
                                        <?php

                                        
                                        ?>

                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                        
                    <?php else : ?>
                        <p>Keine Arbeitszeiten gefunden.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </body>
</html>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_time'])) {
    $id = $_POST['time_id'];
    $newStart = $_POST['startzeit'];
    $newEnd = $_POST['endzeit'];

    // Start- und Endzeit in DateTime-Objekte umwandeln
    $startDateTime = new DateTime($newStart);
    $endDateTime = new DateTime($newEnd);
    
    // Berechnen der Differenz in Sekunden
    $interval = $startDateTime->diff($endDateTime);
    $dauerInSekunden = ($interval->days * 24 * 60 * 60) + 
                       ($interval->h * 60 * 60) + 
                       ($interval->i * 60) + 
                       $interval->s;

    // Aktualisieren der Zeiterfassungsdaten und Dauer in Sekunden speichern
    $updateSql = "UPDATE Zeiterfassung SET startzeit = :startzeit, endzeit = :endzeit, dauer = :dauer WHERE id = :id";
    $stmt = $pdo->prepare($updateSql);
    if ($stmt->execute(['startzeit' => $newStart, 'endzeit' => $newEnd, 'dauer' => $dauerInSekunden, 'id' => $id])) {
        $errorMsg = "Zeiterfassung erfolgreich aktualisiert!";
    } else {
        $errorMsg = "Fehler beim Aktualisieren der Zeiterfassung!";
    }
}
?>