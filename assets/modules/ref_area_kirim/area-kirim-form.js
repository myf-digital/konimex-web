(function () {

    const common = new Common();
    common.setTitle("Area Kirim");
    // declare dom
    let uiForm = $("#fm-area-kirim");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");

    // define from *-content.js
    let param = common.getCookie("module.area.kirim.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_kirim/create") : common.baseURL("ref_area_kirim/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_kirim",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'areakirimid', value: param.areakirimid});
                }
                return true; // MANDATORY!
            },
            rules: {
                areakirimid: {
                    required: true
                },
                nama_areakirim: {
                    required: true
                },
                siteid: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_kirim");
        });

        uiSelectSiteid.on('select2:select', function (e) {
            siteidSelected = e.params.data;
        });

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
    }

    function setupForm(r1) {
        let rows = r1.rows;
        
        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true,
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
        }

    }

})();