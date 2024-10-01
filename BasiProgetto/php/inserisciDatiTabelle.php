<?php
session_start();
@include 'config.php';
if (!isset($_SESSION['tipo_utente']) || $_SESSION['tipo_utente'] !== 'docente') {
    header('Location: ESQL.php');
    exit();
}
echo $_SESSION['nome_tabella_selezionata'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['nome_tabella_selezionata'])) {
    $nomeTabella = $_SESSION['nome_tabella_selezionata'];
    // Preparazione dei dati per l'inserimento
    $campi = [];
    $valori = [];
    foreach ($_POST as $campo => $valore) {
        if ($campo != 'azioneSpecifica') { 
            $campi[] = $campo;
            $valori[] = $valore;
        }
    }

    $campiStringa = implode(", ", $campi);
    $placeholders = implode(", ", array_fill(0, count($campi), "?"));

    $sql = "INSERT INTO $nomeTabella ($campiStringa) VALUES ($placeholders)";

    // Inserimento dei dati
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valori);
        echo "Dati inseriti con successo.";
    } catch (PDOException $e) {
        echo "Errore nell'inserimento dei dati: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Attributo alla Tabella</title>
    
<STYLE>
/* Stili di base per il corpo della pagina */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 40px 20px;
}

/* Stili per l'intestazione principale */
#intestazione h1 {
    color: #333;
    animation: fadeIn 1s ease-out;
}

/* Sottotitolo con colore personalizzato */
#sottotitolo h2 {
    color: #F47D2B;
    margin-bottom: 20px;
}

/* Stili per i titoli delle sezioni */
.titolo {
    font-size: 20px;
    margin-bottom: 10px;
}

/* Personalizzazione dei bottoni */
.bottoni input[type='button'] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease-in-out, transform 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.bottoni input[type='button']:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

/* Responsività */
@media only screen and (max-width: 600px) {
    .bottoni input[type='button'] {
        padding: 10px;
        font-size: 14px;
        width: 100%;
        margin: 10px 0;
    }
    
    #intestazione h1, #sottotitolo h2 {
        text-align: center;
    }
}

/* Animazioni */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stili aggiuntivi per form */
form {
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 5px;
}

input[type='text'], input[type='email'], input[type='submit'] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

input[type='submit'] {
    background-color: #28a745;
    color: white;
    cursor: pointer;
}

    /* Contenitore Flex per le colonne */
    .container {
        display: flex;
    }
    /* Stile per la colonna di sinistra (form) */
    .colonna-sinistra {
        flex: 1;
        padding-right: 20px; /* Spazio tra le colonne */
    }
    /* Stile per la colonna di destra (dati) */
    .colonna-destra {
        flex: 2; /* Assegna più spazio alla colonna dei dati */
    }

</style>
</head>
<body>
<?php


    if (isset($_SESSION['id_tabella_selezionata'])) {
        $idTabellaSelezionata = $_SESSION['id_tabella_selezionata'];
    
        try {
            // Recupera gli attributi della tabella selezionata
            $sql = "SELECT nome_attributo, tipo FROM AttributoTabella WHERE id_tabella = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$idTabellaSelezionata]);
            $attributi = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Genera il form dinamicamente basandosi sugli attributi
            echo "<form action='inserisciDatiTabelle.php' method='post'>";
            foreach ($attributi as $attributo) {
                $nome = htmlspecialchars($attributo['nome_attributo']);
                $tipo = htmlspecialchars($attributo['tipo']);
                // Genera un input per ciascun attributo
                // Qui potresti voler adattare l'input basandoti sul tipo SQL
                echo "<label for='$nome'>$nome:</label>";
                echo "<input type='text' name='$nome' required><br>";
            }
            echo "<input type='submit' value='Inserisci Dati'>";
            echo "</form>";
            try {
                $sqlProcedura = "CALL AggiornaNumRigheTabellaEsercizio(?)";
                $stmtProcedura = $pdo->prepare($sqlProcedura);
                // Esegui la procedura passando il nome della tabella
                $stmtProcedura->execute([$nomeTabella]);
                echo "Procedura eseguita con successo, num_righe aggiornato.";
            } catch (PDOException $e) {
                echo "Errore nell'esecuzione della procedura: " . $e->getMessage();
            }
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
    }
    
    ?>

</body>
</html>

