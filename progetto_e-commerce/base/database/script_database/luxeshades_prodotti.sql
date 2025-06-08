-- Inserimento di prodotti
INSERT INTO LuxeShades.prodotti (nome, marca, modello, prezzo, colore, materiale, quantita_disponibile, immagine) VALUES
('Aviator Classic', 'Ray-Ban', 'RB3025', 149.99, 'Oro', 'Metallo', 50, 'aviator_classic.jpg'),
('Aviator Large Metal', 'Ray-Ban', 'RB3026', 159.99, 'Oro', 'Metallo', 40, 'aviator_large.jpg'),
('Wayfarer Original', 'Ray-Ban', 'RB2140', 129.99, 'Nero', 'Acetato', 30, 'wayfarer_original.jpg'),
('Wayfarer Classic', 'Ray-Ban', 'RB2132', 119.99, 'Nero', 'Acetato', 35, 'wayfarer_classic.jpg'),
('Clubmaster', 'Ray-Ban', 'RB3016', 139.99, 'Tartarugato', 'Metallo/Acetato', 20, 'clubmaster.jpg'),
('Clubmaster Oversized', 'Ray-Ban', 'RB4175', 149.99, 'Tartarugato', 'Metallo/Acetato', 25, 'clubmaster_oversized.jpg'),
('Holbrook', 'Oakley', 'OO9102', 119.99, 'Nero Opaco', 'Plastica', 40, 'oakley_holbrook.jpg'),
('Holbrook XL', 'Oakley', 'OO9417', 129.99, 'Nero Opaco', 'Plastica', 30, 'oakley_holbrook_xl.jpg'),
('Frogskins', 'Oakley', 'OO9013', 109.99, 'Blu', 'Plastica', 25, 'oakley_frogskins.jpg'),
('Frogskins Lite', 'Oakley', 'OO9374', 114.99, 'Blu', 'Plastica', 20, 'oakley_frogskins_lite.jpg'),
('Persol 714', 'Persol', '714SM', 249.99, 'Marrone', 'Acetato', 15, 'persol_714.jpg'),
('Persol 649', 'Persol', '649', 229.99, 'Marrone', 'Acetato', 18, 'persol_649.jpg');

-- Inserimento di utenti
INSERT INTO LuxeShades.utenti (nome, email, password) VALUES
('Marco Rossi', 'marco.rossi@example.com', 'password123'),
('Laura Bianchi', 'laura.bianchi@example.com', 'securePass!'),
('Giovanni Verdi', 'giovanni.verdi@example.com', 'giovanniPass'),
('Sara Neri', 'sara.neri@example.com', 'saraPassword'),
('Andrea Blu', 'andrea.blu@example.com', 'bluPass123');

-- Inserimento di recensioni
INSERT INTO LuxeShades.recensioni (prodotto_id, utente_id, voto, commento, data_recensione) VALUES
(1, 1, 5, 'Occhiali fantastici, stile intramontabile!', '2025-03-15 14:30:00'),
(2, 2, 4, 'Molto belli, ma un po’ pesanti.', '2025-03-16 10:20:00'),
(3, 3, 5, 'Perfetti per ogni occasione, li adoro.', '2025-03-17 18:45:00'),
(4, 4, 3, 'Buona qualità ma la montatura è un po’ larga per me.', '2025-03-18 12:10:00'),
(5, 5, 5, 'Eleganti e raffinati, valgono il prezzo.', '2025-03-19 16:55:00');