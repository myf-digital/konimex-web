(function () {
    const common = new Common();
    common.setTitle("Mapping Objective");
    // declare dom
    let uiForm = $("#fm-mapping-objective");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectClass = $("#classid-id");   
    let uiSelectObjective = $("#objective-id");   
    let uiSearchProduct = $("#products");
    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode"); 

    // define from *-content.js
    let param = common.getCookie("module.mapping.objective.update");
    let paramsession = common.getCookie("session");

    initialize();
    initializeParam();

    function initialize() {
        let url = common.baseURL("mapping_objective/create");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "mapping_objective",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'siteid', value: param.siteid});
                }
                form.push({name: 'usersession', value: paramsession.username});

                var products = document.getElementById("products_to");
                for (var i = 0; i < products.options.length; i++){
                    form.push({name: 'products[]', value: products.options[i].value});
                }
                return true; // MANDATORY!
            }
        });

        uiSearchProduct.multiselect({
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

        loadProduct();
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
                return o;
            }),
        });
        uiSelectClass.val(null).trigger('change');
    }

    function loadObjective() {
        let rows = ['NOO', 'Order Reguler', 'Order Reguler Min Order'];
        uiSelectObjective.select2({
            placeholder: 'Objective',
            allowClear: true,
            data: rows,
        });
        uiSelectObjective.val(null).trigger('change');
    } 

    function loadProduct() {
        common.loading();
        $.post(common.baseURL("api_v1/call_product"), {
            type: 'mapping_objective'
        }, function (res) {
            var select = document.getElementById('products');
            var selectTo = document.getElementById('products_to');

            var length = select.options.length;
            for (i = length-1; i >= 0; i--) {
              select.options[i] = null;
            }

            var selectToLength = selectTo.options.length;
            for (i = selectToLength-1; i >= 0; i--) {
              selectTo.options[i] = null;
            }

            const data = res.result;
            for (var i = 0; i < data.length; i++) {
                var opt = document.createElement('option');
                opt.value = data[i].productid + '||' + data[i].nama_invoice;
                
                let html = '';
                let id = false, nm = false, ct = false;
                if (data[i].productid && data[i].productid != '-') {
                    id = true;
                    html += data[i].productid;
                }
                if (data[i].nama_invoice && data[i].nama_invoice != '-') {
                    if (id) html += ' - ';
                    nm = true;
                    html += data[i].nama_invoice;
                }
                if (data[i].category_product && data[i].category_product != '-') {
                    if (nm) html += ' - ';
                    ct = true;
                    html += data[i].category_product;
                }
                if (data[i].nama_brand && data[i].nama_brand != '-') {
                    if (ct) html += ' - ';
                    html += data[i].nama_brand;
                }

                opt.innerHTML = html;
                opt.setAttribute('data-position', opt.value);
                select.appendChild(opt);
            }
            common.loadingClose();
        });
    }
})();