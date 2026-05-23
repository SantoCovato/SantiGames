<?php session_start(); ?>

<html>
<head>
    
    <title>Registrati | SantiGames</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-nero">

    <div class="login-wrapper">
        <div class="auth-brand">
            <a href="index.php"><img src="img/santiGAMES.svg" alt="Logo" height="60"></a>
        </div>

        <div class="auth-box">
            <div class="auth-header">
                <span class="linea-rossa"></span>
                <h2>REGISTRATI</h2>
            </div>

            <?php if(isset($_GET['errore'])): ?>
                <div style="background: rgba(255,0,0,0.1); border: 1px solid red; color: red; padding: 10px; font-size: 11px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                    <?php 
                        if($_GET['errore'] == 'esistente') echo "USERNAME O EMAIL GIÀ IN USO!";
                        if($_GET['errore'] == 'db') echo "ERRORE DEL SISTEMA. RIPROVA.";
                    ?>
                </div>
            <?php endif; ?>

            <form action="processa_registrazione.php" method="POST">
                <div class="riga-doppia">
                    <div class="input-group">
                        <label>NOME</label>
                        <input type="text" name="nome" placeholder="Inserisci il tuo nome" required>
                    </div>
                    <div class="input-group">
                        <label>COGNOME</label>
                        <input type="text" name="cognome" placeholder="Inserisci il tuo cognome" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>NOME UTENTE</label>
                    <input type="text" name="username" placeholder="Scegli il tuo nome utente" required>
                </div>

                <div class="input-group">
                    <label>EMAIL</label>
                    <input type="email" name="email" placeholder="Inserisci la tua Email" required>
                </div>

                <div class="input-group">
                    <label>PASSWORD</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-cyber">CREA PROFILO</button>
            </form>
            
            <div class="auth-footer">
                <span>Hai già un account?</span>
                <a href="login.php">ACCEDI</a>
            </div>
        </div>
    </div>

</body>
</html>