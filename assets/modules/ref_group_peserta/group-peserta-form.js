(function () {

    const common = new Common();
    common.setTitle("Group Responden");
    // declare dom
    let uiForm = $("#fm-group-peserta");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectPeserta = $("#id-karyawan");
    // define from *-content.js
    let param = common.getCookie("module.group.peserta.update");
	let isUpdate = param !== undefined; // flag create update

    initialize();
	initializeParamPeserta();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_group_peserta/create") : common.baseURL("ref_group_peserta/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_group_peserta",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_group', value: param.id_group});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_group_peserta");
        });
    }

    function setupFormPeserta(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectPeserta.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_karyawan; // replace name with the property used for the text
                o.text = o.nama_karyawan; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
			var gKaryawan = param.group_karyawan;
			var gKaryawanArr = gKaryawan.split(',');
			uiSelectPeserta.val(gKaryawanArr).trigger('change');
		}
    }

    function initializeParamPeserta() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_karyawan/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormPeserta(r1);
        }).fail(resolver.fail);
    }



})();