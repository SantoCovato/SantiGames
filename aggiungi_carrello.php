<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

// Controllo login
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];
$id_prodotto = (int)$_POST['id_prodotto'];
$quantita = (int)$_POST['quantita'];

// Controlliamo se il prodotto è già nel carrello dell' utente
$check = mysqli_query($db, "SELECT id, quantita FROM carrello WHERE id_utente = $id_utente AND id_prodotto = $id_prodotto");

if (mysqli_num_rows($check) > 0) {
    // Se c'è già, aggiorniamo la riga esistente aggiungendo la nuova quantità
    mysqli_query($db, "UPDATE carrello SET quantita = quantita + $quantita WHERE id_utente = $id_utente AND id_prodotto = $id_prodotto");
} else {
    // Se non c'è, creiamo una nuova riga nella tabella
    mysqli_query($db, "INSERT INTO carrello (id_utente, id_prodotto, quantita) VALUES ($id_utente, $id_prodotto, $quantita)");
}

//Rimanda alla home
header("Location: carrello.php");
exit();
?>