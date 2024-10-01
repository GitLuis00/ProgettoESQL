<?php
//PAGINA LOGIN PER UTENTI
@include 'config.php';
session_start();
unset($_SESSION['authorized']);
unset($_SESSION['name']);
unset($_SESSION['email']);
unset($_SESSION['tipo_utente']);
if(isset($_POST['Login']))
{

   if (!empty($_POST["email"]) and !empty($_POST["password"])) {
      $email=trim($_POST["email"]);
      $password=trim($_POST["password"]);

      try {
        if (strlen($email) <= 30 && strlen($password) <= 30) {
            $res = $pdo->prepare('CALL VerificaCredenzialiStudente(?,?)');
            $res->bindParam(1, $email, PDO::PARAM_STR);
            $res->bindParam(2, $password, PDO::PARAM_STR);
            $res->execute();
            
            // Assumendo che la stored procedure ritorni un risultato che possiamo leggere
            $result = $res->fetch(PDO::FETCH_ASSOC);
            $res->closeCursor();

            // Verifica se il risultato è positivo
            if ($result['counter'] == 1) { // Modifica 'authorized' in base al nome della colonna ritornata dalla tua SP
                $_SESSION['authorized'] = 1;
                $_SESSION['email'] = $email;
                $_SESSION['tipo_utente'] = 'studente';
                $stmt = $pdo->prepare("SELECT Studente.email
                       FROM Studente 
                       JOIN Utente ON Studente.email = Utente.email 
                       WHERE Utente.email = :email");

// Esegue la query con il parametro email
$stmt->execute([':email' => $email]);

// Recupera il risultato
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Se la query trova una corrispondenza, l'ID dello studente viene salvato in una variabile
    $emailStudente = $row['email_studente'];
    $_SESSION['email_studente'] =  $emailStudente;
    // Qui puoi fare ulteriori operazioni con l'ID dello studente
    
}
                header("Location: pagewelcome.php");
                exit();
            } else {
                // Qui puoi gestire il caso in cui le credenziali non sono valide
                // Ad esempio, reindirizzare l'utente a una pagina di login con un messaggio di errore
                header("Location: pageloginStudente.php?error=invalid_credentials");
                exit();
            }
        } else {
            // Casi in cui email o password superano la lunghezza massima
            header("Location: pageloginStudente.php?error=input_too_long");
            exit();
        }
       
      }
      catch(PDOException $e) {
         echo("[ERRORE] Query SQL non riuscita. Errore: ".$e->getMessage());
         exit();
      }
    }
   else
    {
        $mess="Non hai riempito entrambi i campi!";


    }  
}
 
     
 ?>



<html>
<head>
    <title>ESQL - Login Studenti</title>
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="stili.css" rel="stylesheet">

    <style>
        body {
            background-color: #FFEBCC;
            font-family: 'Comic Neue', cursive;
            text-align: center;
            padding: 50px;
        }
        #intestazione {
            background-color: #FFB84D;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #FFFFFF;
            font-size: 50px;
            margin: 0;
        }
        h2 {
            color: #F47D2B;
            font-size: 30px;
            margin: 20px 0;
        }
        .titolo {
            color: #FF7043;
            font-size: 24px;
            margin: 20px 0;
        }
        #email, #password {
            padding: 10px;
            font-size: 18px;
            border: 2px solid #FF7043;
            border-radius: 10px;
            width: 250px;
            margin-bottom: 20px;
        }
        #pulsante {
            background-color: #FF7043;
            color: #FFF;
            font-size: 18px;
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            margin: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        #pulsante:hover {
            background-color: #FF5722;
        }
        #errore {
            color: red;
            font-size: 18px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<form action='pageloginStudente.php' method="post">

    <div id="intestazione">
        <h1>ESQL</h1>
    </div>

    <h2 class="titolo">LOGIN STUDENTI</h2>

    <div class="titolo">
        <b>Inserisci Email e Password per il Login!</b>
    </div>

    <div class="titolo">
        <b>Email:</b><br>
        <input type='text' name="email" id="email"><br><br>
        <b>Password:</b><br>
        <input type='password' name="password" id="password"><br><br>
    </div>

    <div>
        <input id="pulsante" type='submit' name='Login' value='Login'>
    </div>

    <div id="errore">
        <?php
        if (isset($mess)) {
            echo("<b>" . $mess . "</b>");
        }
        ?>
    </div>

    <div class="titolo">
        <p>Non sei registrato?</p>
    </div>

    <div class="bottoni">
        <input id="pulsante" type='button' value='Registrazione Studente' onclick="location.href='pageregister.php'"/>
        <input id="pulsante" type='button' value='Registrazione Docente' onclick="location.href='pageregisterDocente.php'"/>
    </div>

    <div>
        <input id="pulsante" type='button' value='Torna alla pagina iniziale' onclick="location.href='ESQL.php'"/>
    </div>

</form>
</body>
</html>

