<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// Controllo login
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

// Recupero dati utente
$sql = "SELECT * FROM utenti WHERE id = $id_utente";
$res = mysqli_query($db, $sql);
$user = mysqli_fetch_assoc($res);

 /* Usiamo DISTINCT per contare gli ordini unici invece dei singoli prodotti */
$res_ordini = mysqli_query($db, "SELECT COUNT(DISTINCT codice_ordine) as tot FROM ordini WHERE id_utente = $id_utente");
$row_ordini = mysqli_fetch_assoc($res_ordini);
$num_ordini = $row_ordini['tot'] ?? 0;
?>

<html>
<head>

    <title>SantiGames | Profilo Utente</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-wrapper { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        .profile-header { background: #1a1a1a; padding: 40px; border-radius: 20px; border-left: 6px solid #ff0000; display: flex; align-items: center; gap: 30px; margin-bottom: 20px; }
        .avatar-main { width: 80px; height: 80px; background: #ff0000; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 35px; font-weight: bold; }
        .user-titles h1 { color: white; margin: 0; font-size: 24px; text-transform: uppercase; }
        .stat-bar { background: #1a1a1a; padding: 20px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #333; display: inline-block; }
        .stat-bar span { color: #888; font-size: 12px; text-transform: uppercase; margin-right: 10px; }
        .stat-bar strong { color: white; font-size: 18px; }
        .details-card { background: #1a1a1a; padding: 40px; border-radius: 20px; color: white; margin-bottom: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-field { background: #0f0f0f; padding: 15px 20px; border-radius: 10px; border: 1px solid #222; }
        .info-field label { display: block; color: #ff0000; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .info-field span { font-size: 16px; color: #eee; }
        .btn-edit { display: inline-block; margin-top: 30px; background: #ff0000; color: white; padding: 12px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-edit:hover { background: #cc0000; transform: scale(1.05); }
        .utente-wrapper { position: relative; }
        .dropdown-popup { display: none; position: absolute; top: 100%; right: 0; background: #0a0a0a; border: 1px solid #333; min-width: 160px; z-index: 1000; border-radius: 5px; }
        .dropdown-popup.show { display: block; }
        .dropdown-popup a { color: white; padding: 10px 15px; text-decoration: none; display: block; font-size: 14px; }
        .dropdown-popup a:hover { background: #1a1a1a; color: red; }
        .logout-btn { color: #ff0000 !important; font-weight: bold; }
    </style>
</head>
<body class="bg-nero">

    <header class="navbar">
        <div class="container flex-nav">
            <div class="logo"><a href="index.php"><img src="img/santiGAMES.svg" alt="SantiGames" height="45"></a></div>
            <div class="ricerca">
                <form action="cerca.php" method="GET" class="search-box">
                    <input type="text" name="query" placeholder="Cerca giochi, console, componenti...">
                    <button type="submit" class="btn-lente"><img src="img/lente.png" alt="Cerca"></button>
                </form>
            </div>
            <div class="icone-destra">
                <?php if(isset($_SESSION['username'])): ?>
                    <div class="utente-wrapper">
                        <a href="javascript:void(0)" class="user-link" onclick="togglePopup()">
                            <img src="img/login.png" width="25"> 
                            <span><?php echo $_SESSION['username']; ?> ▼</span>
                        </a>
                        <div id="popupUtente" class="dropdown-popup">
                            <a href="profilo.php">Mio Profilo</a>
                            <a href="ordini.php">I miei Ordini</a>
                            <hr><a href="logout.php" class="logout-btn">LOGOUT</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php"><img src="img/login.png" width="25"> Accedi</a>
                <?php endif; ?>
                <a href="carrello.php"><img src="img/cart.png" width="30"></a>
            </div>
        </div>
    </header>

    <div class="dashboard-wrapper">
        <div class="profile-header">
            <div class="avatar-main"><?php echo substr($user['username'], 0, 1); ?></div>
            <div class="user-titles">
                <h1><?php echo $user['nome'] . " " . $user['cognome']; ?></h1>
            </div>
        </div>

        <div class="stat-bar">
            <span>Ordini effettuati:</span>
            <strong><?php echo $num_ordini; ?></strong>
        </div>

        <div class="details-card">
            <h3 style="margin-bottom: 20px; border-left: 3px solid red; padding-left: 10px;">Dati Account</h3>
            <div class="info-grid">
                <div class="info-field"><label>Email</label><span><?php echo htmlspecialchars($user['email']); ?></span></div>
                <div class="info-field"><label>Password</label><span>••••••••</span></div>
                <div class="info-field"><label>Nome</label><span><?php echo htmlspecialchars($user['nome']); ?></span></div>
                <div class="info-field"><label>Cognome</label><span><?php echo htmlspecialchars($user['cognome']); ?></span></div>
            </div>

            <h3 style="margin: 30px 0 20px; border-left: 3px solid red; padding-left: 10px;">Indirizzo di Spedizione</h3>
            <div class="info-grid">
                <div class="info-field"><label>Indirizzo</label><span><?php echo htmlspecialchars($user['indirizzo'] ?? 'Non impostato'); ?></span></div>
                <div class="info-field"><label>Città</label><span><?php echo htmlspecialchars($user['citta'] ?? 'Non impostato'); ?></span></div>
                <div class="info-field"><label>CAP</label><span><?php echo htmlspecialchars($user['cap'] ?? '-----'); ?></span></div>
            </div>

            <a href="modifica_profilo.php" class="btn-edit">MODIFICA DATI</a>
        </div>
    </div>

    <script>
        function togglePopup() { document.getElementById("popupUtente").classList.toggle("show"); }
        window.onclick = function(event) {
            if (!event.target.closest('.utente-wrapper')) {
                var dropdowns = document.getElementsByClassName("dropdown-popup");
                for (var i = 0; i < dropdowns.length; i++) {
                    if (dropdowns[i].classList.contains('show')) dropdowns[i].classList.remove('show');
                }
            }
        }
    </script>
</body>
</html>