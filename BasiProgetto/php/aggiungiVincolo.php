<?php
@include 'config.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tabellaOrigine = $_POST['tabella_origine'];
    $attributoOrigine = $_POST['attributo_origine'];
    $tabellaDestinazione = $_POST['tabella_destinazione'];
    $attributoDestinazione = $_POST['attributo_destinazione'];
    $tabellaOrigineId = $_POST['tabella_origine'];
    $tabellaDestinazioneId = $_POST['tabella_destinazione'];
    
    // Recupera il nome della tabella origine
    $stmt = $pdo->prepare("SELECT nome FROM TabellaDiEsercizio WHERE id_tabella = ?");
    $stmt->execute([$tabellaOrigineId]);
    $tabellaOrigine = $stmt->fetchColumn();
    
    // Recupera il nome della tabella destinazione
    $stmt = $pdo->prepare("SELECT nome FROM TabellaDiEsercizio WHERE id_tabella = ?");
    $stmt->execute([$tabellaDestinazioneId]);
    $tabellaDestinazione = $stmt->fetchColumn();

    // Utilizzo di backticks per nomi di tabella e colonna
    $sql = "ALTER TABLE `$tabellaOrigine` ADD CONSTRAINT `fk_{$tabellaOrigine}_{$attributoOrigine}_to_{$tabellaDestinazione}_{$attributoDestinazione}` FOREIGN KEY (`$attributoOrigine`) REFERENCES `$tabellaDestinazione`(`$attributoDestinazione`)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo "Vincolo di integrità referenziale aggiunto con successo.";
    } catch (PDOException $e) {
        echo "Errore nell'aggiungere il vincolo: " . $e->getMessage();
    }
}
?>
