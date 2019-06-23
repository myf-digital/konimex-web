(function () {

    const common = new Common();
    common.setTitle("Rdg");
    // declare dom
    let uiForm = $("#fm-rdg");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSatker = $("#id-satker");
	let uiSelectMatrix = $("#id-matrix");
	let uiTanggalPicker = $("#id-tanggal");
	// define from *-content.js
    let param = common.getCookie("module.rdg.update");
	let isUpdate = param !== undefined; // flag create update

	setupFormUI();
    initialize();
	initializeParamSatker();
	initializeParamMatrix();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_rdg/create") : common.baseURL("ref_rdg/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_rdg",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_rdg', value: param.id_rdg});
                }
                let tanggal = moment(uiTanggalPicker.datepicker('getDate'));
                form = common.replaceFormValue(form, [
                    {key:"tanggal",value:tanggal.format("YYYY-MM-DD")}
                ]);
				
                return true; // MANDATORY!
            }
            ,rules: {
                nama_rdg: {
                    required: true
                },
                tanggal: {
                    required: true
                },
                id_satker: {
                    required: true
                },
                id_Matrix: {
                    required: true
                }
            }
			});
        if (isUpdate) {
            uiTanggalPicker.datepicker('update', new Date(moment(param.tanggal, "YYYY-MM-DD")));
        }
        uiBtnCancel.click(function () {
            common.direct("ref_rdg");
        });
    }
	
    function setupFormUI() {
        uiSelectSatker.select2({multiple: false, placeholder: 'Select value...'});
        //uiSelectSatker.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
        //uiSelectAspek.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
        uiSelectMatrix.select2({multiple: false, placeholder: 'Select value...'});
        uiTanggalPicker.datepicker({
            format: 'dd MM yyyy'
        });
    }

    function setupFormSatker(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectSatker.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_satker; // replace name with the property used for the text
                o.text = o.kode_satker + "(" + o.satker + ")"; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectSatker.val(param.id_satker).trigger('change');

    }

    function initializeParamSatker() {
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
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormMatrix(r1);
        }).fail(resolver.fail);
    }
	
})();