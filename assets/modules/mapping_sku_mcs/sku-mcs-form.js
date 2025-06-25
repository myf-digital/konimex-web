(function () {

    const common = new Common();
    common.setTitle("Sku Active MCS");
    // declare dom
    let uiForm = $("#fm-sku-mcs");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectChannel = $("#typeid-id");
    let uiSelectProduct = $("#productid-id");
    let uiCheckboxProduct = $("#checkboxes");
    let uiSearch = $("#search");
    // define from *-content.js
    let param = common.getCookie("module.sku.mcs.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("mapping_sku_mcs/create") : common.baseURL("mapping_sku_mcs/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "mapping_sku_mcs",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                form.push({name: 'usersession', value: paramsession.username});

                var product = document.getElementById("search_to");
                for (var i = 0; i < product.options.length; i++){
                    form.push({name: 'productid[]', value: product.options[i].value});
                }

                return true; // MANDATORY!
            },
            rules: {
                typeid: {
                    required: true
                },
                productid: {
                    required: true
                }                
            }
        });

        // uiSelectProduct.select2({
        //     placeholder: 'Select Product',
        //     minimumSelectionLength: 1,
        //     maximumSelectionLength: 1000,
        //     allowClear: true,
        //     multiple: true,
        //     tokenSeparators: [',']
        // });
        
        uiBtnCancel.click(function () {
            common.direct("mapping_sku_mcs");
        });

        // uiSearch.on("keyup", function() {
        //     var value = $(this).val().toLowerCase();
        //     console.log(value)
        //     $("div #search-value").filter(function() {
        //       $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        //     });
        // });

        uiSearch.multiselect({
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
    }
    
    
    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		console.log(filter);
        
        $.when(
            $.post(common.baseURL("ref_customer_type/load"), filter.build()),
            $.post(common.baseURL("api_v1/call_product"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1, r2) {
            common.loadingClose();
            console.log("then");
            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);

    }


    function setupForm(r1, r2) {
        let rows1 = r1.rows;
        let rows2 = r2.result;

        uiSelectChannel.select2({
            placeholder: 'Select Channel',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.typeid; // replace name with the property used for the text
                o.text = o.nama_type; // replace name with the property used for the text
                return o;
            }),
        });

        // var container = document.createElement('div');
        // container.setAttribute('class', 'form-group col-md-12 checkboxes');
        // var div;
        // var checkBox;

        // for (var i = 0; i < rows2.length; i++){
        //     div = document.createElement('div');
        //     div.setAttribute('id', 'search-value');
        //     checkBox = document.createElement('input');
        //     checkBox.type = "checkbox";
        //     checkBox.name = "productid[]";
        //     checkBox.value = rows2[i].productid;
        //     checkBox.id = rows2[i].productid;

        //     div.innerHTML = '&nbsp;'+rows2[i].nama_invoice;
        //     div.prepend(checkBox);

        //     container.appendChild(div);
        // }

        // uiCheckboxProduct.prepend(container);

        if (isUpdate) {
            let gProdid = param.group_productid;
            let gProdArr = gProdid.split(',');

            var productAvailable = rows2;

            rows2 = rows2.filter((item) => {
                return !gProdArr.includes(item.productid);
            });
        }

        select = document.getElementById('search');

        for (var i = 0; i < rows2.length; i++){
            var opt = document.createElement('option');
            opt.value = rows2[i].productid;
            opt.innerHTML = '('+rows2[i].productid+') - '+rows2[i].nama_invoice;
            opt.setAttribute('data-position', rows2[i].productid);
            select.appendChild(opt);
        }

        // uiSelectProduct.select2({
        //     placeholder: 'Select Product',
        //     allowClear: true,
        //     minimumSelectionLength: 1,
        //     maximumSelectionLength: 100,
        //     data: $.map(rows2, function (o) {
        //         o.id = o.productid; // replace name with the property used for the text
        //         o.text = '('+o.productid+') - '+o.nama_invoice; // replace name with the property used for the text
        //         return o;
        //     }),
        // });

        if (isUpdate) {
            uiSelectChannel.val(param.typeid).trigger('change');
         	let gProdid = param.group_productid;
			let gProdArr = gProdid.split(',');
			//uiSelectProduct.val(gProdArr).trigger('change');

            // for (var i = 0; i < gProdArr.length; i++) {
            //     var check = document.getElementById(gProdArr[i]);
            //     if (check != null) check.checked = true;
            // }

            selectTo = document.getElementById('search_to');

            for (var i = 0; i < gProdArr.length; i++) {

                var find = productAvailable.find(item => item.productid === gProdArr[i]);

                var opt = document.createElement('option');
                opt.value = find.productid;
                opt.innerHTML = '('+find.productid+') - '+find.nama_invoice;
                opt.setAttribute('data-position', find.productid);
                selectTo.appendChild(opt);

            }

        }else{
            uiSelectChannel.val(null).trigger('change');
            //uiSelectProduct.val(null).trigger('change');
        }
    }

})();