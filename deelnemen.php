<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedstrijd Inzending</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: black;
            color: white;
            text-align: center;
            font-family: 'Raleway', sans-serif;
            margin: 0;
            padding: 0;
        }
        .logo {
            margin-top: 100px;
            width: 330px;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 500px;
            text-align: center;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            color: white;
            background-color: #ff9900;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 700;
        }
        .button:hover {
            background-color: #ff9900;
        }
        p {
            font-size: 18px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <img class="logo" alt="Sleepworld logo" src="https://www.sleepworld.be/static/version1723444772/frontend/Slaapadvies/cognac/nl_NL/images/sleepworld-logo.webp">

    <div class="container">
        <?php
        // Databaseverbinding
        $host = 'sacha.info';
        $dbname = 'sachainfo_bestmx';
        $username = 'sachainfo_bestmx';
        $password = 'LJPeX7HRCfwgQS4V93Fq';

        // Verbinding zonder expliciete SSL-configuratie
        $mysqli = new mysqli($host, $username, $password, $dbname);

        // Controleren op verbinding
        if ($mysqli->connect_error) {
            die("Verbindingsfout: " . $mysqli->connect_error);
        }

        // IP-adres ophalen
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // Formuliergegevens ophalen
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $naam = trim($_POST['naam'] ?? '');
            $voornaam = trim($_POST['voornaam'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $antwoord = trim($_POST['wedstrijd_vraag'] ?? '');

            // Validatie
            if (empty($naam) || empty($voornaam) || empty($email) || empty($antwoord)) {
                echo "<p>Alle velden zijn verplicht.</p>";
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p>Ongeldig e-mailadres.</p>";
                exit;
            }

            // Controleren op dubbele inzending
            $stmt = $mysqli->prepare("SELECT COUNT(*) FROM deelnemers WHERE email = ? OR ip_address = ?");
            $stmt->bind_param("ss", $email, $ipAddress);
            $stmt->execute();
            $stmt->bind_result($exists);
            $stmt->fetch();
            $stmt->close();

            if ($exists) {
                echo "<p>Sorry, Je hebt al deelgenomen.</p><br><a href='https://www.sleepworld.be' class='button'>Terug naar de website</a>";
                exit;
            }

            // Gegevens opslaan
            $stmt = $mysqli->prepare("INSERT INTO deelnemers (naam, voornaam, email, antwoord, ip_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $naam, $voornaam, $email, $antwoord, $ipAddress);

            if ($stmt->execute()) {
                echo "<p>Bedankt voor je deelname!</p>";
            } else {
                echo "<p>Er is iets misgegaan. Probeer het later opnieuw.</p>";
            }

            $stmt->close();
        } else {
            echo "<p>Ongeldige aanvraag.</p>";
        }

        // Verbinding sluiten
        $mysqli->close();
        ?>
        <a href="https://www.sleepworld.be" class="button">Terug naar de website</a>
    </div>
</body>
</html>
