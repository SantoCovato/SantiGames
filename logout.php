<?php
// 1. Inizializziamo la sessione
session_start();

// 2. Rimuoviamo tutte le variabili di sessione
session_unset();

// 3. Distruggiamo definitivamente la sessione sul server
session_destroy();

// 4. Reindirizziamo l'utente alla home page
header("Location: index.php");
exit();
?>