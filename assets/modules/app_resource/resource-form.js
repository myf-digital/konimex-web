(function () {

    const common = new Common();
    common.setTitle("Resource");
    // declare dom
    let uiForm = $("#fm-resource");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectRole = $("#role-id");
    let uiSelectJabatan = $("#idjabatan-id");
    //let uiSelectSite = $("#siteid-id");
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");
    let uiSelectSubArea = $("#subareaid-id");

    // define from *-content.js
    let param = common.getCookie("module.resource.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();
    setupFormUI();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_resource/create") : common.baseURL("app_resource/update");
        let urlcek = param === undefined ? common.baseURL("app_resource/cek_username") : common.baseURL("app_resource/cek_username_update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_resource",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'resource_id', value: param.resource_id});
                }
                return true; // MANDATORY!
            },
            rules: {
                username: {
                    required: true,
					remote	 : {
						url		: urlcek,
						type	: "POST"
					}
                },
                idjabatan: {
                    required: true
                },
                role_id: {
                    required: true
                },
                name: {
                    required: true
                },
                email: {
                    required: true
                },
                telepon: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            messages : {
				username : {
					required : 'Isi Username',
					remote   : 'Username Sudah terdaftar'     
						
                }			
            }

        });

        uiBtnCancel.click(function () {
            common.direct("app_resource");
        });

        /*uiSelectSite.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            loadRegional(siteidSelected);
        });*/

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            siteSelected ="GSK01";
            let srval = {regionalid:uiSelectRegional.val()};
            //srval.push = {siteid:siteSelected};
            loadArea(srval);
        });

        uiSelectArea.on('select2:select', function (e) {
            areaSelected = e.params.data;
            //siteSelected = uiSelectSiteid.val();
            //regionalSelected = uiSelectRegional.val();
            let sraval = {areaid:uiSelectArea.val()};
            //sraval.push = {siteid:siteSelected};
            //sraval.push = {regionalid:regionalSelected};
            loadSubArea(sraval);
        });

        if (isUpdate) {
            //loadSiteid(param.siteid);
            let sval = {siteid:"GSK01"};
            loadRegional(sval);
            uiSelectRegional.val(param.regionalid).trigger('change');
            let srval = {regionalid:param.regionalid};
            loadArea(srval);
            let sraval = {areaid:param.areaid};
            loadSubArea(sraval);
        }else{
            //loadSiteid(null);
            let sval = {siteid:"GSK01"};
            loadRegional(sval);
            let srval = {regionalid:null};
            loadArea(srval);
            let sraval = {areaid:null};
            loadSubArea(sraval);
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("app_role/load"), param.build()),
            $.post(common.baseURL("ref_jabatan/load"), param.build()),
            //$.post(common.baseURL("Conf_setup_site/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);
    }
	
    function setupFormUI() {
        uiSelectRole.select2({
            placeholder: 'Select role'
        });
        uiSelectJabatan.select2({
            placeholder: 'Select Jabatan'
        });
    }

    function setupForm(r1, r2) {
        let rows = r1.rows;
        uiSelectRole.select2({
            placeholder: "Select Role Menu",
            allowClear: true,
            data: $.map(rows, function (o) {
                o.id = o.role_id; // replace name with the property used for the text
                o.text = o.role_name; // replace name with the property used for the text
                return o;
            }),
        });

        let rows2 = r2.rows;
        uiSelectJabatan.select2({
            placeholder: "Select Jabatan",
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.idjabatan; // replace name with the property used for the text
                o.text = o.jabatan; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectRole.val(param.role_id).trigger('change');
            uiSelectJabatan.val(param.idjabatan).trigger('change');
            $("input[name='username']").attr("disabled", true);
            $("input[name='password']").attr("disabled", true);
        }else{
            uiSelectRole.val(null).trigger('change');
            uiSelectJabatan.val(null).trigger('change');
        }

    }

    /*function loadSiteid(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_siteid"), function (res) {
            uiSelectSite.empty();
            uiSelectSite.select2({
                placeholder: "All Site",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.siteid; // replace name with the property used for the text
                    o.text = o.nama_site;
                    return o;
                }),
            });
            uiSelectSite.val(data).trigger('change');
            common.loadingClose();
        });
    }*/

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), {siteid:data.siteid}, function (res) {
            uiSelectRegional.empty();
            uiSelectRegional.select2({
                placeholder: "All Regional",
                multiple: true,
                tokenSeparators: [','],
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
        $.post(common.baseURL("app_resource/call_area"), {regionalid:data.regionalid}, function (res) {
            uiSelectArea.empty();
            uiSelectArea.select2({
                placeholder: "All Area",
                multiple: true,
                tokenSeparators: [','],
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            //alert (data.regionalid);
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
        $.post(common.baseURL("app_resource/call_subarea"), {regionalid:data.regionalid, areaid:data.areaid}, function (res) {
            uiSelectSubArea.empty();
            uiSelectSubArea.select2({
                placeholder: "Select SubArea/City",
                multiple: true,
                tokenSeparators: [','],
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.subareaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSubArea.val(param.subareaid).trigger('change');
            }else{
                uiSelectSubArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    
})();