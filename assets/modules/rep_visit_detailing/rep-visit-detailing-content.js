(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Visit Detailing");
  // ui components
  let uiTbl = $("#tbl");
  let uiMaps = $("#maps");
  let paramsession = common.getCookie("session");

  initializeGrid();
  setupFormUI();
  initializeParamMaps();
  /*
   * initialize content
   */
  function initializeGrid() {
    let option = {
      title: "Visit Detailing",
      toolbar: toolbar(),
      url: common.baseURL("rep_visit_detailing/load"),
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "Action",
            width: 100,
            halign: "center",
            align: "center",
            formatter: formatterButton,
          },
        ],
      ],
      columns: [
        [
          {
            field: "periode",
            title: "Periode",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "salesmanid",
            title: "TPE ID",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_salesman",
            title: "TPE",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "nama_customer",
            title: "Outlet",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "professional_name",
            title: "User",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "spesialisasi",
            title: "Spesialisasi",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "status_label",
            title: "Status",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 125,
          },
          {
            field: "keterangan",
            title: "Keterangan",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "reason",
            title: "Reason",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 150,
          },
        ],
      ],
      onBeforeLoad: function (param) {
        if (paramsession) {
          param.usersession = paramsession.username;
          param.restrict_level = paramsession.restrict_level;
          param.idjabatan = paramsession.idjabatan;
        }
      },
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        optionButton(data);
      },
    };
    uiTbl.datagrid(commonGrid.optionValue(option));
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);
    common.removeFilter(["detail"]);
  }

  function setupFormUI() {
    let uiTanggalPicker1 = $("#get_date1");
    uiTanggalPicker1
      .datepicker({
        format: "yyyy-mm-dd",
      })
      .datepicker("setDate", new Date())
      .on("change", function () {
        $(".datepicker").hide();
      });

    let uiTanggalPicker2 = $("#get_date2");
    uiTanggalPicker2
      .datepicker({
        format: "yyyy-mm-dd",
      })
      .datepicker("setDate", new Date())
      .on("change", function () {
        $(".datepicker").hide();
      });

    uiTanggalPicker1.on("changeDate", function (selected) {
      var startDate = new Date(selected.date.valueOf());
      uiTanggalPicker2.datepicker("setStartDate", startDate);
      if (uiTanggalPicker1.val() > uiTanggalPicker2.val()) {
        uiTanggalPicker2.val(uiTanggalPicker1.val());
      }
    });

    let uiBtnSearch = $("#btn-search");
    uiBtnSearch.click(function () {
      uiTbl.datagrid("load", {
        get_date1: uiTanggalPicker1.val(),
        get_date2: uiTanggalPicker2.val(),
        usersession: paramsession ? paramsession.username : "",
        restrict_level: paramsession ? paramsession.restrict_level : "",
        idjabatan: paramsession ? paramsession.idjabatan : "",
      });
    });

    let uiBtnDownload = $("#btn-download");
    uiBtnDownload.click(function () {
      let start = uiTanggalPicker1.val();
      let end = uiTanggalPicker2.val();
      if (start === null) {
        alert("Periode Start di isi...!");
      } else if (end === null) {
        alert("Periode End di isi...!");
      } else {
        save_xls(start, end);
      }
    });
  }

  function toolbar() {
    const btnSearch = commonGrid.btnBuilderText(
      "btn-search",
      "primary",
      "fa fa-search",
      " Search",
    );
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "success",
      "fa fa-download",
      " Download",
    );
    return (
      '<div class="action-grid-toolbar">' +
      "&nbsp;&nbsp;&nbsp; Periode : &nbsp;" +
      '<input type="text" id="get_date1" value="" name="get_date1" readonly>' +
      '&nbsp; - &nbsp; <input type="text" id="get_date2" value="" name="get_date2" readonly>' +
      btnSearch +
      btnDownload +
      "</div>"
    );
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnPreview = $(btns).find("a.btn-viem-maps, a.btn-success");
      btnPreview.off("click").on("click", function () {
        open_detail(param.salesmanid, param.periode, param.nama_salesman);
      });
      const btnImage = $(btns).find("a.btn-viem-image, a.btn-info");
      btnImage.off("click").on("click", function () {
        open_image(param);
      });
      const btnSurvei = $(btns).find("a.btn-survei, a.btn-warning");
      btnSurvei.off("click").on("click", function () {
        open_survei(param);
      });
      index++;
    }
  }

  /*
   * action button generator
   */
  function formatterButton(val, row, index) {
    const btnPreview = commonGrid.btnBuilder(
      "btn-viem-maps",
      "success",
      "fa fa-map-o",
    );

    let btnImage = "";
    if (
      (row.url_img_detailing && row.url_img_detailing !== "null" && row.url_img_detailing !== "") ||
      (row.url_file_signature && row.url_file_signature !== "null" && row.url_file_signature !== "") ||
      (row.url_file_serahterima && row.url_file_serahterima !== "null" && row.url_file_serahterima !== "")
    ) {
      btnImage = commonGrid.btnBuilder("btn-viem-image", "info", "fa fa-image");
    }

    let btnSurvei = "";
    if (parseInt(row.has_survei || 0) > 0) {
      btnSurvei = commonGrid.btnBuilder(
        "btn-survei",
        "warning",
        "fa fa-clipboard",
      );
    }

    return (
      '<div class="action-grid">' + btnPreview + btnImage + btnSurvei + "</div>"
    );
  }

  function open_image(data) {
    if (data && data != undefined) {
      $("#myModalImage").text(
        `Visit Detailing: ${data.nama_salesman}, Outlet: ${data.nama_customer}`,
      );
      let imgHtml = '<div class="row text-left">';
      if (data.url_img_detailing) {
        imgHtml += `
          <div class="col-md-12" style="margin-bottom:15px;">
            <h5><b>1. Foto Detailing</b></h5>
            <img src="${data.url_img_detailing}" alt="Foto Detailing" class="img-fluid" style="max-width:100%;">
          </div>
        `;
      }
      if (data.url_file_signature) {
        imgHtml += `
          <div class="col-md-12" style="margin-bottom:15px;">
            <h5><b>2. Foto Signature</b></h5>
            <img src="${data.url_file_signature}" alt="Foto Signature" class="img-fluid" style="max-width:100%;">
          </div>
        `;
      }
      if (data.url_file_serahterima) {
        imgHtml += `
          <div class="col-md-12" style="margin-bottom:15px;">
            <h5><b>3. Foto Serah Terima</b></h5>
            <img src="${data.url_file_serahterima}" alt="Foto Serah Terima" class="img-fluid" style="max-width:100%;">
          </div>
        `;
      }
      imgHtml += '</div>';
      $("#show-image").html(imgHtml);
      $("#modal_image").modal("show");
    }
  }

  function open_detail(sid, periode, nama) {
    $.ajax({
      type: "POST",
      dataType: "html",
      url: common.baseURL("rep_visit_detailing/get_gmap"),
      data: "sid=" + sid + "&periode=" + periode,
      success: function (res) {
        $("div .modal-header .modal-title").text("Visit Detailing " + nama);
        $("#modal_detail").modal("show");
        $("#maps").html(res);
      },
      error: function () {
        alert("Load failed");
      },
    });
  }

  $("#modal_detail").on("shown.bs.modal", function () {
    if (typeof google !== "undefined" && typeof google.maps !== "undefined") {
      if (typeof map !== "undefined" && map) {
        google.maps.event.trigger(map, "resize");
        if (typeof directionsDisplay !== "undefined" && directionsDisplay) {
          directionsDisplay.setMap(map);
        }
      }
    }
  });

  function open_survei(data) {
    $.ajax({
      type: "POST",
      dataType: "json",
      url: common.baseURL("rep_visit_detailing/survei"),
      data:
        "siteid=" +
        data.siteid +
        "&periode=" +
        data.periode +
        "&salesmanid=" +
        data.salesmanid +
        "&customerid=" +
        data.customerid,
      success: function (res) {
        response = res;
        if (response && response.length > 0) {
          $("#myModalSurvei").html(`
                        Hasil Survei: <b>${data.nama_salesman}</b> <br>
                        <small>Outlet: ${data.nama_customer}</small> <br>
                        <small>User: ${response[0].nama_professional || "-"}</small>
                    `);
          $("#show-survei").html(`
                        <div class="row">
                            <div class="col-md-12">
                                ${response
                                  .map(
                                    (item) => `
                                    <ol class="text-left">
                                        <li>
                                            <h5><b>${item.question}</b></h5>
                                            <p>Jawaban: ${item.answer}</p>
                                        </li>
                                    </ol>
                                `,
                                  )
                                  .join("")}
                            </div>
                        </div>
                    `);
          $("#modal_survei").modal("show");
        } else {
          alert("Tidak ada hasil survei untuk data ini.");
        }
        console.warn(response);
      },
      error: function () {
        alert("Load failed");
      },
    });
  }

  function initializeParamMaps() {
    common.loading();
    let resolver = new HttpResolver();
    let filter = new Filter();

    $.when($.post(common.baseURL("api_v1/call_siteid"), filter.build()))
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1) {
        common.loadingClose();
        initializemap(r1);
      })
      .fail(resolver.fail);
  }

  function initializemap(r1) {
    let rows = r1.result[0];
    let vloc = { lat: Number(rows.latitude), lng: Number(rows.longitude) };
    var myOptions = {
      zoom: 12,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      zoomControl: true,
      center: new google.maps.LatLng(vloc),
      mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    var map;
    map = new google.maps.Map(document.getElementById("maps"), myOptions);
  }

  function save_xls(start, end) {
    let username = paramsession && paramsession.username ? paramsession.username : "";
    let restrict_level = paramsession && paramsession.restrict_level ? paramsession.restrict_level : "";
    let idjabatan = paramsession && paramsession.idjabatan ? paramsession.idjabatan : "";

    let baseUrl = common.baseURL("rep_visit_detailing/savetoxlsx");
    baseUrl = baseUrl.replace(/([^:]\/)\/+/g, "$1").replace(/\/+$/, "");

    let downloadUrl =
      baseUrl +
      "?get_date1=" + encodeURIComponent(start) +
      "&get_date2=" + encodeURIComponent(end) +
      "&usersession=" + encodeURIComponent(username) +
      "&restrict_level=" + encodeURIComponent(restrict_level) +
      "&idjabatan=" + encodeURIComponent(idjabatan);

    window.location.href = downloadUrl;
  }
})();
