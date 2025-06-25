(function () {

    const common = new Common();
    common.setTitle("Sku Active");
    // declare dom
    let uiForm = $("#fm-sku-active");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectAccount = $("#idaccount-id");
    let uiSelectProduct = $("#productid-id");
    // define from *-content.js
    let param = common.getCookie("module.sku.active.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("mapping_sku_active/create") : common.baseURL("mapping_sku_active/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "mapping_sku_active",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                return true; // MANDATORY!
            },
            rules: {
                idaccount: {
                    required: true
                },
                productid: {
                    required: true
                }                
            }
        });

        uiSelectProduct.select2({
            placeholder: 'Select Product',
            minimumSelectionLength: 1,
            maximumSelectionLength: 1000,
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        
        uiBtnCancel.click(function () {
            common.direct("mapping_sku_active");
        });
    }
    
    
    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("api_v1/call_account_outlet"), filter.build()),
            $.post(common.baseURL("api_v1/call_product"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1, r2) {
            common.loadingClose();
            console.log("then");
            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);

    }


    function setupForm(r1, r2) {
        let rows1 = r1.result;
        let rows2 = r2.result;

        uiSelectAccount.select2({
            placeholder: 'Select Account',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.idaccount; // replace name with the property used for the text
                o.text = o.account; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectProduct.select2({
            placeholder: 'Select Product',
            allowClear: true,
            minimumSelectionLength: 1,
            maximumSelectionLength: 100,
            data: $.map(rows2, function (o) {
                o.id = o.productid; // replace name with the property used for the text
                o.text = '('+o.productid+') - '+o.nama_invoice; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectAccount.val(param.idaccount).trigger('change');
         	let gProdid = param.group_productid;
			let gProdArr = gProdid.split(',');
			uiSelectProduct.val(gProdArr).trigger('change');
        }else{
            uiSelectAccount.val(null).trigger('change');
            uiSelectProduct.val(null).trigger('change');
        }

    }


})();