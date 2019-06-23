(function () {

    const common = new Common();
    common.setTitle("Matrix Table");
    // declare dom
    let uiForm = $("#fm-matrix-table");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectAspek = $("#id-aspek");
	let uiSelectGrafik = $("#id-aspek_grafik");
    // define from *-content.js
    let param = common.getCookie("module.matrix.table.update");

    initialize();
	initializeParamAspek();
	initializeParamGrafik();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_matrix_table/create") : common.baseURL("ref_matrix_table/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_matrix_table",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_matrix', value: param.id_matrix});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_matrix_table");
        });
    }

    function setupFormAspek(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectAspek.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_aspek; // replace name with the property used for the text
                o.text = o.aspek; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectAspek.val(param.id_aspek).trigger('change');
    }

    function initializeParamAspek() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_aspek/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormAspek(r1);
        }).fail(resolver.fail);
    }

    function setupFormGrafik(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectGrafik.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_aspek; // replace name with the property used for the text
                o.text = o.aspek; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectAspek.val(param.id_aspek).trigger('change');
    }

    function initializeParamGrafik() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_aspek/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormGrafik(r1);
        }).fail(resolver.fail);
    }


})();