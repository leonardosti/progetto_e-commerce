<?php
$db = include "base/database/database_connection.php";
include "base/database/db_functions.php";
include 'base/header.php';

# marche occhiali
/*
 * Ray-Ban
 * Oakley
 * Persol
 * */

?>

<!-- Banner Section -->
<section class="banner-section py-5 text-center bg-light">
    <div class="container">
        <h1>I Nostri Prodotti</h1>
        <p class="lead">Esplora la nostra collezione di occhiali da sole di lusso</p>
    </div>
</section>

<!-- Filters Section -->
<section class="filters-section py-3 bg-white">
    <div class="container">
        <form action="" method="get" class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <input type="text" name="cerca" class="form-control" placeholder="Cerca per nome o modello..." value="<?= isset($_GET['cerca']) ? htmlspecialchars($_GET['cerca']) : '' ?>">
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <select name="ordine" class="form-select">
                    <option value="">Ordina per</option>
                    <option value="recenti" <?= (isset($_GET['ordine']) && $_GET['ordine'] === 'recenti') ? 'selected' : '' ?>>Più recenti</option>
                    <option value="prezzo_asc" <?= (isset($_GET['ordine']) && $_GET['ordine'] === 'prezzo_asc') ? 'selected' : '' ?>>Prezzo crescente</option>
                    <option value="prezzo_desc" <?= (isset($_GET['ordine']) && $_GET['ordine'] === 'prezzo_desc') ? 'selected' : '' ?>>Prezzo decrescente</option>
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <select name="marca" class="form-select">
                    <option value="">Filtra per marca</option>
                    <option value="ray-ban" <?= (isset($_GET['marca']) && $_GET['marca'] === 'ray-ban') ? 'selected' : '' ?>>Ray-Ban</option>
                    <option value="persol" <?= (isset($_GET['marca']) && $_GET['marca'] === 'persol') ? 'selected' : '' ?>>Persol</option>
                    <option value="oakley" <?= (isset($_GET['marca']) && $_GET['marca'] === 'oakley') ? 'selected' : '' ?>>Oakley</option>
                </select>
            </div>
            <div class="col-md-1 mb-3 mb-md-0">
                <button type="submit" class="btn btn-dark w-100">Filtra</button>
            </div>
            <div class="col-md-1">
                <a href="archive.php" class="btn btn-light w-100">Reset</a>
            </div>
        </form>
    </div>
</section>

<!-- Products Section -->
<section class="products-section py-5">
    <div class="container">
        <div class="row">
            <?php stampaProdotti($db); ?>
        </div>
    </div>
</section>

<?php include 'base/footer.php'; ?>

