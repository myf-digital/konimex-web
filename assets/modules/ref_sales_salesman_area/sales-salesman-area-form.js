(function () {

    const common = new Common();
    common.setTitle("Sales Salesman Area");
    // declare dom
    let uiForm = $("#fm-sales-salesman-area");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");
    let uiSelectSalesmanid = $("#salesmanid-id");
    let uiSelectAreaid = $("#areaid-id");
    // define from *-content.js
    let param = common.getCookie("module.sales.salesman.area.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_sales_salesman_area/create") : common.baseURL("ref_sales_salesman_area/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_sales_salesman_area",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                salesmanid: {
                    required: true
                },
                areaid: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_sales_salesman_area");
        });
        
        uiSelectSalesmanid.select2({
            placeholder: 'Select Salesman',
            allowClear: true
        });

        uiSelectAreaid.select2({
            placeholder: 'Select Area',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });

        uiSelectSiteid.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            let sval = siteidSelected;
            loadSalesman(sval);
            let srval = siteidSelected;
            loadArea(srval);
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            let pval = {siteid:param.siteid, salesmanid:param.salesmanid};
            loadSalesman(pval);
            let sval = {siteid:param.siteid};
            loadArea(sval);
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
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
        let rows = r1.rows;
        
        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true,
            data: $.map(rows, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
        }

    }

    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman_mapping_area"), {siteid:data.siteid, salesmanid:data.salesmanid}, function (res) {
            uiSelectSalesmanid.empty();
            uiSelectSalesmanid.select2({
                placeholder: "Select Salesman",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSalesmanid.val(param.salesmanid).trigger('change');
            }else{
                uiSelectSalesmanid.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area"), {siteid:data.siteid, regionalid:data.regionalid}, function (res) {
            uiSelectAreaid.empty();
            uiSelectAreaid.select2({
                placeholder: "Select Area",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                let gArea = param.group_areaid;
                let gAreaArr = gArea.split(',');
                uiSelectAreaid.val(gAreaArr).trigger('change');
                //uiSelectAreaid.val(param.areaid).trigger('change');
            }else{
                uiSelectAreaid.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();