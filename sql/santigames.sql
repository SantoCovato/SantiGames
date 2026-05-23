-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Apr 22, 2026 alle 22:43
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `santigames`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `carrello`
--

CREATE TABLE `carrello` (
  `id` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_prodotto` int(11) NOT NULL,
  `quantita` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini`
--

CREATE TABLE `ordini` (
  `id` int(11) NOT NULL,
  `codice_ordine` varchar(20) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_prodotto` int(11) NOT NULL,
  `quantita` int(11) NOT NULL,
  `prezzo_acquisto` decimal(10,2) NOT NULL,
  `data_ordine` timestamp NOT NULL DEFAULT current_timestamp(),
  `stato` enum('in lavorazione','spedito','consegnato') DEFAULT 'in lavorazione'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `ordini`
--

INSERT INTO `ordini` (`id`, `codice_ordine`, `id_utente`, `id_prodotto`, `quantita`, `prezzo_acquisto`, `data_ordine`, `stato`) VALUES
(10, 'ORD-1776881516', 2, 10, 1, 55.00, '2026-04-22 18:11:56', 'in lavorazione'),
(11, 'ORD-1776881516', 2, 31, 1, 449.99, '2026-04-22 18:11:56', 'in lavorazione');

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

CREATE TABLE `prodotti` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `tipologia` varchar(50) NOT NULL,
  `piattaforma` varchar(50) DEFAULT NULL,
  `immagine` varchar(255) DEFAULT 'default.png',
  `stock` int(11) DEFAULT 0,
  `in_sconto` int(11) DEFAULT 0,
  `prezzo_scontato` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`id`, `nome`, `descrizione`, `prezzo`, `tipologia`, `piattaforma`, `immagine`, `stock`, `in_sconto`, `prezzo_scontato`) VALUES
(1, 'Spider-Man 2', 'Esclusiva PS5.', 69.99, 'Gioco', 'PS5', 'spiderman2_ps5.png', 10, 0, NULL),
(2, 'Elden Ring', 'Versione PS5.', 59.99, 'Gioco', 'PS5', 'elden_ps5.png', 5, 0, NULL),
(3, 'FC 26', 'Il calcio su PS5.', 69.90, 'Gioco', 'PS5', 'fc26_ps5.png', 20, 1, 10.99),
(4, 'Zelda: Tears of the Kingdom', 'Il capolavoro per Switch.', 59.90, 'Gioco', 'SWITCH', 'zelda_switch.png', 15, 0, NULL),
(5, 'FC 26', 'Versione ottimizzata per Switch.', 49.90, 'Gioco', 'SWITCH', 'fc26_switch.png', 10, 1, 9.99),
(6, 'Super Mario Wonder', 'Piattaforme 2D.', 45.00, 'Gioco', 'SWITCH', 'mario_switch.png', 12, 0, NULL),
(7, 'Metroid Prime 4', 'Titolo di lancio Switch 2.', 79.99, 'Gioco', 'SWITCH 2', 'metroid4_s2.png', 5, 0, NULL),
(8, 'Mario Kart 8 Deluxe', 'Corse in 4K.', 79.90, 'Gioco', 'SWITCH', 'mk8_switch.png', 8, 0, NULL),
(9, 'Halo Infinite', 'Esclusiva Xbox.', 39.99, 'Gioco', 'XBOX', 'halo_xbox.png', 7, 0, NULL),
(10, 'Forza Motorsport', 'Corse in Messico.', 55.00, 'Gioco', 'XBOX', 'forza_xbox.png', 9, 0, NULL),
(11, 'Elden Ring', 'Versione Xbox Series X.', 59.99, 'Gioco', 'XBOX', 'elden_xbox.png', 4, 0, NULL),
(12, 'Cthulhu The Cosmic Abyss', 'Un viaggio terrificante nell’abisso cosmico, ispirato ai miti di Lovecraft.', 29.99, 'Gioco', 'PS5', 'cthulhu_ps5.png', 10, 0, NULL),
(30, 'PlayStation 5 Standard Edition', 'Versione con lettore disco 4K', 549.99, 'Console', 'PS5', 'ps5_std.png', 12, 1, 499.99),
(31, 'PlayStation 5 Digital Edition', 'Versione all-digital', 449.99, 'Console', 'PS5', 'ps5_dig.png', 10, 0, NULL),
(32, 'PlayStation 5 Slim', 'Design ultra-compact con 1TB SSD', 549.00, 'Console', 'PS5', 'ps5_slim.png', 20, 1, 479.00),
(33, 'PlayStation 5 Pro', 'Massima potenza, Ray Tracing e 4K nativo', 799.99, 'Console', 'PS5', 'ps5_pro.png', 5, 0, NULL),
(34, 'PlayStation 4', 'Modello Fat ricondizionato garantito', 129.00, 'Console', 'PS4', 'ps4_std.png', 4, 0, NULL),
(35, 'PlayStation 4 Slim', 'Modello Slim, basso consumo energetico', 159.00, 'Console', 'PS4', 'ps4_slim.png', 6, 1, 139.00),
(36, 'PlayStation 4 Pro', 'Gaming in 4K e performance migliorate', 199.00, 'Console', 'PS4', 'ps4_pro.png', 3, 0, NULL),
(37, 'Xbox Series X', 'La console più potente di Microsoft', 499.99, 'Console', 'XBOX', 'xbox_sx.png', 15, 1, 449.00),
(38, 'Xbox Series S', 'Next-gen compatta, ideale per Game Pass', 299.99, 'Console', 'XBOX', 'xbox_ss.png', 25, 1, 249.00),
(39, 'Xbox One', 'Modello originale con supporto HDMI In', 89.00, 'Console', 'XBOX', 'xbox_one.png', 2, 0, NULL),
(40, 'Xbox One S', 'Design bianco, supporto HDR e 4K video', 119.00, 'Console', 'XBOX', 'xbox_one_s.png', 5, 1, 99.00),
(41, 'Xbox One X', 'La più potente della scorsa generazione', 159.00, 'Console', 'XBOX', 'xbox_one_x.png', 3, 0, NULL),
(42, 'Nintendo Switch', 'Modello standard con Joy-Con migliorati', 269.00, 'Console', 'SWITCH', 'switch_std.png', 30, 1, 229.00),
(43, 'Nintendo Switch OLED', 'Schermo OLED 7\" e colori brillanti', 329.00, 'Console', 'SWITCH', 'switch_oled.png', 40, 1, 289.00),
(44, 'Nintendo Switch Lite', 'Leggera e compatta, solo portatile', 199.00, 'Console', 'SWITCH', 'switch_lite.png', 15, 1, 169.00),
(45, 'Nintendo Switch 2', 'Nuova generazione con DLSS e display HDR', 499.00, 'Console', 'SWITCH 2', 'switch_2.png', 10, 1, 469.99),
(46, 'God of War Ragnarök', 'L avventura epica di Kratos e Atreus attraverso i nove regni. Versione completa ottimizzata per PS5.', 49.99, 'Gioco', 'PS5', 'gow_ragnarok.png', 20, 1, 39.90),
(47, 'Gran Turismo 7', 'Il simulatore di guida definitivo con oltre 400 auto e tracciati leggendari in 4K HDR.', 69.90, 'Gioco', 'PS5', 'gt7.png', 15, 0, NULL),
(48, 'Mario Kart World', 'Il nuovo capitolo della saga con tracciati dinamici globali e modalità online cross-platform.', 59.99, 'Gioco', 'SWITCH 2', 'mario_kart_world.png', 30, 0, NULL),
(49, 'Battlefield 6', 'Guerra totale su scala mai vista prima con ambienti completamente distruttibili e match da 128 giocatori.', 79.99, 'Gioco', 'XBOX', 'battlefield6.png', 12, 1, 64.99),
(50, 'MotoGP 26', 'La stagione ufficiale 2026. Realismo senza precedenti, fisica delle gomme migliorata e carriera manageriale.', 74.90, 'Gioco', 'XBOX', 'motogp26.png', 10, 0, NULL),
(51, 'Cyberpunk 2077: Ultimate Edition', 'Include l espansione Phantom Liberty e tutti gli aggiornamenti tecnici. Il miglior RPG sci-fi su Xbox.', 44.99, 'Gioco', 'XBOX', 'cyberpunk_ultimate.png', 18, 1, 29.99),
(60, 'Intel Core i9-14900K', 'Il top di gamma Intel per gaming e multitasking estremo. 24 core e 32 thread.', 620.00, 'Componente', 'PC', 'cpu_i9.png', 10, 1, 589.00),
(61, 'AMD Ryzen 9 7950X3D', 'Processore con tecnologia 3D V-Cache, il re indiscusso del gaming su PC.', 680.00, 'Componente', 'PC', 'cpu_r9.png', 10, 0, 680.00),
(62, 'Intel Core i5-13600K', 'Miglior rapporto qualità-prezzo per il gaming a 1440p.', 320.00, 'Componente', 'PC', 'cpu_i5.png', 10, 1, 299.00),
(63, 'AMD Ryzen 5 7600X', 'CPU ideale per build entry level ad alte prestazioni su socket AM5.', 240.00, 'Componente', 'PC', 'cpu_r5.png', 10, 0, 240.00),
(64, 'NVIDIA RTX 4090 24GB', 'La scheda video più potente al mondo. Gaming in 4K nativo senza compromessi.', 2100.00, 'Componente', 'PC', 'gpu_4090.png', 10, 1, 1950.00),
(65, 'AMD Radeon RX 7900 XTX', 'Prestazioni incredibili in rasterizzazione con 24GB di memoria dedicata.', 1050.00, 'Componente', 'PC', 'gpu_7900.png', 10, 0, 1050.00),
(66, 'NVIDIA RTX 4070 Super', 'Perfetta per il gaming a 1440p con Ray Tracing e DLSS 3.5.', 650.00, 'Componente', 'PC', 'gpu_4070.png', 10, 1, 610.00),
(67, 'NVIDIA RTX 4060 Ti', 'Ottima efficienza energetica per giocare in Full HD al massimo dei dettagli.', 390.00, 'Componente', 'PC', 'gpu_4060.png', 10, 0, 390.00),
(68, 'Xbox Wireless Controller', 'Il classico controller Xbox rifinito per un maggiore comfort durante il gioco.', 59.99, 'Accessorio', 'XBOX', 'xbox_controller_black.png', 25, 1, 49.90),
(69, 'Xbox Elite Wireless Series 2', 'Controller professionale con levette a tensione regolabile e componenti intercambiabili.', 179.99, 'Accessorio', 'XBOX', 'xbox_elite.png', 10, 0, 179.99),
(70, 'Scheda di espansione Seagate 1TB', 'Espandi la memoria della tua Series X|S senza sacrificare le prestazioni.', 159.00, 'Accessorio', 'XBOX', 'xbox_seagate_1tb.png', 12, 1, 145.00),
(71, 'DualSense Wireless Controller', 'Controller wireless per PS5 con feedback aptico e grilletti adattivi.', 74.99, 'Accessorio', 'PS5', 'dualsense_white.png', 20, 1, 69.90),
(72, 'Cuffie Wireless Pulse 3D', 'Cuffie con microfono ottimizzate per l\'audio 3D delle console PS5.', 99.99, 'AccessoriO', 'PS5', 'pulse_3d.png', 15, 0, 99.99),
(73, 'PlayStation VR2', 'Visore di realtà virtuale di nuova generazione per un\'esperienza immersiva.', 599.99, 'Accessorio', 'PS5', 'psvr2.png', 5, 1, 549.00),
(74, 'Coppia Joy-Con Neon', 'Set da due controller Joy-Con (Sinistro e Destro) per Nintendo Switch.', 79.99, 'Accessorio', 'SWITCH', 'joycon_neon.png', 18, 0, 79.99),
(75, 'Nintendo Switch Pro Controller', 'Controller con impugnatura classica, perfetto per sessioni di gioco prolungate.', 69.99, 'Accessorio', 'SWITCH', 'switch_pro_controller.png', 20, 1, 64.90),
(76, 'Custodia e pellicola protettiva', 'Set ufficiale per proteggere la tua console Nintendo Switch in viaggio.', 19.99, 'Accessorio', 'SWITCH', 'switch_case.png', 50, 0, 19.99);

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `indirizzo` varchar(255) DEFAULT NULL,
  `citta` varchar(100) DEFAULT NULL,
  `cap` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `carrello`
--
ALTER TABLE `carrello`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `id_prodotto` (`id_prodotto`);

--
-- Indici per le tabelle `ordini`
--
ALTER TABLE `ordini`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_utente` (`id_utente`),
  ADD KEY `id_prodotto` (`id_prodotto`);

--
-- Indici per le tabelle `prodotti`
--
ALTER TABLE `prodotti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `carrello`
--
ALTER TABLE `carrello`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT per la tabella `ordini`
--
ALTER TABLE `ordini`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `carrello`
--
ALTER TABLE `carrello`
  ADD CONSTRAINT `carrello_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carrello_ibfk_2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`) ON DELETE CASCADE;

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `ordini_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordini_ibfk_2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
