(function () {

    const common = new Common();
    common.setTitle("Resource");
    // declare dom
    let uiForm = $("#fm-resource");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectRole = $("#role-id");
    let uiSelectSatker = $("#id-satker");
    // define from *-content.js
    let param = common.getCookie("module.resource.update");
    let isUpdate = param !== undefined; // flag create update

    setupFormUI();
    initialize();
    initializeParam();
    initializeSatker();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_resource/create") : common.baseURL("app_resource/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_resource",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'resource_id', value: param.resource_id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_resource");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("app_role/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
    }

    function initializeSatker() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_satker/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormSatker(r1);
        }).fail(resolver.fail);
    }
	
    function setupFormUI() {
        uiSelectRole.select2({
            placeholder: 'Select role'
        });
        uiSelectSatker.select2({
            placeholder: 'Select Satker'
        });
    }

    function setupForm(r1, r2) {
        let rows = r1.rows;
        uiSelectRole.select2({
            data: $.map(rows, function (o) {
                o.id = o.role_id; // replace name with the property used for the text
                o.text = o.role_name; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) {
            uiSelectRole.val(param.role_id).trigger('change');
            $("input[name='username']").attr("disabled", true);
            $("input[name='password']").attr("disabled", true);
        }
    }
	
    function setupFormSatker(r1, r2) {
        let rows = r1.rows;
        uiSelectSatker.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_satker; // replace name with the property used for the text
                o.text = o.satker+'('+o.kode_satker+')'; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) {
            uiSelectSatker.val(param.id_satker).trigger('change');
        }
    }

})();