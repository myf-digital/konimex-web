(function () {

    const common = new Common();
    common.setTitle("Absen");
    // declare dom
    let uiForm = $("#fm-absen");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiSelectStatus = $("#status-id");
    let uiFlagAdj = $("#flag-adjust");
    let uiCheckboxAdjPJP = $("#flag_adjust-id");
	
    // define from *-content.js
    let param = common.getCookie("module.absen.update");
    let isUpdate = param !== undefined; // flag create update
    let paramsession = common.getCookie("session");

    if (isUpdate) {
		if (param.checkin!=null){
			let checkinArr = param.checkin.split(' ');
			let checkoutArr = param.checkout.split(' ');

			param.checkin_date = checkinArr[0];
			param.checkin_time = checkinArr[1];
			param.checkout_date = checkoutArr[0];
			param.checkout_time = checkoutArr[1];
			}
	}

    initialize();

    function initialize() {

        let url = param === undefined ? common.baseURL("ref_absen_salesman/create") : common.baseURL("ref_absen_salesman/update");

        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_absen_salesman",
            beforeSubmit: function (form, options) {
                return true; // MANDATORY!
            },
            rules: {
                periode: {
                    required: true
                },
                salesmanid: {
                    required: true
                }
            }
        });
		
        loadSalesman({usersession: paramsession.username, idjabatan: paramsession.idjabatan,restrict_level: paramsession.restrict_level});

        uiBtnCancel.click(function () {
            common.direct("ref_absen_salesman");
        });
		
        if (isUpdate) {

        }else{

        }

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom right"
        }).datepicker("setDate", new Date())
        .on('change', function(){
            $('.datepicker').hide();
        });

        $('.clockpicker').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true
        });

        uiSelectStatus.select2({
            placeholder: "Select Status"
        });

		uiCheckboxAdjPJP.change(function(){
            if($(this).is(':checked')) {
                //do something
				uiFlagAdj.val("1").trigger('change');
            }else{
				uiFlagAdj.val("0").trigger('change');
            }
        });

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_gff_admin"), {idjabatan:data.idjabatan, usersession:data.usersession,restrict_level: data.restrict_level}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Salesman",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman + " - " + o.tipe_sales;
                    return o;
                }),
            });
            
            if (isUpdate) {
                if (param.flag_adjust==1) {
                    uiCheckboxAdjPJP.prop("checked","checked");
                    } else {
                    uiCheckboxAdjPJP.prop("checked","");
                    }
				
                uiSelectSalesman.val(param.salesmanid).trigger('change');
            }else{
                uiSelectSalesman.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();