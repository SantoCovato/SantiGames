<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

// 1. Controllo sicurezza: l'utente deve essere loggato
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

// 2. Recupero l'ID della riga da eliminare (passato via URL)
if (isset($_GET['id'])) {
    $id_riga = (int)$_GET['id'];
    $id_utente = $_SESSION['id_utente'];

    // 3. Eseguo la cancellazione
    $sql = "DELETE FROM carrello WHERE id = $id_riga AND id_utente = $id_utente";
    
    if (mysqli_query($db, $sql)) {
        // Torna al carrello con un messaggio di successo
        header("Location: carrello.php?rimosso=1");
    } else {
        // In caso di errore del database
        header("Location: carrello.php?errore=db");
    }
} else {
    // Se qualcuno prova ad aprire il file senza ID
    header("Location: carrello.php");
}
exit();
?>