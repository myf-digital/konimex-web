(function () {

    const common = new Common();
    common.setTitle("Aspek");
    // declare dom
    let uiForm = $("#fm-aspek");
	let uiSelectTipePertanyaan = $("#id-tipe_pertanyaan");
    let uiBtnCancel = $("#btn-cancel-form");
    let uiSelectTipe = $("#id-tipe_pertanyaan");
    // define from *-content.js
    let param = common.getCookie("module.aspek.update");
	let isUpdate = param !== undefined; // flag create update

    initialize();
	initializeParamTipePertanyaan();
	show_jawaban();
	
	function show_jawaban () 
	{
		var tipe_pert = uiSelectTipe.val();
			if (tipe_pert==1) {
				$('#show_type_soal_mc').show();
			}else if (tipe_pert==2) {
				$('#show_type_soal_mc').show();
			} else{
				$('#show_type_soal_mc').hide();
			}
			
	}
	
    function initialize() {
        let url = param === undefined ? common.baseURL("ref_aspek/create") : common.baseURL("ref_aspek/update");
        uiForm.initForm({
            url: url,
            param: param,
            directUrl: "ref_aspek",
            beforeSubmit: function (form, options) {
				if (param !== undefined) {
                    form.push({name: 'id_aspek', value: param.id_aspek});
                }
                return true; // MANDATORY!
            }
        });
        uiBtnCancel.click(function () {
            common.direct("ref_aspek");
        });
		uiSelectTipe.change(function () {
			show_jawaban ();
		});
    }

    function setupFormTipePertanyaan(r1, r2) {
        let rows = [];
        rows = rows.concat(r1.rows);
        uiSelectTipePertanyaan.select2({
            data: $.map(rows, function (o) {
                o.id = o.id_tipe; // replace name with the property used for the text
                o.text = o.nama; // replace name with the property used for the text
                return o;
            }),
        });
        if (isUpdate) uiSelectTipePertanyaan.val(param.id_tipe).trigger('change');

    }

    function initializeParamTipePertanyaan() {
        common.loading();
        let resolver = new HttpResolver();
        let param = new Filter();
        $.when(
            $.post(common.baseURL("ref_aspek/load_tipe_pertanyaan"), param.build()),
        ).done(function (data, textStatus, jqXHR) {
            console.log("done");
        }).then(function (r1, r2) {
            common.loadingClose();
            setupFormTipePertanyaan(r1);
        }).fail(resolver.fail);
    }
	
	
		var wrapper         = $(".input_fields_wrap"); //Fields wrapper
		var add_button      = $("#add_button"); //Add button ID
		var x = 0;
		
		 //initlal text box count
		$(add_button).click(function(e){ //on add input button click
			e.preventDefault();
			//if(x < max_fields){ //max input box allowed
				x++; //text box increment
				$(wrapper).append('<input id="id_listjawaban_'+x+'" type="hidden" name="id_listjawaban[]"  value="NA" autocomplete="off" />\
							<input id="flag_'+x+'" type="hidden" name="flag[]"  value="2" autocomplete="off" />\
							<label class="input"><input id="listjawaban_'+x+'" type="text" name="listjawaban[]" class="form-control" value="" autocomplete="off" style="height:30px; width:350px; margin:5px 0 0 0; padding-top:5px;"/> </label>\
						<a href="#" class="remove_field"><i class="fa fa-lg fa-fw fa-times" style="padding-top:10px; color:red;"></i></a>\
					'); //add input box

		});
	   
		$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
			if (confirm('Apakah Anda Ingin Menghapus Jawaban ?')) {
				// Save it!
				e.preventDefault(); $(this).parent('div').remove(); x--;
			}
			return false;
		});
		
		if (isUpdate){
			if(uiSelectTipePertanyaan.val(param.id_tipe)!= 3){
				var listjawaban = param.listjawaban;
				var listArr = listjawaban.split('|');
				for(let i = 0; i < listArr.length; i++){

					x++; //text box increment
					$(wrapper).append('<input id="id_listjawaban_'+x+'" type="hidden" name="id_listjawaban[]"  value="NA" autocomplete="off" />\
								<input id="flag_'+x+'" type="hidden" name="flag[]"  value="2" autocomplete="off" />\
								<label class="input"><input id="listjawaban_'+x+'" type="text" name="listjawaban[]" class="form-control" value="'+listArr[i]+'" autocomplete="off" style="height:30px; width:350px; margin:5px 0 0 0; padding-top:5px;"/> </label>\
							<a href="#" class="remove_field"><i class="fa fa-lg fa-fw fa-times" style="padding-top:10px; color:red;"></i></a>\
						'); //add input box
					$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
						if (confirm('Apakah Anda Ingin Menghapus Jawaban ?')) {
							// Save it!
							e.preventDefault(); $(this).parent('div').remove(); x--;
						}
						return false;
					});
					
				}
			}
		}

})();