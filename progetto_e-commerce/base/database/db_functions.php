<?php

function stampaHighlights($db){
    $query = "select nome, modello, prezzo, immagine from luxeshades.prodotti where id <= 3;";

    try{
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        foreach ($result as $item) {
            $nomeCompleto = htmlspecialchars($item->nome . " " . $item->modello);
            $prezzo = number_format($item->prezzo, 2, ',', '.');
            $immagine = htmlspecialchars($item->immagine);

            echo '<div class="col-12 col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="style/images/' . $immagine . '" class="card-img-top" alt="' . $nomeCompleto . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $nomeCompleto . '</h5>
                            <p class="card-text">€' . $prezzo . '</p>
                            <a href="#" class="btn btn-outline-dark">Acquista</a>
                        </div>
                    </div>
                  </div>';
        }
    }catch(PDOException $e){
        // error message
        echo "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
    }
}

function stampaProdotti($db) {
    // Query iniziale
    $sql = "SELECT * FROM LuxeShades.prodotti WHERE 1=1";
    $params = [];

    // Filtro per la ricerca (usando REGEXP)
    if (!empty($_GET['cerca'])) {
        // Pulisci e dividi l'input in parole
        $cerca_input = trim($_GET['cerca']);
        $words = explode(" ", $cerca_input);
        // Unisci le parole in una regex, usando preg_quote per sicurezza
        $regex = implode("|", array_map('preg_quote', $words));
        $sql .= " AND (nome REGEXP ? OR modello REGEXP ?)";
        $params[] = $regex;
        $params[] = $regex;
    }

    // Filtro per marca
    if (!empty($_GET['marca'])) {
        $sql .= " AND marca = ?";
        $params[] = $_GET['marca'];
    }

    // Ordinamento
    if (!empty($_GET['ordine'])) {
        switch ($_GET['ordine']) {
            case 'recenti':
                // Se esiste un campo data_inserimento, potresti usare "ORDER BY data_inserimento DESC"
                $sql .= " ORDER BY id DESC";
                break;
            case 'prezzo_asc':
                $sql .= " ORDER BY prezzo ASC";
                break;
            case 'prezzo_desc':
                $sql .= " ORDER BY prezzo DESC";
                break;
        }
    }

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (empty($result)) {
            echo '<div class="col-12 text-center"><p class="text-muted">Nessun prodotto trovato.</p>
</div>';
        }

        foreach ($result as $item) {
            $nomeCompleto = htmlspecialchars($item->nome . " " . $item->modello);
            $prezzo = number_format($item->prezzo, 2, ',', '.');
            $immagine = htmlspecialchars($item->immagine);

            echo '<div class="col-12 col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="style/images/' . $immagine . '" class="card-img-top" alt="' . $nomeCompleto . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $nomeCompleto . '</h5>
                            <p class="card-text">€' . $prezzo . '</p>
                            <a href="product.php?id=' . $item->id . '" class="btn btn-outline-dark">Acquista</a>
                        </div>
                    </div>
                  </div>';
        }
    } catch (PDOException $e) {
        echo "<script>alert('Errore DB: " . addslashes($e->getMessage()) . "');</script>";
    }
}

function prodottiCorrelati($db, $id, $marca) {
    try {
        $sql = "SELECT * FROM LuxeShades.prodotti WHERE marca = ? AND id != ? LIMIT 4";
        $stmt = $db->prepare($sql);
        $stmt->execute([$marca, $id]);
        $prodotti = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($prodotti as $prodotto) {
            echo '<div class="col-md-3 col-6 mb-4">
            <div class="card h-100 border-0 shadow-sm product-card">
                <div class="position-relative">';

            if (!empty($prodotto['immagine'])) {
                echo '<img src="' . htmlspecialchars($prodotto['immagine']) . '" class="card-img-top" alt="' . htmlspecialchars($prodotto['nome']) . '">';
            }

            if ($prodotto['quantita_disponibile'] <= 0) {
                echo '<span class="position-absolute top-0 end-0 badge bg-danger m-2">Esaurito</span>';
            }

            echo '</div>
                <div class="card-body text-center">
                    <h5 class="card-title">' . htmlspecialchars($prodotto['nome']) . '</h5>
                    <p class="card-text fw-bold">' . number_format($prodotto['prezzo'], 2, ',', '.') . ' €</p>
                    <a href="product.php?id=' . $prodotto['id'] . '" class="btn btn-outline-dark">Visualizza</a>
                </div>
            </div>
        </div>';
        }
    }catch (PDOException $e) {
        echo "<script>alert('Errore DB: " . addslashes($e->getMessage()) . "');</script>";
    }
}




