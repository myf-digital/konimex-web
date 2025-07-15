(function () {

    const common = new Common();
    common.setTitle("Rdg");
    // declare dom
    let uiForm = $("#fm-rdg");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSatker = $("#id-satker");
	let uiSelectAspek = $("#id-aspek");
	let uiTanggalPicker = $("#id-tanggal");
	// define from *-content.js
    let param = common.getCookie("module.rdg.update");
	let isUpdate = param !== undefined; // flag create update

	setupFormUI();
    initialize();
	initializeParamSatker();
	initializeParamAspek();

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
                id_aspek: {
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
        uiSelectAspek.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
        uiTanggalPicker.datepicker({
            format: 'dd MM yyyy'
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });
    }

    function setupFormSatker(r1, r2) {
        let rows = [];
        //rows.push({id_satker: "", kode_satker:"", satker: "Pilih Satuan Kerja"});
        rows = rows.concat(r1.rows);
        /*if (isUpdate) { // update
            rows = rows.filter(function (val) {
                return val.id_satker !== param.id_satker;
            })
        }*/
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
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormSatker(r1);
        }).fail(resolver.fail);
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
	
})();