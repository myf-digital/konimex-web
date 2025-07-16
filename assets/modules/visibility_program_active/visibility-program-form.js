(function () {

    const common = new Common();
    common.setTitle("Visibility Program Active");
    // declare dom
    let uiForm = $("#fm-visibility-program-active");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiStartPeriode = $("#start_period"); 
    let uiEndPeriode = $("#end_period"); 
    let uiSelectClass = $("#classid-id");   
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");
    let uiSelectSubArea = $("#subareaid-id");

    // define from *-content.js
    let param = common.getCookie("module.visibility.program.active.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("visibility_program_active/create") : common.baseURL("visibility_program_active/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "visibility_program_active",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_program', value: param.id_program});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("visibility_program_active");
        });

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            siteSelected ="GSK01";
            let srval = {regionalid:uiSelectRegional.val()};
            loadArea(srval);
        });

        uiSelectArea.on('select2:select', function (e) {
            areaSelected = e.params.data;
            // let sraval = {regionalid:uiSelectRegional.val(),areaid:uiSelectArea.val()};
            // loadSubArea(sraval);
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
        
        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            uiEndPeriode.datepicker('setStartDate', startDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }
        });

        if (isUpdate) {
            loadAccount();
            loadRegional();
            let srval = {regionalid:param.regionalid};
            loadArea(srval);
            // let sraval = {regionalid:param.regionalid, areaid:param.areaid};
            // loadSubArea(sraval);
        }else{
            loadAccount();
            loadRegional();
            let srval = {regionalid:param.regionalid};
            loadArea(srval);
            // let sraval = {regionalid:param.regionalid, areaid:param.areaid};
            // loadSubArea(sraval);
        }

    }

    function loadAccount() {
        common.loading();
        $.post(common.baseURL("api_v1/call_account_outlet"), function (res) {
            uiSelectClass.empty();
            let vmin = 1;
            let vmax = 10;

            uiSelectClass.select2({
                placeholder: "Select Account List",
                allowClear: true,
                minimumSelectionLength: vmin,
                maximumSelectionLength: vmax,
                allowClear: true,
                multiple: true,
                tokenSeparators: [','],
                data: $.map(res.result, function (o) {
                    o.id = o.idaccount; // replace name with the property used for the text
                    o.text = o.account;
                    return o;
                }),
            });
            
            if (isUpdate) {
                let arrclassid = param.account ?? '';
                let varclassid = arrclassid.split(',');
                let rarrclassid=[];

                var i;
                for (i = 0; i < varclassid.length; i++) {
                    rarrclassid.push(varclassid[i]);
                }

                uiSelectClass.val(rarrclassid).trigger('change');

            }else{
                uiSelectClass.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadRegional() {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), function (res) {
            uiSelectRegional.empty();

            uiSelectRegional.select2({
                placeholder: "Select Regional",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.regionalid; // replace name with the property used for the text
                    o.text = o.nama_regional;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectRegional.val(param.regionalid).trigger('change');

            }else{
                uiSelectRegional.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }
    
    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area"), {regionalid:data.regionalid}, function (res) {
            uiSelectArea.empty();
            uiSelectArea.select2({
                placeholder: "Select Area",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.areaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            //alert (data.regionalid);
            if (isUpdate) {
                uiSelectArea.val(param.areaid).trigger('change');
            }else{
                uiSelectArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadSubArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_subarea"), {regionalid:data.regionalid, areaid:data.areaid}, function (res) {
            uiSelectSubArea.empty();
            let vmin = 1;
            let vmax = 10;

            uiSelectSubArea.select2({
                placeholder: "Select List City",
                allowClear: true,
                minimumSelectionLength: vmin,
                maximumSelectionLength: vmax,
                allowClear: true,
                multiple: true,
                tokenSeparators: [','],
                data: $.map(res.result, function (o) {
                    o.id = o.subareaid; // replace name with the property used for the text
                    o.text = o.nama_area;
                    return o;
                }),
            });
            
            if (isUpdate) {
                let arrsubareaid = param.subareaid ?? '';
                let varsubareaid = arrsubareaid.split(',');
                let rarrsubareaid=[];

                var i;
                for (i = 0; i < varsubareaid.length; i++) {
                    rarrsubareaid.push(varsubareaid[i]);
                }

                uiSelectSubArea.val(rarrsubareaid).trigger('change');

            }else{
                uiSelectSubArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }    
})();