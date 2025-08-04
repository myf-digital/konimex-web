(function () {
    const common = new Common();
    common.setTitle("Product Knowledge");
    const baseURL = document.getElementById('base_url')?.content;
    // declare dom
    let uiForm = $("#fm-product-knowledge");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectBrand = $("#brand");

    // define from *-content.js
    let param = common.getCookie("module.product_knowledge.update");
    let paramsession = common.getCookie("session");
    let isUpdate = param !== undefined; // flag create update

    initialize();
    initializeParam();
    if (isUpdate) previewFiles(param);

    function initialize() {
        let url = param === undefined ? common.baseURL("product_knowledge/create") : common.baseURL("product_knowledge/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "product_knowledge",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id', value: param.id});
                }
                form.push({name: 'usersession', value: paramsession.username});
                return true; // MANDATORY!
            }
        });

        uiBtnCancel.click(function () {
            common.direct("product_knowledge");
        });

        uiSelectBrand.select2({
            placeholder: 'Select brand',
            allowClear: true,
        });
    }

    function initializeParam() {
        common.loading();
        let resolver = new HttpResolver();
        $.when(
            $.post(common.baseURL("ref_brand/load")),
        ).done(function (data, textStatus, jqXHR) {
        }).then(function (r1, r2) {
            common.loadingClose();
            setupForm(r1);
        }).fail(resolver.fail);
    }

    function setupForm(r1) {
        let rows = r1.rows
        uiSelectBrand.select2({
            placeholder: 'Select brand',
            allowClear: true,
            data: $.map(rows, function (o) {
                o.id = o.brandid + '||' + o.brand; // replace name with the property used for the text
                o.text = o.brand; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectBrand.val(param.brandid + '||' + param.brand).trigger('change');
        else uiSelectBrand.val(null).trigger('change');
    }

    document.getElementById('fileInput').addEventListener('change', function () {
        const list = document.getElementById('fileList');
        list.innerHTML = '';
        for (let file of this.files) {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = file.name;
            list.appendChild(li);
        }
    });

    const elements = document.getElementsByClassName('btn-preview_file');
    for (let i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function () {
            let pdf = $(this).attr('data-pdf');

            $('#fileModalLabel').html(`Brand: <b>${param.brand}</b> <br/> Judul: <b>${param.judul}</b>`);
            $('#bodyFileModal').html(`<embed src="${pdf}" type="application/pdf" width="100%" height="500px" />`);
            $('#viewFileModal').modal('show');
        });
    }

    function previewFiles(data) {
        if (data && data.files) {
            const files = data.files.split('||').map(f => baseURL + f);

            const list = document.getElementById('fileListData');
            list.innerHTML = '';
            for (let file of files) {
                let ext = file.split('.').pop();

                const li = document.createElement('li');
                li.className = 'list-group-item';
                if (ext.toLowerCase() == 'pdf') li.innerHTML = `<a tabindex="0" class="pointer btn-preview_file" data-pdf="${file}">Show PDF</a>`;
                else li.innerHTML = `<img src="${file}" width="100">`;
                list.appendChild(li);
            }
        }
    }
})();