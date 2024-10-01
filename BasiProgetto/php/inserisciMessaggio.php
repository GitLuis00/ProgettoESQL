<?php
session_start();
@include 'config.php';

// Verifica se il metodo della richiesta è POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ottieni i dati dal modulo
    $titolo = $_POST['titolo'];
    $testo = $_POST['testo'];
    $id_test = $_SESSION['id_test']; // Assicurati che questo valore sia impostato correttamente nella sessione

    // Verifica il tipo di utente
    $tipo_utente = isset($_SESSION['tipo_utente']) ? $_SESSION['tipo_utente'] : '';

    try {
        if ($tipo_utente === 'studente') {
            // Prepara la query per l'inserimento del messaggio per lo studente
            $stmt = $pdo->prepare("CALL InserisciMessaggioStudente(:titolo, :testo, :id_test, :email_docente)");
            $email_docente = $_POST['email_docente'];
            $stmt->bindParam(':email_docente', $email_docente, PDO::PARAM_STR);
        } else {
            // Prepara la query per l'inserimento del messaggio per il docente
            $stmt = $pdo->prepare("CALL InserisciMessaggio(:titolo, :testo, :id_test)");
        }

        $stmt->bindParam(':titolo', $titolo, PDO::PARAM_STR);
        $stmt->bindParam(':testo', $testo, PDO::PARAM_STR);
        $stmt->bindParam(':id_test', $id_test, PDO::PARAM_INT);

        // Esegui la query
        $stmt->execute();

        // Reindirizza in base al tipo di utente
        if ($tipo_utente === 'studente') {
            // Se tipo_utente è 'studente'
            header("Location: visulizzatestStudente.php");
        } else {
            // Altrimenti, è un docente
            header("Location: paginaTest.php?id_test=" . urlencode($id_test));
        }
        exit();
    } catch (PDOException $e) {
        // Gestione degli errori
        die("Errore durante l'inserimento del messaggio: " . htmlspecialchars($e->getMessage()));
    }
} else {
    // Reindirizza alla pagina di inserimento del messaggio se non è una richiesta POST
    header('Location: inserimentoMessaggio.php');
    exit();
}
?>
