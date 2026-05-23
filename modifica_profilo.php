<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];
$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($db, $_POST['nome']); //stessa funzione di html special chars
    $cognome = mysqli_real_escape_string($db, $_POST['cognome']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $indirizzo = mysqli_real_escape_string($db, $_POST['indirizzo']);
    $citta = mysqli_real_escape_string($db, $_POST['citta']);
    $cap = mysqli_real_escape_string($db, $_POST['cap']);
    $nuova_pass = $_POST['password'];

    // Update completo
    $sql_update = "UPDATE utenti SET 
                   nome='$nome', cognome='$cognome', email='$email', 
                   indirizzo='$indirizzo', citta='$citta', cap='$cap' 
                   WHERE id=$id_utente";
    
    if (mysqli_query($db, $sql_update)) {
        // Se è stata inserita una nuova password, la aggiorno
        if (!empty($nuova_pass)) {
            mysqli_query($db, "UPDATE utenti SET password='$nuova_pass' WHERE id=$id_utente");
        }
        
        // REINDIRIZZAMENTO IMMEDIATO A PROFILO.PHP
        header("Location: profilo.php");
        exit(); // Interrompe l'esecuzione così non carica il resto della pagina inutilmente
    }
}

$res = mysqli_query($db, "SELECT * FROM utenti WHERE id = $id_utente");
$user = mysqli_fetch_assoc($res);
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>SantiGames | Modifica Dati</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .edit-wrapper { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .edit-card { background: #1a1a1a; padding: 40px; border-radius: 20px; border-top: 5px solid #ff0000; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full { grid-column: span 2; }
        .form-group label { display: block; color: #ff0000; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 12px; background: #0f0f0f; border: 1px solid #333; color: white; border-radius: 8px; outline: none; }
        .form-group input:focus { border-color: #ff0000; }
        .btn-save { width: 100%; padding: 15px; background: #ff0000; color: white; border: none; border-radius: 50px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: #cc0000; }
        /* Stessi stili Navbar di prima... */
    </style>
</head>
<body class="bg-nero">

    <style>
        .form-grid input { 
            max-width: 200px; /* larghezza campi input */
        }
    </style>

    <main class="edit-wrapper">
        <div class="edit-card">
            <h2 style="color: white; margin-bottom: 30px; text-align: center; text-transform: uppercase; letter-spacing: 1px;">Aggiorna il tuo Profilo</h2>
            
            <?php echo $messaggio; ?>

            <form action="modifica_profilo.php" method="POST">
                
                <h3 style="color: #666; font-size: 13px; text-transform: uppercase; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 5px;">Dati Account</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cognome</label>
                        <input type="text" name="cognome" value="<?php echo htmlspecialchars($user['cognome']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Cambia Password</label>
                        <input type="password" name="password" placeholder="Lascia vuoto per non modificare">
                    </div>
                </div>

                <h3 style="color: #666; font-size: 13px; text-transform: uppercase; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #333; padding-bottom: 5px;">Indirizzo di Spedizione</h3>
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Indirizzo e Numero Civico</label>
                        <input type="text" name="indirizzo" value="<?php echo htmlspecialchars($user['indirizzo']); ?>" placeholder="Es. Via Roma 12">
                    </div>
                    <div class="form-group">
                        <label>Città</label>
                        <input type="text" name="citta" value="<?php echo htmlspecialchars($user['citta']); ?>" placeholder="Es. Milano">
                    </div>
                    <div class="form-group">
                        <label>CAP</label>
                        <input type="text" name="cap" value="<?php echo htmlspecialchars($user['cap']); ?>" placeholder="Es. 20100">
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <button type="submit" class="btn-save">SALVA TUTTE LE MODIFICHE</button>
                    <a href="profilo.php" style="display:block; text-align:center; color:#666; margin-top:20px; text-decoration:none; font-size: 14px; font-weight: bold;">ANNULLA</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>