(function () {

    const common = new Common();
    //const commonFilter = new CommonFilter();

    common.setTitle("Menu");
    // declare dom
    let uiForm = $("#fm-menu");
    let uiInputIcon = $("#menu-icon");
    let uiSelectParent = $("#parent-id");
    let uiBtnCancel = $("#btn-cancel-form");
    // define from *-content.js
    let param = common.getCookie("module.menu.update");
    let isUpdate = param !== undefined; // flag create update

    setupFormUI();
    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_menu/create") : common.baseURL("app_menu/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_menu",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'menu_id', value: param.menu_id});
                }
                return true; // MANDATORY!
            },
            rules: {
                menu_name: {
                    required: true
                },
                module_name: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_menu");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let filter = new Filter();
		if(isUpdate) filter.add("a.menu_id", "!=", param.menu_id);
        $.when(
            $.post(common.baseURL("app_menu/load"), filter.build()),
            // $.post(common.baseURL("app_menu/load"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
    }

    function setupFormUI() {
        uiInputIcon.iconpicker();
        uiSelectParent.select2({
            placeholder: 'Select parent menu'
        });
    }

    function setupForm(r1, r2) {
        let rows = [];
        rows.push({menu_id: 0, menu_name: "Root"});
        rows = rows.concat(r1.rows);
        if (isUpdate) { // update
            rows = rows.filter(function (val) {
                return val.menu_id !== param.menu_id;
            })
        }
        uiSelectParent.select2({
            data: $.map(rows, function (o) {
                o.id = o.menu_id; // replace name with the property used for the text
                o.text = o.menu_name; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectParent.val(param.parent_id).trigger('change');

    }


})();