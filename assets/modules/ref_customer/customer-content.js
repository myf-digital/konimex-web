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

    $("#btn-upload").click(function () {
      common.direct("ref_customer/form_upload");
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
    const btnUpload = commonGrid.btnBuilderText(
      "btn-upload",
      "warning",
      "fa fa-upload",
      " Upload Outlet",
    );
    return (
      '<div class="action-grid-toolbar">' +
      btnCreate +
      "&nbsp;" +
      btnDownload +
      "&nbsp;" +
      btnUpload +
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
            field: "kode_outlet",
            title: "Kode Outlet",
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
            field: "alamat",
            title: "Alamat",
            halign: "center",
            align: "left",
            sortable: "true",
            width: 350,
            formatter: formatterAlamat,
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
      },
    };

    let gridOptions = commonGrid.optionValue(option);
    gridOptions.fitColumns = false;
    uiTbl.datagrid(gridOptions);
    uiTbl.datagrid("enableFilter");
    common.removeFilter(["options"]);
    common.removeFilter(["detail"]);

    const panel = uiTbl.datagrid("getPanel");

    panel.off("click", ".action-grid a.btn-success").on("click", ".action-grid a.btn-success", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        updateRow(rows[index]);
      }
    });

    panel.off("click", ".action-grid a.btn-danger").on("click", ".action-grid a.btn-danger", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        deleteRow(rows[index]);
      }
    });

    panel.off("click", ".action-grid a.btn-info").on("click", ".action-grid a.btn-info", function (e) {
      e.preventDefault();
      const tr = $(this).closest("tr.datagrid-row");
      const index = parseInt(tr.attr("datagrid-row-index"));
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        showLocation(rows[index]);
      }
    });

    panel.off("click", ".btn-show-more-user").on("click", ".btn-show-more-user", function (e) {
      e.preventDefault();
      let index = parseInt($(this).attr("data-index"));
      if (isNaN(index)) {
        const tr = $(this).closest("tr.datagrid-row");
        index = parseInt(tr.attr("datagrid-row-index"));
      }
      const rows = uiTbl.datagrid("getRows");
      if (!isNaN(index) && rows[index]) {
        showAllUsers(rows[index]);
      }
    });
  }

  function formatterListProfessional(val, row, index) {
    if (val) {
      let listProfessional = val
        .split("||")
        .map(function (item) {
          return item.trim();
        })
        .filter(function (item) {
          return item !== "";
        });

      if (listProfessional.length === 0) {
        return "";
      }

      if (listProfessional.length <= 4) {
        let result =
          '<ol style="margin: 0; padding-left: 15px; width: 320px; min-width: 320px; max-width: 320px; white-space: normal; word-break: break-word;">';
        for (const professional of listProfessional) {
          result += "<li>" + professional + "</li>";
        }
        result += "</ol>";
        return result;
      }

      let firstFour = listProfessional.slice(0, 4);
      let result =
        '<ol style="margin: 0; padding-left: 15px; width: 320px; min-width: 320px; max-width: 320px; white-space: normal; word-break: break-word;">';
      for (const professional of firstFour) {
        result += "<li>" + professional + "</li>";
      }
      result += "</ol>";
      result +=
        '<div style="margin-top: 5px;">' +
        '<button type="button" class="btn btn-xs btn-primary btn-show-more-user" data-index="' +
        index +
        '">' +
        '<i class="fa fa-list"></i> Lihat Selengkapnya' +
        "</button>" +
        "</div>";
      return result;
    }
    return "";
  }

  function formatterAlamat(val, row, index) {
    return val
      ? '<div style="white-space: normal; word-wrap: break-word;">' + val + "</div>"
      : "";
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
    common.setCookie("module.customer.update", { customerid: val.customerid });
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

  function ensureModalUser() {
    if ($("#modalUser").length === 0) {
      const modalHtml = `
        <div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="myModalUserLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="myModalUserLabel">Daftar User</h4>
                    </div>
                    <div class="modal-body">
                        <div id="modalUserContent"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
      `;
      $("body").append(modalHtml);
    }
  }

  function showAllUsers(val) {
    if (!val || !val.list_professional) return;
    ensureModalUser();

    let listProfessional = val.list_professional
      .split("||")
      .map(function (item) {
        return item.trim();
      })
      .filter(function (item) {
        return item !== "";
      });

    if (listProfessional.length === 0) return;

    let outletName = val.nama_customer || "Outlet";
    $("#myModalUserLabel").html(
      `Daftar User — <b>${outletName}</b> <span class="badge bg-green" style="margin-left: 8px;">Total: ${listProfessional.length} User</span>`
    );

    let contentHtml = `
      <div style="max-height: 420px; overflow-y: auto; padding: 5px;">
        <ol class="list-group" style="padding-left: 0; margin-bottom: 0;">
    `;

    listProfessional.forEach(function (user, idx) {
      contentHtml += `
        <li class="list-group-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 15px; border-left: 3px solid #3c8dbc; margin-bottom: 5px; border-radius: 3px;">
          <span class="badge bg-blue" style="font-size: 12px; min-width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">${idx + 1}</span>
          <span style="font-size: 13px; color: #333; word-break: break-word;">${user}</span>
        </li>
      `;
    });

    contentHtml += `
        </ol>
      </div>
    `;

    $("#modalUserContent").html(contentHtml);
    $("#modalUser").modal("show");
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
