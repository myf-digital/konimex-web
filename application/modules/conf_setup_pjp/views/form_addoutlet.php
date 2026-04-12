<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Peta Outlet Radius</title>
  <style>
  body { margin:0; padding:0; display:flex; height:100vh; font-family:sans-serif; }
  #sidebar { width:300px; overflow-y:auto; border-right:1px solid #ccc; padding:10px; }
  #map { flex:1; }
  .outlet-item { padding:5px; border-bottom:1px solid #eee; cursor:pointer; transition:background 0.2s; }
  .outlet-item:hover { background:#f1f1f1; }
  .outlet-item.selected { background:#d1e7dd; }
  select, button, input { width:95%; margin-top:5px; padding:5px; }
  #btnSearchArea {
    position: absolute;
    top: 10px;
    left: 60%;
    transform: translateX(-50%);
    background: white;
    border: 1px solid #ccc;
    padding: 6px 12px;
    cursor: pointer;
    z-index: 1;
    width: 200px;
    border-radius: 10px;
  }
</style>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAxXaXhYT8PzX2BEDwnqy0aeWXOZ5yYzLo&libraries=places,marker"></script>
</head>
<body>

<div id="sidebar">
  <input type="text" id="search-input" placeholder="Cari lokasi...">
  
  <select id="radiusSelect">
    <option value="1">Radius 1 km</option>
    <option value="3">Radius 3 km</option>
    <option value="5">Radius 5 km</option>
    <option value="10">Radius 10 km</option>
    <option value="50" selected>Radius 50 km</option>
  </select>
  
  <button id="btnSearchArea" class="btn btn-info">Telusuri area ini</button>
  
  <div id="outletList" style="overflow: auto; height: 75vh; margin-top:10px;"></div>
  
  <button id="btnSave" class="btn btn-primary">Simpan Outlet Terpilih</button>
</div>

<div id="map"></div>

<script>
  let map, markers = [], selectedOutlets = [], searchCircle = null;
  const icon = `<?= site_url('assets/images/ic_store_48.png') ?>`;

  function initMap() {
    const defaultCenter = { lat: -6.8990926, lng: 107.6578193 };
    const defaultRadius = 50;

    map = new google.maps.Map(document.getElementById("map"), {
      center: defaultCenter,
      zoom: 14,
    });

    // 🔍 Autocomplete input lokasi
    const input = document.getElementById("search-input");
    const autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo("bounds", map);

    autocomplete.addListener("place_changed", () => {
      const place = autocomplete.getPlace();
      if (!place.geometry || !place.geometry.location) return;

      const lat = place.geometry.location.lat();
      const lng = place.geometry.location.lng();
      const radius = parseFloat(document.getElementById("radiusSelect").value);

      map.setCenter({ lat, lng });
      map.setZoom(15);

      drawSearchRadius(lat, lng, radius);
      loadOutlets(lat, lng, radius);
    });

    // 🔘 Tombol “Telusuri area ini”
    document.getElementById("btnSearchArea").addEventListener("click", () => {
      const center = map.getCenter();
      const radius = parseFloat(document.getElementById("radiusSelect").value);
      drawSearchRadius(center.lat(), center.lng(), radius);
      loadOutlets(center.lat(), center.lng(), radius);
    });

    // 💾 Tombol “Simpan Outlet Terpilih”
    document.getElementById("btnSave").addEventListener("click", saveSelected);

    // Load awal
    drawSearchRadius(defaultCenter.lat, defaultCenter.lng, defaultRadius);
    loadOutlets(defaultCenter.lat, defaultCenter.lng, defaultRadius);
  }

  // 🟢 Gambar lingkaran radius area pencarian
  function drawSearchRadius(lat, lng, radiusKm) {
    if (searchCircle) searchCircle.setMap(null);

    searchCircle = new google.maps.Circle({
      strokeColor: "#0d6efd",
      strokeOpacity: 0.8,
      strokeWeight: 2,
      fillColor: "#0d6efd",
      fillOpacity: 0.15,
      map,
      center: { lat, lng },
      radius: radiusKm * 1000, // km → meter
    });

    map.fitBounds(searchCircle.getBounds());
  }

  // 📡 Ambil outlet berdasarkan koordinat + radius
  function loadOutlets(lat, lng, radiusKm) {
    fetch(`<?= site_url('api_v1/outlet_pjp') ?>?lat_center=${lat}&lng_center=${lng}&radius_km=${radiusKm}&salesmanid=PAR100`)
      .then(res => res.json())
      .then(data => {
        clearMarkers();
        renderList(data.result);
        
        data.result.forEach(d => {
          const marker = new google.maps.Marker({
            position: { lat: parseFloat(d.latitude), lng: parseFloat(d.longitude) },
            map,
            title: d.outlet,
            icon: {
              url: icon,
              labelOrigin: {x: 17, y: 45}
            },
            label: {
              text: d.outlet,
              color: '#222222',
              fontSize: '10px'
            },
            outletId: d.customerid,
          });

          marker.addListener("click", () => {
            map.panTo(marker.getPosition());
            toggleSelectById(d.customerid);
          });

          markers.push(marker);
        });
      })
      .catch(err => console.error("Error load outlets:", err));
  }

  // 📋 Tampilkan daftar outlet di sidebar
  function renderList(data) {
    const listDiv = document.getElementById('outletList');
    listDiv.innerHTML = '';
    if (!data || data.length === 0) {
      listDiv.innerHTML = '<p style="text-align:center;color:#999;">Tidak ada outlet dalam radius ini.</p>';
      return;
    }

    data.forEach(d => {
      const div = document.createElement('div');
      div.className = 'outlet-item';
      div.dataset.id = d.customerid;
      div.innerHTML = `
        <b>${d.outlet}</b><br>
        <small>${d.account}</small><br>
        <small>${parseFloat(d.distance_km).toFixed(2)} km</small>`;
      div.onclick = () => toggleSelect(div, d.customerid);
      listDiv.appendChild(div);
    });
  }

  // ✅ Pilih / batalkan outlet
  function toggleSelect(div, id) {
    if (selectedOutlets.includes(id)) {
      selectedOutlets = selectedOutlets.filter(x => x !== id);
      div.classList.remove('selected');
      updateMarkerStyle(id, false);
    } else {
      selectedOutlets.push(id);
      div.classList.add('selected');
      updateMarkerStyle(id, true);
    }
  }

  function toggleSelectById(id) {
    const div = document.querySelector(`.outlet-item[data-id="${id}"]`);
    if (div) toggleSelect(div, id);
  }

  // 💾 Simpan outlet terpilih
  function saveSelected() {
    console.log("Outlet terpilih:", selectedOutlets);
    alert(`Outlet terpilih: ${selectedOutlets.join(', ')}`);
    // fetch(`<?= site_url('outlet/save_selected') ?>`, {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify({ outlet_ids: selectedOutlets })
    // }).then(res => res.json())
    //   .then(r => alert('Outlet tersimpan!'));
  }

  // 🗑️ Bersihkan marker lama
  function clearMarkers() {
    markers.forEach(m => m.setMap(null));
    markers = [];
  }

  function updateMarkerStyle(id, isSelected) {
    const marker = markers.find(m => m.outletId === id);
    if (!marker) return;

    if (isSelected) {
      marker.setIcon({
        url: icon,
        labelOrigin: { x: 17, y: 45 },
      });
      marker.setLabel({
        text: marker.getLabel().text,
        color: "#1a73e8",
        fontWeight: "bold",
        fontSize: "11px",
      });
    } else {
      marker.setIcon({
        url: icon,
        labelOrigin: { x: 17, y: 45 },
      });
      marker.setLabel({
        text: marker.getLabel().text,
        color: "#222222",
        fontWeight: "normal",
        fontSize: "10px",
      });
    }
  }

  initMap();
</script>
</body>
</html>
