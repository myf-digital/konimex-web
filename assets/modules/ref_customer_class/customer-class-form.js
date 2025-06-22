(function () {

    const common = new Common();
    common.setTitle("Account Outlet");
    // declare dom
    let uiForm = $("#fm-customer-class");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectSubChannel = $("#subchannel-id");
    // define from *-content.js
    let param = common.getCookie("module.customer.class.update");

    initialize();

    function initialize() {

        let resolver = new HttpResolver();
        let filter = new Filter();
        console.log(filter);
        
        $.when(
            $.post(common.baseURL("ref_customer_class/load_subchannel"), filter.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
            //console.log(d);
        }).then(function (r1) {
            console.log("then");
            setupForm(r1);
        }).fail(resolver.fail);


        let url = param === undefined ? common.baseURL("ref_customer_class/create") : common.baseURL("ref_customer_class/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer_class",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'classid', value: param.classid});
                }
                return true; // MANDATORY!
            },
            rules: {
                classid: {
                    required: true
                },
                nama_class: {
                    required: true
                },
                subchannel: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer_class");
        });

        uiSelectSubChannel.val(null).trigger('change');
    }

    function setupForm(r1) {
        let rows1 = r1.rows;

        uiSelectSubChannel.select2({
            placeholder: 'Select Sub Channel',
            allowClear: true,
            data: $.map(rows1, function (o) {
                o.id = o.typeid; // replace name with the property used for the text
                o.text = o.nama_type; // replace name with the property used for the text
                return o;
            }),
        });

        if (param !== undefined) {
            uiSelectSubChannel.val(param.typeid).trigger('change');
        }  
    } 

})();