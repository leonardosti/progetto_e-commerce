document.addEventListener("DOMContentLoaded", function() {
  let currencySymbol = "€";
  let cart = JSON.parse(localStorage.getItem('cart')) || [];

  // Funzioni principali
  function renderCart() {
    const cartContainer = document.getElementById('cartItems');
    const totalElement = document.getElementById('totalAmount');
    let total = 0;

    cartContainer.innerHTML = '';
    
    if(cart.length === 0) {
      cartContainer.innerHTML = `
        <div class="col-12 text-center py-5">
          <h3 class="text-muted mb-4">Il carrello è vuoto</h3>
          <a href="archive.html" class="btn btn-primary">Vedi prodotti</a>
        </div>
      `;
      totalElement.textContent = `${currencySymbol}0.00`;
      return;
    }

    cart.forEach((item, index) => {
      const price = parseFloat(item.price.replace(/[^\d.]/g, ''));
      total += price * item.quantity;

      const itemHTML = `
        <div class="col-12">
          <div class="card mb-3">
            <div class="row g-0">
              <div class="col-md-2">
                <img src="${item.image}" class="img-fluid rounded-start" alt="${item.title}">
              </div>
              <div class="col-md-8">
                <div class="card-body">
                  <h5 class="card-title">${item.title}</h5>
                  <p class="card-text">
                    <small class="text-muted">Taglia: ${item.size}</small><br>
                    <small class="text-muted">Colore: ${item.color}</small>
                  </p>
                  <div class="d-flex align-items-center">
                    <input type="number" 
                           value="${item.quantity}" 
                           min="1" 
                           class="form-control quantity-input" 
                           style="width: 100px;"
                           data-index="${index}">
                    <button class="btn btn-danger ms-3 remove-btn" data-index="${index}">
                      <i class="bi bi-trash"></i> Rimuovi
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-2 d-flex align-items-center justify-content-end">
                <h5 class="mb-0">${currencySymbol}${(price * item.quantity).toFixed(2)}</h5>
              </div>
            </div>
          </div>
        </div>
      `;
      cartContainer.insertAdjacentHTML('beforeend', itemHTML);
    });

    totalElement.textContent = `${currencySymbol}${total.toFixed(2)}`;
    addEventListeners();
  }

  function addEventListeners() {
    // Gestione quantità
    document.querySelectorAll('.quantity-input').forEach(input => {
      input.addEventListener('change', function() {
        const index = this.dataset.index;
        const newQuantity = parseInt(this.value) || 1;
        
        if(newQuantity < 1) {
          this.value = 1;
          return;
        }

        cart[index].quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
      });
    });

    // Gestione rimozione
    document.querySelectorAll('.remove-btn').forEach(button => {
      button.addEventListener('click', function() {
        const index = this.dataset.index;
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
      });
    });
  }

  // Pulsanti
  document.getElementById('updateCartBtn').addEventListener('click', renderCart);
  document.getElementById('applyCouponBtn').addEventListener('click', () => {
    alert('Funzionalità coupon non ancora implementata');
  });

  // Inizializzazione
  renderCart();
});
