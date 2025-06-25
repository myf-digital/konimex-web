(function () {

    const common = new Common();
    common.setTitle("Sales Salesman Category");
    // declare dom
    let uiForm = $("#fm-sales-salesman-category");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.sales.salesman.category.update");

    initialize();

    function initialize() {
        let url = param === undefined ? common.baseURL("ref_sales_salesman_category/create") : common.baseURL("ref_sales_salesman_category/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_sales_salesman_category",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: '', value: param.});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_sales_salesman_category");
        });
    }

})();