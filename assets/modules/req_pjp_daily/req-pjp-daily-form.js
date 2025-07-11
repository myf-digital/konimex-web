(function () {
    const common = new Common();
    common.setTitle("Request PJP Daily");
    // declare dom
    let uiForm = $("#fm-req-pjp-daily");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectWeeks1 = $("#week1-id");
    let uiSelectWeeks2 = $("#week2-id");
    let uiSelectWeeks3 = $("#week3-id");
    let uiSelectWeeks4 = $("#week4-id");
    let uiSelectSalesman = $("#salesmanid-id");
    let uiSearchOutlet = $("#customerid");

    // define from *-content.js
    let param = common.getCookie("module.req.pjp.daily.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined; // flag create update
    if (!isUpdate) {
        common.direct("req_pjp_daily");
        return;
    }

    initialize();
    initializeParam();

    function initialize() {
        let url = common.baseURL("req_pjp_daily/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "req_pjp_daily",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});

                var customer = document.getElementById("customerid_to");
                for (var i = 0; i < customer.options.length; i++){
                    form.push({name: 'customerid[]', value: customer.options[i].value});
                }
                return true; // MANDATORY!
            }
        });

        loadSalesman();

        uiSearchOutlet.multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            },
            fireSearch: function(value) {
                return value.length > 3;
            },
            submitAllLeft: false,
            submitAllRight: false
        });

        uiBtnCancel.click(function () {
            common.direct("req_pjp_daily");
        });

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            multiple: true,
            tokenSeparators: [',']
        });

    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
        
        $.when(
            $.post(common.baseURL("api_v1/call_days"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);

    }

    function setupForm(r1) {
        let rows1 = r1.result;

        uiSelectWeeks1.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks2.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks3.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectWeeks4.select2({
            placeholder: 'Select Hari',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.iddays; // replace name with the property used for the text
                o.text = o.days; // replace name with the property used for the text
                return o;
            }),
        });

        uiSelectWeeks1.val(null).trigger('change');
        uiSelectWeeks2.val(null).trigger('change');
        uiSelectWeeks3.val(null).trigger('change');
        uiSelectWeeks4.val(null).trigger('change');
    }

    function loadSalesman() {
        uiSelectSalesman.empty();
        uiSelectSalesman.select2({
            placeholder: "Select Salesman",
            allowClear: true,
            data: [{
                id: param.salesmanid, // replace name with the property used for the text
                text: param.salesmanid + " - " +param.salesman_name + " - " + param.tipe_sales,
            }],
        });
        uiSelectSalesman.val(param.salesmanid).trigger('change');
        loadOutlet(param);
    }

    function loadOutlet(data) {
        console.warn(data);
        common.loading();
        $.post(common.baseURL("api_v1/call_outlet_pjp"), {
            type:'daily',
            salesmanid:data.salesmanid,
        }, function (res) {
            var select = document.getElementById('customerid');
            var selectTo = document.getElementById('customerid_to');

            var length = select.options.length;
            for (i = length-1; i >= 0; i--) {
              select.options[i] = null;
            }

            var selectToLength = selectTo.options.length;
            for (i = selectToLength-1; i >= 0; i--) {
              selectTo.options[i] = null;
            }

            let pjpDetail = data.pjp_detail && data.pjp_detail.length > 0 ? data.pjp_detail.map(pd => pd.customerid) : [];
            for (var i = 0; i < res.result.length; i++) {
                var opt = document.createElement('option');
                opt.value = res.result[i].customerid;
                
                let html = '';
                let ko = false, o = false, a = false;
                if (res.result[i].kode_outlet && res.result[i].kode_outlet != '-') {
                    ko = true;
                    html += res.result[i].kode_outlet;
                }
                if (res.result[i].outlet && res.result[i].outlet != '-') {
                    if (ko) html += ' - ';
                    o = true;
                    html += res.result[i].outlet;
                }
                if (res.result[i].account && res.result[i].account != '-') {
                    if (o) html += ' - ';
                    a = true;
                    html += res.result[i].account;
                }
                if (res.result[i].dc && res.result[i].dc != '-') {
                    if (a) html += ' - ';
                    html += res.result[i].dc;
                }

                opt.innerHTML = html;
                opt.setAttribute('data-position', res.result[i].customerid);

                if (pjpDetail.includes(opt.value)) {
                    selectTo.appendChild(opt);
                } else {
                    select.appendChild(opt);
                }
            }
            
            common.loadingClose();
        });
    }
})();