document.addEventListener("DOMContentLoaded", function () {
    // Effettua la richiesta per ottenere i prodotti in evidenza dal database
    fetch('indedx.php')
        .then(response => response.json())
        .then(data => {
            // Controlla se c'è un eventuale errore
            if (data.error) {
                console.error("Errore nel caricamento dei prodotti:", data.error);
                return;
            }

            // Seleziona il contenitore dove inserire i prodotti
            const container = document.getElementById('highlightsProducts');
            // Pulisce eventuali contenuti preesistenti
            container.innerHTML = '';

            // Itera sull'array dei prodotti e crea il markup per ciascuno
            data.forEach(product => {
                const productHTML = `
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="style/images/${product.immagine}" class="card-img-top" alt="${product.nome}">
                            <div class="card-body">
                                <h5 class="card-title">${product.nome}</h5>
                                <p class="card-text">€${product.prezzo}</p>
                                <a href="#" class="btn btn-outline-dark">Acquista</a>
                            </div>
                        </div>
                    </div>
                `;
                // Aggiunge il markup creato al contenitore
                container.innerHTML += productHTML;
            });
        })
        .catch(error => console.error("Errore nel caricamento JSON:", error));
});
