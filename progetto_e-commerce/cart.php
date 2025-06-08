<?php
session_start();
$db = include "base/database/database_connection.php";
include "base/database/db_functions.php";
include 'base/header.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Gestione richieste POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aggiunta di un prodotto al carrello
    if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        // Verifica disponibilità prodotto
        $stmt = $db->prepare("SELECT quantita_disponibile FROM LuxeShades.prodotti WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['quantita_disponibile'] >= $quantity) {
            if (isset($_SESSION['cart'][$product_id])) {
                // Verifica che la quantità totale non superi la disponibilità
                $new_qty = $_SESSION['cart'][$product_id] + $quantity;
                if ($new_qty <= $product['quantita_disponibile']) {
                    $_SESSION['cart'][$product_id] = $new_qty;
                    $_SESSION['success_message'] = "Quantità aggiornata nel carrello!";
                } else {
                    $_SESSION['error_message'] = "Non puoi aggiungere altri pezzi di questo prodotto. Disponibilità massima raggiunta.";
                }
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
                $_SESSION['success_message'] = "Prodotto aggiunto al carrello!";
            }
        } else {
            $_SESSION['error_message'] = "Prodotto non disponibile nella quantità richiesta.";
        }
    }

    // Aggiunta di un bundle al carrello
    if (isset($_POST['add_bundle'])) {
        $bundle_id = (int)$_POST['bundle_id'];

        // In un'implementazione reale, qui dovresti recuperare i prodotti del bundle dal database
        if ($bundle_id == 1) { // Bundle Protezione Completa
            $_SESSION['bundles'][$bundle_id] = [
                'name' => 'Bundle Protezione Completa',
                'price' => 29.99,
                'items' => ['Custodia rigida premium', 'Panno in microfibra', 'Spray detergente (30ml)']
            ];
            $_SESSION['success_message'] = "Bundle Protezione Completa aggiunto al carrello!";
        }
        elseif ($bundle_id == 2) { // Bundle Manutenzione Annuale
            $_SESSION['bundles'][$bundle_id] = [
                'name' => 'Bundle Manutenzione Annuale',
                'price' => 49.99,
                'items' => ['Servizio di pulizia professionale', 'Controllo e regolazione tecnica', 'Sostituzione naselli e viti']
            ];
            $_SESSION['success_message'] = "Bundle Manutenzione Annuale aggiunto al carrello!";
        }
    }

    // Aggiornamento quantità prodotto
    if (isset($_POST['update_quantity'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity > 0) {
            // Verifica disponibilità
            $stmt = $db->prepare("SELECT quantita_disponibile FROM LuxeShades.prodotti WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && $product['quantita_disponibile'] >= $quantity) {
                $_SESSION['cart'][$product_id] = $quantity;
                $_SESSION['success_message'] = "Quantità aggiornata!";
            } else {
                $_SESSION['error_message'] = "Quantità richiesta non disponibile.";
            }
        }
    }

    // Rimozione di un prodotto
    if (isset($_POST['remove_item'])) {
        $product_id = (int)$_POST['product_id'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success_message'] = "Prodotto rimosso dal carrello!";
        }
    }

    // Rimozione di un bundle
    if (isset($_POST['remove_bundle'])) {
        $bundle_id = (int)$_POST['bundle_id'];
        if (isset($_SESSION['bundles'][$bundle_id])) {
            unset($_SESSION['bundles'][$bundle_id]);
            $_SESSION['success_message'] = "Bundle rimosso dal carrello!";
        }
    }

    // Svuotamento completo del carrello
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['bundles'] = [];
        $_SESSION['coupon'] = null;
        $_SESSION['success_message'] = "Carrello svuotato! Puoi procedere con gli acquisti";
    }

    // Applicazione coupon
    if (isset($_POST['apply_coupon'])) {
        $coupon_code = trim($_POST['coupon_code']);

        // In un caso reale, verificheremmo il coupon nel database
        // Per questo esempio, creiamo alcuni coupon di esempio
        $valid_coupons = [
            'WELCOME10' => ['tipo' => 'percentuale', 'valore' => 10],
            'LUXE20' => ['tipo' => 'percentuale', 'valore' => 20],
            'FREESHIP' => ['tipo' => 'fisso', 'valore' => 5.99]
        ];

        if (isset($valid_coupons[$coupon_code])) {
            $_SESSION['coupon'] = [
                'codice' => $coupon_code,
                'tipo' => $valid_coupons[$coupon_code]['tipo'],
                'valore' => $valid_coupons[$coupon_code]['valore']
            ];
            $_SESSION['success_message'] = "Codice sconto applicato con successo!";
        } else {
            $_SESSION['error_message'] = "Codice sconto non valido.";
        }
    }

    // Rimozione coupon
    if (isset($_POST['remove_coupon'])) {
        unset($_SESSION['coupon']);
        $_SESSION['success_message'] = "Codice sconto rimosso!";
    }

    // Reindirizza per evitare il rienvio del form
    header('Location: cart.php');
    exit;
}

// Recupera i dati dei prodotti nel carrello
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';

    $stmt = $db->prepare("SELECT * FROM LuxeShades.prodotti WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $item_total = $product['prezzo'] * $quantity;
        $subtotal += $item_total;

        $cart_items[] = [
            'id' => $product['id'],
            'nome' => $product['nome'],
            'marca' => $product['marca'],
            'modello' => $product['modello'],
            'immagine' => $product['immagine'],
            'prezzo' => $product['prezzo'],
            'quantita' => $quantity,
            'item_total' => $item_total,
            'quantita_disponibile' => $product['quantita_disponibile']
        ];
    }
}

// Calcola il totale dei bundle
$bundle_total = 0;
if (!empty($_SESSION['bundles'])) {
    foreach ($_SESSION['bundles'] as $bundle) {
        $bundle_total += $bundle['price'];
    }
    $subtotal += $bundle_total;
}

// Calcola sconto se presente
$discount = 0;
$total = $subtotal;

if (isset($_SESSION['coupon']) && $subtotal > 0) {
    if ($_SESSION['coupon']['tipo'] === 'percentuale') {
        $discount = $subtotal * ($_SESSION['coupon']['valore'] / 100);
    } else {
        $discount = $_SESSION['coupon']['valore'];
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }
    }
    $total = $subtotal - $discount;
}

// Calcola spese di spedizione
$shipping = 0;
if ($total > 0 && $total < 100) {
    $shipping = 5.99;
}

// Calcola il totale finale
$grand_total = $total + $shipping;
?>

    <!-- Banner Section -->
    <section class="banner-section py-5 text-center bg-light">
        <div class="container">
            <h1 class="display-4 fw-bolder">Il tuo carrello</h1>
            <p class="lead fw-bold">Completa il tuo acquisto di occhiali di lusso</p>
        </div>
    </section>

    <!-- Carrello Section -->
    <section class="py-5">
        <div class="container">
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success_message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (empty($cart_items) && empty($_SESSION['bundles'])): ?>
                <!-- Carrello vuoto -->
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 mb-4 text-muted"></i>
                    <h2 class="mb-3">Il tuo carrello è vuoto</h2>
                    <p class="lead mb-4">Aggiungi qualche prodotto al tuo carrello per procedere all'acquisto.</p>
                    <a href="index.php" class="btn btn-dark btn-lg">Continua lo shopping</a>
                </div>
            <?php else: ?>
                <!-- Carrello con prodotti -->
                <div class="row">
                    <!-- Elenco prodotti -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">I tuoi prodotti</h5>
                            </div>
                            <div class="card-body">
                                <!-- Prodotti nel carrello -->
                                <?php if (!empty($cart_items)): ?>
                                    <div class="table-responsive">
                                        <table class="table align-middle">
                                            <thead>
                                            <tr>
                                                <th colspan="2">Prodotto</th>
                                                <th>Prezzo</th>
                                                <th>Quantità</th>
                                                <th>Totale</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($cart_items as $item): ?>
                                                <tr>
                                                    <td style="width: 80px;">
                                                        <?php if (!empty($item['immagine'])): ?>
                                                            <img src="<?= htmlspecialchars($item['immagine']) ?>" alt="<?= htmlspecialchars($item['nome']) ?>" class="img-fluid rounded" style="max-width: 70px;">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="product.php?id=<?= $item['id'] ?>" class="text-decoration-none text-dark">
                                                            <h6 class="mb-1"><?= htmlspecialchars($item['nome']) ?></h6>
                                                            <small class="text-muted"><?= htmlspecialchars($item['marca']) ?> | <?= htmlspecialchars($item['modello']) ?></small>
                                                        </a>
                                                    </td>
                                                    <td class="price" data-base-price="<?= $item['prezzo'] ?>">
                                                        <?= number_format($item['prezzo'], 2, ',', '.') ?> €
                                                    </td>
                                                    <td style="width: 150px;">
                                                        <form action="cart.php" method="post" class="d-flex">
                                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                            <div class="input-group input-group-sm">
                                                                <button class="btn btn-outline-dark" type="button" onclick="decrementCart(this)">
                                                                    <i class="bi bi-dash"></i>
                                                                </button>
                                                                <input type="number" class="form-control text-center" name="quantity" value="<?= $item['quantita'] ?>" min="1" max="<?= $item['quantita_disponibile'] ?>">
                                                                <button class="btn btn-outline-dark" type="button" onclick="incrementCart(this)">
                                                                    <i class="bi bi-plus"></i>
                                                                </button>
                                                            </div>
                                                            <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-dark ms-2">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td class="total-price">
                                                        <?= number_format($item['item_total'], 2, ',', '.') ?> €
                                                    </td>
                                                    <td>
                                                        <form action="cart.php" method="post">
                                                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                            <button type="submit" name="remove_item" class="btn btn-sm text-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>

                                <!-- Bundle nel carrello -->
                                <?php if (!empty($_SESSION['bundles'])): ?>
                                    <div class="mt-4">
                                        <h6 class="mb-3">Bundle selezionati</h6>
                                        <?php foreach ($_SESSION['bundles'] as $bundle_id => $bundle): ?>
                                            <div class="card mb-3 border-0 shadow-sm">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h6 class="mb-1"><?= htmlspecialchars($bundle['name']) ?></h6>
                                                            <small class="text-muted">
                                                                <?= implode(', ', $bundle['items']) ?>
                                                            </small>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <span class="fw-bold me-3"><?= number_format($bundle['price'], 2, ',', '.') ?> €</span>
                                                            <form action="cart.php" method="post">
                                                                <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">
                                                                <button type="submit" name="remove_bundle" class="btn btn-sm text-danger">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Azioni carrello -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="index.php" class="btn btn-outline-dark">
                                        <i class="bi bi-arrow-left me-2"></i>Continua lo shopping
                                    </a>
                                    <form action="cart.php" method="post">
                                        <button type="submit" name="clear_cart" class="btn btn-outline-danger">
                                            <i class="bi bi-x-circle me-2"></i>Svuota carrello
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Codice sconto</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!isset($_SESSION['coupon'])): ?>
                                    <form action="cart.php" method="post" class="d-flex">
                                        <input type="text" name="coupon_code" class="form-control" placeholder="Inserisci il tuo codice sconto" required>
                                        <button type="submit" name="apply_coupon" class="btn btn-dark ms-2">Applica</button>
                                    </form>
                                    <small class="text-muted mt-2">
                                        Prova i codici: WELCOME10 (10% sconto), LUXE20 (20% sconto), FREESHIP (spedizione gratuita)
                                    </small>
                                <?php else: ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-success me-2">Applicato</span>
                                            <strong><?= htmlspecialchars($_SESSION['coupon']['codice']) ?></strong>
                                            <?php if ($_SESSION['coupon']['tipo'] === 'percentuale'): ?>
                                                (<?= $_SESSION['coupon']['valore'] ?>% di sconto)
                                            <?php else: ?>
                                                (<?= number_format($_SESSION['coupon']['valore'], 2, ',', '.') ?> € di sconto)
                                            <?php endif; ?>
                                        </div>
                                        <form action="cart.php" method="post">
                                            <button type="submit" name="remove_coupon" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle me-1"></i>Rimuovi
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Bundle consigliati -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Bundle consigliati</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Bundle 1 -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">Bundle Protezione Completa</h6>
                                                <ul class="small mb-3">
                                                    <li>Custodia rigida premium</li>
                                                    <li>Panno in microfibra</li>
                                                    <li>Spray detergente (30ml)</li>
                                                </ul>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="text-decoration-line-through text-muted me-2">39,99 €</span>
                                                        <span class="fw-bold">29,99 €</span>
                                                    </div>
                                                    <?php if (!isset($_SESSION['bundles'][1])): ?>
                                                        <form action="cart.php" method="post">
                                                            <input type="hidden" name="bundle_id" value="1">
                                                            <button type="submit" name="add_bundle" class="btn btn-sm btn-outline-dark">
                                                                <i class="bi bi-plus-circle me-1"></i>Aggiungi
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Nel carrello</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bundle 2 -->
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">Bundle Manutenzione Annuale</h6>
                                                <ul class="small mb-3">
                                                    <li>Servizio di pulizia professionale</li>
                                                    <li>Controllo e regolazione tecnica</li>
                                                    <li>Sostituzione naselli e viti</li>
                                                </ul>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="text-decoration-line-through text-muted me-2">59,99 €</span>
                                                        <span class="fw-bold">49,99 €</span>
                                                    </div>
                                                    <?php if (!isset($_SESSION['bundles'][2])): ?>
                                                        <form action="cart.php" method="post">
                                                            <input type="hidden" name="bundle_id" value="2">
                                                            <button type="submit" name="add_bundle" class="btn btn-sm btn-outline-dark">
                                                                <i class="bi bi-plus-circle me-1"></i>Aggiungi
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Nel carrello</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Riepilogo ordine -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0">Riepilogo ordine</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotale prodotti</span>
                                    <span><?= number_format($subtotal - $bundle_total, 2, ',', '.') ?> €</span>
                                </div>

                                <?php if ($bundle_total > 0): ?>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Bundle</span>
                                        <span><?= number_format($bundle_total, 2, ',', '.') ?> €</span>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotale</span>
                                    <span><?= number_format($subtotal, 2, ',', '.') ?> €</span>
                                </div>

                                <?php if ($discount > 0): ?>
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span>Sconto</span>
                                        <span>-<?= number_format($discount, 2, ',', '.') ?> €</span>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Spedizione</span>
                                    <?php if ($shipping > 0): ?>
                                        <span><?= number_format($shipping, 2, ',', '.') ?> €</span>
                                    <?php else: ?>
                                        <span class="text-success">Gratuita</span>
                                    <?php endif; ?>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-4">
                                    <span class="fw-bold">Totale (IVA inclusa)</span>
                                    <span class="fw-bold h5 mb-0"><?= number_format($grand_total, 2, ',', '.') ?> €</span>
                                </div>

                                <div class="d-grid">
                                    <form action="cart.php" method="post">
                                        <button type="submit" name="clear_cart" class="btn btn-dark btn-lg">
                                            <i class="bi bi-credit-card me-2"></i>Procedi al checkout
                                        </button>
                                    </form>
                                </div>

                                <div class="mt-3 small text-center">
                                    <div class="mb-2">Metodi di pagamento accettati</div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <i class="bi bi-credit-card fs-4"></i>
                                        <i class="bi bi-paypal fs-4"></i>
                                        <i class="bi bi-bank fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informazioni spedizione -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h6 class="mb-3"><i class="bi bi-truck me-2"></i>Informazioni sulla spedizione</h6>
                                <p class="small mb-0">
                                    <?php if ($shipping > 0): ?>
                                        <strong>Spedizione standard:</strong> <?= number_format($shipping, 2, ',', '.') ?> €<br>
                                        <span class="text-muted">Consegna in 2-4 giorni lavorativi</span><br>
                                        <span class="text-success">Spedizione gratuita per ordini superiori a 100€</span>
                                    <?php else: ?>
                                        <span class="text-success">Spedizione gratuita</span><br>
                                        <span class="text-muted">Consegna in 2-4 giorni lavorativi</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Script per incrementare e decrementare le quantita' del prodotto -->
    <!-- Sostituisci la sezione script vuota con questo codice -->
    <script>
        function incrementCart(button) {
            const inputGroup = button.closest('.input-group');
            const input = inputGroup.querySelector('input[name="quantity"]');
            let currentValue = parseInt(input.value);
            const maxValue = parseInt(input.getAttribute('max'));

            if (currentValue < maxValue) {
                input.value = currentValue + 1;
                updateVisualFeedback(input); // Aggiorna il feedback visivo
            }
        }

        function decrementCart(button) {
            const inputGroup = button.closest('.input-group');
            const input = inputGroup.querySelector('input[name="quantity"]');
            let currentValue = parseInt(input.value);
            const minValue = parseInt(input.getAttribute('min'));

            if (currentValue > minValue) {
                input.value = currentValue - 1;
                updateVisualFeedback(input); // Aggiorna il feedback visivo
            }
        }

        function updateVisualFeedback(input) {
            // Animazione di aggiornamento
            input.style.transform = 'scale(1.1)';
            setTimeout(() => {
                input.style.transform = 'scale(1)';
            }, 150);

            // Aggiorna il totale della riga in tempo reale (opzionale)
            const row = input.closest('tr');
            if (row) {
                const price = parseFloat(row.querySelector('.price').dataset.basePrice);
                const totalCell = row.querySelector('.total-price');
                totalCell.textContent = (price * input.value).toFixed(2) + ' €';
            }
        }
    </script>

<?php include 'base/footer.php'; ?>