    /*
		CONFIG FORM
	*/
	var tbl, setup = 0;
	var gridConfigGeneral = {
			singleSelect: true,
			resizable: true,
			collapsible: true,
			fitColumn: true,
			rownumbers: false,
			pagination: true,
			nowrap: false,
			remoteFilter: true,
			autoRowHeight: false, 		// default true
		};
	
	var gridConfigGeneralNoRowNum = {
			singleSelect: true,
			resizable: true,
			collapsible: true,
			fitColumn: true,
			rownumbers: false,
			pagination: true,
			nowrap: false,
			remoteFilter: true,
			autoRowHeight: false, 		// default true
		};
	
	var gridConfigGeneralNoRowNumFixHeight = {
			singleSelect: true,
			resizable: true,
			collapsible: true,
			fitColumn: true,
			rownumbers: false,
			pagination: true,
			nowrap: false,
			remoteFilter: true,
			autoRowHeight: false, 		// default true
			height:480
		};
		
	// datagrid when redirect back
	function optionPage(page, size){
		var gridConfigPage = {
			pageNumber: page,
			pageSize: size,
		}
		return gridConfigPage;
	}
	
	// get index row, ok on pagging
    function getRowIndex(target) {
        var tr = $(target).closest('tr.datagrid-row');
        return parseInt(tr.attr('datagrid-row-index'));
    }
	
	// handler click without select row
    function getRowOnClick(target) {
		getUserSessionValid();
        var rows = tbl.datagrid('getRows');
        return rows[getRowIndex(target)];
    }
	
	/*
	--------------------------------------------------------------------------
		TABLE COLUMN FORMAT
	--------------------------------------------------------------------------
	*/
	
	// datagrid-required
    function myParser(s) {
        if (!s)
            return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0], 10);
        var m = parseInt(ss[1], 10);
        var d = parseInt(ss[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
            return new Date(y, m - 1, d);
        } else {
            return new Date();
        }
    }
	
	// datagrid-format date
    function myFormatter(date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        var d = date.getDate();
        return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
    }
	
	/*
	--------------------------------------------------------------------------
		TABLE COLUMN FORMAT
	--------------------------------------------------------------------------
	*/
	// inline editor on row
    function formatButtonDef(val, row, index) {
        
		// priview button
        var updateBtn = '<a onclick="editrow(id)" style="margin:4px;" class="btn btn-success btn-xs" href="javascript:void(0)" group="" data-toggle="tooltip" title="Update">\
                            <i class="fa fa-lg fa-fw fa-edit"></i>Edit\
                        </a>';
        var deleteBtn = '<a onclick="deleterow(id)" style="margin:4px;" class="btn btn-danger btn-xs" href="javascript:void(0)" group="" data-toggle="tooltip" title="Delete">\
							<i class="fa fa-lg fa-fw fa-trash-o"></i>Delete\
                        </a>';
		var scpace = '&nbsp;&nbsp;&nbsp;';
        
		var retButton =  updateBtn + scpace + deleteBtn;
		// return previewBtn
        return retButton;
		
    }
	
	// datagrid-format date
    function myFormatter(date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        var d = date.getDate();
        return y + '-' + (m < 10 ? ('0' + m) : m) + '-' + (d < 10 ? ('0' + d) : d);
    }

    // datagrid-required
    function myParser(s) {
        if (!s)
            return new Date();
        var ss = (s.split('-'));
        var y = parseInt(ss[0], 10);
        var m = parseInt(ss[1], 10);
        var d = parseInt(ss[2], 10);
        if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
            return new Date(y, m - 1, d);
        } else {
            return new Date();
        }
    }
	
	/*
	--------------------------------------------------------------------------
		TABLE COLUMN FORMAT
	--------------------------------------------------------------------------
	*/
	// requered numeral.js
	function formatNumber(val, row, index){
		return numeral(val).format('0,0');
	}
	
	// datagrid-format to image
    function formatToImage(val, row) {
        if (val !== null) {
            var values = val.split('|');
            return '<a href="javascript:void(0)" onclick="imageToFancyBox(' + row.row_id + ');" data-fancybox-group="thumb" href="#"><img class="img-thumbnail datagrid-img" src="<?php echo base_url() . $imgdir; ?>' + values[0] + '" alt="" /></a>';
        }
    }
	
	// image to fancybox from function formatToImage
    function imageToFancyBox(id_image) {
        $.ajax({
            url: '< ?php echo (isset($url_image)?$url_image:"") ?>',
            type: 'POST',
            // async: false,
            data: {row_id: id_image},
            dataType: 'json',
            success: function (result) {
                // var p = result.images;
                var tempFile = [];
                $.each(result.images, function (index, value) {
                    var tempFileElemet = {href: '< ?php echo base_url() . (isset($imgdir)? $imgdir: ""); ?>' + value.image_name, title: value.image_name};
                    tempFile.push(tempFileElemet);
                });
                // open fancy
                $.fancybox.open(tempFile, {
                    helpers: {
                        thumbs: {
                            width: 75,
                            height: 50
                        }
                    }
                });
            }
        });
    }
	
	// format from HTML
    function formatFromHTML(val, row) {
        return '<div style="grid-format-html">' + val + '</div>';
    }
	
	// format Content
	function formatContent(val, row) {
		var previewBtn = '<a onclick="previewHtmlToFancyBox(this)" class="various l-btn l-btn-small l-btn-plain" href="javascript:void(0)" group="" data-toggle="tooltip" title="Priview">\
                            <i class="fa fa-eye fa-lg"></i>\
                        </a>';
		return previewBtn;
	}
	
	/*
	--------------------------------------------------------------------------
	UPDATE | DELETE
	--------------------------------------------------------------------------
	*/
	
	// core function edit
    function editrow(target) {
		//getUserSessionValid();
        var row = getRowOnClick(target);
        if (row) {
            $('#page-wrapper').load("<?php echo (isset($url_update)?$url_update:''); ?>",
			{psize: tbl.datagrid('options').pageSize, pnumber: tbl.datagrid('options').pageNumber},
                    function () {
                        $('#admin-crud').form('load', row);
                        setFormUpdate(row); // only for update, location on view-crud
                        resizeContentDOMFit();
                    }
            );
        }
    }

    // core function delete
    function deleterow(target) {
		//getUserSessionValid();
        $.messager.confirm('Konfirmasi', 'Hapus data?', function (r) {
            if (r) {
                var row = getRowOnClick(target);
                $.post("<?php echo (isset($url_delete)?$url_delete:''); ?>", {row_id: row.row_id}, function (result) {
                    if (result.success) {
                        tbl.datagrid('deleteRow', getRowIndex(target));
                    } else {
                        $.messager.show({// show error message
                            title: 'Error',
                            msg: result.errorMsg
                        });
                    }
                }, 'json');
            }
        });
    }
	
	