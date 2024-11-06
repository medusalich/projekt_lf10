<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <H1>Test</H1><br>

    <h5>Login</h5><br>

    <form action="login.php" method="POST">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Anmelden</button>
    </form>


    <h2>Registrierung</h2>
    <form action="register.php" method="POST">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <label for="email">E-Mail:</label>
        <input type="email" id="email" name="email" required>
        <br><br>
        
        <label for="vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" required>
        <br><br>
        
        <label for="nachname">Nachname:</label>
        <input type="text" id="nachname" name="nachname" required>
        <br><br>
        
        <label for="strasse">Straße:</label>
        <input type="text" id="strasse" name="strasse" required>
        <br><br>
        
        <label for="postleitzahl">Postleitzahl:</label>
        <input type="text" id="postleitzahl" name="postleitzahl" required>
        <br><br>
        
        <label for="ort">Ort:</label>
        <input type="text" id="ort" name="ort" required>
        <br><br>
        
        <label for="geburtsdatum">Geburtsdatum:</label>
        <input type="date" id="geburtsdatum" name="geburtsdatum" required>
        <br><br>
        

        
        <button type="submit">Registrieren</button>
    </form>
</body>
</html>