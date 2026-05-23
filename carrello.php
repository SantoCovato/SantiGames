<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// Controllo se l'utente è loggato
if (!isset($_SESSION['id_utente'])) {
    header("Location: login.php");
    exit();
}

$id_utente = $_SESSION['id_utente'];

// Query per recuperare tutti i prodotti nel carrello dell'utente
$sql = "SELECT carrello.id AS id_riga, prodotti.id AS id_prod, prodotti.nome, prodotti.immagine, 
               prodotti.prezzo, prodotti.prezzo_scontato, prodotti.in_sconto, 
               carrello.quantita 
        FROM carrello 
        JOIN prodotti ON carrello.id_prodotto = prodotti.id 
        WHERE carrello.id_utente = $id_utente";

$res = mysqli_query($db, $sql);
$totale_carrello = 0;
?>

<html>
<head>
    <title>SantiGames | Il Tuo Carrello</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .cart-container { margin: 50px auto; min-height: 600px; }
        .cart-table { width: 100%; border-collapse: collapse; background: #1a1a1a; border-radius: 10px; overflow: hidden; margin-top: 30px; }
        .cart-table th { background: #111; color: #ff0000; text-align: left; padding: 20px; font-size: 14px; text-transform: uppercase; }
        .cart-table td { padding: 20px; border-bottom: 1px solid #333; color: white; vertical-align: middle; }
        
        .product-cell { display: flex; align-items: center; gap: 20px; }
        .product-cell img { width: 70px; border-radius: 5px; background: #000; }
        .product-name { font-weight: bold; font-size: 1.1rem; }

        .qty-badge { background: #333; padding: 8px 15px; border-radius: 5px; font-weight: bold; border: 1px solid #444; }
        .price-text { font-weight: bold; color: #00ff00; }
        
        .btn-rimuovi { color: #ff4444; text-decoration: none; font-size: 13px; font-weight: bold; transition: 0.3s; }
        .btn-rimuovi:hover { color: #ff0000; text-shadow: 0 0 5px rgba(255,0,0,0.5); }

        .cart-summary { background: #111; padding: 30px; margin-top: 20px; border-radius: 10px; border: 1px solid #333; display: flex; justify-content: space-between; align-items: center; }
        .total-amount { font-size: 2rem; font-weight: 800; color: white; }
        .total-amount span { color: #ff0000; }

        .btn-checkout { background: #ff0000; color: white; padding: 15px 40px; border-radius: 50px; text-decoration: none; font-weight: bold; font-size: 1.2rem; transition: 0.3s; border: none; cursor: pointer; }
        .btn-checkout:hover { background: #cc0000; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(255,0,0,0.3); }
        
        .empty-cart { text-align: center; padding: 100px 0; color: #555; }
        .empty-cart h2 { color: white; margin-bottom: 20px; }
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
                <?php endif; ?>
                <a href="carrello.php"><img src="img/cart.png" width="30"></a>
            </div>
        </div>
    </header>

    <main class="container cart-container">
        <h1 class="titolo red-border">IL TUO CARRELLO</h1>

        <?php if(mysqli_num_rows($res) > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Prodotto</th>
                        <th>Quantità</th>
                        <th>Prezzo Unitario</th>
                        <th>Subtotale</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($res)): 
                        $prezzo_vero = ($row['in_sconto'] == 1) ? $row['prezzo_scontato'] : $row['prezzo'];
                        $subtotale = $prezzo_vero * $row['quantita'];
                        $totale_carrello += $subtotale;
                    ?>
                    <tr>
                        <td class="product-cell">
                            <img src="img/<?php echo $row['immagine']; ?>" alt="">
                            <div>
                                <div class="product-name"><?php echo $row['nome']; ?></div>
                                <small style="color:#666;">Cod. Prodotto: #<?php echo $row['id_prod']; ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="qty-badge"><?php echo $row['quantita']; ?></span>
                        </td>
                        <td>
                            <?php echo number_format($prezzo_vero, 2, ',', '.'); ?> €
                        </td>
                        <td class="price-text">
                            <?php echo number_format($subtotale, 2, ',', '.'); ?> €
                        </td>
                        <td>
                            <a href="rimuovi_dal_carrello.php?id=<?php echo $row['id_riga']; ?>" class="btn-rimuovi">RIMUOVI</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div>
                    <a href="index.php" style="color:#888; text-decoration:none;">← Continua lo shopping</a>
                </div>
                <div style="text-align: right;">
                    <div class="total-amount">TOTALE: <span><?php echo number_format($totale_carrello, 2, ',', '.'); ?> €</span></div>
                    <p style="color:#666; margin-bottom:20px;">IVA inclusa e spedizione calcolata al checkout</p>
                    <a href="checkout.php" class="btn-checkout">PROCEDI ALL'ORDINE</a>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <img src="img/cart.png" width="80" style="opacity:0.1; margin-bottom:20px; filter: invert(1);">
                <h2>Il tuo carrello è vuoto</h2>
                <p>Non hai ancora aggiunto nessun gioco al carrello.</p><br>
                <a href="index.php" class="btn-filtra" style="text-decoration:none; padding: 15px 30px;">TORNA ALLO SHOP</a>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

    <script>
        function togglePopup() { 
            const p = document.getElementById('popupUtente');
            if(p) p.classList.toggle('show'); 
        }
    </script>
</body>
</html>