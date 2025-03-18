document.addEventListener("DOMContentLoaded", function() {
  // Configurazione iniziale
  const productId = new URLSearchParams(window.location.search).get('id');
  
  // Funzione per popolare la navbar
function populateNavbar(navbarData) {
  // Brand
  document.getElementById('navbarBrand').textContent = navbarData.brand;
  
  // Menu items
  const navbarMenu = document.getElementById('navbarMenu');
  navbarData.menu.forEach(item => {
    const li = document.createElement('li');
    li.className = 'nav-item';
    li.innerHTML = `<a class="nav-link" href="${item.link}">${item.name}</a>`;
    navbarMenu.appendChild(li);
  });
  
  // CTA Button
  const ctaButton = document.getElementById('navbarCTA');
  ctaButton.textContent = navbarData.cta.name;
  ctaButton.href = navbarData.cta.link;
}

// Funzione per popolare il footer
function populateFooter(footerData) {
  // Testo footer
  document.getElementById('footerText').textContent = footerData.text;
  
  // Links
  const footerLinks = document.getElementById('footerLinks');
  footerData.links.forEach(link => {
    const li = document.createElement('li');
    li.className = 'list-inline-item';
    li.innerHTML = `<a class="text-white text-decoration-none" href="${link.link}">${link.name}</a>`;
    footerLinks.appendChild(li);
  });
}

  // Funzioni principali
  function loadProduct() {
    fetch('../data/product.json')
      .then(response => response.json())
      .then(data => {
        populateNavbar(data.layout.navbar);
        populateFooter(data.layout.footer);
        
        const product = data.products.find(p => p.id === productId);
        if(!product) throw new Error('Prodotto non trovato');

        // Popola i dati del prodotto
        document.title = product.title;
        document.getElementById('productTitle').textContent = product.title;
        document.getElementById('productDescription').textContent = product.description;
        document.getElementById('productPrice').textContent = product.price;
        document.getElementById('productImage').src = product.image;

        // Popola le varianti
        const sizeSelect = document.getElementById('sizeSelect');
        product.variants.sizes.forEach(size => {
          sizeSelect.innerHTML += `<option value="${size}">${size}</option>`;
        });

        const colorSelect = document.getElementById('colorSelect');
        product.variants.colors.forEach(color => {
          colorSelect.innerHTML += `<option value="${color}">${color}</option>`;
        });

        // Tabella tecnica
        const techBody = document.getElementById('techTableBody');
        product.technicalTable.features.forEach(feat => {
          techBody.innerHTML += `
            <tr>
              <td>${feat.name}</td>
              <td>${feat.value}</td>
            </tr>
          `;
        });

        // Gestione carrello
        document.getElementById('addToCartBtn').addEventListener('click', () => {
          const cartItem = {
            ...product,
            size: sizeSelect.value,
            color: colorSelect.value,
            quantity: 1
          };

          let cart = JSON.parse(localStorage.getItem('cart')) || [];
          const existingIndex = cart.findIndex(item => 
            item.id === product.id && 
            item.size === cartItem.size && 
            item.color === cartItem.color
          );

          if(existingIndex > -1) {
            cart[existingIndex].quantity += 1;
          } else {
            cart.push(cartItem);
          }

          localStorage.setItem('cart', JSON.stringify(cart));
          
          // Mostra feedback
          const toast = new bootstrap.Toast(document.getElementById('cartToast'));
          toast.show();
        });
      })
      .catch(error => {
        console.error(error);
        window.location.href = 'archive.html';
      });
  }

  // Inizializzazione
  if(!productId) {
    window.location.href = 'archive.html';
  } else {
    loadProduct();
  }
});

