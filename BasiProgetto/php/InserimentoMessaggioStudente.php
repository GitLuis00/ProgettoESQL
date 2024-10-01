<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserisci Messaggio</title>
    <link href="stili.css" rel="stylesheet">
    <style>
        body {
            font-family: "Lato", sans-serif;
        }

        .form-container {
            padding: 20px;
            margin: 20px auto;
            width: 80%;
            max-width: 600px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-group button {
            padding: 10px 15px;
            color: #fff;
            background-color: #3399CC;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #2878a9;
        }
    </style>
</head>
<body>
<?php
session_start();
@include 'config.php';
$_SESSION['tipoInserimento']=1;

// Salvare l'ID del test nella variabile di sessione
$_SESSION['id_test'] = $_POST['id_test'];
?>


   


<form action="inserisciMessaggio.php" method="post">
    <div class="form-group">
        <input type="hidden" name="id_test" value="<?php echo $_POST['id_test']; ?>">
        <input type="hidden" name="email_docente" value="<?php echo $_POST['email_docente']; ?>">
        <label for="titolo">Titolo</label>
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



</body>
</html>
