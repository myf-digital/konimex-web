(function () {

    const common = new Common();
    common.setTitle("Product Competitor");
    // declare dom
    let uiForm = $("#fm-product-competitor");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectKategoriProduk = $("#category-id");
    let uiSelectStatus = $("#status-id");

    // define from *-content.js
    let param = common.getCookie("module.product.competitor.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_product_competitor/create") : common.baseURL("ref_product_competitor/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_product_competitor",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'productid', value: param.productid});
                }
                return true; // MANDATORY!
            },
			rules: {
                nama_invoice: {
                    required: true
                },
                category: {
                    required: true
                },
                status: {
                    required: true
                }
            }        
        });
        uiBtnCancel.click(function () {
            common.direct("ref_product_competitor");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_cat_competitor"}, filter.build()),
            $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_status_product"}, filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1,r2) {
            common.loadingClose();
            setupForm(r1[0],r2[0]);
        }).fail(resolver.fail);
    }

    function setupForm(r1,r2) {
        let rows1 = r1.result;
        let rows2 = r2.result;

        uiSelectKategoriProduk.select2({
            placeholder: 'Select Kategori',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.value; // replace name with the property used for the text
                o.text = o.desc; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectStatus.select2({
            placeholder: 'Select Status',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.value; // replace name with the property used for the text
                o.text = o.desc; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectKategoriProduk.val(param.category).trigger('change');
            uiSelectStatus.val(param.status).trigger('change');
        }else{
            uiSelectKategoriProduk.val(null).trigger('change');
            uiSelectStatus.val(null).trigger('change');
        }

    }

})();