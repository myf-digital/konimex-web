(function () {

    const common = new Common();
    common.setTitle("Role Menu");
    // declare dom
    let uiForm = $("#fm-role-menu");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectRole = $("#role-id");
    let uiSelectMenu = $("#menu-id");
    // define from *-content.js
    let param = common.getCookie("module.role.menu.update");
    let isUpdate = param !== undefined; // flag create update

    setupFormUI();
    initialize();
    initializeParam();

    function initialize() {
        let url = param === undefined ? common.baseURL("app_role_menu/create") : common.baseURL("app_role_menu/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "app_role_menu",
            beforeSubmit: function (form, options) {
                if (param !== undefined) {
                    form.push({name: 'role_menu_id', value: param.role_menu_id});
                }
                return true; // MANDATORY!
            },
            rules : {
                role_id: {
                    required: true
                },
                menu_id: {
                    required: true
                }
            }
        });
        uiBtnCancel.click(function () {
            common.direct("app_role_menu");
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("app_menu/load"), param.build()),
            $.post(common.baseURL("app_role/load"), param.build()),
            // $.post(common.baseURL("app_menu/load"), param.build()),
            // $.post(common.baseURL("app_menu/load"), param.build()),
            // $.post(common.baseURL("app_menu/load"), param.build()),
            // $.post(common.baseURL("app_menu/load"), param.build())
        ).done(function (data, textStatus, jqXHR) {

            //console.log(d);
        }).then(function (r1, r2) {
            common.loadingClose();

            setupForm(r1[0], r2[0]);
        }).fail(resolver.fail);
    }

    function setupFormUI() {
        uiSelectMenu.select2({
            placeholder: 'Select menu',
            allowClear: true
        });

        uiSelectRole.select2({
            placeholder: 'Select role',
            allowClear: true
        });
    }

    function setupForm(r1, r2) {
        console.log(r1);
        let rows = r1.rows;
        let rowsRole = r2.rows;
        if (isUpdate) { // update
            // rows = rows.filter(function (val) {
            //     return val.menu_id !== param.menu_id;
            // })
        }
        uiSelectMenu.select2({
            data: $.map(rows, function (o) {
                o.id = o.menu_id; // replace name with the property used for the text
                o.text = o.menu_name; // replace name with the property used for the text
                return o;
            }),
        });
        uiSelectRole.select2({
            data: $.map(rowsRole, function (o) {
                o.id = o.role_id; // replace name with the property used for the text
                o.text = o.role_name; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) {
            uiSelectRole.val(param.role_id).trigger('change');
            uiSelectMenu.val(param.menu_id).trigger('change');
            uiSelectRole.attr("disabled", true);
        }
    }

})();