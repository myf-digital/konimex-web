(function () {

    const common = new Common();
    common.setTitle("Report Top Rating");

    let uiSelectSalesman = $("#salesmanid-id");
    let uiInputSearch = $("#search-id");
    let uiSelectTop = $("#top_rating-id");
    let uiBtnPreview = $("#btn-preview-form");
    let uiBtnDownload = $("#btn-download-form");

    let paramsession = common.getCookie("session");

    // Total number of rows visible at a time
    var limit = 10;

    initializeParam();

    load_data_outlet();

    function initializeParam() {
        uiBtnPreview.click(function() {
            load_data_outlet();
        });

        uiBtnDownload.click(function () {
            save_xls();
        });

        uiSelectTop.select2({
            placeholder: 'Select Top Rating',
            data: [
                {
                    id: 10,
                    text: "Top 10"
                },
                {
                    id: 50,
                    text: "Top 50"
                },
                {
                    id: 100,
                    text: "Top 100"
                }
            ],
        });

        loadSalesman(paramsession);

    }

     function loadSalesman(data) {
        common.loading();
        $.post(common.baseURL("api_v1/call_salesman"), {idjabatan:data.idjabatan, usersession:data.usersession,restrict_level: data.restrict_level}, function (res) {
            uiSelectSalesman.empty();
            uiSelectSalesman.select2({
                placeholder: "Select User GFF",
                allowClear: true,
                data: $.map(res.result, function (o) {
                    o.id = o.salesmanid; // replace name with the property used for the text
                    o.text = o.salesmanid + " - " +o.nama_salesman + " - " + o.tipe_sales;
                    return o;
                }),
            });
            
            uiSelectSalesman.val(null).trigger('change');

            common.loadingClose();
        });
    }

    function load_data_outlet() {

        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;

        var search = uiInputSearch.val();
        var gff = uiSelectSalesman.val();
        var top = uiSelectTop.val();

        common.loading();

        $.ajax({
            type:"POST",
            dataType: "html",
            url: common.baseURL("rep_top_rating/load"),
            data : "idjabatan="+idjabatan+"&usersession="+usersession+"&restrict_level="+restrict_level+"&search="+search+"&gff="+gff+"&top="+top,
            success:function(res){
                response = res;         
                $('#tbl-content').html(response);
                
                $('#detail-info').empty();
                $('#detail-content').empty();

                common.loadingClose();
            },
            error:function(){
                alert("Load failed");

                common.loadingClose();
            }
        });   
    }


    function createRowDetail(outlet) {
        $('#detail-info').empty();

        var content = '<div class="rating-detail-container">';
            content += '<div class="rating-detail-info">';
            content += '<div class="rating-score">';
            content += '<p>'+outlet.nama+'</p>';
            content += '<p>'+outlet.star+'</p>';
            content += '</div>';
            content += '<div class="rating-start">';
            content += star(outlet.star);
            content += '</div>';
            content += '</div>';
            content += '<div class="retaing-detail-filter">';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="all" data-id="'+outlet.id+'">Semua</button>';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="5" data-id="'+outlet.id+'">5 Bintang</button>';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="4" data-id="'+outlet.id+'">4 Bintang</button>';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="3" data-id="'+outlet.id+'">3 Bintang</button>';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="2" data-id="'+outlet.id+'">2 Bintang</button>';
            content += '<button type="button" class="btn btn-default" id="btn-star" data-filter="1" data-id="'+outlet.id+'">1 Bintang</button>';
            content += '</div>';
            content += '</div>';

        $('#detail-info').html(content);
    }

    function createRowDetailContent(result, option) {

        $('#detail-content').empty();

        let disabledPrev = '';
        let disabledNext = '';

        if (Number(option.offset) == 0) {
            disabledPrev = 'disabled = "disabled"';
        }

        if ((Number(option.offset)+Number(option.limit)) >= Number(option.resultCount)) {
            disabledNext = 'disabled = "disabled"';
        }

        var content = '';
            for(index in result) {
                let images = result[index].images;

                content += '<div class="rating-detail-content">';
                content += '<div class="rating-detail-avatar">';
                content += '<span class="fa fa-user fa-2x"></span>';
                content += '</div>';
                content += '<div class="rating-detail-content-info">';
                content += '<p>';
                content += result[index].username;
                content += '</p>';
                content += star(result[index].rating_star);
                content += '<p>';
                content += result[index].review;
                content += '</p>';

                content += '<p>';

                for(i in images) {
                    content += '<img class="img-rounded img-rating" src="'+images[i].image+'">';
                }

                content += '</p>';

                content += '<p>';
                content += result[index].periode;
                content += '</p>';
                content += '</div>';
                content += '</div>';
            }

            content += '<div class="pagination-detail">';
            content += '<input type="hidden" id="id-detail" value="'+option.id+'">';
            content += '<input type="hidden" id="filter-detail" value="'+option.filter+'">';
            content += '<input type="hidden" id="offset-detail" value="'+option.offset+'">';
            content += '<input type="hidden" id="result-count-detail" value="'+option.resultCount+'">';
            content += '<button type="button" '+disabledPrev+' class="btn btn-default" id="detail-prev"><i class="fa fa-arrow-left"></i></button>';
            content += '<button type="button" '+disabledNext+' class="btn btn-default" id="detail-next"><i class="fa fa-arrow-right"></i></button>';
            content += '</div>';

        $('#detail-content').html(content);
    }

    function star(count) {
        var star = Math.round(count);
        
        if (star == 5) {
            var html = '<p>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '</p>';

            return html;
        } else if (star == 4) {
            var html = '<p>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '</p>';

            return html;
        } else if (star == 3) {
            var html = '<p>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '</p>';

            return html;
        } else if (star == 2) {
            var html = '<p>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '</p>';

            return html;
        } else if (star == 1) {
            var html = '<p>';
                html += '<span class="fa fa-star checked"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '<span class="fa fa-star"></span>';
                html += '</p>';

            return html;
        }
    }

    $(document).on('click', '.clickable-row', function(e){
        e.preventDefault();

        $('html, body').animate({
            scrollTop: $("#tbl-content").offset().top
        }, 1000);

        let id = $(this)[0].dataset.id;
        let nama = $(this)[0].dataset.nama;
        let star = $(this)[0].dataset.star;
        let filter = 'all';

        let offset = 0;

        common.loading();

        $.ajax({
            type:"post",
            dataType: "json",
            url: common.baseURL("rep_top_rating/load_detail"),
            data : "id="+id+"&nama="+nama+"&star="+star+"&filter="+filter+"&limit="+limit+"&offset="+offset,
            success:function(res){

                createRowDetail(res.outlet);
                createRowDetailContent(res.result, res.option);

                common.loadingClose();
            },
            error:function(){
                alert("Load failed");

                common.loadingClose();
            }
        });
    })

    $(document).on('click', '#detail-prev', function(e){
        e.preventDefault();

        var offset = Number($("#offset-detail").val());
        var resultCount = Number($("#result-count-detail").val());
        offset -= limit;
        if(offset < 0){
            offset = 0;
        }
        $("#offset-detail").val(offset);
        load_detail_content()
    });

    $(document).on('click', '#detail-next', function(e){
        e.preventDefault();

        var offset = Number($("#offset-detail").val());
        var resultCount = Number($("#result-count-detail").val());
        offset += limit;
        if(offset <= resultCount){
            $("#offset-detail").val(offset);
            load_detail_content()
        }

    });

    $(document).on('click', '#btn-star', function(e){
        e.preventDefault();

        $('html, body').animate({
            scrollTop: $("#detail-info").offset().top
        }, 1000);

        let id = $(this)[0].dataset.id;
        let filter = $(this)[0].dataset.filter;

        $("#id-detail").val(id);
        $("#filter-detail").val(filter);

        load_detail_content();
    });

    function load_detail_content()
    {

        let id = $("#id-detail").val();
        let filter = $("#filter-detail").val();
        let offset = $("#offset-detail").val();

        common.loading();

        $.ajax({
            type:"post",
            dataType: "json",
            url: common.baseURL("rep_top_rating/load_detail_content"),
            data : "id="+id+"&filter="+filter+"&offset="+offset+"&limit="+limit,
            success:function(res){

                createRowDetailContent(res.result, res.option);

                common.loadingClose();
            },
            error:function(){
                alert("Load failed");

                common.loadingClose();
            }
        });
    }

    function save_xls() {
        
        var idjabatan = paramsession.idjabatan;
        var usersession = paramsession.username;
        var restrict_level = paramsession.restrict_level;

        var search = uiInputSearch.val();
        var gff = uiSelectSalesman.val();
        var top = uiSelectTop.val();
        
        common.direct("rep_top_rating/savetoxlsx/"+idjabatan+"/"+usersession+"/"+restrict_level+"/"+top+"/"+gff+"/"+search);

    }

})();