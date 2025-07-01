(function () {

    const common = new Common();
    common.setTitle("Jabatan");
    // declare dom
    let uiForm = $("#fm-jabatan");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectRestrictLevel = $("#restrict_level-id");
    let uiSelectSegment = $("#restrict_bu-id");

    // define from *-content.js
    let param = common.getCookie("module.jabatan.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_jabatan/create") : common.baseURL("ref_jabatan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_jabatan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'idjabatan', value: param.idjabatan});
                }
                return true; // MANDATORY!
            },
            rules: {
                jabatan: {
                    required: true
                },
                restrict_level: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_jabatan");
        });

        if (isUpdate) {
            let sval = {restrict_level:param.restrict_level};
            loadParamKey(sval);
            uiSelectRestrictLevel.val(param.restrict_level).trigger('change');
        }else{
            let sval = {restrict_level:null};
            loadParamKey(sval);
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("ref_customer_segment/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r2) {
            common.loadingClose();
            setupForm(r2);
        }).fail(resolver.fail);

    }

    function setupForm(r2) {
        let rows2 = r2.rows;
        uiSelectSegment.select2({
            placeholder: 'Select BU',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.segmentid; // replace name with the property used for the text
                o.text = o.nama_segment; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSegment.val(param.restrict_bu).trigger('change');
        }else{
            uiSelectSegment.val(null).trigger('change');
        }

    }

    function loadParamKey(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_restrict_level"}, function (res) {
            uiSelectRestrictLevel.empty();
            uiSelectRestrictLevel.select2({
                placeholder: "Restrict Level",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.value; // replace name with the property used for the text
                    o.text = o.desc;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectRestrictLevel.val(param.restrict_level).trigger('change');
            }else{
                uiSelectRestrictLevel.val(data).trigger('change');
            }
            common.loadingClose();
        });
    }

})();