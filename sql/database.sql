CREATE DATABASE IF NOT EXISTS santigames;
USE santigames;

CREATE TABLE IF NOT EXISTS utenti (
id int AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(50) NOT NULL,
cognome VARCHAR(50) NOT NULL,
username VARCHAR(50) NOT NULL UNIQUE,
email VARCHAR(100) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS prodotti (
id int AUTO_INCREMENT PRIMARY KEY,
nome VARCHAR(100) NOT NULL, 
descrizione TEXT,
prezzo DECIMAL(10, 2) NOT NULL,
tipologia VARCHAR(50) NOT NULL,
piattaforma VARCHAR(50),
immagine VARCHAR(255) DEFAULT 'default.png',
stock int DEFAULT 0,
in_sconto INT DEFAULT 0,
prezzo_scontato DECIMAL(10, 2) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS carrello (
id int AUTO_INCREMENT PRIMARY KEY,
id_utente int NOT NULL,
id_prodotto int NOT NULL,
quantita int DEFAULT 1,
FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE,
FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ordini (
id int AUTO_INCREMENT PRIMARY KEY,
codice_ordine VARCHAR(20) NOT NULL,
id_utente int NOT NULL,
id_prodotto int NOT NULL,
quantita int NOT NULL,
prezzo_acquisto DECIMAL (10, 2) NOT NULL,
data_ordine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
stato ENUM('in lavorazione', 'spedito', 'consegnato') DEFAULT 'in lavorazione',
FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE,
FOREIGN KEY (id_prodotto) REFERENCES prodotti(id) ON DELETE CASCADE
);

