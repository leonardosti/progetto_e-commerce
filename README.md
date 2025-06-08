# LuxShades

Un’applicazione e-commerce per la vendita di occhiali da sole, realizzata con **PHP**, **HTML**, **CSS**, **Bootstrap**, **JavaScript** e **MySQL**. Tutti i contenuti testuali (titoli, descrizioni, prezzi, ecc.) sono gestiti in file JSON esterni: l’HTML contiene solo la struttura, mentre un modulo JavaScript ne inietta dinamicamente i valori.

---

## 📂 Caratteristiche principali

- **Single Product**  
  - Visualizza immagine, nome, marca, prezzo e descrizione da JSON  
  - Versione _single2_ con tabella delle specifiche tecniche e selettore varianti (colore, taglia)

- **Product Archive**  
  - Griglia di tutti i prodotti, caricata via JSON  
  - Filtri e ordinamenti client-side con JavaScript

- **Carrello**  
  - Aggiunta, modifica quantità e rimozione articoli  
  - Calcolo automatico del totale  
  - Gestione di **bundle promozionali**  
  - Inserimento di **coupon code** per sconti

---

## 🔧 Backend & Database

- **Utenti**  
  - Registrazione, login, autenticazione e gestione sessione via PHP/PDO

- **Prodotti**  
  - Attributi: nome, marca, descrizione, prezzo, immagine, quantità a magazzino, pezzi venduti

- **Varianti**  
  - Tabella relazionata per gestire colore e taglia

- **Carrello**  
  - Tabella associativa `utente ↔ prodotto ↔ variante ↔ quantità`

- **Bundle** & **Coupon** (opzionali)  
  - Tabelle per offerte multiple e codici sconto

- **Homepage** (opzionale)  
  - Sezione dinamica per banner, promozioni e prodotti in evidenza, alimentata da JSON

Il database MySQL include le tabelle:  
```sql
utenti, prodotti, varianti, carrello, ordini, bundle, coupon
