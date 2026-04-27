(function () {
    const common = new Common();
    common.setTitle("Setup Outlet");
    // declare dom
    let uiForm = $("#form-set-outlet");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectUser = $("#professionalid");
    let uiSearchOutlet = $("#customerid");

    let param = common.getCookie("module.setup.professional-outlet");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined;
    let selectedMedrep = null;

    initialize();

    function initialize() {
        let url = common.baseURL("professional/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "professional",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_professional', value: param.id});
                }
                form.push({name: 'usersession', value: paramsession.username});

                let customer = document.getElementById("customerid_to");
                for (let i = 0; i < customer.options.length; i++){
                    form.push({name: 'customerid[]', value: customer.options[i].value});
                }
                return true;
            }
        });

        loadProfessional({id_professional: param.id});
        if (isUpdate) {
            loadOutlet({id_professional: param.id});
        }
        uiSelectUser.on('change', function () {
            selectedMedrep = uiSelectUser.select2('data')[0];
        });

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
            common.direct("professional");
        });
    }

    function loadProfessional(data) {
        common.loading();
        $.post(common.baseURL("professional/detail"), data, function (res) {
            uiSelectUser.empty();
            uiSelectUser.select2({
                placeholder: "Select User (Professional)",
                allowClear: true,
                data: $.map(res, function (o) {
                    o.id = o.id;
                    o.text = o.id + " - " +o.nama_professional;
                    return o;
                }),
            });
            
            if (isUpdate) {
                uiSelectUser.val(param.id).trigger('change');
            } else {
                uiSelectUser.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadOutlet(data) {
        common.loading();
        $.post(common.baseURL("professional/outlet"), {
            id_professional: data.id_professional,
        }, function (res) {
            let { select, selectTo } = resetSelectOutlet();

            let selectedOutlet = [];
            const customerList = param.customer_list ? param.customer_list.split('||') : [];
            customerList.forEach(item => {
                selectedOutlet.push(item.split(' - ')[0]);
            });

            for (let i = 0; i < res.length; i++) {
                let html = '';
                if (res[i].customerid) html += res[i].customerid;
                if (res[i].nama_customer) html += ` - ${res[i].nama_customer}`;
                if (res[i].typeid) html += ` - ${res[i].typeid}`;
                
                if (selectedOutlet.includes(res[i].customerid)) {
                    let optSelected = document.createElement('option');
                    optSelected.value = res[i].customerid;
                    optSelected.innerHTML = html;
                    optSelected.setAttribute('data-position', res[i].customerid);
                    selectTo.appendChild(optSelected);
                } else {
                    let opt = document.createElement('option');
                    opt.value = res[i].customerid;
                    opt.innerHTML = html;
                    opt.setAttribute('data-position', res[i].customerid);
                    select.appendChild(opt);
                }
            }
            
            common.loadingClose();
        });
    }

    function resetSelectOutlet() {
        let select = document.getElementById('customerid');
        let selectTo = document.getElementById('customerid_to');

        let length = select.options.length;
        for (i = length-1; i >= 0; i--) {
            select.options[i] = null;
        }

        let selectToLength = selectTo.options.length;
        for (i = selectToLength-1; i >= 0; i--) {
            selectTo.options[i] = null;
        }

        return { select, selectTo };
    }
})();
