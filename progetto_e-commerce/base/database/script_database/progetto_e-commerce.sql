create database LuxeShades;

create table LuxeShades.prodotti(
    id INT PRIMARY key auto_increment,
    nome VARCHAR(255) NOT NULL,
    marca VARCHAR(100) NOT NULL,
    modello VARCHAR(100) NOT NULL,
    prezzo DECIMAL(10,2) NOT NULL,
    colore VARCHAR(50),
    materiale VARCHAR(100),
    quantita_disponibile INT DEFAULT 0,
    immagine VARCHAR(255)
);

create table LuxeShades.utenti(
    id INT PRIMARY key auto_increment,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

create table LuxeShades.recensioni(
    id INT PRIMARY key auto_increment,
    prodotto_id INT NOT NULL,
    utente_id INT NOT NULL,
    voto TINYINT CHECK (voto BETWEEN 1 AND 5),
    commento TEXT,
    data_recensione DATETIME,
    FOREIGN KEY (prodotto_id) REFERENCES LuxeShades.prodotti(id) ON DELETE CASCADE,
    FOREIGN KEY (utente_id) REFERENCES LuxeShades.utenti(id) ON DELETE CASCADE
);

