(function () {

    const common = new Common();
    common.setTitle("SubArea");
    // declare dom
    let uiForm = $("#fm-area-city");
    let uiBtnCancel = $("#btn-cancel-form");
    //let uiSelectSite = $("#siteid-id");
    let uiSelectRegional = $("#regionalid-id");
    let uiSelectArea = $("#areaid-id");
    
    // define from *-content.js
    let param = common.getCookie("module.area.city.update");
    let isUpdate = param !== undefined; // flag create update
	//alert(isUpdate);
	
    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_area_city/create") : common.baseURL("ref_area_city/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_area_city",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'subareaid', value: param.subareaid});
                }
                return true; // MANDATORY!
            },
            rules: {
                siteid: {
                    required: true
                },
                regionalid: {
                    required: true
                },
                areaid: {
                    required: true
                },
                city: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_area_city");
        });

        uiSelectRegional.on('select2:select', function (e) {
            regionalSelected = e.params.data;
            //siteSelected = uiSelectSite.val();
            let srval = regionalSelected;
            srval.push = {siteid:"GSK01"};
            loadArea(srval);
        });

        if (isUpdate) {
			//alert("edit");
            let sval = {siteid:"GSK01",regionalid:param.regionalid};
            loadRegional(sval);
            //uiSelectRegional.val(param.regionalid).trigger('change');

            let srval = {siteid:"GSK01",regionalid:param.regionalid,areaid:param.areaid};
            loadArea(srval);
            //uiSelectArea.val(param.areaid).trigger('change');

        }else{
            //loadSiteid(null);
			//alert("create");

            let sval = {siteid:"GSK01"};
            loadRegional(sval);

            let srval = {siteid:"GSK01",regionalid:null};
            loadArea(srval);
        }

    }

    function loadRegional(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_regional"), {siteid:data.siteid}, function (res) {
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
				//alert("edit regional"+data.regionalid);
                uiSelectRegional.val(data.regionalid).trigger('change');
            }else{
                uiSelectRegional.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadArea(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_area"), {siteid:data.siteid, regionalid:data.regionalid}, function (res) {
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
            
            if (isUpdate) {
                uiSelectArea.val(data.areaid).trigger('change');
            }else{
                uiSelectArea.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

})();