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

// 2. Recupero dati e pulizia
$nome = mysqli_real_escape_string($connessione, $_POST['nome']);
$cognome = mysqli_real_escape_string($connessione, $_POST['cognome']);
$username = mysqli_real_escape_string($connessione, $_POST['username']);
$email = mysqli_real_escape_string($connessione, $_POST['email']);
$password = mysqli_real_escape_string($connessione, $_POST['password']);

// 3. Controllo se Username o Email esistono già
$check = "SELECT id FROM utenti WHERE username = '$username' OR email = '$email'";
$risultatoCheck = $connessione->query($check);

if ($risultatoCheck->num_rows > 0) {
    header("Location: registrati.php?errore=esistente");
    exit();
}

// 4. Inserimento nel Database
$sql = "INSERT INTO utenti (nome, cognome, username, email, password) 
        VALUES ('$nome', '$cognome', '$username', '$email', '$password')";

if ($connessione->query($sql) === TRUE) {
    // Recuperiamo l'ID appena creato dal database
    $nuovo_id = $connessione->insert_id; 
    
    $_SESSION['id_utente'] = $nuovo_id; // Fondamentale per il carrello
    $_SESSION['username'] = $username;
    
    header("Location: index.php");
    exit();
} else {
    header("Location: registrati.php?errore=db");
    exit();
}

$connessione->close();
?>