<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "santigames");

if (!$db) { die("Connessione fallita: " . mysqli_connect_error()); }

if (!isset($_SESSION['id_utente'])) { header("Location: login.php"); exit(); }

$id_utente = $_SESSION['id_utente'];
$res_utente = mysqli_query($db, "SELECT * FROM utenti WHERE id = $id_utente");
$user = mysqli_fetch_assoc($res_utente);

$sql_carrello = "SELECT carrello.*, prodotti.nome, prodotti.prezzo, prodotti.prezzo_scontato, prodotti.in_sconto 
                 FROM carrello JOIN prodotti ON carrello.id_prodotto = prodotti.id 
                 WHERE carrello.id_utente = $id_utente";
$res_prodotti = mysqli_query($db, $sql_carrello);

if (mysqli_num_rows($res_prodotti) == 0) { header("Location: carrello.php"); exit(); }

// LOGICA DI SALVATAGGIO (Uguale per entrambi i metodi)
if (isset($_POST['finalizza_ordine'])) {
    $codice_ordine = "ORD-" . time();
    mysqli_data_seek($res_prodotti, 0);
    while ($item = mysqli_fetch_assoc($res_prodotti)) {
        $prezzo_f = ($item['in_sconto'] == 1) ? $item['prezzo_scontato'] : $item['prezzo'];
        $sql_ins = "INSERT INTO ordini (codice_ordine, id_utente, id_prodotto, quantita, prezzo_acquisto, stato) 
                    VALUES ('$codice_ordine', $id_utente, {$item['id_prodotto']}, {$item['quantita']}, $prezzo_f, 'in lavorazione')";
        mysqli_query($db, $sql_ins);
    }
    mysqli_query($db, "DELETE FROM carrello WHERE id_utente = $id_utente");
    header("Location: ordini.php");
    exit();
}
?>


<html>
<head>

    <title>SantiGames | Checkout</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .checkout-wrapper { max-width: 650px; margin: 40px auto; padding: 20px; color: white; }
        .checkout-card { background: #1a1a1a; padding: 30px; border-radius: 20px; border-top: 5px solid #ff0000; }
        
        /* Metodi di Pagamento */
        .payment-methods { margin: 25px 0; display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .method { 
            background: #0a0a0a; border: 2px solid #333; padding: 20px; border-radius: 10px; 
            cursor: pointer; text-align: center; transition: 0.3s; 
        }
        .method:hover { border-color: #555; }
        .method.active { border-color: #ff0000; background: #150000; }
        .method img { height: 25px; display: block; margin: 0 auto 10px; }
        .method span { font-size: 12px; font-weight: bold; text-transform: uppercase; }

        /* Modal PayPal */
        #paypalModal { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.9); z-index: 1000; align-items: center; justify-content: center; 
        }
        .paypal-box { background: white; width: 400px; padding: 40px; border-radius: 10px; color: #333; text-align: center; }
        .paypal-box input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        .btn-pp-login { background: #0070ba; color: white; border: none; width: 100%; padding: 12px; border-radius: 5px; font-weight: bold; cursor: pointer; }

        .btn-final { width: 100%; background: #ff0000; color: white; border: none; padding: 20px; border-radius: 50px; font-weight: 900; cursor: pointer; margin-top: 20px; }
    </style>
</head>
<body class="bg-nero">

<div class="checkout-wrapper">
    <div class="checkout-card">
        <h2>Pagamento</h2>
        
        <div class="payment-methods">
            <div class="method active" onclick="selectMethod('consegna', this)">
                <span>Alla Consegna</span>
            </div>
            <div class="method" onclick="selectMethod('paypal', this)">
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="Paypal">
                <span>Paga Ora</span>
            </div>
        </div>

        <div id="info-extra" style="font-size: 13px; color: #888; text-align: center; margin-bottom: 20px;">
            Pagherai in contanti direttamente al corriere.
        </div>

        <form id="mainForm" method="POST">
            <input type="hidden" name="finalizza_ordine" value="1">
            <button type="button" onclick="handleCheckout()" class="btn-final">CONFERMA ORDINE</button>
        </form>
    </div>
</div>

<div id="paypalModal">
    <div class="paypal-box">
        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" width="120" style="margin-bottom: 20px;">
        <h3>Accedi a PayPal</h3>
        <input type="email" placeholder="Email o numero di cellulare">
        <input type="password" placeholder="Password">
        <button type="button" class="btn-pp-login" onclick="fakeLogin()">Accedi e Paga</button>
        <p style="font-size: 12px; margin-top: 15px; color: #0070ba; cursor: pointer;">Hai dimenticato l'email?</p>
    </div>
</div>

<script>
    let selectedMethod = 'consegna';

    function selectMethod(method, element) {
        selectedMethod = method;
        document.querySelectorAll('.method').forEach(m => m.classList.remove('active'));
        element.classList.add('active');
        
        const info = document.getElementById('info-extra');
        info.innerText = (method === 'consegna') ? 'Pagherai in contanti direttamente al corriere.' : 'Verrai reindirizzato al login di PayPal.';
    }

    function handleCheckout() {
        if (selectedMethod === 'paypal') {
            document.getElementById('paypalModal').style.display = 'flex';
        } else {
            document.getElementById('mainForm').submit();
        }
    }

    function fakeLogin() {
        const btn = document.querySelector('.btn-pp-login');
        btn.innerText = "Elaborazione...";
        btn.disabled = true;
        
        setTimeout(() => {
            alert("Pagamento autorizzato con successo!");
            document.getElementById('mainForm').submit();
        }, 2000);
    }
</script>

</body>
</html>