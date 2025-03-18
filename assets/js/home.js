document.addEventListener("DOMContentLoaded", function(){
    fetch('data/home.json')
      .then(response => response.json())
      .then(data => {
        // Titolo della pagina
        document.title = data.pageTitle;
  
        // Navbar
        document.getElementById('navbarBrand').textContent = data.navbar.brand;
        const navbarMenu = document.getElementById('navbarMenu');
        data.navbar.menu.forEach(item => {
          const li = document.createElement('li');
          li.classList.add("nav-item");
          const a = document.createElement('a');
          a.classList.add("nav-link");
          a.textContent = item.name;
          a.href = item.link;
          li.appendChild(a);
          navbarMenu.appendChild(li);
        });
        document.getElementById('navbarCTA').textContent = data.navbar.cta.name;
        document.getElementById('navbarCTA').setAttribute('href', data.navbar.cta.link);
  
        // Hero Section
        const heroSection = document.getElementById('heroSection');
        heroSection.style.backgroundImage = `url(${data.hero.backgroundImage})`;
        document.getElementById('heroTitle').textContent = data.hero.title;
        document.getElementById('heroSubtitle').textContent = data.hero.subtitle;
        document.getElementById('heroButton').textContent = data.hero.button.text;
        document.getElementById('heroButton').setAttribute('href', data.hero.button.link);
  
        // Prodotti in Evidenza
        document.getElementById('highlightsTitle').textContent = data.sections[0].title;
        document.getElementById('highlightsDescription').textContent = data.sections[0].description;
        const highlightsProducts = document.getElementById('highlightsProducts');
        data.sections[0].products.forEach(product => {
          const col = document.createElement('div');
          col.className = "col-md-4";
          col.innerHTML = `
            <div class="card mb-4">
              <img src="${product.image}" class="card-img-top img-product" alt="">
              <div class="card-body text-center">
                <h5 class="card-title">${product.title}</h5>
                <a href="${product.link}" class="btn btn-primary">Scopri di più</a>
              </div>
            </div>
          `;
          highlightsProducts.appendChild(col);
        });
  
        // Chi Siamo
        document.getElementById('aboutTitle').textContent = data.sections[1].title;
        document.getElementById('aboutDescription').textContent = data.sections[1].description;
  
        // Footer
        document.getElementById('footerText').textContent = data.footer.text;
        const footerLinks = document.getElementById('footerLinks');
        data.footer.links.forEach(link => {
          const li = document.createElement('li');
          li.className = "list-inline-item";
          const a = document.createElement('a');
          a.classList.add("text-white");
          a.textContent = link.name;
          a.href = link.link;
          li.appendChild(a);
          footerLinks.appendChild(li);
        });
      })
      .catch(error => console.error('Errore nel caricamento del JSON:', error));
  });
  