document.addEventListener("DOMContentLoaded", function(){
  fetch('data/archive.json')
    .then(response => response.json())
    .then(data => {
      document.title = data.pageTitle;
      document.getElementById('archiveTitle').textContent = data.pageTitle;
      const productList = document.getElementById('productList');
      data.products.forEach(product => {
        const col = document.createElement('div');
        col.className = "col-md-4 d-flex";
        col.innerHTML = `
          <div class="card h-100 w-100">
            <img src="${product.image}" class="card-img-top img-product" alt="image-${product.title}">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${product.title}</h5>
              <p class="card-text">${product.price}</p>
              <a href="product.html?id=${product.id}" class="btn btn-primary">Visualizza Prodotto</a>
            </div>
          </div>
        `;
        productList.appendChild(col);
      });
    })
    .catch(error => console.error('Errore nel caricamento del JSON:', error));
});
