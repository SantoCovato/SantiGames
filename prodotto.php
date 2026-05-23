<?php 
session_start(); 
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// Recupero l'ID del prodotto
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php");
    exit();
}

// Query per recuperare il prodotto specifico
$sql = "SELECT *, IF(in_sconto = 1, prezzo_scontato, prezzo) AS prezzo_reale FROM prodotti WHERE id = $id";
$res = mysqli_query($db, $sql);
$p = mysqli_fetch_assoc($res);

if (!$p) {
    die("<h1 style='color:white; text-align:center; margin-top:100px;'>Prodotto non trovato!</h1>");
}
?>


<html>
<head>
    <meta charset="UTF-8">
    <title>SantiGames | <?php echo $p['nome']; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* STILI SPECIFICI PER IL SITO*/
        .product-header {
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin: 40px 0 30px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .product-header h1 { font-size: 2.5rem; color: white; margin: 0; text-transform: uppercase; }
        .product-header .platform-tag { color: #ff0000; font-size: 1.2rem; font-weight: bold; }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 50px;
            margin-bottom: 80px;
        }

        /* LATO SINISTRO: FOTO E DESC */
        .left-side img { width: 100%; max-width: 500px; border-radius: 10px; background: #111; }
        .description-container { margin-top: 40px; color: #ccc; }
        .description-container h2 { color: white; border-bottom: 2px solid #ff0000; display: inline-block; padding-bottom: 5px; margin-bottom: 20px; }
        .description-text { line-height: 1.8; font-size: 1.1rem; }

        /* LATO DESTRO: BOX ACQUISTO */
        .buy-box { background: #1a1a1a; padding: 25px; border-radius: 15px; border: 1px solid #333; position: sticky; top: 20px; }
        .buy-box h3 { font-size: 1.1rem; margin-bottom: 20px; color: #888; text-transform: uppercase; }
        
        .price-display { margin-bottom: 25px; }
        .current-price { font-size: 2.5rem; font-weight: 800; color: white; display: block; }
        .old-price { font-size: 1.3rem; text-decoration: line-through; color: #666; margin-bottom: 5px; display: block; }

        .btn-add-cart {
            background: #ff0000; color: white; width: 100%; border: none; padding: 18px; border-radius: 50px;
            font-size: 1.1rem; font-weight: bold; cursor: pointer; display: flex; align-items: center;
            justify-content: center; gap: 10px; transition: 0.3s;
        }
        .btn-add-cart:hover { background: #cc0000; transform: translateY(-2px); }
        .btn-add-cart:disabled { background: #444; cursor: not-allowed; }

        .stock-label { display: block; margin-top: 15px; text-align: center; font-size: 0.9rem; font-weight: bold; }

        /* REGOLE NAVBAR (Copiate per sicurezza) */
        .dropdown-container { position: relative; }
        .dropdown-popup-cat { display: none; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); background-color: #0a0a0a; border: 1px solid #333; min-width: 180px; z-index: 9999; box-shadow: 0 10px 20px rgba(0,0,0,0.8); border-radius: 5px; padding: 5px 0; }
        .dropdown-container:hover .dropdown-popup-cat { display: block; }
        .dropdown-popup-cat a { color: white !important; padding: 10px 15px !important; text-decoration: none; display: block !important; font-size: 13px !important; text-align: left; transition: 0.3s; }
        .dropdown-popup-cat a:hover { background-color: #151515; color: red !important; }
        .popup-header { padding: 10px 15px; font-size: 10px; color: #555; text-transform: uppercase; font-weight: bold; }
        
        /* Container principale della quantità */
            .quantity-selector {
                margin-bottom: 25px;
            }

            .quantity-selector label {
                color: #888;
                font-size: 11px;
                font-weight: bold;
                display: block;
                margin-bottom: 8px;
                letter-spacing: 1px;
            }

            /* Flexbox per mettere i tasti ai lati dell'input */
            .qty-controls {
                display: flex;
                align-items: center;
                background: #222;
                border: 1px solid #333;
                border-radius: 8px;
                width: fit-content;
                overflow: hidden;
            }

            /* Bottoni + e - */
            .qty-btn {
                background: transparent;
                border: none;
                color: white;
                width: 45px;
                height: 45px;
                font-size: 20px;
                cursor: pointer;
                transition: 0.2s;
            }

            .qty-btn:hover {
                background: #333;
                color: #ff0000;
            }

            /* L'input numerico centrale */
            #qty-input {
                width: 60px;
                height: 45px;
                background: transparent;
                border: none;
                border-left: 1px solid #333;
                border-right: 1px solid #333;
                color: white;
                text-align: center;
                font-size: 1.1rem;
                font-weight: bold;
                -moz-appearance: textfield; /* Nasconde freccette su Firefox */
            }

            /* Nasconde freccette quantità */
            #qty-input::-webkit-outer-spin-button,
            #qty-input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
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
                <a href="carrello.html"><img src="img/cart.png" width="30"></a>
            </div>
        </div>
    </header>

    <nav class="categorie">
        <ul>
            <li class="dropdown-container"><a href="categoria.php?p=PLAYSTATION">PLAYSTATION</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=PLAYSTATION&t=Console">Console</a>
                    <a href="categoria.php?p=PLAYSTATION&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=PLAYSTATION&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container"><a href="categoria.php?p=XBOX">XBOX</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=XBOX&t=Console">Console</a>
                    <a href="categoria.php?p=XBOX&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=XBOX&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container"><a href="categoria.php?p=SWITCH">NINTENDO</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=SWITCH&t=Console">Console</a>
                    <a href="categoria.php?p=SWITCH&t=Gioco">Giochi</a>
                    <a href="categoria.php?p=SWITCH&t=Accessorio">Accessori</a>
                </div>
            </li>
            <li class="dropdown-container"><a href="categoria.php?p=PC">PC GAMING</a>
                <div class="dropdown-popup-cat">
                    <a href="categoria.php?p=PC&t=Componente">Componenti</a>
                    <a href="categoria.php?p=PC&t=Gioco">Giochi PC</a>
                </div>
            </li>
            <li><a href="offerte.php" style="color:red">OFFERTE</a></li>
        </ul>
    </nav>

    <main class="container">
        
        <header class="product-header">
            <h1><?php echo $p['nome']; ?></h1>
            <div class="platform-tag"><?php echo $p['piattaforma']; ?></div>
        </header>

        <div class="main-content">
            
            <div class="left-side">
                <img src="img/<?php echo $p['immagine']; ?>" alt="<?php echo $p['nome']; ?>">
                
                <div class="description-container">
                    <h2>DESCRIZIONE</h2>
                    <div class="description-text">
                        <?php echo nl2br($p['descrizione']); ?>
                    </div>
                </div>
            </div>

            <aside class="right-side">
                <div class="buy-box">
                    <h3>Acquista ora</h3>
                    
                    <div class="price-display">
                        <?php if ($p['in_sconto'] == 1): ?>
                            <span class="old-price"><?php echo number_format($p['prezzo'], 2, ',', '.'); ?> €</span>
                        <?php endif; ?>
                        <span class="current-price"><?php echo number_format($p['prezzo_reale'], 2, ',', '.'); ?> €</span>
                    </div>

                    <form action="aggiungi_carrello.php" method="POST">
                        <input type="hidden" name="id_prodotto" value="<?php echo $p['id']; ?>">
                        
                        <div class="quantity-selector">
                            <label>QUANTITÀ</label>
                            <div class="qty-controls">
                                <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                                <input type="number" name="quantita" id="qty-input" value="1" min="1" max="<?php echo $p['stock']; ?>" readonly>
                                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                            </div>
                        </div>

                        <button type="submit" class="btn-add-cart" <?php if($p['stock'] <= 0) echo 'disabled'; ?>>
                            <img src="img/cart.png" width="20" style="filter: brightness(0) invert(1);"> 
                            <?php echo ($p['stock'] > 0) ? "AGGIUNGI AL CARRELLO" : "NON DISPONIBILE"; ?>
                        </button>
                    </form>

                    <span class="stock-label" style="color: <?php echo ($p['stock'] > 0) ? '#00ff00' : '#ff0000'; ?>">
                        ● <?php echo ($p['stock'] > 0) ? "In Magazzino (" . $p['stock'] . " pezzi)" : "Prodotto Esaurito"; ?>
                    </span>
                </div>
            </aside>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

    <script>
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

        function changeQty(delta) {
            const input = document.getElementById('qty-input');
            let currentValue = parseInt(input.value);
            let max = parseInt(input.max);
            
            let newValue = currentValue + delta;
            
            // Controlli: non meno di 1, non più dello stock
            if (newValue >= 1 && newValue <= max) {
                input.value = newValue;
            }
        }
    </script>
</body>
</html>