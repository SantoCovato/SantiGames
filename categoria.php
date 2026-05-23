<?php 
session_start(); 
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// 1. Recupero parametri URL
$p_scelta = isset($_GET['p']) ? mysqli_real_escape_string($db, $_GET['p']) : 'PS5';
$t_scelta = isset($_GET['t']) ? mysqli_real_escape_string($db, $_GET['t']) : ''; 
$ordine = isset($_GET['ordine']) ? $_GET['ordine'] : 'default';

// Mappatura per trasformare i nomi del DB in plurali per il titolo
$nomi_plurali = [
    'Gioco' => 'GIOCHI',
    'Console' => 'CONSOLE',
    'Accessorio' => 'ACCESSORI',
    'Componente' => 'COMPONENTI',
    'Periferica' => 'PERIFERICHE'
];

// 2. Calcolo prezzo massimo dinamico
$res_max = mysqli_query($db, "SELECT MAX(IF(in_sconto = 1, prezzo_scontato, prezzo)) as max_p FROM prodotti");
$row_max = mysqli_fetch_assoc($res_max);
$prezzo_limite_db = ceil($row_max['max_p'] ?? 100);

$prezzo_min_filter = isset($_GET['prezzo_min']) ? (float)$_GET['prezzo_min'] : 0;
$prezzo_max_filter = isset($_GET['prezzo_max']) ? (float)$_GET['prezzo_max'] : $prezzo_limite_db;

// 3. Gestione Ordinamento
switch ($ordine) {
    case 'prezzo_cresce': $sql_order = "ORDER BY prezzo_reale ASC"; break;
    case 'prezzo_decresce': $sql_order = "ORDER BY prezzo_reale DESC"; break;
    case 'alpha_az': $sql_order = "ORDER BY nome ASC"; break;
    case 'alpha_za': $sql_order = "ORDER BY nome DESC"; break;
    default: $sql_order = "ORDER BY id DESC"; break;
}

// 4. Costruzione Titolo Dinamico 
if ($p_scelta == 'PLAYSTATION') {
    $label_pait = "PLAYSTATION";
} elseif ($p_scelta == 'SWITCH') {
    $label_pait = "NINTENDO";
} elseif ($p_scelta == 'ALL') {
    $label_pait = "CATALOGO";
} else {
    $label_pait = $p_scelta;
}

$label_tipo = isset($nomi_plurali[$t_scelta]) ? $nomi_plurali[$t_scelta] : (!empty($t_scelta) ? strtoupper($t_scelta) : "PRODOTTI");
$titolo_pagina = "$label_tipo $label_pait";
?>

<html>
<head>

    <title>SantiGames | <?php echo $titolo_pagina; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* FILTRI */
        .filtri-wrapper { background: #1a1a1a; padding: 20px; border-radius: 12px; margin: 30px 0; border-left: 5px solid #ff0000; }
        .filtri-form { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 20px; }
        .filtro-item { display: flex; flex-direction: column; gap: 8px; color: white; }
        .double-range-slider { width: 220px; position: relative; height: 20px; }
        .double-range-slider input[type="range"] { position: absolute; width: 100%; pointer-events: none; -webkit-appearance: none; background: none; outline: none; top: 50%; transform: translateY(-50%); }
        input[type="range"]::-webkit-slider-thumb { pointer-events: auto; -webkit-appearance: none; height: 16px; width: 16px; border-radius: 50%; background: #ff0000; border: 2px solid white; cursor: pointer; z-index: 5; }
        .slider-track { width: 100%; height: 4px; background: #444; position: absolute; top: 50%; transform: translateY(-50%); border-radius: 10px; }
        .prezzo-labels { display: flex; justify-content: space-between; color: #ff0000; font-weight: bold; font-size: 14px; }
        .btn-filtra { background: #ff0000; color: white; border: none; padding: 10px 25px; border-radius: 50px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        select { background: #333; color: white; border: 1px solid #444; padding: 8px; border-radius: 5px; }

        /* POPUP CATEGORIE */
        .dropdown-container { position: relative; }
        .dropdown-popup-cat { display: none; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); background-color: #0a0a0a; border: 1px solid #333; min-width: 180px; z-index: 9999; box-shadow: 0 10px 20px rgba(0,0,0,0.8); border-radius: 5px; padding: 5px 0; }
        .dropdown-container:hover .dropdown-popup-cat { display: block; }
        .dropdown-popup-cat hr { border: 0; border-top: 1px solid #222; margin: 5px 0; }
        .dropdown-popup-cat a { color: white !important; padding: 10px 15px !important; text-decoration: none; display: block !important; font-size: 13px !important; text-align: left; transition: 0.3s; }
        .dropdown-popup-cat a:hover { background-color: #151515; color: red !important; }
        .popup-header { padding: 10px 15px; font-size: 10px; color: #555; text-transform: uppercase; font-weight: bold; text-align: left; }
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
                            <img src="img/login.png" width="25"> <span><?php echo $_SESSION['username']; ?> ▼</span>
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

    <nav class="categorie">
        <ul>
            <li class="dropdown-container">
                <a href="categoria.php?p=PLAYSTATION">PLAYSTATION</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=PLAYSTATION&t=Console">Console</a>
                    <a href="categoria.php?p=PLAYSTATION&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=PLAYSTATION&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container">
                <a href="categoria.php?p=XBOX">XBOX</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=XBOX&t=Console">Console</a>
                    <a href="categoria.php?p=XBOX&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=XBOX&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container">
                <a href="categoria.php?p=SWITCH">NINTENDO</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=SWITCH&t=Console">Console</a>
                    <a href="categoria.php?p=SWITCH&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=SWITCH&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container">
                <a href="categoria.php?p=PC">PC GAMING</a>
            </li>
            <li><a href="offerte.php" style="color:red">OFFERTE</a></li>
        </ul>
    </nav>

    <main class="container">
        <h2 class="titolo red-border" style="margin-top:40px;"><?php echo $titolo_pagina; ?></h2>

        <div class="filtri-wrapper">
            <form method="GET" action="categoria.php" class="filtri-form">
                <input type="hidden" name="p" value="<?php echo $p_scelta; ?>">
                <input type="hidden" name="t" value="<?php echo $t_scelta; ?>">

                <div class="filtro-item">
                    <label>Ordina per:</label>
                    <select name="ordine">
                        <option value="default" <?php if($ordine=='default') echo 'selected'; ?>>Più recenti</option>
                        <option value="prezzo_cresce" <?php if($ordine=='prezzo_cresce') echo 'selected'; ?>>Prezzo: Crescente</option>
                        <option value="prezzo_decresce" <?php if($ordine=='prezzo_decresce') echo 'selected'; ?>>Prezzo: Decrescente</option>
                        <option value="alpha_az" <?php if($ordine=='alpha_az') echo 'selected'; ?>>A-Z</option>
                        <option value="alpha_za" <?php if($ordine=='alpha_za') echo 'selected'; ?>>Z-A</option>
                    </select>
                </div>

                <div class="filtro-item">
                    <label>Prezzo (€):</label>
                    <div class="double-range-slider">
                        <div class="slider-track"></div>
                        <input type="range" name="prezzo_min" id="minRange" min="0" max="<?php echo $prezzo_limite_db; ?>" value="<?php echo $prezzo_min_filter; ?>" oninput="updateRange()">
                        <input type="range" name="prezzo_max" id="maxRange" min="0" max="<?php echo $prezzo_limite_db; ?>" value="<?php echo $prezzo_max_filter; ?>" oninput="updateRange()">
                    </div>
                    <div class="prezzo-labels">
                        <span id="labelMin"><?php echo $prezzo_min_filter; ?>€</span>
                        <span id="labelMax"><?php echo $prezzo_max_filter; ?>€</span>
                    </div>
                </div>
                <button type="submit" class="btn-filtra">APPLICA</button>
            </form>
        </div>

        <div class="griglia">
            <?php
            // Filtro Piattaforma (LOGICA PS5 + PS4)
            if ($p_scelta == 'PLAYSTATION') {
                $f_piattaforma = "piattaforma IN ('PS5', 'PS4')";
            } elseif ($p_scelta == 'XBOX') {
                $f_piattaforma = "piattaforma IN ('XBOX SERIES X', 'XBOX ONE', 'XBOX')";
            } elseif ($p_scelta == 'SWITCH') {
                $f_piattaforma = "piattaforma IN ('SWITCH', 'SWITCH 2')";
            } elseif ($p_scelta == 'ALL') {
                $f_piattaforma = "1=1"; 
            } else {
                $f_piattaforma = "piattaforma = '$p_scelta'";
            }

            // Filtro Tipologia
            $f_tipo = !empty($t_scelta) ? "AND tipologia = '$t_scelta'" : "";

            $sql = "SELECT *, IF(in_sconto = 1, prezzo_scontato, prezzo) AS prezzo_reale 
                    FROM prodotti 
                    WHERE $f_piattaforma $f_tipo 
                    HAVING prezzo_reale >= $prezzo_min_filter AND prezzo_reale <= $prezzo_max_filter 
                    $sql_order";
            
            $res = mysqli_query($db, $sql);

            if(mysqli_num_rows($res) > 0) {
                while ($p = mysqli_fetch_assoc($res)): ?>
                    <div class="card">
                        <a href="prodotto.php?id=<?php echo $p['id']; ?>" style="text-decoration: none; color: inherit;">
                            <img src="img/<?php echo $p['immagine']; ?>" alt="<?php echo $p['nome']; ?>">
                            <h3><?php echo $p['nome']; ?></h3>
                        </a>

                        <div style="font-size: 11px; margin-bottom: 10px; font-weight: bold; color: <?php echo ($p['stock'] > 0) ? '#00ff00' : '#ff0000'; ?>;">
                            ● <?php echo ($p['stock'] > 0) ? "DISPONIBILE" : "ESAURITO"; ?>
                        </div>

                        <div class="prezzo-container">
                            <?php if ($p['in_sconto'] == 1): ?>
                                <span class="prezzo-tagliato"><?php echo number_format($p['prezzo'], 2, ',', '.'); ?> €</span>
                                <span class="prezzo-scontato"><?php echo number_format($p['prezzo_scontato'], 2, ',', '.'); ?> €</span>
                            <?php else: ?>
                                <p class="prezzo"><?php echo number_format($p['prezzo'], 2, ',', '.'); ?> €</p>
                            <?php endif; ?>
                        </div>

                        <form action="aggiungi_carrello.php" method="POST">
                            <input type="hidden" name="id_prodotto" value="<?php echo $p['id']; ?>">
                            
                            <?php if ($p['stock'] > 0): ?>
                                <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px; gap: 5px;">
                                    <span style="font-size: 12px; color: #888;">Qtà:</span>
                                    <input type="number" name="quantita" value="1" min="1" max="<?php echo $p['stock']; ?>" 
                                        style="width: 50px; background: #222; border: 1px solid #444; color: white; text-align: center; border-radius: 4px; padding: 3px;">
                                </div>
                                
                                <button type="submit" class="btn-compra"> 
                                    <img src="img/cart.png" alt="" width="16" style="margin-right: 8px; vertical-align: middle;"> AGGIUNGI AL CARRELLO
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn-compra" disabled style="background: #333 !important; cursor: not-allowed; opacity: 0.6; color: #777;">
                                    NON DISPONIBILE
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endwhile;
            } else {
                echo "<p style='color:gray; grid-column:1/-1; text-align:center;'>Nessun prodotto trovato.</p>";
            }
            ?>
        </div>
    </main>

    <script>
        function updateRange() {
            let min = document.getElementById('minRange');
            let max = document.getElementById('maxRange');
            if (parseInt(min.value) > parseInt(max.value)) min.value = max.value;
            document.getElementById('labelMin').innerText = min.value + "€";
            document.getElementById('labelMax').innerText = max.value + "€";
        }
        function togglePopup() { 
            const p = document.getElementById('popupUtente');
            if(p) p.classList.toggle('show'); 
        }
        window.onclick = function(event) {
            if (!event.target.closest('.utente-wrapper')) {
                const p = document.getElementById('popupUtente');
                if (p && p.classList.contains('show')) p.classList.remove('show');
            }
        }
    </script>
</body>
</html>