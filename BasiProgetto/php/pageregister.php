
<?php
// PAGINA DI REGISTRAZIONE PER STUDENTI
@include 'config.php';
session_start();
unset($_SESSION['authorized']);
$error = "";

if (isset($_POST['submit'])) {
    if (!empty($_POST["email"]) && !empty($_POST["pass"]) && !empty($_POST["nome"]) && !empty($_POST["cognome"]) && !empty($_POST["cell"]) && !empty($_POST["year"])) {

        $email = trim($_POST["email"]);
        $password = trim($_POST["pass"]);
        $name = trim($_POST["nome"]);
        $surname = trim($_POST["cognome"]);
        $cell = trim($_POST["cell"]);
        $year = trim($_POST["year"]);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) <= 30 && strlen($name) <= 100 && strlen($surname) <= 100 && ctype_digit($cell) && is_numeric($year)) {

            try {
                // Chiamata alla Stored Procedure per la registrazione dello studente
                $stmt = $pdo->prepare("CALL RegistraStudente(?, ?, ?, ?, ?, ?)");
                $stmt->execute([$email, $password, $name, $surname, $cell, $year]);

                $_SESSION['authorized'] = 1; // Imposta un valore appropriato per la sessione
                $_SESSION['name'] = $name;
                header("Location: pagewelcome.php"); // Reindirizza alla pagina di benvenuto
            } catch (PDOException $e) {
                $error = "Errore durante la registrazione: " . $e->getMessage();
            }
        } else {
            $error = "I dati forniti non sono validi!";
        }
    } else {
        $error = "Tutti i campi sono obbligatori!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>ESQL - Registrazione Studenti</title>
    <link href="stili.css" rel="stylesheet">
    <style>
        /* Stili di base */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f4f8;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        /* Intestazione */
        #intestazione {
            background-color: #F47D2B;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        #intestazione h1 {
            margin: 0;
            font-size: 2.5em;
            letter-spacing: 2px;
        }
        
        /* Titoli e testo */
        h2.titolo {
            color: #F47D2B;
            font-size: 1.5em;
            margin: 20px 0;
            text-align: center;
        }
        
        /* Form */
        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        form div {
            margin-bottom: 15px;
        }
        
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        input[type='text'] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        input[type='submit'], input[type='button'] {
            background-color: #F47D2B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 10px;
        }
        
        input[type='submit']:hover, input[type='button']:hover {
            background-color: #D9641F;
            transform: translateY(-2px);
        }
        
        /* Messaggi di errore */
        #errore {
            color: #D9534F;
            font-weight: bold;
            text-align: center;
        }
        
        small {
            display: block;
            margin-top: 5px;
            color: #777;
        }
    </style>
</head>
<body>

<div id="intestazione">
    <h1>ESQL</h1>
</div>

<h2 class="titolo">Pagina Registrazione Studenti</h2>

<form action="pageregister.php" method="post">
    <div>
        <label for="email">E-Mail:</label>
        <input type="text" name="email" id="email" placeholder="nome.cognome@studente.it" required>
        <small>Example: nome.cognome@studente.it</small>
    </div>
    
    <div>
        <label for="pass">Password:</label>
        <input type="text" name="pass" id="pass" placeholder="Password" required>
        <small>La password dev'essere lunga al max 30 caratteri</small>
    </div>
    
    <div>
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" placeholder="Nome" required>
        <small>Il nome dev'essere lungo al max 30 caratteri</small>
    </div>
    
    <div>
        <label for="cognome">Cognome:</label>
        <input type="text" name="cognome" id="cognome" placeholder="Cognome" required>
        <small>Il cognome dev'essere lungo al max 30 caratteri</small>
    </div>
    
    <div>
        <label for="cell">Telefono:</label>
        <input type="text" name="cell" id="cell" placeholder="Telefono" required>
        <small>Deve contenere solo numeri</small>
    </div>
    
    <div>
        <label for="year">Anno di immatricolazione:</label>
        <input type="text" name="year" id="year" placeholder="Anno di immatricolazione" required>
        <small>Indica l'anno in cui ti sei immatricolato</small>
    </div>
    
    <div>
        <input type="submit" name="submit" value="Registrati">
    </div>
    
    <div id="errore">
        <?php if(isset($error)): ?>
            <b><?php echo htmlspecialchars($error); ?></b>
        <?php endif; ?>
    </div>
    
    <div>
        <input type="button" value="Torna alla pagina iniziale" onclick="location.href='ESQL.php'">
    </div>
</form>

</body>
</ht
