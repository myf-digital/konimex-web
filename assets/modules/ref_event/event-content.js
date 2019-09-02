(function () {

    // import commons
    const common = new Common();
    const commonGrid = new CommonGrid();
    // update title
    common.setTitle("Event");
    // ui components
    let uiTbl = $("#tbl-event");
	let uiSelectRdgPublish = $("#id-rdg-Publish");
    
	
	//let param = common.getCookie("module.event.update");
	//let isUpdate = param !== undefined; // flag create update

    initializeGrid();
    initialize();
	
    /*
    * initialize content
    */
    function initialize() {
        $("#btn-create").click(function () {
            common.removeCookie("module.event.update");
            common.direct("ref_event/form");
        });
    }
    
	function setupFormRdgPublish(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectRdgPublish.select2({
			closeOnSelect : false,
			placeholder : "Select Publish",
			allowHtml: true,
			allowClear: true,
			tags: true,
            data: $.map(rows, function (o) {
                o.id = o.id_rdg; // replace name with the property used for the text
                o.text = o.nama_rdg; // replace name with the property used for the text
                return o;
            }),
        });

        /*if (isUpdate) {
			let gRdg = param.group_rdg;
			let gRdgArr = gRdg.split(',');
			uiSelectRdgPublish.val(gRdgArr).trigger('change');
		}*/
    }
	
    function initializeParamRdgPublish() {
		common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_event/load_mapping_rdg"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormRdgPublish(r1);
        }).fail(resolver.fail);
    }

    function initializeGrid() {
        let option = {
            title: "Event",
            toolbar: toolbar(),
            url: common.baseURL("ref_event/load"),
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
				//{field:'id_event', title:'ID EVENT', halign: 'center', align: 'left', sortable:"true", width:200},
				{field:'event', title:'EVENT', halign: 'center', align: 'left', sortable:"true", width:300},
				{field:'start_reformat_tanggal', title:'Start Periode', halign: 'center', align: 'left', sortable:"true", width:120},
				{field:'end_reformat_tanggal', title:'End Periode', halign: 'center', align: 'left', sortable:"true", width:120},
				//{field:'tanggal', title:'TANGGAL', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'id_rdg', title:'ID RDG', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'id_satker', title:'ID SATKER', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_by', title:'CREATED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'created_date', title:'CREATED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_by', title:'MODIFIED BY', halign: 'center', align: 'left', sortable:"true", width:200},
				//{field:'modified_date', title:'MODIFIED DATE', halign: 'center', align: 'left', sortable:"true", width:200},
            ]],
            onBeforeLoad: function (param) {
                
            },
            onLoadSuccess: function (data) {
                $(this).datagrid('resize');
                optionButton(data);
            }
        };
        uiTbl.datagrid(commonGrid.optionValue(option));
    }

    function toolbar() {
        const btnCreate = commonGrid.btnBuilder('btn-create', 'success', 'fa fa-pencil');
        return '<div class="action-grid-toolbar">' + btnCreate + '</div>';
    }

    function optionButton(data) {
        let btnContent = $(".action-grid");
        let index = 0;
        for (const btns of btnContent) {
            const param = data.rows[index];
            const btnEdit = $(btns).find("a.btn-success");
            const btnDelete = $(btns).find("a.btn-danger");
            const btnView = $(btns).find("a.btn-info");
            btnEdit.click(function () {
                updateRow(param);
            });
            btnDelete.click(function () {
                deleteRow(param);
            });
			btnView.click(function () {
                viewPublishRow(param);
            });
            index++;
        }
    }

    /*
    * action button generator
    */
    function formatterButton(val, row, index) {
        const btnUpdate = commonGrid.btnBuilder('btn-update', 'success', 'fa fa-pencil');
        const btnDelete = commonGrid.btnBuilder('btn-delete', 'danger', 'fa fa-times');
        const btnView = commonGrid.btnBuilder('btn-view', 'info', 'fa fa-navicon');
        return '<div class="action-grid">' + btnUpdate + ' ' + btnDelete + ' ' + btnView + '</div>';
    }

    function updateRow(val) {
        common.setCookie("module.event.update", val);
        common.direct("ref_event/form");
    }

    function viewPublishRow(val) {
        common.setCookie("module.event.update_publish", val);
        common.direct("ref_event/form_publish");
    }

    function deleteRow(val) {
        common.dialogDelete(function () {
            $.post("ref_event/delete", val, function (data, status) {
                if (200 === data.code) {
                    $.alert("Delete success!");
                    uiTbl.datagrid("reload");
                } else {
                    $.alert(status);
                }
            })
        });
    }

    function viewRow(val) {
		//initializeParamRdgPublish();
		$('#modal-content').modal({
        show: true
		});
    }

})();