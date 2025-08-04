(function () {
    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    const baseURL = document.getElementById('base_url')?.content;
    // update title
    common.setTitle("Product Knowledge");
    // ui components
    let uiTbl = $("#tbl-product-knowledge");

    initializeGrid();
    initialize();

    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.product_knowledge.update");
            common.direct("product_knowledge/form");
        });
    }

    function initializeGrid() {
        let option = {
            title: "Product Knowledge",
            toolbar: toolbar(),
            url: common.baseURL("product_knowledge/load"),
            pageNumber: 1,
            pageSize: commonGrid.getCurentSize(),
            pageList: commonGrid.getPageSize(),
            frozenColumns: [[
                {
                    field: 'options',
                    title: 'ACTION',
                    width: 100,
                    halign: 'center',
                    align: 'center',
                    formatter: formatterButton
                }
            ]],
            columns: [[
				{field:'judul', title:'Judul', halign: 'left', align: 'left', sortable:"true", width:200},
				{field:'brandid', title:'Brand ID', halign: 'center', align: 'center', sortable:"true", width:75},
				{field:'brand', title:'Brand', halign: 'center', align: 'center', sortable:"true", width:100},
				{field:'files', title:'File', halign: 'center', align: 'center', sortable:"true", width:75, formatter: formatterFile},
            ]],
            onBeforeLoad: function (param) {
                
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
        uiTbl.datagrid('enableFilter');
        common.removeFilter(['options']);        
    }

    function toolbar() {
        const btnCreate = commonGrid.btnBuilderDash('btn-create', 'success', '../assets/images/ic_edit.png');
        return '<div class="action-grid-toolbar">' + btnCreate + '</div>';
    }

    function optionButton(data) {
        let btnContent = $(".action-grid");
        let btnFiles = $(".btn-preview_files");
        let index = 0;
        for (const btns of btnContent) {
            const param = data.rows[index];
            const btnEdit = $(btns).find("a.btn-success");
            const btnDelete = $(btns).find("a.btn-danger");
            btnEdit.click(function () {
                updateRow(param);
            });
            btnDelete.click(function () {
                deleteRow(param);
            });
            btnFiles.click(function () {
                previewFiles(param);
            });
            index++;
        }
    }

    /*
    * action button generator
    */
    function formatterButton(val, row, index) {
        const btnUpdate = commonGrid.btnBuilderDash('btn-update', 'success', '../assets/images/ic_edit.png');
        const btnDelete = commonGrid.btnBuilderDash('btn-delete', 'danger', '../assets/images/ic_trash.png');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnDelete + '</div>';
    }

    function formatterFile(val, row, index) {
        let html = '';
        if (row && row.files) {
            html = `<a tabindex="0" class="pointer btn-preview_files">Show</a>`;
        }
        return html;
    }

    function previewFiles(data) {
        $('#fileModalLabel').html(`Brand: <b>${data.brand}</b> <br/> Judul: <b>${data.judul}</b>`);

        let html = '';
        if (data && data.files) {
            const files = data.files.split('||').map(f => baseURL + f);
            files.forEach((f, i) => {
                let ext = f.split('.').pop();
                if (ext.toLowerCase() == 'pdf') {
                    html += `<p>File ${i+1}</p><embed src="${f}" type="application/pdf" width="100%" height="500px" />`;
                } else html += `<p>File ${i+1}</p><div class="text-center"><img src="${f}" width="400"></div>`;
            });
        }
        $('#bodyFileModal').html(html);

        $('#viewFileModal').modal('show');
    }

    function updateRow(val) {
        common.setCookie("module.product_knowledge.update", val);
        common.direct("product_knowledge/form");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("product_knowledge/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Delete success!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }
})();
