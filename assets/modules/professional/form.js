(function () {
    const common = new Common();
    common.setTitle("Form User");
    // declare dom
    let uiForm = $("#form-set-outlet");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiInputUser = $("#professional");
    let uiSelectSpesialisasi = $("#spesialisasi");
    let uiSearchOutlet = $("#customerid");

    let param = common.getCookie("module.professional.update");
    let paramsession = common.getCookie("session");

    let isUpdate = param !== undefined;
    let selectedMedrep = null;
    let searchTimeout = null;

    initialize();

    function initialize() {
        let url = common.baseURL("professional/update");
        $.validator.setDefaults({
            errorPlacement: function (error, element) {
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            }
        });

        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "professional",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({ name: 'id_professional', value: param.id });
                }
                form.push({ name: 'usersession', value: paramsession.username });
                form.push({ name: 'spesialisasi', value: document.getElementById("spesialisasi").value });

                let customer = document.getElementById("customerid_to");
                for (let i = 0; i < customer.options.length; i++) {
                    form.push({ name: 'customerid[]', value: customer.options[i].value });
                }
                return true;
            },
            rules: {
                professional: {
                    required: true
                },
                spesialisasi: {
                    required: true
                }
            },
            message: {
                professional: {
                    required: 'User wajib diisi.'
                },
                spesialisasi: {
                    required: 'Spesialisasi wajib dipilih.'
                }
            }
        });

        loadOutlet();
        loadSpesialisasi();
        if (isUpdate) {
            uiInputUser.val(param.nama_professional || '');
        }

        uiSearchOutlet.multiselect({
            search: {
                left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                right: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
            },
            fireSearch: function (value) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    loadOutlet(value);
                }, 500);
                return false;
            },
            submitAllLeft: false,
            submitAllRight: false
        });

        uiBtnCancel.click(function () {
            common.direct("professional");
        });
    }

    function loadSpesialisasi() {
        common.loading();
        $.post(common.baseURL("professional/spesialisasi"), function (res) {
            uiSelectSpesialisasi.empty();
            uiSelectSpesialisasi.select2({
                placeholder: "Select User (Professional)",
                allowClear: true,
                data: $.map(res, function (o) {
                    o.id = o.id;
                    o.text = o.name;
                    return o;
                }),
            });

            if (isUpdate) {
                uiSelectSpesialisasi.val(param.spesialisasi_id).trigger('change');
            } else {
                uiSelectSpesialisasi.val(null).trigger('change');
            }
            common.loadingClose();
        });
    }

    function loadOutlet(keyword = '') {
        $.post(common.baseURL("professional/outlet"), {
            q: keyword
        }, function (res) {
            let { select, selectTo } = resetSelectOutlet();

            let selectedOutlet = [];
            if (isUpdate && param.customer_list) {
                const customerList = param.customer_list ? param.customer_list.split('||') : [];
                customerList.forEach(item => {
                    const parts = item.split(' - ');
                    const customerId = parts[0];
                    selectedOutlet.push(customerId);

                    let optSelected = document.createElement('option');
                    optSelected.value = customerId;
                    optSelected.innerHTML = item;
                    optSelected.setAttribute('data-position', customerId);
                    selectTo.appendChild(optSelected);
                });
            }

            for (let i = 0; i < res.length; i++) {
                if (selectedOutlet.includes(res[i].customerid)) {
                    continue;
                }

                let html = '';
                if (res[i].customerid) html += res[i].customerid;
                if (res[i].nama_customer) html += ` - ${res[i].nama_customer}`;
                if (res[i].typeid) html += ` - ${res[i].typeid}`;

                let opt = document.createElement('option');
                opt.value = res[i].customerid;
                opt.innerHTML = html;
                opt.setAttribute('data-position', res[i].customerid);
                select.appendChild(opt);
            }
        });
    }

    function resetSelectOutlet() {
        let select = document.getElementById('customerid');
        let selectTo = document.getElementById('customerid_to');

        let length = select.options.length;
        for (i = length - 1; i >= 0; i--) {
            select.options[i] = null;
        }

        let selectToLength = selectTo.options.length;
        for (i = selectToLength - 1; i >= 0; i--) {
            selectTo.options[i] = null;
        }

        return { select, selectTo };
    }
})();
