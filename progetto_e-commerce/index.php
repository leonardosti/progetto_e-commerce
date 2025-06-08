<?php
$db = include "base/database/database_connection.php";
include "base/database/db_functions.php";
include 'base/header.php';
?>
<!-- Hero Section  -->
<section id="heroSection" class="text-center text-white position-relative overflow-hidden" style="height: 100vh;">
    <div class="position-absolute w-100 h-100"
         style="background-image: url('style/images/wallpaper.jpg');
            background-size: cover;
            background-position: center;
            transform: scale(1.1);
            filter: brightness(0.7);
            z-index: -1;">
    </div>
    <div class="container d-flex align-items-center justify-content-center h-100">
        <div class="text-white">
            <h1 id="heroTitle" class="display-4 fw-bolder">LuxShades</h1>
            <p id="heroSubtitle" class="lead fw-bold">Nuova Collezione 2025</p>
            <a id="heroButton" class="btn btn-light fw-bold" href="archive.php">Esplora Ora</a>
        </div>
    </div>
</section>

<!-- Content Section -->
<section id="contentSection">
    <div class="row g-0">
        <div class="col-12 col-md-6 image-container">
            <img src="style/images/img-donna.png" alt="img-donna" class="img-fluid">
        </div>
        <div class="col-12 col-md-6 image-container">
            <img src="style/images/img-uomo.png" alt="img-uomo" class="img-fluid">
        </div>
    </div>
</section>

<!-- Highlights Section -->
<section id="highlightsSection" class="py-5">
    <div class="container text-center">
        <h2 id="highlightsTitle" class="mb-4">I Nostri Prodotti in Evidenza</h2>
        <p id="highlightsDescription" class="mb-5">Esplora i nostri capi più venduti della stagione</p>
        <div class="row" id="highlightsProducts">
            <?php stampaHighlights($db); ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="aboutSection" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start">
                <h2 id="aboutTitle" class="mb-4 fw-bold text-center">Chi Siamo</h2>
                <p id="aboutDescription" class="p-3">Siamo un brand di moda innovativo, dedicato alla creazione di capi di alta qualità che combinano stile, comfort e sostenibilità. La nostra missione è offrire prodotti esclusivi che rispecchiano le ultime tendenze della moda, mantenendo sempre un occhio di riguardo per l'ambiente e l'etica produttiva. Con oltre 15 anni di esperienza nel settore, continuiamo a crescere e ad espandere la nostra presenza in tutto il mondo.</p>
            </div>
            <div class="col-12 col-md-6">
                <img src="style/images/img-about.jpg" alt="img-about" class="img-fluid w-100">
            </div>
        </div>
    </div>
</section>
<?php
include 'base/footer.php';
?>