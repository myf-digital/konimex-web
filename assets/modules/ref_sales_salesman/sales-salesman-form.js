(function () {

    const common = new Common();
    common.setTitle("Sales Salesman");
    // declare dom
    let uiForm = $("#fm-sales-salesman");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");
    let uiSalesmanid = $("#salesmanid-id");
    let uiPassword = $("#password-id");
    let uiSelectAktif = $("#aktif-id");
    let uiSelectTipesales = $("#tipe_sales-id");
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");
    let uiSelectSubarea = $("#subareaid-id");
    /*let uiSelectRamRsm = $("#ram_rsm-id");
    let uiSelectAasaamTsstsm = $("#aas_aam_tss_tsm-id");
    let uiSelectFc = $("#fc-id");*/

    // define from *-content.js
    let param = common.getCookie("module.sales.salesman.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_sales_salesman/create") : common.baseURL("ref_sales_salesman/update");
        let urlcek = param === undefined ?  common.baseURL("ref_sales_salesman/cek_user_gff") : common.baseURL("ref_sales_salesman/cek_user_gff_update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_sales_salesman",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                //form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                salesmanid : {
					required : true,
					remote	 : {
						url		: urlcek,
						type	: "POST"
					}
				},
                password: {
                    required: true
                },
                nama_salesman: {
                    required: true
                },
                regionalid: {
                    required: true
                },
                areaid: {
                    required: true
                },
                // subareaid: {
                //     required: true
                // }
            },
            messages : {
				salesmanid : {
					required : 'Isi User GFF',
					remote   : 'User GFF Sudah terdaftar'     
						
                }			
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_sales_salesman");
        });

        uiSelectAktif.select2({
            placeholder: 'Select Status',
            allowClear: true
        });

        uiSelectTipesales.select2({
            placeholder: 'Select Sales Type',
            allowClear: true
        });

        //loadRegional();
        loadRegional({usersession:paramsession.username,idjabatan:paramsession.idjabatan,restrict_level:paramsession.restrict_level,restrict_bu:paramsession.restrict_bu});

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            let srval = regionalSelected;
            srval = {regionalid:srval.regionalid, usersession:paramsession.username,restrict_level:paramsession.restrict_level};
            loadArea(srval);
        });

        uiSelectArea.on('select2:select', function (e) {
            areaSelected = e.params.data;
            //siteSelected = uiSelectSiteid.val();
            regionalSelected = uiSelectRegional.val();
            // let sraval = areaSelected;
            // sraval = {regionalid:regionalSelected, areaid:sraval.areaid, usersession:paramsession.username,restrict_level:paramsession.restrict_level};
            // loadSubArea(sraval);
        });
        
        if (isUpdate) {
            document.getElementById("salesmanid-id").readOnly = true;        
            uiSelectSiteid.val(param.siteid).trigger('change');
            let pval = {siteid:param.siteid};
            //loadGudang(pval);
            /*loadRegional();
            let srval = {regionalid:param.regionalid};
            loadArea(srval);
            let sraval = {regionalid:param.regionalid, areaid:param.areaid};
            loadSubArea(sraval);*/

            loadRegional({usersession:paramsession.username, idjabatan: paramsession.idjabatan, restrict_level: paramsession.restrict_level});
            loadArea({regionalid:param.regionalid, usersession:paramsession.username, idjabatan: paramsession.idjabatan, restrict_level:paramsession.restrict_level});
            // loadSubArea({regionalid:param.regionalid, areaid:param.areaid, usersession:paramsession.username, restrict_level:paramsession.restrict_level});

        }
    }

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional_restrict"), {usersession:data.usersession,restrict_level: data.restrict_level}, function (res) {
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
        $.post(common.baseURL("api_v1/call_area_restrict"), {regionalid:data.regionalid, usersession:data.usersession, restrict_level:data.restrict_level}, function (res) {
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
        $.post(common.baseURL("api_v1/call_subarea_restrict"), {regionalid:data.regionalid, areaid:data.areaid, usersession: data.usersession, restrict_level: data.restrict_level}, function (res) {
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

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
            $.post(common.baseURL("api_v1/call_statusaktif"), filter.build()),
            $.post(common.baseURL("api_v1/call_tipesalesman"), filter.build()),
            //$.post(common.baseURL("api_v1/call_ram_rsm"), filter.build()),
            //$.post(common.baseURL("api_v1/call_aas_aam_tss_tsm"), filter.build()),
            //$.post(common.baseURL("api_v1/call_fc"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2, r3) {
            common.loadingClose();
            setupForm(r1[0], r2[0], r3[0]);
        }).fail(resolver.fail);
    }

    function setupForm(r1, r2, r3) {
        let rows = r1.rows;
        let rows2 = r2.result;
        let rows3 = r3.result;
        //let rows4 = r4.result;
        //let rows5 = r5.result;
        //let rows6 = r6.result;
        
        uiSelectAktif.select2({
            placeholder: 'Select Status',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.idaktif; // replace name with the property used for the text
                o.text = o.status; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectTipesales.select2({
            placeholder: 'Select Sales Type',
            allowClear: true,
            data: $.map(rows3, function (o) {
                o.id = o.idtipesales; // replace name with the property used for the text
                o.text = o.tipesales; // replace name with the property used for the text
                return o;
            }),
        });
        
        /*uiSelectRamRsm.select2({
            placeholder: 'Select RAM - RSM',
            allowClear: true,
            data: $.map(rows4, function (o) {
                o.id = o.username; // replace name with the property used for the text
                o.text = o.name; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectAasaamTsstsm.select2({
            placeholder: 'Select AAS-AAM-TSS-TSM',
            allowClear: true,
            data: $.map(rows5, function (o) {
                o.id = o.username; // replace name with the property used for the text
                o.text = o.name; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectFc.select2({
            placeholder: 'Select FC',
            allowClear: true,
            data: $.map(rows6, function (o) {
                o.id = o.username; // replace name with the property used for the text
                o.text = o.name; // replace name with the property used for the text
                return o;
            }),
        });*/

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectAktif.val(param.aktif).trigger('change');
            uiSelectTipesales.val(param.tipe_sales).trigger('change');
            //uiSelectRamRsm.val(param.usernameram).trigger('change');
            //uiSelectAasaamTsstsm.val(null).trigger('change');
            //uiSelectFc.val(param.usernamefc).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
            uiSelectAktif.val(null).trigger('change');
            uiSelectTipesales.val(null).trigger('change');
            //uiSelectRamRsm.val(null).trigger('change');
            //uiSelectAasaamTsstsm.val(null).trigger('change');
            //uiSelectFc.val(null).trigger('change');
        }

    }

})();