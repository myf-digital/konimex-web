(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  // update title
  common.setTitle("Outlet");
  // ui components
  let uiTbl = $("#tbl-customer");
  let paramsession = common.getCookie("session");

  initializeGrid();
  initialize();

  /*
   * initialize content
   */
  function initialize() {
    $("#btn-create").click(function () {
      common.removeCookie("module.customer.update");
      common.direct("ref_customer/form");
    });

    $("#btn-download").click(function () {
      common.removeCookie("module.setup.pjp.update");
      common.direct("ref_customer/form_download");
    });
  }

  function toolbar() {
    const btnDownload = commonGrid.btnBuilderText(
      "btn-download",
      "primary",
      "fa fa-download",
      " Download Outlet",
    );
    const btnCreate = commonGrid.btnBuilderText(
      "btn-create",
      "success",
      "fa fa-pencil",
      " Create New",
    );
    return (
      '<div class="action-grid-toolbar">' +
      btnCreate +
      "&nbsp;" +
      btnDownload +
      "</div>"
    );
  }

  function initializeGrid() {
    let option = {
      title: "Outlet",
      toolbar: toolbar(),
      url: common.baseURL("ref_customer/load"),
      queryParams: {
        usersession: paramsession.username,
        idjabatan: paramsession.idjabatan,
        restrict_level: paramsession.restrict_level,
        restrict_bu: paramsession.restrict_bu,
      },
      pageNumber: 1,
      pageSize: commonGrid.getCurentSize(),
      pageList: commonGrid.getPageSize(),
      frozenColumns: [
        [
          {
            field: "options",
            title: "ACTION",
            width: 175,
            halign: "center",
            align: "center",
            formatter: formatterButton,
          },
        ],
      ],
      columns: [
        [
          {
            field: "customerid",
            title: "ID Outlet",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "cust_id_map",
            title: "ID Outlet Distributor",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 150,
          },
          {
            field: "nama_customer",
            title: "Nama Outlet",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 200,
          },
          {
            field: "nama_regional",
            title: "Regional",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_area",
            title: "Area",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 120,
          },
          {
            field: "nama_subarea",
            title: "Sub Area",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 120,
          },
          {
            field: "typeid",
            title: "Channel",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 100,
          },
          {
            field: "nama_account",
            title: "Sub Channel",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 120,
          },
          {
            field: "alamat",
            title: "Alamat",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 350,
          },
          {
            field: "list_professional",
            title: "User",
            halign: "left",
            align: "left",
            sortable: "true",
            width: 400,
            formatter: formatterListProfessional,
          },
        ],
      ],
      onBeforeLoad: function (param) {},
      onLoadSuccess: function (data) {
        $(this).datagrid("resize");
        $(this).datagrid("autoSizeColumn", "list_professional");
        optionButton(data);
      },
    };

    let gridOptions = commonGrid.optionValue(option);
    gridOptions.fitColumns = false;
    uiTbl.datagrid(gridOptions);
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);
    common.removeFilter(["detail"]);
  }

  function formatterListProfessional(val, row, index) {
    if (val) {
      let listProfessional = val.split("||");
      let chunks = [];
      for (let i = 0; i < listProfessional.length; i += 5) {
        chunks.push(listProfessional.slice(i, i + 5));
      }

      let result =
        '<div style="display: flex; gap: 15px; align-items: start;">';
      chunks.forEach((chunk, chunkIdx) => {
        let startNum = chunkIdx * 5 + 1;
        result +=
          '<ol start="' +
          startNum +
          '" style="margin: 0; padding-left: 15px; width: 320px; min-width: 320px; max-width: 320px; white-space: normal; word-break: break-word;">';
        for (const professional of chunk) {
          result += "<li>" + professional + "</li>";
        }
        result += "</ol>";
      });
      result += "</div>";
      return result;
    }
    return "";
  }

  function optionButton(data) {
    let btnContent = $(".action-grid");
    let btnContentdtl = $(".action-grid-detail");
    let index = 0;
    for (const btns of btnContent) {
      const param = data.rows[index];
      const btnEdit = $(btns).find("a.btn-success");
      const btnDelete = $(btns).find("a.btn-danger");
      const btnLocation = $(btns).find("a.btn-info");
      btnEdit.click(function () {
        updateRow(param);
      });
      btnDelete.click(function () {
        deleteRow(param);
      });
      btnLocation.click(function () {
        showLocation(param);
      });
      index++;
    }

    let indexd = 0;
    for (const btnsd of btnContentdtl) {
      const param = data.rows[indexd];
      const btnDetailGff = $(btnsd).find("a.btn-primary");
      btnDetailGff.click(function () {
        viewGff(param);
      });
      indexd++;
    }
  }

  /*
   * action button generator
   */
  function formatterButton(val, row, index) {
    const btnUpdate = commonGrid.btnBuilderText(
      "btn-update",
      "success",
      "fa fa-edit",
      " Edit",
    );
    const btnDelete = commonGrid.btnBuilderText(
      "btn-delete",
      "danger",
      "fa fa-times",
      " Delete",
    );

    let btnLocation = "";
    if (
      row.latitude &&
      row.latitude != "0" &&
      row.longitude &&
      row.longitude != "0"
    ) {
      btnLocation = commonGrid.btnBuilderText(
        "btn-maps",
        "info",
        "fa fa-map-marker",
        " Lokasi",
      );
    }
    return (
      '<div class="action-grid">' +
      btnUpdate +
      " " +
      btnDelete +
      (btnLocation
        ? '<div style="margin-top: 5px;">' + btnLocation + "</div>"
        : "") +
      "</div>"
    );
  }

  function updateRow(val) {
    common.setCookie("module.customer.update", val);
    common.direct("ref_customer/form");
  }

  function deleteRow(val) {
    common.dialogDelete(function () {
      $.post("ref_customer/delete", val, function (data, status) {
        if (200 === data.code) {
          $.alert("Delete success!");
          uiTbl.datagrid("reload");
        } else {
          $.alert(status);
        }
      });
    });
  }

  function showLocation(val) {
    if (
      val.latitude &&
      val.latitude != "0" &&
      val.longitude &&
      val.longitude != "0"
    ) {
      get_map(val);
      return;
    }
    $.alert("Latitude Longitude kosong!");
  }

  function get_map(data) {
    $("#myModalMapsLabel").html(`Outlet: ${data.nama_customer}`);

    // maps
    setTimeout(function () {
      let lat = parseFloat(data.latitude);
      let lng = parseFloat(data.longitude);
      let newLat = lat;
      let newLng = lng;
      let map = new google.maps.Map(document.getElementById("maps"), {
        zoom: 15,
        center: { lat, lng },
      });
      let marker = new google.maps.Marker({
        position: { lat, lng },
        map: map,
        draggable: true,
        icon: {
          url: `${window.location.origin}/assets/images/ic_store_48.png`,
          labelOrigin: {
            x: 17,
            y: 45,
          },
        },
        title: data.nama_customer,
        label: {
          text: data.nama_customer,
          color: "#222222",
          fontSize: "10px",
        },
      });

      let infoWindow = new google.maps.InfoWindow();
      function updateInfoWindow(lat, lng, type) {
        let btnSave = "";
        if (type == "save")
          btnSave =
            '<br><br><button id="btnSaveLocation" class="btn btn-sm btn-primary mt-2">Simpan Lokasi</button>';
        infoWindow.setContent(`
                    <div style="font-size:13px;">
                        <strong>${data.nama_customer}</strong><br>
                        Latitude: <span id="info-lat">${lat.toFixed(6)}</span><br>
                        Longitude: <span id="info-lng">${lng.toFixed(6)}</span>
                        ${btnSave}
                    </div>
                `);
        infoWindow.open(map, marker);
      }

      marker.addListener("click", function () {
        updateInfoWindow(lat, lng, "show");
      });

      google.maps.event.addListener(marker, "dragend", function (event) {
        newLat = event.latLng.lat();
        newLng = event.latLng.lng();
        updateInfoWindow(newLat, newLng, "save");
      });

      function dialogSaveLocation(callback) {
        $.confirm({
          title: "Konfirmasi!",
          content: `<b>Simpan lokasi baru?</b> <br> Latitude: ${newLat} <br> Longitude: ${newLng}`,
          buttons: {
            confirm: {
              btnClass: "btn-primary",
              action: callback,
            },
            cancel: function () {
              $.alert("Simpan lokasi baru, batal!");
            },
          },
        });
      }

      $(document).on("click", "#btnSaveLocation", function () {
        dialogSaveLocation(function () {
          $.ajax({
            url: common.baseURL("ref_customer/update_location"),
            method: "POST",
            data: {
              customerid: data.customerid,
              latitude: newLat,
              longitude: newLng,
            },
            success: function (res) {
              $.alert({
                title: "Berhasil!",
                content: "Lokasi berhasil disimpan!",
                type: "green",
                buttons: {
                  ok: {
                    text: "OK",
                    btnClass: "btn-success",
                    action: function () {
                      infoWindow.close();
                      $("#modalMaps").modal("hide");
                      uiTbl.datagrid("reload");
                    },
                  },
                },
              });
            },
            error: function () {
              $.alert("Terjadi kesalahan saat menyimpan lokasi.");
            },
          });
        });
      });

      $("#modalMaps").modal("show");
    }, 500);
  }

  function viewGff(val) {
    $.ajax({
      type: "POST",
      dataType: "html",
      url: common.baseURL("ref_customer/open_gff_detail"),
      data: "customerid=" + val.customerid,
      success: function (res) {
        $("#tbl-listgff").html(res);
        $("#viewModal").modal();
      },
      error: function () {
        alert("Load failed");
      },
    });
  }
})();
