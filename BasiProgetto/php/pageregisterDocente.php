<?php
// PAGINA DI REGISTRAZIONE PER DOCENTI
@include 'config.php';
session_start();
unset($_SESSION['authorized']);
$error = "";

if (isset($_POST['submit'])) {
    if (!empty($_POST["email"]) && !empty($_POST["pass"]) && !empty($_POST["nome"]) && !empty($_POST["cognome"]) && !empty($_POST["cell"]) && !empty($_POST["dip"]) && !empty($_POST["corso"])) {

        $email = trim($_POST["email"]);
        $password = trim($_POST["pass"]);
        $nome = trim($_POST["nome"]);
        $cognome = trim($_POST["cognome"]);
        $telefono = trim($_POST["cell"]);
        $dipartimento = trim($_POST["dip"]);
        $corso = trim($_POST["corso"]);

        $pos1 = strpos($email, " ");
        $pos2 = strpos($password, " ");
        $pos4 = strpos($email, "@");
        $pos5 = strpos($email, ".");

        try {
          if ($pos4 !== false && $pos5 !== false) {
              if ($pos1 === false && $pos2 === false && strlen($email) <= 255 && strlen($password) <= 255 && strlen($nome) <= 100 && strlen($cognome) <= 100 && ctype_digit($telefono)) {
                  // Chiamata alla Stored Procedure per Inserimento in Utenti
                  $stmt = $pdo->prepare("CALL InserisciUtente(?, ?, ?, ?, ?, 'Docente')");
                  $stmt->execute([$email, $password, $nome, $cognome, $telefono]);
                  $row = $stmt->fetch();
                  
                  // Chiamata alla Stored Procedure per Inserimento in Docenti
                  $stmt = $pdo->prepare("CALL InserisciDocente(?, ?, ?)");
                  $stmt->execute([$email, $dipartimento, $corso]);

                  $_SESSION['authorized'] = 1; // 
                  $_SESSION['name'] = $name;
                  header("Location: pagewelcomeDocente.php"); // 
                  
              } else {
                  $error = "Hai riempito i campi in maniera non corretta!";
              }
          } else {
              $error = "La mail inserita non ha un formato consono!";
          }
      } catch (PDOException $e) {
          echo "[ERRORE] Query SQL non riuscita. Errore: " . $e->getMessage();
          exit();
      }
    } else {
        $error = "Non hai riempito tutti i campi!";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione Docenti - ESQL</title>
    <link href="stili.css" rel="stylesheet">
    <style>
        /* Stili di base */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7f9;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        #intestazione h1 {
            margin: 0;
            font-size: 2.5em;
            letter-spacing: 1.5px;
        }
        
        /* Titoli e testo */
        h1.titolo {
            color: #F47D2B;
            font-size: 1.8em;
            text-align: center;
            margin: 20px 0;
        }
        
        h3.titolo {
            color: #6A5ACD;
            text-align: center;
            margin-top: -10px;
        }
        
        /* Form */
        form {
            max-width: 600px;
            margin: 20px auto;
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
            border-radius: 5px;
            border: 2px solid #F47D2B;
            box-sizing: border-box;
        }
        
        input[type='submit'], input[type='button'] {
            background-color: #F47D2B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            transition: background-color 0.3s, transform 0.3s;
            display: block;
            margin: 20px auto;
        }
        
        input[type='submit']:hover, input[type='button']:hover {
            background-color: #D9641F;
            transform: translateY(-2px);
        }
        
        /* Messaggi di errore */
        #errore {
            text-align: center;
            color: #D9534F;
            font-weight: bold;
        }
        
        small {
            display: block;
            color: #808080;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <form action="pageregisterDocente.php" method="post">
        <div id="intestazione">
            <h1>ESQL</h1>
        </div>

        <h1 class="titolo">Pagina di Registrazione Docenti</h1>
        
        <!-- Campi del Form -->
        <div>
            <label for="email">E-Mail:</label>
            <input type="text" name="email" id="email" placeholder="nome.cognome@docente.it" required>
            <small>Example: nome.cognome@docente.it</small>
        </div>
        
        <div>
            <label for="pass">Password:</label>
            <input type="text" name="pass" id="pass" placeholder="Password" required>
            <small>Massimo 30 caratteri.</small>
        </div>
        
        <div>
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" placeholder="Nome" required>
            <small>Massimo 30 caratteri.</small>
        </div>
        
        <div>
            <label for="cognome">Cognome:</label>
            <input type="text" name="cognome" id="cognome" placeholder="Cognome" required>
            <small>Massimo 30 caratteri.</small>
        </div>
        
        <div>
            <label for="cell">Telefono:</label>
            <input type="text" name="cell" id="cell" placeholder="Telefono" required>
            <small>Solo numeri.</small>
        </div>
        
        <div>
            <label for="dip">Dipartimento:</label>
            <input type="text" name="dip" id="dip" placeholder="Dipartimento" required>
            <small>Indica il nome del tuo dipartimento.</small>
        </div>
        
        <div>
            <label for="corso">Corso:</label>
            <input type="text" name="corso" id="corso" placeholder="Corso" required>
            <small>Indica il nome del tuo corso.</small>
        </div>
        
        <!-- Pulsante Registrati -->
        <div>
            <input type="submit" name="submit" value="Registrati">
        </div>

        <!-- Messaggio di errore -->
        <div id="errore">
            <b><?php if (isset($error)) { echo htmlspecialchars($error); } ?></b>
        </div>

        <!-- Pulsante Torna alla Pagina Iniziale -->
        <div>
            <input type="button" value="Torna alla Pagina Iniziale" onclick="location.href='ESQL.php'">
        </div>
    </form>
</body>
</html>
