(function () {

    const common = new Common();
    common.setTitle("Report Promo");
    // declare dom
    let uiForm = $("#fm-report-stock");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectAccount = $("#account-id");   
    let uiSelectProduct = $("#productid-id");
    
    //let uiStartPeriode = $("#start_periode"); 
    //let uiEndPeriode = $("#end_periode"); 
    //let uiTblReport = $("#tbl-content"); 
    
    // define from *-content.js
    //let param = common.getCookie("module.report.promo.update");
    let paramsession = common.getCookie("session");
    
    initializeParam();

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            //$.post(common.baseURL("rep_promo/load_promo"), filter.build()),
            $.post(common.baseURL("rep_promo/load_account"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);

        uiBtnPreview.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Sub Channel / Account harus di isi...!');
            }else if (uiSelectProduct.val()===null){
                //uiAlertNotif.show();
                alert ('Product harus di isi...!');
            }else{
                open_preview();
            }
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Sub Channel / Account harus di isi...!');
            }else if (uiSelectProduct.val()===null){
                //uiAlertNotif.show();
                alert ('Product harus di isi...!');
            }else{
                save_xls();
            }
        });

        /*$(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });*/

        uiSelectAccount.on('select2:select', function (e) {
            accountSelected = e.params.data;
            //alert(accountSelected["idaccount"]);
            loadProduct(accountSelected);
        });

        uiSelectProduct.select2({
            placeholder: 'Select Product',
            allowClear: true,
        });
        
        /*uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });*/

    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectAccount.select2({
            placeholder: 'Select Account',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.classid; // replace name with the property used for the text
                o.text = o.nama_class; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectAccount.val(null).trigger('change');
    }    

    function open_preview() {
		
        var classid = uiSelectAccount.val();
        var productid = uiSelectProduct.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;

            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("rep_stock/view_stock"),
                data : "idaccount="+classid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession,
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
        //alert(data.idaccount);
        $.post(common.baseURL("rep_stock/load_product"), {classid: data.classid}, function (res) {
            uiSelectProduct.empty();
            uiSelectProduct.select2({
                placeholder: "Select Product",
                allowClear: true,
                multiple: true,
                tokenSeparators: [','],
                data: $.map(res.rows, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = o.nama_invoice;
                    return o;
                }),
            });
            common.loadingClose();
        });

    }

    function save_xls() {
		
        var idpromo = uiSelectPromo.val();
        var start = uiStartPeriode.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        //idpromo = idpromo.replace(",", "|");
        //var url = encodeURI();
        if (uiSelectTypePromo.val()==='DDJ'){
            common.direct("rep_promo/savetoxlsx/"+idpromo+"/"+start+"/"+idjabatan+"/"+usersession);
        }else{
            common.direct("rep_promo/savetoxlsx_gimmick/"+idpromo+"/"+start+"/"+idjabatan+"/"+usersession);
        }
        /*
        $.ajax({
            type:"POST",
            dataType: "html",
            beforeSend : function() {
                //$("#map-content").html('Populating data, please wait..');
            },
            url: common.baseURL("rep_promo/savetoxls"),
            data : "idpromo="+idpromo+"&start="+start+"&end="+end+"&idjabatan="+idjabatan+"&usersession="+usersession,
            success:function(res){
                response = res;
                $('#tbl-content').html(response);
            },
            error:function(){
                alert("Load failed");
            }
        });
        */
    }

})();