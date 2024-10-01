<?php
session_start();
include 'config.php';
// Verifica se l'utente ha effettuato l'accesso e se è un docente
if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != 1) {

    header('Location: ESQL.php');
    exit();
}
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}


// Recupera l'email del docente dalla sessione
$email_docente = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Messaggio</title>
    <link href="stili.css" rel="stylesheet">
    <style>
        body {
            font-family: "Lato", sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            max-width: 500px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            color: #333;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .form-group button {
            padding: 12px 20px;
            color: #fff;
            background-color: #3399CC;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .form-group button:hover {
            background-color: #2878a9;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="inserisciMessaggio.php" method="post">
            <div class="form-group">
                <label for="titolo">Titolo</label>
                <input type="hidden" name="id_test" value="<?php echo htmlspecialchars($_POST['id_test']); ?>">
                <input type="text" id="titolo" name="titolo" required>
            </div>
            <div class="form-group">
                <label for="testo">Testo del Messaggio</label>
                <textarea id="testo" name="testo" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Invia Messaggio</button>
            </div>
        </form>
    </div>
</body>
</html>

