(function () {

    const common = new Common();
    common.setTitle("Report Sales PJP");
    // declare dom
    let uiForm = $("#fm-report-sales-pjp");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectSalesID = $("#salesid-id"); 
    
    initializeParam();

    function initializeParam() {
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        uiBtnPreview.click(function () {
            open_preview();
        });

        uiBtnDownload.click(function () {
            save_xls();
        });

        load_salesid();
    }

    function open_preview() {
		
        let salesmanid = uiSelectSalesID.val();

        $.ajax({
            type:"POST",
            dataType: "html",
            url: common.baseURL("rep_sales_pjp/load_view_salespjp"),
            data : "salesmanid="+salesmanid,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
        });
        
    }

    function save_xls() {
        
        let salesmanid = uiSelectSalesID.val();

        common.direct("rep_sales_pjp/download_to_excel_spreadsheet?salesmanid="+salesmanid);
    }

    function load_salesid() {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), function (res) {
            uiSelectSalesID.empty();
            uiSelectSalesID.select2({
                placeholder: "Select Sales ID",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid+' - '+o.nama_salesman;
                    return o;
                }),
            });
            
            uiSelectSalesID.val(null).trigger('change');
            common.loadingClose();
        });
    }

})();