<?php 
session_start(); 
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

// 1. Recupero la chiave di ricerca
$chiave = isset($_GET['query']) ? mysqli_real_escape_string($db, $_GET['query']) : '';

?>

<html>
<head>
    <meta charset="UTF-8">
    <title>SantiGames | Ricerca: <?php echo htmlspecialchars($chiave); ?></title>
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

    <main class="container">
        <h2 class="titolo red-border" style="margin-top:40px;"> RISULTATI PER: "<?php echo strtoupper(htmlspecialchars($chiave)); ?>"</h2>

        <div class="griglia">
            <?php
            if (!empty($chiave)) {
                // Cerchiamo la parola nel nome, nella descrizione o nella piattaforma
                $sql = "SELECT *, IF(in_sconto = 1, prezzo_scontato, prezzo) AS prezzo_reale 
                        FROM prodotti 
                        WHERE nome LIKE '%$chiave%' 
                        OR descrizione LIKE '%$chiave%' 
                        OR piattaforma LIKE '%$chiave%'
                        ORDER BY id DESC";
                
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
                                    <button type="submit" class="btn-compra"><img src="img/cart.png" alt="" width="16" style="margin-right: 8px; vertical-align: middle;">AGGIUNGI AL CARRELLO</button>
                                <?php else: ?>
                                    <button type="button" class="btn-compra" disabled style="background:#333; color:#777;">ESAURITO</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endwhile;
                } else {
                    echo "<p style='color:gray; grid-column:1/-1; text-align:center; padding:50px;'>Nessun prodotto trovato per questa ricerca.</p>";
                }
            } else {
                echo "<p style='color:gray; grid-column:1/-1; text-align:center; padding:50px;'>Scrivi qualcosa nella barra di ricerca sopra.</p>";
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 SantiGames - Gaming & Tech</p>
    </footer>

    <script>
        /* --- NAVBAR --- */
        function togglePopup() {
            const popup = document.getElementById("popupUtente");
            if (popup) {
                popup.classList.toggle("show");
            }
        }

        // Chiude il popup se l'utente clicca in qualsiasi altro punto della pagina
        window.onclick = function(event) {
            if (!event.target.closest('.utente-wrapper')) {
                const dropdowns = document.getElementsByClassName("dropdown-popup");
                for (let i = 0; i < dropdowns.length; i++) {
                    let openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

</body>
</html>