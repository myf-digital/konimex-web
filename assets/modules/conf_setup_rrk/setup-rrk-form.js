(function () {

    const common = new Common();
    common.setTitle("Setup Rrk");
    // declare dom
    let uiForm = $("#fm-setup-rrk");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSiteid = $("#siteid-id");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiSelectFrequency = $("#frvisit-id");
    let uiSelectAreaBySales = $("#areaid-id");
    let uiSelectWeeks = $("#minggu-id");
    let uiSelectDays = $("#day-id");

    // define from *-content.js
    let param = common.getCookie("module.setup.rrk.update");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("conf_setup_rrk/create") : common.baseURL("conf_setup_rrk/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "conf_setup_rrk",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({siteid: '', value: param.siteid});
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
                frvisit: {
                    required: true
                },
                areaid: {
                    required: true
                },
                minggu: {
                    required: true
                },
                day: {
                    required: true
                }                
            }
        });
        uiBtnCancel.click(function () {
            common.direct("conf_setup_rrk");
        });

        uiSelectFrequency.on('select2:select', function (e) {
            frequencySelected = e.params.data;
            let vmaxweeks = 0;
            let vmaxdays = 0;
            let vmin = 1;
            if(uiSelectFrequency.val()=='F1'){
                vmaxweeks=1;
                vmaxdays=1;
            }else if(uiSelectFrequency.val()=='F2'){
                vmaxweeks=2;
                vmaxdays=1;
            }else if(uiSelectFrequency.val()=='F4'){
                vmaxweeks=4;
                vmaxdays=1;
            }else if(uiSelectFrequency.val()=='F8'){
                vmaxweeks=4;
                vmaxdays=2;
            }
            uiSelectWeeks.select2({
                placeholder: 'Select Weeks',
                minimumSelectionLength: vmin,
                maximumSelectionLength: vmaxweeks,
                allowClear: true,
                multiple: true,
                tokenSeparators: [',']
            });
    
            uiSelectDays.select2({
                placeholder: 'Select Days',
                minimumSelectionLength: vmin,
                maximumSelectionLength: vmaxdays,
                allowClear: true,
                multiple: true,
                tokenSeparators: [',']
            });
            //maxAllowedMultiselect(uiSelectWeeks, 1);
            //maxAllowedMultiselect(uiSelectDays, 1);
        });

        uiSelectSiteid.on('select2:select', function (e) {
            siteidSelected = e.params.data;
            loadSalesman(siteidSelected);
        });

        uiSelectSalesman.on('select2:select', function (e) {
            salesSelected = e.params.data;
            siteidSelected = uiSelectSiteid.val();
            let sarval = salesSelected;
            sarval.push = {siteid:siteidSelected};
            loadAreaBySales(sarval);
        });

        uiSelectSalesman.select2({
            placeholder: 'Select Medrep',
            allowClear: true
        });

        uiSelectAreaBySales.select2({
            placeholder: 'Select Area',
            allowClear: true,
            //multiple: true,
            //tokenSeparators: [',']
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            let sval = {siteid:param.siteid};
            loadSalesman(sval);
        }

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("conf_setup_site/load"), filter.build()),
            $.post(common.baseURL("api_v1/call_frequency"), filter.build()),
            $.post(common.baseURL("api_v1/call_weeks"), filter.build()),
            $.post(common.baseURL("api_v1/call_days"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2, r3, r4) {
            common.loadingClose();
            setupForm(r1[0], r2[0], r3[0], r4[0]);
        }).fail(resolver.fail);

    }

    function setupForm(r1, r2, r3, r4) {
        let rows1 = r1.rows;
        let rows2 = r2.result;
        let rows3 = r3.result;
        let rows4 = r4.result;

        uiSelectSiteid.select2({
            placeholder: 'Select SiteId',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.siteid; // replace name with the property used for the text
                o.text = o.nama_site; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectFrequency.select2({
            placeholder: 'Select Frequency',
            allowClear: true,
            data: $.map(rows2, function (o) {
                o.id = o.idfrequency; // replace name with the property used for the text
                o.text = o.frequency; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectWeeks.select2({
            placeholder: 'Select Weeks',
            allowClear: true,
            minimumSelectionLength: 1,
            maximumSelectionLength: 4,
            data: $.map(rows3, function (o) {
                o.id = o.idweeks; // replace name with the property used for the text
                o.text = o.weeks; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectDays.select2({
            placeholder: 'Select Days',
            allowClear: true,
            minimumSelectionLength: 1,
            maximumSelectionLength: 4,
            data: $.map(rows4, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });

        if (isUpdate) {
            uiSelectSiteid.val(param.siteid).trigger('change');
            uiSelectFrequency.val(param.idfrequency).trigger('change');
            uiSelectWeeks.val(param.idweeks).trigger('change');
            uiSelectDays.val(param.iddays).trigger('change');
        }else{
            uiSelectSiteid.val(null).trigger('change');
            uiSelectFrequency.val(null).trigger('change');
            uiSelectWeeks.val(null).trigger('change');
            uiSelectDays.val(null).trigger('change');
        }

    }


    function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {siteid:data.siteid}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select Medrep",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectSalesman.val(param.salesmanid).trigger('change');
            }else{
                uiSelectSalesman.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadAreaBySales(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area_by_sales"), {siteid:data.siteid,salesmanid:data.salesmanid}, function (res) {
            uiSelectAreaBySales.empty();
            uiSelectAreaBySales.select2({
                placeholder: "Select Area",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectAreaBySales.val(param.areaid).trigger('change');
            }else{
                uiSelectAreaBySales.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }
    
    function maxAllowedMultiselect(obj, maxAllowedCount) {
        var selectedOptions = jQuery('#'+obj.id+" option[value!=\'\']:selected");
        if (selectedOptions.length >= maxAllowedCount) {
            if (selectedOptions.length > maxAllowedCount) {
                selectedOptions.each(function(i) {
                    if (i >= maxAllowedCount) {
                        jQuery(this).prop("selected",false);
                    }
                });
            }
            jQuery('#'+obj.id+' option[value!=\'\']').not(':selected').prop("disabled",true);
        } else {
            jQuery('#'+obj.id+' option[value!=\'\']').prop("disabled",false);
        }
    }

})();