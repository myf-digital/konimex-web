(function () {

    const common = new Common();
    common.setTitle("DRC Dashboard");
    // declare dom
    let uiForm = $("#fm-report-promo");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnPreviewPa = $("#btn-preview-form-pa");
    let uiBtnDownloadPa = $("#btn-download-form-pa");
    let uiBtnDownloadSOS = $("#btn-download-form-sos");
    let uiBtnPreviewSos = $("#btn-preview-form-sos");
    let uiBtnPreviewSlob = $("#btn-preview-form-slob");
    let uiBtnDownload = $("#btn-download-form");
    let uiSelectTahun = $("#tahun-id");
    let uiSelectBulan = $("#bln-id");
    let uiSelectTahunPa = $("#tahun-id-pa");
    let uiSelectBulanPa = $("#bln-id-pa");
    let uiSelectTahunSos = $("#tahun-id-sos");
    let uiSelectBulanSos = $("#bln-id-sos");
    let uiSelectTanggal = $("#tgl-id");
    let uiSelectClass = $("#classid-id-pa");   
    let uiSelectClassSos = $("#classid-id-sos");   
    let uiSelectSku = $("#productid-id");   
    let uiSelectBrand = $("#brandid-id");
    let uiSelectBrandSlob = $("#brandid-slob-id");
    let uiSelectSkuSlob = $("#productid-slob-id");   

    let uiBtnMP = $("#btn-mp-form");
    let uiBtnPA = $("#btn-pa-form");
    let uiBtnSOS = $("#btn-sos-form");
    let uiBtnSlob = $("#btn-slob-form");

    let uiLinkHc = $("#hcnational");

    document.getElementById("id-tbpa").style.display= "none";
    document.getElementById("id-tbsos").style.display = "none";
    document.getElementById("id-tbslob").style.display = "none";

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

		console.log(filter);
        uiBtnMP.click(function (){
            document.getElementById("id-tbmp").style.display="table"; 
            document.getElementById("id-tbpa").style.display="none"; 
            document.getElementById("id-tbsos").style.display="none";
            document.getElementById("id-tbslob").style.display="none";
            $('#tbl-content').html("");
        });
        uiBtnPA.click(function (){
            document.getElementById("id-tbmp").style.display="none"; 
            document.getElementById("id-tbpa").style.display="table"; 
            document.getElementById("id-tbsos").style.display="none";
            document.getElementById("id-tbslob").style.display="none";
            $('#tbl-content').html("");

            $.when(
                $.post(common.baseURL("api_v1/call_product_brand"), filter.build()),
                $.post(common.baseURL("ref_customer_class/load"), filter.build()),
            ).done(function (data, textStatus, jqXHR) {
                console.log("done");
                //console.log(d);
            }).then(function (r1, r2) {
                common.loadingClose();
                console.log("then");
                setupForm(r1[0], r2[0]);
            }).fail(resolver.fail);

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
        
                uiSelectClass.select2({
                    placeholder: 'ALL Sub Channel',
                    allowClear: true,
                    data: $.map(rows2, function (o) {
                        o.id = o.classid; // replace name with the property used for the text
                        o.text = o.nama_class; // replace name with the property used for the text
                        return o;
                    }),
					multiple: true,
					tokenSeparators: [',']
                });

                uiSelectClassSos.select2({
                    placeholder: 'Select Sub Channel',
                    allowClear: true,
                    data: $.map(rows2, function (o) {
                        o.id = o.classid; // replace name with the property used for the text
                        o.text = o.nama_class; // replace name with the property used for the text
                        return o;
                    }),
                });

                uiSelectBrand.val(null).trigger('change');
                uiSelectClass.val(null).trigger('change');
                uiSelectClassSos.val(null).trigger('change');
        
            }
        });
        uiBtnSOS.click(function (){
            document.getElementById("id-tbmp").style.display="none"; 
            document.getElementById("id-tbpa").style.display="none"; 
            document.getElementById("id-tbsos").style.display="table";    
            document.getElementById("id-tbslob").style.display="none";
            $('#tbl-content').html("");

            $.when(
                $.post(common.baseURL("ref_customer_class/load"), filter.build()),
            ).done(function (data, textStatus, jqXHR) {
                console.log("done");
                //console.log(d);
            }).then(function (r3) {
                common.loadingClose();
                console.log("then");
                setupFormSos(r3);
            }).fail(resolver.fail);

            function setupFormSos(r3) {
                let rows3 = r3.rows;

                uiSelectClassSos.select2({
                    placeholder: 'All Account',
                    allowClear: true,
                    data: $.map(rows3, function (o) {
                        o.id = o.classid; // replace name with the property used for the text
                        o.text = o.nama_class; // replace name with the property used for the text
                        return o;
                    }),
                });

                uiSelectClassSos.val(null).trigger('change');
        
            }
            
        });

        uiBtnSlob.click(function (){
            document.getElementById("id-tbmp").style.display="none"; 
            document.getElementById("id-tbpa").style.display="none"; 
            document.getElementById("id-tbsos").style.display="none";
            document.getElementById("id-tbslob").style.display="table";
            $('#tbl-content').html("");

            $.when(
                $.post(common.baseURL("api_v1/call_product_brand"), filter.build()),
                $.post(common.baseURL("ref_customer_class/load"), filter.build()),
            ).done(function (data, textStatus, jqXHR) {
                console.log("done");
                //console.log(d);
            }).then(function (r1, r2) {
                common.loadingClose();
                console.log("then");
                setupForm(r1[0], r2[0]);
            }).fail(resolver.fail);

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

                uiSelectBrandSlob.select2({
                    placeholder: 'Select Brand',
                    allowClear: true,
                    data: $.map(rows1, function (o) {
                        o.id = o.brandid; // replace name with the property used for the text
                        o.text = o.brand; // replace name with the property used for the text
                        return o;
                    }),
                });
                
                uiSelectClass.select2({
                    placeholder: 'ALL Sub Channel',
                    allowClear: true,
                    data: $.map(rows2, function (o) {
                        o.id = o.classid; // replace name with the property used for the text
                        o.text = o.nama_class; // replace name with the property used for the text
                        return o;
                    }),
					multiple: true,
					tokenSeparators: [',']
                });

                uiSelectClassSos.select2({
                    placeholder: 'Select Sub Channel',
                    allowClear: true,
                    data: $.map(rows2, function (o) {
                        o.id = o.classid; // replace name with the property used for the text
                        o.text = o.nama_class; // replace name with the property used for the text
                        return o;
                    }),
                });

                uiSelectBrand.val(null).trigger('change');
                uiSelectBrandSlob.val(null).trigger('change');
                uiSelectClass.val(null).trigger('change');
                uiSelectClassSos.val(null).trigger('change');
        
            }
        });

        uiBtnPreview.click(function () {
            //alert(paramsession.restrict_bu);
            open_preview_national();
            //open_preview_productivity();
            /*if (uiSelectTypePromo.val()===null){
                //uiAlertNotif.show();
                alert ('Type Promo harus di isi...!');
            }else if (uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Sub Channel / Account harus di isi...!');
            }else if (uiSelectPromo.val()===null){
                //uiAlertNotif.show();
                alert ('Promo harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else{
                open_preview();
            }*/
        });

        uiBtnPreviewPa.click(function () {
            //alert(paramsession.restrict_bu);
            //open_preview_productivity();
            if (uiSelectBulanPa.val()=='All'){
                //uiAlertNotif.show();
                alert ('Bulan harus di isi...!');
            //}else if (uiSelectClass.val()===null){
                //uiAlertNotif.show();
                //alert ('Sub Channel / Account harus di isi...!');
            }else{
                open_detail_pa_national();
            }
        });

        uiBtnDownloadPa.click(function () {
            //alert(paramsession.restrict_bu);
            //open_preview_productivity();
            if (uiSelectBulanPa.val()=='All'){
                //uiAlertNotif.show();
                alert ('Bulan harus di isi...!');
            }else if (uiSelectClass.val()===null){
                //uiAlertNotif.show();
                alert ('Sub Channel / Account harus di isi...!');
            }else if (uiSelectBrand.val()===null){
                //uiAlertNotif.show();
                alert ('Brand harus di isi...!');
            }else{
                save_xls_pa_national();
            }
        });

        uiBtnDownloadSOS.click(function () {
            //alert(paramsession.restrict_bu);
            //open_preview_productivity();
            if (uiSelectBulanSos.val()=='All'){
                //uiAlertNotif.show();
                alert ('Bulan harus di isi...!');
            }else{
                save_xls_sos_national();
            }
        });

        uiBtnPreviewSos.click(function () {
            //alert(paramsession.restrict_bu);
            //open_preview_productivity();
            if (uiSelectBulanSos.val()=='All'){
                //uiAlertNotif.show();
                alert ('Bulan harus di isi...!');
            //}else if (uiSelectClassSos.val()===null){
                //uiAlertNotif.show();
                //alert ('Sub Channel / Account harus di isi...!');
            }else{
                open_detail_sos_national();
            }
        });

        uiBtnPreviewSlob.click(function () {
            open_detail_slob_national();
        });

        uiSelectBrand.on('select2:select', function (e) {
            brandSelected = e.params.data;
            loadSku(brandSelected);
        });

        uiSelectBrandSlob.on('select2:select', function (e) {
            brandSelected = e.params.data;
            loadSkuSlob(brandSelected);
        });

        uiBtnDownload.click(function () {
            //alert(uiSelectSalesman.val());
            if (uiSelectTypePromo.val()===null){
                //uiAlertNotif.show();
                alert ('Type Promo harus di isi...!');
            }else if (uiSelectAccount.val()===null){
                //uiAlertNotif.show();
                alert ('Sub Channel / Account harus di isi...!');
            }else if (uiSelectPromo.val()===null){
                //uiAlertNotif.show();
                alert ('Promo harus di isi...!');
            }else if ( uiStartPeriode.val()===''){
                //uiAlertNotif.show();
                alert ('Periode harus di isi...!');
            }else{
                save_xls();
            }
        });
        
        /*uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });*/
        uiSelectSku.select2({
            placeholder: 'Select SKU',
            allowClear: true,
        });

        uiSelectSkuSlob.select2({
            placeholder: 'Select SKU',
            allowClear: true,
        });
        common.loadingClose();

    }

    function open_preview_national() {
		
        var tahun = uiSelectTahun.val();
        var bulan = uiSelectBulan.val();
        var tanggal = uiSelectTanggal.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

        if (uiSelectBulan.val()!='All' || uiSelectTanggal.val()!='All')
        {
            common.loading();
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("drc_dashboard/open_detail_national"),
                data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
                success:function(res){
                    response = res;
                    //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                    $('#tbl-content').html(response);
                    //$("#modal_detail").modal('show');
                    common.loadingClose();
                },
                error:function(){
                    alert("Load failed");
                    common.loadingClose();
                }
            });

            /*$.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("drc_dashboard/open_detail_productivity"),
                data : "tahun="+tahun+"&bulan="+bulan+"&tanggal="+tanggal+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
                success:function(res){
                    response = res;
                    //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                    $('#tbl-productivity').html(response);
                    //$("#modal_detail").modal('show');
                },
                error:function(){
                    alert("Load failed");
                }
            });*/
            
        }
        
    }

    function open_detail_sos_national() {
		
        var tahun = uiSelectTahunSos.val();
        var bulan = uiSelectBulanSos.val();
        var account = uiSelectClassSos.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

        if (uiSelectBulanSos.val()!='All' || uiSelectClassSos.val()!='null')
        {
            common.loading();
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("drc_dashboard/open_detail_sos_national"),
                data : "tahun="+tahun+"&bulan="+bulan+"&classid="+account+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
                success:function(res){
                    response = res;
                    //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                    $('#tbl-content').html(response);
                    common.loadingClose();
                    //$("#modal_detail").modal('show');
                },
                error:function(){
                    alert("Load failed");
                    common.loadingClose();
                }
            });
            
        }
        
    }

    function open_detail_pa_national() {
		
        var tahun = uiSelectTahunPa.val();
        var bulan = uiSelectBulanPa.val();
        var account = uiSelectClass.val();
        var brandid = uiSelectBrand.val();
        var productid = uiSelectSku.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

        if (uiSelectBulanSos.val()!='All' || uiSelectClassSos.val()!='null')
        {
            common.loading();
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("drc_dashboard/open_detail_product_available_national"),
                data : "tahun="+tahun+"&bulan="+bulan+"&classid="+account+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
                success:function(res){
                    response = res;
                    //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                    $('#tbl-content').html(response);
                    common.loadingClose();
                    //$("#modal_detail").modal('show');
                },
                error:function(){
                    alert("Load failed");
                    common.loadingClose();
                }
            });
            
        }
        
    }

    function open_detail_slob_national() {
		
        var brandid = uiSelectBrandSlob.val();
        var productid = uiSelectSkuSlob.val();
        //var end = uiEndPeriode.val();
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

            common.loading();
            $.ajax({
                type:"POST",
                dataType: "html",
                beforeSend : function() {
                    //$("#map-content").html('Populating data, please wait..');
                },
                url: common.baseURL("drc_dashboard/open_detail_slob_national"),
                data : "brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_bu="+restrict_bu+"&restrict_level="+restrict_level,
                success:function(res){
                    response = res;
                    //$('div .modal-header .modal-title').text('Detail Productifity Sales');			
                    $('#tbl-content').html(response);
                    common.loadingClose();
                    //$("#modal_detail").modal('show');
                },
                error:function(){
                    alert("Load failed");
                    common.loadingClose();
                }
            });
        
    }

    function loadSku(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_product_filter_brand"), {brandid: data.brandid}, function (res) {
            uiSelectSku.empty();
            uiSelectSku.select2({
                placeholder: "Select SKU",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = o.nama_invoice;
                    return o;
                }),
            });
            uiSelectSku.val(null).trigger('change');
            common.loadingClose();
        });

    }

    function loadSkuSlob(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_product_filter_brand"), {brandid: data.brandid}, function (res) {
            uiSelectSkuSlob.empty();
            uiSelectSkuSlob.select2({
                placeholder: "Select SKU",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.productid; // replace name with the property used for the text
                    o.text = o.nama_invoice;
                    return o;
                }),
            });
            uiSelectSkuSlob.val(null).trigger('change');
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

    function save_xls_pa_national() {
		
        var tahun = uiSelectTahunPa.val();
        var bulan = uiSelectBulanPa.val();
        var account = uiSelectClass.val();
        var brandid = uiSelectBrand.val();
        var productid = uiSelectSku.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

        common.loading();
        common.direct("drc_dashboard/save_xls_product_availability_national_row_data?tahun="+tahun+"&bulan="+bulan+"&classid="+account+"&brandid="+brandid+"&productid="+productid+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu);
        common.loadingClose();
       
    }

    function save_xls_sos_national() {
		
        var tahun = uiSelectTahunSos.val();
        var bulan = uiSelectBulanSos.val();
        var account = uiSelectClassSos.val();

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;
        var restrict_bu = paramsession.restrict_bu;

        common.loading();
        common.direct("drc_dashboard/save_xls_sos_national_row_data?tahun="+tahun+"&bulan="+bulan+"&classid="+account+"&idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&restrict_bu="+restrict_bu);
        common.loadingClose();
        
    }

})();