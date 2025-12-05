(function () {
    const common = new Common();
    common.setTitle("Report SLOB");
    // declare dom
    let uiForm = $("#fm-report-return");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnTest = $("#btn-test");
    let uiSelectBrand = $("#brand-id");
    let uiSelectProduct = $("#product-id");

    let paramsession = common.getCookie("session");

    initializeParam();

    function initializeParam() {
        let resolver = new HttpResolver();
        let filter = new Filter();

        $.when(
            $.post(common.baseURL("api_v1/call_product_brand"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            common.loadingClose();
            setupForm(data);
        }).fail(resolver.fail);

        uiBtnPreview.click(function () {
            open_preview();
        });

        uiBtnTest.click(function () {
            save_xls();
        });

        uiSelectBrand.on('select2:select', function (e) {
            brandSelected = e.params.data;
            loadProduct(brandSelected);
        });

        uiSelectProduct.select2({
            placeholder: 'Select Product',
            allowClear: true
        });
    }

    function setupForm(r1) {
        let rows1 = r1.result;

        uiSelectBrand.select2({
            placeholder: 'Select Brand',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.brandid; // replace name with the property used for the text
                o.text = o.brand; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectBrand.val(null).trigger('change');
    }

    function loadProduct(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_product_filter_brand"), {brandid: data.brandid }, function (res) {
            uiSelectProduct.empty();
            uiSelectProduct.select2({
                placeholder: "Select Product",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = o.productid+' - '+o.nama_invoice;
                    return o;
                }),
            });
            uiSelectProduct.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function open_preview() {

		let brandid = uiSelectBrand.val();
        let productid = uiSelectProduct.val();

        common.loading();

        $.ajax({
            type:"POST",
            dataType: "html",
            url: common.baseURL("rep_return_detector/load_view_detector_national"),
            data : "brandid="+brandid+"&productid="+productid,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);

                common.loadingClose();
            },
            error:function(){
                alert("Load failed");

                common.loadingClose();
            }
        });
        
    }

})();