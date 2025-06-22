(function () {

    const common = new Common();
    common.setTitle("Business Unit");
    // declare dom
    let uiForm = $("#fm-customer-segment");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.customer.segment.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_customer_segment/create") : common.baseURL("ref_customer_segment/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_customer_segment",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'segmentid', value: param.segmentid});
                }
                return true; // MANDATORY!
            },
            rules: {
                segmentid: {
                    required: true
                },
                nama_segment: {
                    required: true
                },
                flag_harga: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_customer_segment");
        });
    }

})();