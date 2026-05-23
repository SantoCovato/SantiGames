<?php
session_start();

// 1. Connessione al Database
$host = "localhost";
$user = "root";
$psw = "";
$db_name = "santigames"; 

$connessione = new mysqli($host, $user, $psw, $db_name);

if ($connessione->connect_error) {
    die("Errore di connessione: " . $connessione->connect_error);
}

// 2. Recupero dati e protezione da SQL Injection (consigliato)
$email = mysqli_real_escape_string($connessione, $_POST['email']);
$password = mysqli_real_escape_string($connessione, $_POST['password']);

if (empty($email) || empty($password)) {
    header("Location: login.php?errore=vuoto");
    exit();
}

// 3. Cerco l'utente nel DB
$query = "SELECT * FROM utenti WHERE email = '$email' AND password = '$password'";
$risultato = $connessione->query($query);

if ($risultato->num_rows == 1) {
    // Utente trovato!
    $utente = $risultato->fetch_assoc();
    

    $_SESSION['id_utente'] = $utente['id']; // Salva l'ID per il carrello
    $_SESSION['username'] = $utente['username']; // Salva lo username per la navbar
    // ---------------------------------
    
    header("Location: index.php");
    exit();
} else {
    // Credenziali sbagliate
    header("Location: login.php?errore=errato");
    exit();
}

$connessione->close();
?>