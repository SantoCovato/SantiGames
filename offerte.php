<?php 
session_start(); 
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// 1. Recupero parametri URL
// Per le offerte non serve il parametro 'p' perché mostriamo tutto ciò che è scontato
$ordine = isset($_GET['ordine']) ? $_GET['ordine'] : 'default';

// Calcolo prezzo massimo specifico per i prodotti in sconto
$res_max = mysqli_query($db, "SELECT MAX(prezzo_scontato) as max_p FROM prodotti WHERE in_sconto = 1");
$row_max = mysqli_fetch_assoc($res_max);
$prezzo_limite_db = ceil($row_max['max_p'] ?? 100);

$prezzo_min_filter = isset($_GET['prezzo_min']) ? (float)$_GET['prezzo_min'] : 0;
$prezzo_max_filter = isset($_GET['prezzo_max']) ? (float)$_GET['prezzo_max'] : $prezzo_limite_db;

// Ordinamento basato sul prezzo già scontato
switch ($ordine) {
    case 'prezzo_cresce': $sql_order = "ORDER BY prezzo_scontato ASC"; break;
    case 'prezzo_decresce': $sql_order = "ORDER BY prezzo_scontato DESC"; break;
    case 'alpha_az': $sql_order = "ORDER BY nome ASC"; break;
    case 'alpha_za': $sql_order = "ORDER BY nome DESC"; break;
    default: $sql_order = "ORDER BY id DESC"; break;
}

$titolo_pagina = "OFFERTE";
?>

<html>
<head>
    <title>SantiGames | <?php echo $titolo_pagina; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* BARRA FILTRI */
        .filtri-wrapper {
            background: #1a1a1a;
            padding: 20px;
            border-radius: 12px;
            margin: 30px 0;
            border-left: 5px solid #ff0000;
        }
        .filtri-form {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: 20px;
        }
        .filtro-item { display: flex; flex-direction: column; gap: 8px; color: white; }
        
        /* RANGE SLIDER */
        .double-range-slider {
            width: 220px;
            position: relative;
            height: 20px;
        }
        .double-range-slider input[type="range"] {
            position: absolute;
            width: 100%;
            pointer-events: none;
            -webkit-appearance: none;
            background: none;
            outline: none;
            top: 50%;
            transform: translateY(-50%);
        }
        input[type="range"]::-webkit-slider-thumb {
            pointer-events: auto;
            -webkit-appearance: none;
            height: 16px; width: 16px;
            border-radius: 50%;
            background: #ff0000;
            border: 2px solid white;
            cursor: pointer;
            z-index: 5;
        }
        .slider-track {
            width: 100%;
            height: 4px;
            background: #444;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 10px;
        }
        .prezzo-labels { display: flex; justify-content: space-between; color: #ff0000; font-weight: bold; font-size: 14px; }
        
        .btn-filtra {
            background: #ff0000; color: white; border: none; padding: 10px 25px;
            border-radius: 50px; cursor: pointer; font-weight: bold; transition: 0.3s;
        }
        .btn-filtra:hover { background: #b30000; transform: translateY(-2px); }
        
        select { background: #333; color: white; border: 1px solid #444; padding: 8px; border-radius: 5px; }
    </style>
</head>
<body class="bg-nero">

    <header class="navbar">
        <div class="container flex-nav">
            <div class="logo">
                <a href="index.php"><img src="img/santiGAMES.svg" alt="SantiGames" height="45"></a>
            </div>
            
            <div class="ricerca">
                <form action="cerca.php" method="GET" class="search-box">
                    <input type="text" name="query" placeholder="Cerca giochi, console, componenti...">
                    <button type="submit" class="btn-lente">
                        <img src="img/lente.png" alt="Cerca">
                    </button>
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
                            <hr>
                            <a href="logout.php" class="logout-btn">LOGOUT</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php">
                        <img src="img/login.png" width="25"> Accedi
                    </a>
                <?php endif; ?>

                <a href="carrello.html">
                    <img src="img/cart.png" width="30">
                </a>
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
            <div class="dropdown-popup-cat">
                <a href="categoria.php?p=PC&t=Componente">Componenti</a>
                <a href="categoria.php?p=PC&t=Accessorio">Periferiche</a>
                <a href="categoria.php?p=PC&t=Gioco">Giochi PC</a>
            </div>
        </li>

        <li><a href="offerte.php" style="color:red">OFFERTE</a></li>
    </ul>
</nav>

    <main class="container">
        <h2 class="titolo red-border" style="margin-top:40px; color: white;"><?php echo $titolo_pagina; ?></h2>

        <div class="filtri-wrapper">
            <form method="GET" action="offerte.php" class="filtri-form">
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
                    <label>Prezzo Scontato (€):</label>
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
            // Query specifica: Mostra solo dove in_sconto è 1
            $sql = "SELECT * FROM prodotti 
                    WHERE in_sconto = 1 
                    AND prezzo_scontato >= $prezzo_min_filter 
                    AND prezzo_scontato <= $prezzo_max_filter 
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
                            <span class="prezzo-tagliato"><?php echo number_format($p['prezzo'], 2, ',', '.'); ?> €</span>
                            <span class="prezzo-scontato"><?php echo number_format($p['prezzo_scontato'], 2, ',', '.'); ?> €</span>
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
                echo "<p style='color:gray; grid-column:1/-1; text-align:center;'>Nessuna offerta attiva in questa fascia di prezzo.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

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

        window.onclick = function(e) {
            if (!e.target.closest('.utente-wrapper')) {
                const p = document.getElementById('popupUtente');
                if (p && p.classList.contains('show')) p.classList.remove('show');
            }
        }
    </script>
</body>
</html>