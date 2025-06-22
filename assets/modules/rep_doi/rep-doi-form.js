(function () {

    const common = new Common();
    common.setTitle("Report DOI");
    // declare dom
    let uiForm = $("#fm-report-doi");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");

    let uiSelectMonth = $("#month-id");
    let uiSelectYear = $("#year-id");
    let uiSelectBrand = $("#brand-id");
    let uiSelectSku = $("#sku-id");
    let uiSelectRegional = $("#regional-id");
    let uiSelectCity = $("#city-id");

    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("api_v1/call_product_brand"), filter.build()),
            $.post(common.baseURL("rep_doi/load_regional"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1, r2) {
            common.loadingClose();
            console.log("then");
            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);

        uiBtnPreview.click(function () {

            if (uiSelectYear.val()===null){
                alert ('Tahun harus di isi...!');
            }else if (uiSelectMonth.val()===null){
                alert ('Bulan harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            if (uiSelectYear.val()===null){
                alert ('Tahun harus di isi...!');
            }else if (uiSelectMonth.val()===null){
                alert ('Bulan harus di isi...!');
            }else{
                save_xls();
            }
        });

        uiSelectBrand.on('select2:select', function (e) {
            brandSelected = e.params.data;
            loadProduct(brandSelected);
        });

        uiSelectRegional.on('select2:select', function (e) {
            regional = e.params.data;

            loadArea(regional);
        });
    }

    function setupForm(r1, r2) {
        let rows1 = r1.result;
        let rows2 = r2.rows;

        uiSelectBrand.select2({
            placeholder: 'Select Brand',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.brandid; // replace name with the property used for the text
                o.text = o.brand; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectRegional.select2({
            placeholder: 'Select Regional',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.regionalid; // replace name with the property used for the text
                o.text = o.nama_regional; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectYear.select2({
            placeholder: 'Select Year Period'
        });

        uiSelectMonth.select2({
            placeholder: 'Select Month Period'
        });

        uiSelectSku.select2({
            placeholder: 'Select SKU',
            allowClear: true
        });

        uiSelectCity.select2({
            placeholder: 'Select City',
            allowClear: true
        });

        uiSelectBrand.val(null).trigger('change');

        uiSelectRegional.val(null).trigger('change');
    }    

    function open_preview() {
		
        var year = uiSelectYear.val();
        var month = uiSelectMonth.val();
        var brand = uiSelectBrand.val();
        var sku = uiSelectSku.val();
        var regionalid = uiSelectRegional.val();
        var areaid = uiSelectCity.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_doi/open_detail"),
            data : "year="+year+"&month="+month+"&brand="+brand+"&sku="+sku+"&regionalid="+regionalid+"&areaid="+areaid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level,
            success:function(res){
                response = res;
                //$('div .modal-header .modal-title').text('Detail Productifity Sales');            
                $('#tbl-content').html(response);
                //$("#modal_detail").modal('show');
            },
            error:function(){
                alert("Load failed");
            }
        });
        
    }

    function loadProduct(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_product_filter_brand"), {brandid: data.brandid }, function (res) {
            uiSelectSku.empty();
            uiSelectSku.select2({
                placeholder: "Select SKU",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = o.productid+' - '+o.nama_invoice;
                    return o;
                }),
            });
            uiSelectSku.val(null).trigger('change');
            common.loadingClose();
        });
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("rep_doi/load_city"), {regionalid: data.regionalid}, function (res) {
            uiSelectCity.empty();
            uiSelectCity.select2({
                placeholder: "Select City",
                allowClear: true,
                data: $.map(res.rows, function (o) {
                    o.id = o.subareaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            uiSelectCity.val(null).trigger('change');
            common.loadingClose();
        });

    }

    function save_xls() {
		
        var year = uiSelectYear.val();
        var month = uiSelectMonth.val();
        var brand = uiSelectBrand.val();
        var sku = uiSelectSku.val();
        var regionalid = uiSelectRegional.val();
        var areaid = uiSelectCity.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        
        common.direct("rep_doi/savetoxlsx/"+year+"/"+month+"/"+brand+"/"+sku+"/"+regionalid+"/"+areaid+"/"+usersession+"/"+restrict_level+"/"+idjabatan);
    }

})();