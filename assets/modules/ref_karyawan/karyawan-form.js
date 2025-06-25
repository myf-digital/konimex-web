(function () {

    const common = new Common();
    common.setTitle("Karyawan");
    // declare dom
    let uiForm = $("#fm-karyawan");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectSatker = $("#id-satker");
    // define from *-content.js
    let param = common.getCookie("module.karyawan.update");
    let isUpdate = param !== undefined; // flag create update
	
	setupFormUI();
    initialize();
    initializeParam();
	
    function initialize() {
        let url = param === undefined ? common.baseURL("ref_karyawan/create") : common.baseURL("ref_karyawan/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_karyawan",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_karyawan', value: param.id_karyawan});
                }
                return true; // MANDATORY!
            },
            rules: {
                nama_karyawan: {
                    required: true
                },
                nip: {
                    required: true
                },
                telepon: {
                    required: true
                },
                email: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_karyawan");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_satker/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
    }
	
    function setupFormUI() {
        uiSelectSatker.select2({
            placeholder: 'Pilih Satker'
        });
    }

    function setupForm(r1, r2) {
        let rows = [];
        rows.push({id_satker: 0, kode_satker:"", satker: "Pilih SatKer"});
        rows = rows.concat(r1.rows);
        /*if (isUpdate) { // update
            rows = rows.filter(function (val) {
                return val.id_satker !== param.id_satker;
            })
        }*/
        uiSelectSatker.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_satker; // replace name with the property used for the text
                o.text = o.kode_satker+"("+o.satker+")"; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectSatker.val(param.id_satker).trigger('change');

    }
	
	
	
})();