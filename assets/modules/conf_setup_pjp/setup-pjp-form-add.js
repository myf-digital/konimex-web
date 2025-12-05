(function () {
    const common = new Common();
    common.setTitle("Setup FJP");
    // declare dom
    let uiForm = $("#fm-add-setup-pjp");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiBtnMaps = $("#btn-maps");
    let uiSelectWeeks1 = $("#week1-id");
    let uiSelectWeeks2 = $("#week2-id");
    let uiSelectWeeks3 = $("#week3-id");
    let uiSelectWeeks4 = $("#week4-id");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiSearchOutlet = $("#customerid");

    let param = common.getCookie("module.setup.pjp.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined;
    let selectedParma = null;

    // maps
    let map;
    let centerMarker = null;
    let outletMarkers = [];
    let selectedOutlets = [];
    let searchCircle = null;
    let dataOutlets = [];
    let defaultRadius = 5;
    let mapInitialized = false;

    const defaultCenter = { lat: -6.8990926, lng: 107.6578193 };

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setup_pjp/create") : common.baseURL("conf_setup_pjp/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setup_pjp",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});

                let customer = document.getElementById("customerid_to");
                for (let i = 0; i < customer.options.length; i++){
                    form.push({name: 'customerid[]', value: customer.options[i].value});
                }
                return true;
            }
        });

        loadSalesman({
            usersession: paramsession.username,
            idjabatan: paramsession.idjabatan,
            restrict_level: paramsession.restrict_level
        });
        uiSelectSalesman.on('change', function () {
            selectedParma = uiSelectSalesman.select2('data')[0];

            // reset maps
            centerMarker = null;
            outletMarkers = [];
            selectedOutlets = [];
            dataOutlets = [];
            searchCircle = null;
            mapInitialized = false;
            defaultRadius = 5;

            if (selectedParma) {
                uiBtnMaps.removeAttr('disabled');
            } else {
                uiBtnMaps.attr('disabled', 'disabled');
                resetSelectOutlet();
            }
        });

        uiSearchOutlet.multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            },
            fireSearch: function(value) {
                return value.length > 3;
            },
            submitAllLeft: false,
            submitAllRight: false
        });

        uiBtnCancel.click(function () {
            common.direct("conf_setup_pjp");
        });

        uiBtnMaps.click(function () {
            if (!selectedParma) return;

            loadOutlet(selectedParma);
            $('#myModalMapsLabel').html(`User (PAR-MA): ${selectedParma.text}`);
            $('#modalMaps').modal('show');
        });

        $('#modalMaps').on('shown.bs.modal', function () {
            if (typeof google === 'undefined' || !google.maps) {
                console.error('Google Maps belum siap!');
                return;
            }
            
            if (!mapInitialized) {
                setTimeout(() => {
                    initMap();
                    mapInitialized = true;
                }, 200);
            } else {
                google.maps.event.trigger(map, 'resize');
                if (centerMarker) {
                    map.setCenter(centerMarker.getPosition());
                }
            }
        });

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("api_v1/call_days"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);

    }

    function setupForm(r1) {
        let rows1 = r1.result;

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays;
                o.text = o.days;
                return o;
            }),
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays;
                o.text = o.days;
                return o;
            }),
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays;
                o.text = o.days;
                return o;
            }),
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays;
                o.text = o.days;
                return o;
            }),
        });

        uiSelectWeeks1.val(null).trigger('change');
        uiSelectWeeks2.val(null).trigger('change');
        uiSelectWeeks3.val(null).trigger('change');
        uiSelectWeeks4.val(null).trigger('change');

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {
            idjabatan: data.idjabatan,
            usersession: data.usersession,
            restrict_level: data.restrict_level
        }, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Salesman",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid;
                    o.text = o.salesmanid + " - " +o.nama_salesman + " - " + o.tipe_sales;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSalesman.val(param.salesmanid).trigger('change');
            } else {
                uiSelectSalesman.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadOutlet(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_outlet_pjp"), {
            salesmanid: data.salesmanid
        }, function (res) {
            let { select, selectTo } = resetSelectOutlet();

            for (let i = 0; i < res.result.length; i++){
                let opt = document.createElement('option');
                opt.value = res.result[i].customerid;
                
                let html = '';
                if (res.result[i].kode_outlet) html += res.result[i].kode_outlet;
                if (res.result[i].outlet) html += ` - ${res.result[i].outlet}`;
                if (res.result[i].account) html += ` - ${res.result[i].account}`;
                if (res.result[i].dc) html += ` - ${res.result[i].dc}`;

                opt.innerHTML = html;
                opt.setAttribute('data-position', res.result[i].customerid);
                select.appendChild(opt);
            }
            
            common.loadingClose();
        });
    }

    function resetSelectOutlet() {
        let select = document.getElementById('customerid');
        let selectTo = document.getElementById('customerid_to');

        let length = select.options.length;
        for (i = length-1; i >= 0; i--) {
            select.options[i] = null;
        }

        let selectToLength = selectTo.options.length;
        for (i = selectToLength-1; i >= 0; i--) {
            selectTo.options[i] = null;
        }

        return { select, selectTo };
    }

    // Start Maps
    const icon = `${window.location.origin}/assets/images/ic_store_48.png`;
    const iconPin = `${window.location.origin}/assets/images/icon_pin_maps.png`;

    function initMap() {
        try {
        let centerLatLong = defaultCenter;
        if (selectedParma.latitude && selectedParma.longitude) {
            centerLatLong = { lat: parseFloat(selectedParma.latitude), lng: parseFloat(selectedParma.longitude) };
        }

        map = new google.maps.Map(document.getElementById('maps'), {
            center: centerLatLong,
            zoom: 13,
        });

        const input = document.getElementById('search-input');
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        const pacContainer = document.querySelector('.pac-container');
        if (pacContainer) {
            document.querySelector('#modalMaps .modal-body').appendChild(pacContainer);
        }

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) return;

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            const radius = parseFloat(document.getElementById('radiusSelect').value);
            defaultRadius = radius;

            map.setCenter({ lat, lng });
            map.setZoom(15);

            drawSearchRadius(lat, lng, radius);
            loadOutletMaps(lat, lng, radius);
        });

        document.getElementById('btnSearchArea').addEventListener('click', () => {
            const center = map.getCenter();
            const radius = parseFloat(document.getElementById('radiusSelect').value);
            const input = document.getElementById('search-input');
            input.value = '';
            
            defaultRadius = radius;
            drawSearchRadius(center.lat(), center.lng(), radius);
            loadOutletMaps(center.lat(), center.lng(), radius);
        });

        document.getElementById('radiusSelect').addEventListener('change', () => {
            const center = map.getCenter();
            const radius = parseFloat(document.getElementById('radiusSelect').value);
            
            defaultRadius = radius;
            drawSearchRadius(center.lat(), center.lng(), radius);
            loadOutletMaps(center.lat(), center.lng(), radius);
        });

        document.getElementById('btnSave').addEventListener('click', saveSelected);

        google.maps.event.addListenerOnce(map, 'idle', () => {
            drawSearchRadius(centerLatLong.lat, centerLatLong.lng, defaultRadius);
            setCenterMarker(centerLatLong.lat, centerLatLong.lng);
        });

        setTimeout(() => google.maps.event.trigger(map, 'resize'), 300);
    } catch (err) {
        console.error(err)
    }
    }

    function drawSearchRadius(lat, lng, radiusKm) {
        if (searchCircle) searchCircle.setMap(null);

        searchCircle = new google.maps.Circle({
            strokeColor: "#0d6efd",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#0d6efd",
            fillOpacity: 0.15,
            map,
            center: {
                lat,
                lng
            },
            radius: radiusKm * 1000,
        });

        map.fitBounds(searchCircle.getBounds());
    }

    function setCenterMarker(lat, lng) {
        if (centerMarker) {
            centerMarker.setPosition({ lat, lng });
        } else {
            centerMarker = new google.maps.Marker({
                position: { lat, lng },
                map,
                draggable: true,
                icon: {
                    url: iconPin,
                }
            });

            centerMarker?.addListener('dragend', () => {
                if (!centerMarker) return;

                const pos = centerMarker.getPosition();
                const newLat = pos.lat();
                const newLng = pos.lng();
                const radius = parseFloat(document.getElementById('radiusSelect').value);

                centerMarker.setAnimation(google.maps.Animation.DROP);

                drawSearchRadius(newLat, newLng, radius);
                loadOutletMaps(newLat, newLng, radius);
                map.panTo(pos);
            });
        }
    }

    function loadOutletMaps(lat, lng, radiusKm) {
        common.loading();
        $.post(common.baseURL('api_v1/outlet_pjp'), {
            lat_center: lat,
            lng_center: lng,
            radius_km: radiusKm,
            salesmanid: selectedParma.salesmanid,
            selected_outlets: selectedOutlets,
        }, function (res) {
            dataOutlets = res.result;
            clearMarkers();
            renderList(res.result);

            setCenterMarker(lat, lng);
            
            res.result.forEach(d => {
                const marker = new google.maps.Marker({
                    position: {
                        lat: parseFloat(d.latitude),
                        lng: parseFloat(d.longitude)
                    },
                    map,
                    title: d.outlet,
                    outletId: d.customerid,
                    icon: {
                        url: icon,
                        labelOrigin: {x: 17, y: 45}
                    },
                    label: {
                        text: d.outlet,
                        color: '#222222',
                        fontSize: '10px'
                    },
                });
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="font-size:13px; line-height:1.4;">
                            <b>${d.outlet}</b><br>
                            ${d.account ? `${d.account}<br>` : ''}
                            ${d.distance_km ? `${parseFloat(d.distance_km).toFixed(2)} km` : ''}
                        </div>
                    `
                });

                marker.addListener('mouseover', () => {
                    infoWindow.open(map, marker);
                });

                marker.addListener('mouseout', () => {
                    infoWindow.close();
                });

                marker.addListener('click', () => {
                    map.panTo(marker.getPosition());
                    toggleSelectById(d.customerid);
                });

                outletMarkers.push(marker);
            });
            
            common.loadingClose();
        });
    }
    // End Maps

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

    function saveSelected() {
        let selectFrom = document.getElementById('customerid');
        let selectTo = document.getElementById('customerid_to');

        for (let i = 0; i < selectedOutlets.length; i++) {
            let outlet = dataOutlets.find(da => da.customerid == selectedOutlets[i]);
            if (outlet) {
                let opt = document.createElement('option');
                opt.value = outlet.customerid;
                
                let html = '';
                if (outlet.kode_outlet) html += outlet.kode_outlet;
                if (outlet.outlet) html += ` - ${outlet.outlet}`;
                if (outlet.account) html += ` - ${outlet.account}`;
                if (outlet.dc) html += ` - ${outlet.dc}`;

                opt.innerHTML = html;
                opt.setAttribute('data-position', outlet.customerid);
                selectTo.appendChild(opt);
            }
        }

        for (let i = selectFrom.options.length - 1; i >= 0; i--) {
            const option = selectFrom.options[i];
            if (selectedOutlets.includes(option.value)) {
                selectFrom.remove(i);
            }
        }
        $('#modalMaps').modal('hide');
    }

    function clearMarkers() {
        outletMarkers.forEach(m => m.setMap(null));
        outletMarkers = [];
    }

    function updateMarkerStyle(id, isSelected) {
        const marker = outletMarkers.find(m => m.outletId === id);
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
    // End Maps
})();