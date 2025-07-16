(function () {

    const common = new Common();
    common.setTitle("Event Product Knowledge");
    // declare dom
    let uiForm = $("#fm-product-knowledge");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiStartPeriode = $("#start_period"); 
    let uiEndPeriode = $("#end_period"); 

    // define from *-content.js
    let param = common.getCookie("module.product.knowledge.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    if (isUpdate) {
		if (param.end_period!=null){
			let startArr = param.start_period.split(' ');
			let endArr = param.end_period.split(' ');

			param.start_period = startArr[0];
			param.start_time = startArr[1];
			param.end_period = endArr[0];
			param.end_time = endArr[1];
			}
	}

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("event_product_knowledge/create") : common.baseURL("event_product_knowledge/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "event_product_knowledge",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_event', value: param.id_event});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("event_product_knowledge");
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom left"
        });

        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true
        });
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });


    }


})();