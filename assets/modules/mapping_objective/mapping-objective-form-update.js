(function () {
    const common = new Common();
    common.setTitle("Mapping Objective");
    // declare dom
    let uiForm = $("#fm-mapping-objective");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectClass = $("#classid-id");   
    let uiSelectObjective = $("#objective-id");   
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 

    // define from *-content.js
    let param = common.getCookie("module.mapping.objective.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = common.baseURL("mapping_objective/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "mapping_objective",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});

                var productid = document.getElementById("product");
                form.push({name: 'productid', value: productid});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("mapping_objective");
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

        uiSelectObjective.on('change', function(selected) {
            let value = selected.target.value;

            let uiDivMinOrder = document.getElementById('div_min_order');
            if (uiDivMinOrder) {
                uiDivMinOrder.style.display = 'none';
                if (value && value == 'Order Reguler Min Order') {
                    uiDivMinOrder.style.display = 'block';
                }
            }
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("ref_customer_class/load"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            loadClass(r1);
        }).fail(resolver.fail);

        loadObjective();
    }

    function loadClass(r1) {
        let rows1 = r1.rows;
        let vmin = 1;
        let vmax = 10;

        uiSelectClass.empty();
        uiSelectClass.select2({
            placeholder: 'Sub Channel / Account',
            allowClear: true,
            minimumSelectionLength: vmin,
            maximumSelectionLength: vmax,
            allowClear: true,
            multiple: true,
            tokenSeparators: [','],
            data: $.map(rows1, function (o) {
                o.id = o.classid + '||' + o.nama_class; // replace name with the property used for the text
                o.text = o.nama_class; // replace name with the property used for the text
                console.warn('o', o)
                return o;
            }),
        });

        if (isUpdate) {
            let arrids = param.accountids ?? '';
            let arrnms = param.accounts ?? '';
            let varsku = arrids.split('||');
            let nmsku = arrnms.split('||');
            let rarrsku = [];

            var i;
            for (i = 0; i < varsku.length; i++) {
                rarrsku.push({
                    id: `${varsku[i]}||${nmsku[i]}`,
                    text: nmsku[i],
                });
            }
            console.warn(param.accountids, varsku, rarrsku)
            uiSelectClass.val(rarrsku).trigger('change');
        } else {
            uiSelectClass.val(null).trigger('change');
        }
    }

    function loadObjective() {
        let rows = ['NOO', 'Order Reguler', 'Order Reguler Min Order'];
        uiSelectObjective.select2({
            placeholder: 'Objective',
            allowClear: true,
            data: rows,
        });

        if (isUpdate) {
            uiSelectObjective.val(param.objective).trigger('change');
        } else {
            uiSelectObjective.val(null).trigger('change');
        }
    }
})();