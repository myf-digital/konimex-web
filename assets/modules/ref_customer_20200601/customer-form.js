(function () {

    const common = new Common();
    common.setTitle("Outlet");
    // declare dom
    let uiForm = $("#fm-customer");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectPropinsi = $("#propinsiid-id");
    let uiSelectKota = $("#kotaid-id");
    let uiSelectKecamatan = $("#kecamatanid-id");
    let uiSelectKelurahan = $("#kelurahanid-id");
    let uiSelectSegment = $("#segmentid-id");
    let uiSelectType = $("#typeid-id");
    let uiSelectClass = $("#classid-id");   
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");
    let uiSelectSubarea = $("#subareaid-id");
    let uiSelectSalesman = $("#salesmanid-id");
    // define from *-content.js
    let param = common.getCookie("module.customer.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initializeParam();
    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_customer/create") : common.baseURL("ref_customer/update");
        //param.push= {usersession: paramsession.username, idjabatan: paramsession.idjabatan};
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            },
            rules: {
                kode_outlet: {
                    required: true/*,
					remote	 : {
						url		: common.baseURL("ref_customer/cek_kode_outler"),
						type	: "POST",
						data: {
							kode_outlet: function() {
								return $("#kode_outlet").val();
							},
							kode_outlet : $("#kode_outlet").val()
						}
					}*/
                },
                nama_customer: {
                    required: true
                },
                salesmanid: {
                    required: true
                },
                classid: {
                    required: true
                },
                typeid: {
                    required: true
                },
                segmentid: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer");
        });

        loadPropinsi();
        loadRegional();
        loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan});

        uiSelectPropinsi.on('select2:select', function (e) {
            propinsiSelected = e.params.data;
            loadKota(propinsiSelected);
        });

        uiSelectKota.on('select2:select', function (e) {
            kotaSelected = e.params.data;
            propinsiSelected = uiSelectPropinsi.val();
            let gval = kotaSelected;
            gval.push = {propinsiid:propinsiSelected};
            loadKecamatan(gval);
        });

        uiSelectKecamatan.on('select2:select', function (e) {
            kecamatanSelected = e.params.data;
            propinsiSelected = uiSelectPropinsi.val();
            kotaSelected = uiSelectKota.val();
            let gval = kecamatanSelected;
            gval.push = {propinsiid:propinsiSelected};
            gval.push = {kotaid:kotaSelected};
            loadKelurahan(gval);
        });

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            //siteSelected = uiSelectSiteid.val();
            let srval = regionalSelected;
            //srval.push = {siteid:siteSelected};
            loadArea(srval);
        });

        uiSelectArea.on('select2:select', function (e) {
            areaSelected = e.params.data;
            //siteSelected = uiSelectSiteid.val();
            regionalSelected = uiSelectRegional.val();
            let sraval = areaSelected;
            //sraval.push = {siteid:siteSelected};
            sraval.push = {regionalid:regionalSelected};
            loadSubArea(sraval);
        });

        uiSelectPropinsi.select2({
            placeholder: 'Select Propinsi',
            allowClear: true
        });

        uiSelectKota.select2({
            placeholder: 'Select Kota/Kabupaten',
            allowClear: true
        });

        uiSelectKecamatan.select2({
            placeholder: 'Select Kecamatan',
            allowClear: true
        });

        uiSelectKelurahan.select2({
            placeholder: 'Select Kelurahan',
            allowClear: true
        });

        uiSelectRegional.select2({
            placeholder: 'Select Regional',
            allowClear: true
        });
        
        uiSelectArea.select2({
            placeholder: 'Select Area',
            allowClear: true
        });

        uiSelectSubarea.select2({
            placeholder: 'Select SubArea',
            allowClear: true
        });

        uiSelectSalesman.select2({
            placeholder: 'Select PAR-MA',
            allowClear: true
        });


        if (isUpdate) {
            //uiSelectSiteid.val(param.siteid).trigger('change');

            loadPropinsi();
            let kval = {propinsiid:param.propinsiid};
            loadKota(kval);
            let pkval = {propinsiid:param.propinsiid,kotaid:param.kotaid};
            loadKecamatan(pkval);
            let pkkval = {propinsiid:param.propinsiid,kotaid:param.kotaid,kecamatanid:param.kecamatanid};
            loadKelurahan(pkkval);

            //let sval = {siteid:param.siteid};
            loadRegional();
            loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan});
            let srval = {regionalid:param.regionalid};
            loadArea(srval);
            let sraval = {regionalid:param.regionalid, areaid:param.areaid};
            loadSubArea(sraval);

        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("ref_customer_segment/load"), filter.build()),
            $.post(common.baseURL("ref_customer_type/load"), filter.build()),
            $.post(common.baseURL("ref_customer_class/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r2, r3, r4) {
            common.loadingClose();
            setupForm(r2[0], r3[0], r4[0]);
        }).fail(resolver.fail);

    }

    function setupForm(r2, r3, r4) {
        let rows2 = r2.rows;
        let rows3 = r3.rows;
        let rows4 = r4.rows;

        uiSelectSegment.select2({
            placeholder: 'Select BU',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.segmentid; // replace name with the property used for the text
                o.text = o.nama_segment; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectType.select2({
            placeholder: 'Select Channel',
            allowClear: true,
            data: $.map(rows3, function (o) {
                o.id = o.typeid; // replace name with the property used for the text
                o.text = o.nama_type; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectClass.select2({
            placeholder: 'Select Sub Channel',
            allowClear: true,
            data: $.map(rows4, function (o) {
                o.id = o.classid; // replace name with the property used for the text
                o.text = o.nama_class; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSegment.val(param.segmentid).trigger('change');
            uiSelectType.val(param.typeid).trigger('change');
            uiSelectClass.val(param.classid).trigger('change');
        }else{
            uiSelectSegment.val(null).trigger('change');
            uiSelectType.val(null).trigger('change');
            uiSelectClass.val(null).trigger('change');
        }

    }
        
    function loadPropinsi() {
        common.loading();
        $.post(common.baseURL("api_v1/call_propinsi"),  function (res) {
            uiSelectPropinsi.empty();
            uiSelectPropinsi.select2({
                placeholder: "Select Province",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.propinsiid; // replace name with the property used for the text
                    o.text = o.nama_propinsi;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectPropinsi.val(param.propinsiid).trigger('change');
            }else{
                uiSelectPropinsi.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

    function loadKota(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_kota"), {propinsiid: data.propinsiid}, function (res) {
            uiSelectKota.empty();
            uiSelectKota.select2({
                placeholder: "Select Kota/Kabupaten",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.kotaid; // replace name with the property used for the text
                    o.text = o.nama_kota;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectKota.val(param.kotaid).trigger('change');
            }else{
                uiSelectKota.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

    function loadKecamatan(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_kecamatan"), {propinsiid: data.propinsiid, kotaid: data.kotaid}, function (res) {
            uiSelectKecamatan.empty();
            uiSelectKecamatan.select2({
                placeholder: "Select Kecamatan",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.kecamatanid; // replace name with the property used for the text
                    o.text = o.nama_kecamatan;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectKecamatan.val(param.kecamatanid).trigger('change');
            }else{
                uiSelectKecamatan.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

    function loadKelurahan(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_kelurahan"), {propinsiid: data.propinsiid, kotaid: data.kotaid, kecamatanid: data.kecamatanid}, function (res) {
            uiSelectKelurahan.empty();
            uiSelectKelurahan.select2({
                placeholder: "Select Kelurahan",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.kelurahanid; // replace name with the property used for the text
                    o.text = o.nama_kelurahan;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectKelurahan.val(param.kelurahanid).trigger('change');
            }else{
                uiSelectKelurahan.val(null).trigger('change');
            }
            common.loadingClose();
        });

    }

    function loadRegional() {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), function (res) {
            uiSelectRegional.empty();
            uiSelectRegional.select2({
                placeholder: "Select Regional",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.regionalid; // replace name with the property used for the text
                    o.text = o.nama_regional;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectRegional.val(param.regionalid).trigger('change');
            }else{
                uiSelectRegional.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area"), {regionalid:data.regionalid}, function (res) {
            uiSelectArea.empty();
            uiSelectArea.select2({
                placeholder: "Select Area",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectArea.val(param.areaid).trigger('change');
            }else{
                uiSelectArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadSubArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_subarea"), {regionalid:data.regionalid, areaid:data.areaid}, function (res) {
            uiSelectSubarea.empty();
            uiSelectSubarea.select2({
                placeholder: "Select SubArea",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.subareaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSubarea.val(param.subareaid).trigger('change');
            }else{
                uiSelectSubarea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Salesman",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSalesman.val(param.salesmanid).trigger('change');
            }else{
                uiSelectSalesman.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();