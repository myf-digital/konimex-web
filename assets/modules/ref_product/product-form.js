(function () {

    const common = new Common();
    common.setTitle("Product");
    // declare dom
    let uiForm = $("#fm-product");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectGroupProduk = $("#group_product-id");
    let uiSelectKategoriProduk = $("#category_product-id");
    let uiSelectStatus = $("#status-id");
    let uiSelectBrandid = $("#brandid-id");

    // define from *-content.js
    let param = common.getCookie("module.product.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_product/create") : common.baseURL("ref_product/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_product",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'productid', value: param.productid});
                }
                return true; // MANDATORY!
            },
            rules: {
                productid: {
                    required: true/*,
					remote	 : {
						url		: common.baseURL("ref_customer/cek_kode_outler"),
						type	: "POST",
						data: {
							kode_outlet: function() {
								return $("#kode_outlet").val();
							},
							kode_outlet : $("#kode_outlet").val()
						}
					}*/
                },
                nama_invoice: {
                    required: true
                },
                group_product: {
                    required: true
                },
                category_product: {
                    required: true
                },
                brandid: {
                    required: true
                },
                status: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_product");
        });

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_group_prod"}, filter.build()),
            $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_category_prod"}, filter.build()),
            $.post(common.baseURL("api_v1/call_param_key"), {parkey:"key_status_product"}, filter.build()),
            $.post(common.baseURL("api_v1/call_product_brand"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2, r3, r4) {
            common.loadingClose();
            setupForm(r1[0], r2[0], r3[0], r4[0]);
        }).fail(resolver.fail);

    }

    function setupForm(r1, r2, r3, r4) {
        let rows1 = r1.result;
        let rows2 = r2.result;
        let rows3 = r3.result;
        let rows4 = r4.result;

        uiSelectGroupProduk.select2({
            placeholder: 'Select Group',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.value; // replace name with the property used for the text
                o.text = o.desc; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectKategoriProduk.select2({
            placeholder: 'Select Group',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.value; // replace name with the property used for the text
                o.text = o.desc; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectStatus.select2({
            placeholder: 'Select Group',
            allowClear: true,
            data: $.map(rows3, function (o) {
                o.id = o.value; // replace name with the property used for the text
                o.text = o.desc; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectBrandid.select2({
            placeholder: 'Select Brand',
            allowClear: true,
            data: $.map(rows4, function (o) {
                o.id = o.brandid; // replace name with the property used for the text
                o.text = o.brand; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectGroupProduk.val(param.group_product).trigger('change');
            uiSelectKategoriProduk.val(param.category_product).trigger('change');
            uiSelectStatus.val(param.status).trigger('change');
            uiSelectBrandid.val(param.brandid).trigger('change');
        }else{
            uiSelectGroupProduk.val(null).trigger('change');
            uiSelectKategoriProduk.val(null).trigger('change');
            uiSelectStatus.val(null).trigger('change');
            uiSelectBrandid.val(null).trigger('change');
        }

    }

})();