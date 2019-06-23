(function () {

    const common = new Common();
    common.setTitle("Event");
    // declare dom
    let uiForm = $("#fm-event");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectRdg = $("#id-rdg");
    let uiSelectPeserta = $("#id-karyawan");
	let uiTanggalPicker = $("#id-tanggal");
	
    // define from *-content.js
    let param = common.getCookie("module.event.update");
	let isUpdate = param !== undefined; // flag create update

    initialize();
	initializeParamRdg();
	initializeParamPeserta();
	setupFormUI();
	
    function initialize() {
        let url = param === undefined ? common.baseURL("ref_event/create") : common.baseURL("ref_event/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_event",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_event', value: param.id_event});
                }
                let tanggal = moment(uiTanggalPicker.datepicker('getDate'));
                form = common.replaceFormValue(form, [
                    {key:"tanggal",value:tanggal.format("YYYY-MM-DD")}
                ]);
				
                return true; // MANDATORY!
            }
        });
        if (isUpdate) {
            uiTanggalPicker.datepicker('update', new Date(moment(param.tanggal, "YYYY-MM-DD")));
        }
        uiBtnCancel.click(function () {
            common.direct("ref_event");
        });
    }

    function setupFormUI() {
        uiSelectRdg.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
        uiSelectPeserta.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
        uiTanggalPicker.datepicker({
            format: 'dd MM yyyy'
        });
    }

    function setupFormRdg(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectRdg.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_rdg; // replace name with the property used for the text
                o.text = o.nama_rdg; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) uiSelectRdg.val(param.id_rdg).trigger('change');
    }

    function initializeParamRdg() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_rdg/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormRdg(r1);
        }).fail(resolver.fail);
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

        if (isUpdate) uiSelectPeserta.val(param.id_karyawan).trigger('change');
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