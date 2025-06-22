(function () {

    const common = new Common();
    common.setTitle("Area");
    // declare dom
    let uiForm = $("#fm-area");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectReginalid = $("#idregional-id");

    // define from *-content.js
    let param = common.getCookie("module.area.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area/create") : common.baseURL("ref_area/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'idarea', value: param.idarea});
                }
                return true; // MANDATORY!
            },
            rules: {
                idregional: {
                    required: true
                },
                area: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area");
        });

        if (isUpdate) {
            let rval = {idregional:param.idregional};
            loadRegional(rval);
            uiSelectReginalid.val(param.idregional).trigger('change');
        }else{
            loadRegional();
        }

    }

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), function (res) {
            uiSelectReginalid.empty();
            uiSelectReginalid.select2({
                placeholder: "Select Regional",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.idregional; // replace name with the property used for the text
                    o.text = o.regional;
                    return o;
                }),
            });

            if (isUpdate) {
                uiSelectReginalid.val(param.idregional).trigger('change');
            }else{
                uiSelectReginalid.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();