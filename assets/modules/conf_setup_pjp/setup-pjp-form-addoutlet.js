(function () {
    const common = new Common();
    common.setTitle("Setup FJP Outlet");

    // declare dom
    // let uiForm = $("#fm-setup-pjp");
    // let uiBtnCancel = $("#btn-cancel-form");
    // let uiSelectWeeks1 = $("#week1-id");
    // let uiSelectWeeks2 = $("#week2-id");
    // let uiSelectWeeks3 = $("#week3-id");
    // let uiSelectWeeks4 = $("#week4-id");
    // let uiSelectSalesman = $("#salesmanid-id");

    const RADIUS_KM = 500;
    let map = L.map('map').setView([-7.888, 110.33], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    let outletMarkers = [];
    let selectedOutlet = null;
    const searchBtn = document.getElementById('searchAreaBtn');
    const searchBox = document.getElementById('searchBox');
    const suggestions = document.getElementById('suggestions');

    let circle = L.circle(map.getCenter(), { radius: RADIUS_KM * 1000, color: 'blue', fillOpacity: 0.1 }).addTo(map);

    loadOutlets();

    // ========== SEARCH BOX HANDLER ==========
    searchBox.addEventListener('input', () => {
      const query = searchBox.value.trim();
      if (query.length < 3) {
        suggestions.style.display = 'none';
        return;
      }
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
        .then(res => res.json())
        .then(data => {
          suggestions.innerHTML = '';
          data.slice(0, 5).forEach(loc => {
            const div = document.createElement('div');
            div.textContent = loc.display_name;
            div.onclick = () => {
              map.setView([loc.lat, loc.lon], 14);
              suggestions.style.display = 'none';
              searchBox.value = loc.display_name;
            };
            suggestions.appendChild(div);
          });
          suggestions.style.display = data.length ? 'block' : 'none';
        });
    });

    // Klik di luar untuk menutup suggestion
    document.addEventListener('click', (e) => {
      if (!searchBox.contains(e.target)) suggestions.style.display = 'none';
    });

    // ========== PETA & OUTLET ==========
    map.on('moveend', () => {
      searchBtn.style.display = 'block';
    });

    searchBtn.addEventListener('click', loadOutlets);

    function loadOutlets() {
      const center = map.getCenter();
      
      $.post(common.baseURL(`api_v1/outlet_pjp`), {
        lat_center: center.lat,
        lng_center: center.lng,
        radius_km: RADIUS_KM,
        salesmanid: 'PAR100',
      }, function (res) {
          outletMarkers.forEach(m => map.removeLayer(m));
          outletMarkers = [];
          document.getElementById('outletList').innerHTML = '';

          circle.setLatLng(center);

          if (res && res.result.length > 0) {
            res.result.forEach(d => {
              const marker = L.marker([d.latitude, d.longitude])
                .addTo(map)
                .bindPopup(`<b>${d.outlet}</b><br>${d.account}<br>${d.distance_km} km`);
              outletMarkers.push(marker);

              const div = document.createElement('div');
              div.className = 'outlet-item';
              div.innerHTML = `<b>${d.outlet}</b><br><small>${d.account}</small><br><small>${d.distance_km} km</small>`;
              div.onclick = () => selectOutlet(div, d);
              document.getElementById('outletList').appendChild(div);
            });
          }

          searchBtn.style.display = 'none';
      });

      // fetch(`<?= site_url('api_v1/outlet_pjp') ?>?lat_center=${center.lat}&lng_center=${center.lng}&radius_km=${RADIUS_KM}&salesmanid=PAR100`)
      //   .then(res => res.json())
      //   .then(data => {
      //     outletMarkers.forEach(m => map.removeLayer(m));
      //     outletMarkers = [];
      //     document.getElementById('outletList').innerHTML = '';

      //     circle.setLatLng(center);

      //     data.forEach(d => {
      //       const marker = L.marker([d.latitude, d.longitude])
      //         .addTo(map)
      //         .bindPopup(`<b>${d.outlet}</b><br>${d.account}<br>${d.distance_km.toFixed(2)} km`);
      //       outletMarkers.push(marker);

      //       const div = document.createElement('div');
      //       div.className = 'outlet-item';
      //       div.innerHTML = `<b>${d.outlet}</b><br><small>${d.account}</small><br><small>${d.distance_km.toFixed(2)} km</small>`;
      //       div.onclick = () => selectOutlet(div, d);
      //       document.getElementById('outletList').appendChild(div);
      //     });

      //     searchBtn.style.display = 'none';
      //   });
    }

    function selectOutlet(div, outlet) {
      if (selectedOutlet) selectedOutlet.classList.remove('selected');
      div.classList.add('selected');
      selectedOutlet = div;

      map.setView([d.latitude, d.longitude], 16);

      console.warn('selectOutlet', outlet)
      // Simpan outlet via AJAX
      //   fetch("<?= site_url('outlet/save_selected') ?>", {
      //     method: "POST",
      //     headers: { "Content-Type": "application/x-www-form-urlencoded" },
      //     body: `id_outlet=${outlet.id}`
      //   })
      //   .then(res => res.json())
      //   .then(res => {
      //     if (res.status === 'success') {
      //       alert(`Outlet "${outlet.nama}" disimpan!`);
      //     }
      //   });
    }
})();