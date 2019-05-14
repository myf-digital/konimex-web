(function ($) {

    CommonGrid = function () {

    }

    var dgOption = {
        width: 'auto',
        // height: '100%', // dont set this if u want auto height by data
        minHeight: 480,
        singleSelect: true,
        resizable: true,
        collapsible: true,
        fit:false,
        fitColumn: true,
        rownumbers: true,
        pagination: true,
        nowrap: true,
        remoteSort: true,
        remoteFilter: true,
        autoRowHeight: true,
        pageSize: 30,
        pageList: [30, 60, 90, 120, 150],
    };

    CommonGrid.prototype.optionValue = function (output) {
        let source = dgOption;
        for (let prop in source) {
            if (source.hasOwnProperty(prop)) {
                output[prop] = source[prop];
            }
        }
        return output;
    }

    CommonGrid.prototype.merge = function (source, output) {
        for (let prop in source) {
            if (source.hasOwnProperty(prop)) {
                output[prop] = source[prop];
            }
        }
        return output;
    }

    CommonGrid.prototype.mergeObject = function (source, output) {
        for (var prop in source) {
            if (source.hasOwnProperty(prop)) {
                output[prop] = source[prop];
            }
        }
        return output;
    }

    CommonGrid.prototype.getCurentSize = function () {
        // var height = window.screen.height;
        // if (height >= 864) {
        //     return 14;
        // } else {
        //     return 10;
        // }
        return 30;
    }

    CommonGrid.prototype.getPageSize = function () {
        // var height = window.screen.height;
        // if (height >= 864) {
        //     return [14, 30, 45, 60];
        // } else {
        //     return [10, 20, 30, 40, 50];
        // }
        return [30, 60, 90, 120, 150]
    }

    /*
    color: bootsrap class color button
    */
    CommonGrid.prototype.btnBuilder = function (id, color, icon) {
        if (undefined === color) color = "btn-primary";
        if (undefined === icon) icon = "";
        return '<a id="' + id + '" href="javascript:void(0)" style="width: 25px;height: 25px" class="btn btn-' + color + ' btn-xs" ><i style="padding-top: 5px" class="' + icon + '"></i></a>';
    }

    /*
    color: bootsrap class color button
    */
    CommonGrid.prototype.btnGenerator = function (color, icons, action) {
        if (undefined == color) color = 'btn-primary';
        if (undefined == icons) icons = '';
        if (undefined == action) action = 'javascript:void(0)';
        return '<a href="javascript:void(0)" style="width: 30px;height: 30px" class="btn btn-' + color + ' btn-xs" ><i style="padding-top: 7px" class="' + icons + '"></i></a>';
    }

    // get index row, ok on pagging
    CommonGrid.prototype.getRowIndex = function (target) {
        var tr = $(target).closest('tr.datagrid-row');
        return parseInt(tr.attr('datagrid-row-index'));
    }

    // handler click without select row
    CommonGrid.prototype.getRowValue = function (tbl, target) {
        var rows = tbl.datagrid('getRows');
        rows.xdata = localStorage.getItem('xdata');
        return rows[getRowIndex(target)];
    }

    CommonGrid.prototype.resizeDatagrid = function () {
        var dg = $("table.resizeable");
        if (dg.data('datagrid')) {
            dg.datagrid("resize");
        }
    }

}(jQuery));


$(document).ready(function () {

    var doResize;

    $("div.content-tbl").resize(function () {
        clearTimeout(doResize);
        doResize = setTimeout(function () {
            resizeDatagrid();
        }, 20);

    });
});