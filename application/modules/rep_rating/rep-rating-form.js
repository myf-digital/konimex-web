(function () {

    const common = new Common();
    common.setTitle("Report Rating");

    let uiStartPeriode = $("#start_periode"); 
    let uiEndPeriode = $("#end_periode");
    let uiStart = $("#start-periode"); 
    let uiEnd = $("#end-periode");
    let uiBtnPreview = $("#btn-preview-form");

    let paramsession = common.getCookie("session");

    // Total number of rows visible at a time
    var limit = 10;

    initializeParam();

    function initializeParam() {
        uiBtnPreview.click(function() {
            if ( uiStartPeriode.val() === '' || uiEndPeriode.val() === ''){
                alert ('Periode harus di isi...!');
            }else{
                load_data_outlet();
            }
        });

        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });

        uiStartPeriode.on('changeDate', function(selected) {
            var startDate = new Date(selected.date.valueOf());
            var endDate = new Date(selected.date.valueOf());
            endDate.setDate(endDate.getDate() + 31);
            uiEndPeriode.datepicker('setStartDate', startDate);
            uiEndPeriode.datepicker('setEndDate', endDate);
            if(uiStartPeriode.val() > uiEndPeriode.val()){
                uiEndPeriode.val(uiStartPeriode.val());
            }

            uiStart.val("0");
        });

        uiEndPeriode.on('changeDate', function(selected) {
            uiEnd.val("0");
        });
    }

    function load_data_outlet() {

        let offset = $("#offset").val();
        let start = uiStart.val() != '0' ? uiStart.val() : uiStartPeriode.val();
        let end = uiEnd.val() != '0' ? uiEnd.val() : uiEndPeriode.val();

        common.loading();

        $.ajax({
            type:"post",
            dataType: "json",
            url: common.baseURL("rep_rating/load"),
            data : "limit="+limit+"&offset="+offset+"&start="+start+"&end="+end,
            success:function(res){

                $('html, body').animate({
                    scrollTop: $("#outlet-list").offset().top
                }, 1000);

                if (Number(offset) == 0) {
                    $("#outlet-prev").attr('disabled', 'disabled');
                } else {
                    $("#outlet-prev").removeAttr('disabled');
                }

                if ((Number(offset)+Number(limit)) > Number(res.resultCount)) {
                    $("#outlet-next").attr('disabled', 'disabled');
                } else {
                    $("#outlet-next").removeAttr('disabled');
                }

                createRow(res.result);

                $("#result-count").val(res.resultCount);
                
                if (res.result.length > 0) {
                    $(".box-footer").show();
                } else {
                    $(".box-footer").hide();
                }

                uiStart.val(start);
                uiEnd.val(end);

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

    function createRow(result) {

       $('#outlet-list').empty();

        for(index in result) {

            var content = '<div class="rating-box effect-rating-box" id="img-target" data-id="'+result[index].customerid+'" data-nama="'+result[index].nama_customer+'" data-star="'+result[index].rating_star+'">';
            content += '<p><img class="img-rounded img-outlet" src="'+result[index].image+'"></p>';
            content += '<p class="title">'+result[index].nama_customer+'</p>';
            content += '<p class="subtitle">'+result[index].outletid+' . '+result[index].nama_class+' . '+result[index].salesmanid+'</p>';
            content += star(result[index].rating_star);
            content += '</div>';
            
            $('#outlet-list').append(content);
        }
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
            content += '<input type="hidden" id="start-periode-detail" value="'+option.start+'">';
            content += '<input type="hidden" id="end-periode-detail" value="'+option.end+'">';
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

    $("#outlet-prev").click(function(){
        var offset = Number($("#offset").val());
        var resultCount = Number($("#result-count").val());
        offset -= limit;
        if(offset < 0){
            offset = 0;
        }
        $("#offset").val(offset);
        load_data_outlet();
    });

    $("#outlet-next").click(function(){
        var offset = Number($("#offset").val());
        var resultCount = Number($("#result-count").val());
        offset += limit;
        if(offset <= resultCount){
            $("#offset").val(offset);
            load_data_outlet();
        }

    });

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

    $(document).on('click', '#img-target', function(e){
        e.preventDefault();

        $('html, body').animate({
            scrollTop: $("#tbl-content").offset().top
        }, 1000);

        $('.rating-box').removeClass('outlet-checked');

        let start = uiStart.val() != '0' ? uiStart.val() : uiStartPeriode.val();
        let end = uiEnd.val() != '0' ? uiEnd.val() : uiEndPeriode.val();

        let id = $(this)[0].dataset.id;
        let nama = $(this)[0].dataset.nama;
        let star = $(this)[0].dataset.star;
        let filter = 'all';
        $(this).addClass('outlet-checked');

        let offset = 0;

        common.loading();

        $.ajax({
            type:"post",
            dataType: "json",
            url: common.baseURL("rep_rating/load_detail"),
            data : "id="+id+"&nama="+nama+"&star="+star+"&start="+start+"&end="+end+"&filter="+filter+"&limit="+limit+"&offset="+offset,
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
        let start = $("#start-periode-detail").val();
        let end = $("#end-periode-detail").val();
        let offset = $("#offset-detail").val();

        common.loading();

        $.ajax({
            type:"post",
            dataType: "json",
            url: common.baseURL("rep_rating/load_detail_content"),
            data : "id="+id+"&filter="+filter+"&start="+start+"&end="+end+"&offset="+offset+"&limit="+limit,
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

})();