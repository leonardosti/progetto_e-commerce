let currencySymbol = "€";

document.addEventListener("DOMContentLoaded", function(){
  fetch('data/cart.json')
    .then(response => response.json())
    .then(staticData => {
      // Imposta i testi statici
      document.title = staticData.pageTitle;
      document.getElementById('cartTitle').textContent = staticData.cartTitle;
      document.getElementById('thProduct').textContent = staticData.headers.product;
      document.getElementById('thQuantity').textContent = staticData.headers.quantity;
      document.getElementById('thPrice').textContent = staticData.headers.price;
      document.getElementById('thBundle').textContent = staticData.headers.bundle;
      document.getElementById('couponInput').placeholder = staticData.couponPlaceholder;
      document.getElementById('applyCouponBtn').textContent = staticData.applyCoupon;
      document.getElementById('totalText').textContent = staticData.totalText;
      document.getElementById('updateCartBtn').textContent = staticData.updateCart;
      
      // Memorizza il simbolo della valuta
      currencySymbol = staticData.currency;

      // Carica i prodotti dinamici dal localStorage
      let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
      renderCartItems(cart, currencySymbol);
    })
    .catch(error => console.error('Errore nel caricamento del JSON statico:', error));
});

// Funzione per generare le righe del carrello
function renderCartItems(cart, currencySymbol) {
  const cartBody = document.getElementById('cartBody');
  cartBody.innerHTML = ""; // Pulisce le righe attuali
  if(cart.length === 0) {
    cartBody.innerHTML = `<tr><td colspan="5" class="text-center">Il carrello è vuoto</td></tr>`;
    document.getElementById('totalAmount').textContent = currencySymbol + "0.00";
    return;
  }
  let total = 0;
  cart.forEach((item, index) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        ${item.title} <br>
        <small>Taglia: ${item.size}, Colore: ${item.color}</small>
      </td>
      <td>
        <input type="number" value="${item.quantity}" min="1" class="form-control" style="width:80px;" data-index="${index}">
      </td>
      <td>${item.price}</td>
      <td>${item.bundle || ''}</td>
      <td>
        <button class="btn btn-danger btn-sm" data-index="${index}">Rimuovi</button>
      </td>
    `;
    cartBody.appendChild(tr);
    let priceNumber = parseFloat(item.price.replace(/[^0-9\.]+/g, ""));
    total += priceNumber * item.quantity;
  });
  document.getElementById('totalAmount').textContent = currencySymbol + total.toFixed(2);
  
  // Aggiunge i listener per il cambio quantità e per i pulsanti di rimozione
  addCartEventListeners();
}

function addCartEventListeners() {
  // Gestione del cambio quantità
  document.querySelectorAll('#cartBody input[type="number"]').forEach(input => {
    input.addEventListener('change', function() {
      const index = this.getAttribute('data-index');
      let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
      let newQuantity = parseInt(this.value);
      if(newQuantity < 1) {
        newQuantity = 1;
        this.value = 1;
      }
      cart[index].quantity = newQuantity;
      localStorage.setItem('cartItems', JSON.stringify(cart));
      renderCartItems(cart, currencySymbol);
    });
  });
  
  // Gestione del pulsante "Rimuovi"
  document.querySelectorAll('#cartBody button.btn-danger').forEach(button => {
    button.addEventListener('click', function() {
      const index = this.getAttribute('data-index');
      let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
      cart.splice(index, 1);
      localStorage.setItem('cartItems', JSON.stringify(cart));
      renderCartItems(cart, currencySymbol);
    });
  });
}

document.getElementById('updateCartBtn').addEventListener('click', function(){
  let cart = JSON.parse(localStorage.getItem('cartItems')) || [];
  renderCartItems(cart, currencySymbol);
  alert("Carrello aggiornato!");
});
