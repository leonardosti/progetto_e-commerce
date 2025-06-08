<?php
$db = include "base/database/database_connection.php";
include "base/database/db_functions.php";
include 'base/header.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    exit("ID non valido.");
}

// Recuperare i dati del prodotto
$sql = "SELECT * FROM LuxeShades.prodotti WHERE id = ?";

$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$prodotto = $stmt->fetch(PDO::FETCH_ASSOC);
?>

    <section class="banner-section py-5 text-center bg-light">
        <div class="container">
                <h1 class="display-4 fw-bolder"><?php echo htmlspecialchars($prodotto['marca']); ?></h1>
                <p class="lead fw-bold"><?php echo htmlspecialchars($prodotto['modello']); ?></p>
        </div>
    </section>

    <!-- Product Detail Section -->
    <section id="productDetailSection" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 mb-4">
                    <div class="position-relative">
                        <?php if (!empty($prodotto['immagine'])): ?>
                            <img src="<?php echo htmlspecialchars($prodotto['immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="img-fluid w-100 rounded shadow">
                        <?php endif; ?>

                        <?php if ($prodotto['quantita_disponibile'] <= 5 && $prodotto['quantita_disponibile'] > 0): ?>
                            <span class="position-absolute top-0 end-0 badge bg-warning m-2">Ultimi pezzi</span>
                        <?php elseif ($prodotto['quantita_disponibile'] <= 0): ?>
                            <span class="position-absolute top-0 end-0 badge bg-danger m-2">Esaurito</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Dettagli prodotto -->
                <div class="col-12 col-md-6">
                    <h2 class="fw-bold mb-3"><?php echo htmlspecialchars($prodotto['nome']); ?></h2>

                    <div class="mb-4">
                        <span class="h3 fw-bold"><?php echo number_format($prodotto['prezzo'], 2, ',', '.'); ?> €</span>
                    </div>
                    <hr class="my-4">
                    <div class="mb-4">
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Marca</div>
                            <div class="col-8"><?php echo htmlspecialchars($prodotto['marca']); ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Modello</div>
                            <div class="col-8"><?php echo htmlspecialchars($prodotto['modello']); ?></div>
                        </div>
                        <?php if (!empty($prodotto['colore'])): ?>
                            <div class="row mb-3">
                                <div class="col-4 fw-bold">Colore</div>
                                <div class="col-8">
                                    <?php echo htmlspecialchars($prodotto['colore']); ?>
                                    <span class="color-circle d-inline-block ms-2" style="width: 20px; height: 20px; border-radius: 50%; background-color: <?php echo htmlspecialchars($prodotto['colore']); ?>; vertical-align: middle;"></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($prodotto['materiale'])): ?>
                            <div class="row mb-3">
                                <div class="col-4 fw-bold">Materiale</div>
                                <div class="col-8"><?php echo htmlspecialchars($prodotto['materiale']); ?></div>
                            </div>
                        <?php endif; ?>
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Disponibilità</div>
                            <div class="col-8">
                                <?php if ($prodotto['quantita_disponibile'] > 5): ?>
                                    <span class="text-success">Disponibile</span>
                                <?php elseif ($prodotto['quantita_disponibile'] > 0): ?>
                                    <span class="text-warning">Solo <?php echo $prodotto['quantita_disponibile']; ?> pezzi disponibili</span>
                                <?php else: ?>
                                    <span class="text-danger">Non disponibile</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <?php if ($prodotto['quantita_disponibile'] > 0): ?>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $prodotto['id']; ?>">

                            <div class="mb-4">
                                <label for="quantity" class="form-label fw-bold">Quantità</label>
                                <div class="input-group" style="max-width: 150px;">
                                    <button class="btn btn-outline-dark" type="button" onclick="decrementQuantity()">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center" id="quantity" name="quantity" value="1" min="1" max="<?php echo $prodotto['quantita_disponibile']; ?>">
                                    <button class="btn btn-outline-dark" type="button" onclick="incrementQuantity()">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" name="add_to_cart" class="btn btn-dark btn-lg flex-grow-1">
                                    <i class="bi bi-bag-plus me-2"></i>Aggiungi al carrello
                                </button>
                                <button type="button" class="btn btn-outline-dark btn-lg">
                                    <i class="bi bi-heart"></i>
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="d-grid">
                            <button class="btn btn-secondary btn-lg" disabled>
                                <i class="bi bi-x-circle me-2"></i>Prodotto non disponibile
                            </button>
                        </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="d-flex align-items-center">
                        <span class="me-3">Condividi:</span>
                        <a href="#" class="social-icon me-2" style="color: #151515;">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="social-icon me-2" style="color: #151515;">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="social-icon me-2" style="color: #151515;">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="social-icon" style="color: #151515;">
                            <i class="bi bi-pinterest"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Details Tab Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Descrizione</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="false">Dettagli tecnici</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Spedizione e resi</button>
                </li>
            </ul>

            <div class="tab-content p-4 bg-white rounded shadow-sm" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <h4 class="mb-3">Descrizione del prodotto</h4>
                    <p class="mb-3">
                        Gli occhiali <?php echo htmlspecialchars($prodotto['nome']); ?> di <?php echo htmlspecialchars($prodotto['marca']); ?> rappresentano il perfetto equilibrio tra design innovativo e funzionalità.
                        Realizzati con materiali di alta qualità, questi occhiali offrono comfort e stile per ogni occasione.
                    </p>
                    <p>
                        Il modello <?php echo htmlspecialchars($prodotto['modello']); ?> si distingue per le sue linee eleganti e la cura dei dettagli, confermando la reputazione di <?php echo htmlspecialchars($prodotto['marca']); ?>
                        come marchio leader nel settore degli occhiali di lusso.
                    </p>
                </div>

                <div class="tab-pane fade" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <h4 class="mb-3">Specifiche tecniche</h4>
                    <table class="table table-striped">
                        <tbody>
                        <tr>
                            <th scope="row" style="width: 30%;">Marca</th>
                            <td><?php echo htmlspecialchars($prodotto['marca']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row">Modello</th>
                            <td><?php echo htmlspecialchars($prodotto['modello']); ?></td>
                        </tr>
                        <?php if (!empty($prodotto['colore'])): ?>
                            <tr>
                                <th scope="row">Colore</th>
                                <td><?php echo htmlspecialchars($prodotto['colore']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($prodotto['materiale'])): ?>
                            <tr>
                                <th scope="row">Materiale</th>
                                <td><?php echo htmlspecialchars($prodotto['materiale']); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th scope="row">Paese di produzione</th>
                            <td>Italia</td>
                        </tr>
                        <tr>
                            <th scope="row">Garanzia</th>
                            <td>2 anni</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                    <h4 class="mb-3">Informazioni su spedizione e resi</h4>
                    <p class="mb-3">
                        <strong>Spedizione:</strong><br>
                        Tutti gli ordini vengono elaborati entro 24-48 ore e spediti tramite corriere espresso. La consegna avviene generalmente entro 2-4 giorni lavorativi.
                        Per ordini superiori a 100€, la spedizione è gratuita in tutta Italia.
                    </p>
                    <p>
                        <strong>Politica di reso:</strong><br>
                        Hai 30 giorni dalla data di consegna per restituire il prodotto. Il prodotto deve essere restituito nelle condizioni originali, con tutte le etichette
                        e il packaging intatto. Per maggiori informazioni, consulta la nostra pagina dedicata ai resi e rimborsi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products Section -->
    <section id="relatedProductsSection" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4 fw-bold">Prodotti correlati</h2>
            <p class="text-center mb-5">Altri prodotti della collezione <?php echo htmlspecialchars($prodotto['marca']); ?></p>

            <div class="row">
                <?= prodottiCorrelati($db, $id, $prodotto['marca'])?>
            </div>
        </div>
    </section>

    <!-- Script per incrementare e decrementare le quantita' del prodotto -->
    <script>
        function incrementQuantity() {
            var input = document.getElementById('quantity');
            var max = parseInt(input.getAttribute('max'));
            var value = parseInt(input.value);
            if (value < max) {
                input.value = value + 1;
            }
        }

        function decrementQuantity() {
            var input = document.getElementById('quantity');
            var value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        }
    </script>

<?php
include 'base/footer.php';
?>