(function () {

    const common = new Common();
    common.setTitle("Table Sequence");
    // declare dom
    let uiForm = $("#fm-table-sequence");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.table.sequence.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_table_sequence/create") : common.baseURL("app_table_sequence/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_table_sequence",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_table_sequence");
        });
    }

})();