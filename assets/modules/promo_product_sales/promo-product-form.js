(function () {

    const common = new Common();
    common.setTitle("Promo TPE");
    // declare dom
    let uiForm = $("#fm-promo-product");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 
    let uiProduct = $("#productid-id");
    let uiSelectType = $("#typeid-id");

    // define from *-content.js
    let param = common.getCookie("module.promo.product.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initializeParam();
    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("promo_product_sales/create") : common.baseURL("promo_product_sales/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "promo_product_sales",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("promo_product_sales");
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
            loadProduct();
        }else{
            loadProduct();
        }

    }    

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
            
        $.post(common.baseURL("ref_customer_type/load"), filter.build())
            .done(function(res){
                common.loadingClose();
                //console.log("response:", res); // DEBUG
                setupForm(res);                // <-- Langsung kirim
            })
            .fail(resolver.fail);
    }

    function setupForm(r2) {
        let rows2 = r2.rows;
        uiSelectType.select2({
            placeholder: 'Select Cluster',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.typeid; // replace name with the property used for the text
                o.text = o.nama_type; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectType.val(param.typeid).trigger('change');
        }else{
            uiSelectType.val(null).trigger('change');
        }

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
    
})();