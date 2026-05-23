<?php 
// Avviamo la sessione
session_start(); 

// Connessione al database santigames
$db = mysqli_connect("localhost", "root", "", "santigames");

// Controllo connessione
if (!$db) {
    die("Connessione fallita: " . mysqli_connect_error());
}
?>

<html>
<head>
    <title>SantiGames | Gaming Store</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-nero">

    <header class="navbar">
        <div class="container flex-nav">
            <div class="logo">
                <a href="index.php">
                    <img src="img/santiGAMES.svg" alt="SantiGames" height="45">
                </a>
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

                <a href="carrello.php">
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
        </li>

        <li><a href="offerte.php" style="color:red">OFFERTE</a></li>
    </ul>
</nav>

    <main>
        <section class="container">
            <div class="mosaico-promo">
                <div class="col-lunga">
                    <a href="prodotto.php?id=12"><img src="img/cthulhu.png" alt="Promo Verticale"></a>
                </div>
                <div class="col-piccole">
                    <a href="prodotto.php?id=45"><img src="img/switchpromo.png" alt="Promo 1"></a>
                    <a href="prodotto.php?id=30"><img src="img/ps5promo.png" alt="Promo 2"></a>
                </div>
            </div>
        </section>

        <section class="container">
            <h2 class="titolo red-border">PRODOTTI IN EVIDENZA</h2>
            <div class="griglia">
                <?php
                $res = mysqli_query($db, "SELECT * FROM prodotti ORDER BY RAND() LIMIT 4");
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
                <?php endwhile; ?>
            </div>
        </section>

        <section class="container">
            <h2 class="titolo ps5-border">GIOCHI PS5</h2>
            <div class="griglia">
                <?php
                $res = mysqli_query($db, "SELECT * FROM prodotti WHERE tipologia = 'Gioco' AND piattaforma = 'PS5' LIMIT 4");
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
                <?php endwhile; ?>
            </div>
            <div class="footer-sezione">
                <a href="categoria.php?p=PS5&t=Gioco" class="btn-vedi-tutti">VEDI TUTTI I GIOCHI PS5</a>
            </div>
        </section>

        <section class="container">
            <h2 class="titolo nintendo-border">GIOCHI NINTENDO</h2>
            <div class="griglia">
                <?php
                $res = mysqli_query($db, "SELECT * FROM prodotti WHERE tipologia = 'Gioco' AND piattaforma IN ('SWITCH', 'SWITCH 2') LIMIT 4");
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
                <?php endwhile; ?>
            </div>
            <div class="footer-sezione">
                <a href="categoria.php?p=SWITCH&t=Gioco" class="btn-vedi-tutti">VEDI TUTTI I GIOCHI NINTENDO</a>
            </div>
        </section>

        <section class="container">
            <h2 class="titolo xbox-border">GIOCHI XBOX</h2>
            <div class="griglia">
                <?php
                $res = mysqli_query($db, "SELECT * FROM prodotti WHERE tipologia = 'Gioco' AND piattaforma = 'XBOX' LIMIT 4");
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
                <?php endwhile; ?>
            </div>
            <div class="footer-sezione">
                <a href="categoria.php?p=XBOX&t=Gioco" class="btn-vedi-tutti">VEDI TUTTI I GIOCHI XBOX</a>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

    <script>
        function togglePopup() {
            const popup = document.getElementById('popupUtente');
            if(popup) {
                popup.classList.toggle('show');
            }
        }

        window.onclick = function(event) {
            if (!event.target.closest('.utente-wrapper')) {
                const popup = document.getElementById('popupUtente');
                if (popup && popup.classList.contains('show')) {
                    popup.classList.remove('show');
                }
            }
        }
    </script>

</body>
</html>