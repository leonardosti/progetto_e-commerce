// Funzione per ottenere il parametro dalla query string
function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}

document.addEventListener("DOMContentLoaded", function () {
  const productId = getQueryParam("id");

  if (!productId) {
    console.error("Nessun ID prodotto nella URL.");
    alert("Errore: prodotto non trovato!");
    return;
  }

  fetch("data/products.json")
    .then(response => response.json())
    .then(products => {
      const product = products.find(p => p.id === productId);
      if (!product) {
        console.error("Prodotto non trovato.");
        alert("Prodotto non esistente!");
        return;
      }

      // Popola i dati nella pagina
      document.title = product.title;
      document.getElementById("productTitle").textContent = product.title;
      document.getElementById("productDescription").textContent = product.description;
      document.getElementById("productPrice").textContent = product.price;
      document.getElementById("productImage").src = product.image;
      document.getElementById("productImage").alt = "Immagine di " + product.title;

      // Aggiunta Varianti Taglia
      const sizeSelect = document.getElementById("sizeSelect");
      sizeSelect.innerHTML = ""; 
      product.variants.sizes.forEach(size => {
        const option = document.createElement("option");
        option.value = size;
        option.textContent = size;
        sizeSelect.appendChild(option);
      });

      // Aggiunta Varianti Colore
      const colorSelect = document.getElementById("colorSelect");
      colorSelect.innerHTML = "";
      product.variants.colors.forEach(color => {
        const option = document.createElement("option");
        option.value = color;
        option.textContent = color;
        colorSelect.appendChild(option);
      });

      // Tabella Tecnica
      const tableHeader = document.getElementById("techTableHeader");
      tableHeader.innerHTML = ""; 
      product.technicalTable.headers.forEach(header => {
        const th = document.createElement("th");
        th.textContent = header;
        tableHeader.appendChild(th);
      });

      const tableBody = document.getElementById("techTableBody");
      tableBody.innerHTML = "";
      product.technicalTable.features.forEach(feature => {
        const tr = document.createElement("tr");
        tr.innerHTML = `<td>${feature.name}</td><td>${feature.value}</td>`;
        tableBody.appendChild(tr);
      });

      // Aggiunta al carrello (simulato)
      document.getElementById("addToCartBtn").addEventListener("click", function () {
        alert(`Aggiunto al carrello: ${product.title}`);
      });

    })
    .catch(error => {
      console.error("Errore nel caricamento dei dettagli del prodotto:", error);
      alert("Errore nel caricamento del prodotto!");
    });
});
