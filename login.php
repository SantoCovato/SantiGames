<?php session_start(); ?>
<html>
<head>
    <title>Accedi | SantiGames</title>
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
                <h2>LOGIN</h2>
            </div>

            <?php if(isset($_GET['errore'])): ?>
                <div style="background: rgba(255,0,0,0.1); border: 1px solid red; color: red; padding: 10px; font-size: 11px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                    <?php 
                        if($_GET['errore'] == 'vuoto') echo "COMPILA TUTTI I CAMPI!";
                        if($_GET['errore'] == 'errato') echo "EMAIL O PASSWORD NON VALIDI!";
                    ?>
                </div>
            <?php endif; ?>

            <form action="processa_login.php" method="POST">
                <div class="input-group">
                    <label>EMAIL</label>
                    <input type="email" name="email" placeholder="Inserisci la tua Email" required>
                </div>
                <div class="input-group">
                    <label>PASSWORD</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-cyber">ACCEDI</button>
            </form>
            
            <div class="auth-footer">
                <span>Nuovo utente?</span>
                <a href="registrati.php">CREA ACCOUNT</a>
            </div>
        </div>
    </div>

</body>
</html>