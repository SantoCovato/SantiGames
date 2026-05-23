<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

// Query per recuperare i prodotti ordinati
$sql = "SELECT ordini.*, prodotti.nome as nome_prodotto, prodotti.immagine 
        FROM ordini 
        LEFT JOIN prodotti ON ordini.id_prodotto = prodotti.id 
        WHERE ordini.id_utente = $id_utente 
        ORDER BY ordini.data_ordine DESC";

$res = mysqli_query($db, $sql);

// Raggruppamento per codice_ordine
$ordini_raggruppati = [];
while($row = mysqli_fetch_assoc($res)) {
    $codice = $row['codice_ordine'];
    if (!isset($ordini_raggruppati[$codice])) {
        $ordini_raggruppati[$codice] = [
            'data' => $row['data_ordine'],
            'stato' => $row['stato'],
            'prodotti' => []
        ];
    }
    $ordini_raggruppati[$codice]['prodotti'][] = $row;
}
?>

<html>
<head>
    <title>SantiGames | I Miei Ordini</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .ordini-wrapper { max-width: 900px; margin: 60px auto; padding: 0 20px; }
        
        /* Container dell'intero ordine raggruppato */
        .ordine-container {
            background: #1a1a1a;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 5px solid #333;
            transition: 0.3s;
        }
        .ordine-container:hover { border-left-color: #ff0000; }

        .ordine-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .codice-titolo { color: #ff0000; font-weight: bold; font-size: 13px; text-transform: uppercase; }
        .data-ordine { color: #888; font-size: 13px; }

        /* Lista prodotti dentro l'ordine */
        .prodotto-riga {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #222;
        }
        .prodotto-riga:last-child { border-bottom: none; }

        .prod-info { display: flex; align-items: center; gap: 15px; }
        .prod-info img { width: 50px; border-radius: 5px; background: #000; }
        .prod-nome { color: white; font-size: 16px; }

        .stato-badge {
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: #333; color: #aaa;
        }
        .stato-spedito { background: #ff0000; color: white; }
        .stato-consegnato { background: #00ff00; color: black; }

        .totale-ordine-box {
            text-align: right;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #333;
        }
        .prezzo-finale { color: white; font-size: 22px; font-weight: bold; }

        /* TASTO VAI ALLO SHOP (ROSSO ARROTONDATO) */
        .vuoto { text-align: center; color: #666; padding: 100px 0; }
        .btn-home { 
            color: white; 
            background: #ff0000; 
            text-decoration: none; 
            padding: 12px 35px; 
            border-radius: 50px; 
            display: inline-block; 
            margin-top: 20px; 
            font-weight: bold; 
            transition: 0.3s;
        }
        .btn-home:hover { background: #cc0000; transform: scale(1.05); }

        /* Navbar Dropdown */
        .utente-wrapper { position: relative; }
        .dropdown-popup { display: none; position: absolute; top: 100%; right: 0; background: #0a0a0a; border: 1px solid #333; min-width: 160px; z-index: 1000; border-radius: 5px; }
        .dropdown-popup.show { display: block; }
        .dropdown-popup a { color: white; padding: 10px 15px; text-decoration: none; display: block; font-size: 14px; }
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
                        <a href="javascript:void(0)" onclick="togglePopup()" style="color:white; text-decoration:none;">
                            <img src="img/login.png" width="25"> <?php echo $_SESSION['username']; ?> ▼
                        </a>
                        <div id="popupUtente" class="dropdown-popup">
                            <a href="profilo.php">Mio Profilo</a>
                            <a href="ordini.php">I miei Ordini</a>
                            <hr><a href="logout.php" style="color:red;">LOGOUT</a>
                        </div>
                    </div>
                <?php endif; ?>
                <a href="carrello.php"><img src="img/cart.png" width="30"></a>
            </div>
        </div>
    </header>

    <main class="ordini-wrapper">
        <h2 style="color: white; border-left: 5px solid #ff0000; padding-left: 15px; margin-bottom: 40px;">I TUOI ORDINI</h2>

        <?php if(!empty($ordini_raggruppati)): ?>
            <?php foreach($ordini_raggruppati as $codice => $dati): ?>
                <div class="ordine-container">
                    
                    <div class="ordine-header">
                        <div>
                            <span class="codice-titolo">CODICE: <?php echo $codice; ?></span><br>
                            <span class="data-ordine"><?php echo date('d/m/Y H:i', strtotime($dati['data'])); ?></span>
                        </div>
                        <div>
                            <?php 
                                $classe = "";
                                if($dati['stato'] == 'spedito') $classe = "stato-spedito";
                                if($dati['stato'] == 'consegnato') $classe = "stato-consegnato";
                            ?>
                            <span class="stato-badge <?php echo $classe; ?>">
                                <?php echo $dati['stato']; ?>
                            </span>
                        </div>
                    </div>

                    <div class="lista-prodotti">
                        <?php 
                        $totale_ordine = 0;
                        foreach($dati['prodotti'] as $p): 
                            $sub = $p['prezzo_acquisto'] * $p['quantita'];
                            $totale_ordine += $sub;
                        ?>
                            <div class="prodotto-riga">
                                <div class="prod-info">
                                    <img src="img/<?php echo $p['immagine']; ?>" alt="">
                                    <div>
                                        <span class="prod-nome"><?php echo htmlspecialchars($p['nome_prodotto']); ?></span><br>
                                        <small style="color:#666;">Qtà: <?php echo $p['quantita']; ?></small>
                                    </div>
                                </div>
                                <div style="color: white; font-weight: bold;">
                                    <?php echo number_format($sub, 2, ',', '.'); ?> €
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="totale-ordine-box">
                        <span style="color:#666; font-size: 12px;">TOTALE ORDINE</span><br>
                        <span class="prezzo-finale"><?php echo number_format($totale_ordine, 2, ',', '.'); ?> €</span>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="vuoto">
                <img src="img/cart.png" width="60" style="opacity:0.2; margin-bottom: 20px; filter: invert(1);">
                <h2>Non ci sono ordini</h2>
                <p>Sembra che tu non abbia ancora acquistato nulla.</p>
                <a href="index.php" class="btn-home">VAI ALLO SHOP</a>
            </div>
        <?php endif; ?>
    </main>

    <footer style="text-align: center; padding: 40px; color: #444; font-size: 13px;">
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

    <script>
        function togglePopup() { document.getElementById("popupUtente").classList.toggle("show"); }
        window.onclick = function(event) {
            if (!event.target.closest('.utente-wrapper')) {
                const p = document.getElementById("popupUtente");
                if(p) p.classList.remove("show");
            }
        }
    </script>
</body>
</html>