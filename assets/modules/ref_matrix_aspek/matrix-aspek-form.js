(function () {

    const common = new Common();
    common.setTitle("Matrix Aspek");
    // declare dom
    let uiForm = $("#fm-matrix-aspek");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectMatrix = $("#id-matrix");
	let uiSelectAspek = $("#id-aspek");
    // define from *-content.js
    let param = common.getCookie("module.matrix.aspek.update");

    initialize();
	initializeParamAspek();
	initializeParamMatrix();
	
    function initialize() {
        let url = param === undefined ? common.baseURL("ref_matrix_aspek/create") : common.baseURL("ref_matrix_aspek/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_matrix_aspek",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_matrix_aspek', value: param.id_matrix_aspek});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_matrix_aspek");
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
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormAspek(r1);
        }).fail(resolver.fail);
    }

    function setupFormMatrix(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectMatrix.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_matrix; // replace name with the property used for the text
                o.text = o.matrix_table; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) uiSelectMatrix.val(param.id_matrix).trigger('change');
    }

    function initializeParamMatrix() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_matrix_table/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormMatrix(r1);
        }).fail(resolver.fail);
    }
	
})();