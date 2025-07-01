(function () {

    const common = new Common();
    common.setTitle("Gudang");
    // declare dom
    let uiForm = $("#fm-gudang");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");
    let uiSelectAktif = $("#status_aktif-id");

    // define from *-content.js
    let param = common.getCookie("module.gudang.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_gudang/create") : common.baseURL("ref_gudang/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_gudang",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                gudangid: {
                    required: true
                },
                gudang_name: {
                    required: true
                },
                status_aktif: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_gudang");
        });

        uiSelectAktif.select2({
            placeholder: 'Select Status',
            allowClear: true
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectAktif.val(param.status_aktif).trigger('change');
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
            $.post(common.baseURL("api_v1/call_statusaktif"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);
    }

    function setupForm(r1, r2) {
        let rows = r1.rows;
        let rows2 = r2.result;
        
        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true,
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectAktif.select2({
            placeholder: 'Select Status',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.idaktif; // replace name with the property used for the text
                o.text = o.status; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectAktif.val(param.status_aktif).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
            uiSelectAktif.val(null).trigger('change');
        }

    }    
})();