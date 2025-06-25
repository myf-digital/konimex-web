(function () {

    const common = new Common();
    common.setTitle("Event");
    // declare dom
    let uiForm = $("#fm-event-publish");
    let uiBtnCancel = $("#btn-cancel-form");
	let uiSelectRdgPublish = $("#id-rdg-publish");
	
    // define from *-content.js
    let param = common.getCookie("module.event.update_publish");
	let isUpdate = param !== undefined; // flag create update

    initialize();
	initializeParamRdgPublish();
	setupFormUI();
	
    function initialize() {
        let url = param === undefined ? common.baseURL("ref_event/create") : common.baseURL("ref_event/update_publish");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_event",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_event', value: param.id_event});
                };
				
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("ref_event");
        });
    }

    function setupFormUI() {
        uiSelectRdgPublish.select2({multiple: true, placeholder: 'Select value...', tokenSeparators: [',']});
    }

    function setupFormRdgPublish(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectRdgPublish.select2({
			closeOnSelect : false,
			placeholder : "Select Publish",
			allowHtml: true,
			allowClear: true,
			tags: true,
            data: $.map(rows, function (o) {
                o.id = o.id_rdg; // replace name with the property used for the text
                o.text = o.nama_rdg; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
			let gRdg = param.group_rdg_publish;
			let gRdgArr = gRdg.split(',');
			uiSelectRdgPublish.val(gRdgArr).trigger('change');
		}
    }

    function initializeParamRdgPublish() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        console.log(param.build());
		$.when(
            $.post(common.baseURL("ref_event/load_mapping_rdg"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormRdgPublish(r1);
        }).fail(resolver.fail);
    }

})();