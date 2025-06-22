(function () {

    const common = new Common();
    common.setTitle("Promo Active");
    // declare dom
    let uiForm = $("#fm-promo-active");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectClass = $("#classid-id");   
    let uiSelectTipepromo = $("#tipepromo-id");   
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiProduct = $("#productid-id");

    // define from *-content.js
    let param = common.getCookie("module.promo.active.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("mapping_promo_active/create") : common.baseURL("mapping_promo_active/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "mapping_promo_active",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'idpromo', value: param.idpromo});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("mapping_promo_active");
        });


        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

        if (isUpdate) {
            let sval = {tipepromo:param.tipepromo};
            loadParamKey(sval);
            loadProduct();
            uiSelectTipepromo.val(param.tipepromo).trigger('change');
        }else{
            let sval = {tipepromo:null};
            loadProduct();
            loadParamKey(sval);
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("ref_customer_class/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            common.loadingClose();
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);

    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectClass.select2({
            placeholder: 'Sub Channel / Account',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.classid; // replace name with the property used for the text
                o.text = o.nama_class; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectClass.val(param.classid).trigger('change');
        }else{
            uiSelectClass.val(null).trigger('change');
        }

    }    

    function loadParamKey(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_tipe_promo"}, function (res) {
            uiSelectTipepromo.empty();
            uiSelectTipepromo.select2({
                placeholder: "Select Type Promo",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.value; // replace name with the property used for the text
                    o.text = o.desc;
                    return o;
                }),
            });
            if (isUpdate) {
                uiSelectTipepromo.val(param.tipepromo).trigger('change');
            }else{
                uiSelectTipepromo.val(data).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadProduct() {
        common.loading();
        $.post(common.baseURL("api_v1/call_product"), function (res) {
            uiProduct.empty();
            let vmin = 1;
            let vmax = 10;
            if(isUpdate) {
                //do something
                let arrsku = param.productid ?? '';
                let varsku = arrsku.split(',');
            }

            uiProduct.select2({
                placeholder: "Select Product List",
                allowClear: true,
                minimumSelectionLength: vmin,
                maximumSelectionLength: vmax,
                allowClear: true,
                multiple: true,
                tokenSeparators: [','],
                data: $.map(res.result, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = "( "+o.productid + " ) " +o.nama_invoice;
                    return o;
                }),
            });
            
            if (isUpdate) {
                let arrsku = param.productid ?? '';
                let varsku = arrsku.split(',');
                let rarrsku=[];

                var i;
                for (i = 0; i < varsku.length; i++) {
                    rarrsku.push(varsku[i]);
                }

                uiProduct.val(rarrsku).trigger('change');

            }else{
                uiProduct.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }
    
})();