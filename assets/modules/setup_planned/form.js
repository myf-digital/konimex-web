(function () {
  const common = new Common();
  common.setTitle("Setup Planned");
  // declare dom
  let uiForm = $("#fm-add-setup-planned");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectSalesman = $("#salesmanid-id");

  let param = common.getCookie("module.setup_planned.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined;
  let selectedMedrep = null;
  let loadedOutlets = [];
  let selectedPlanned = {};
  let activeModalDate = null;

  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("setup_planned/create")
        : common.baseURL("setup_planned/update");
    if (param !== undefined) {
      uiForm.form("load", param);
    }

    $.validator.setDefaults({
      errorPlacement: function (error, element) {
        if (element.hasClass("select2-hidden-accessible")) {
          error.insertAfter(element.next(".select2-container"));
        } else if (element.parent(".input-group").length) {
          error.insertAfter(element.parent(".input-group"));
        } else {
          error.insertAfter(element);
        }
      },
    });

    uiForm.validate({
      rules: {
        salesmanid: {
          required: true,
        },
        periode: {
          required: true,
        },
      },
      messages: {
        salesmanid: {
          required: "User wajib diisi.",
        },
        periode: {
          required: "Periode wajib diisi.",
        },
      },
      submitHandler: function (form) {
        if (!uiSelectSalesman.val()) {
          Swal.fire({
            title: "Validation",
            html: "User wajib diisi.",
            icon: "warning",
          });
          return false;
        }

        let totalPlanned = getTotalPlannedCount();

        if (totalPlanned == 0) {
          Swal.fire({
            title: "Validation",
            html: "Detail planned wajib diisi.",
            icon: "warning",
          });
          return false;
        }

        let formData = $(form).serializeArray();
        if (param !== undefined) {
          formData.push({ name: "siteid", value: param.siteid });
          formData.push({ name: "req_no", value: param.req_no });
        }
        formData.push({ name: "usersession", value: paramsession.username });
        formData.push({ name: "rolename", value: paramsession.role_name });

        let uniqueCustomerIds = [];
        Object.keys(selectedPlanned).forEach(function (dateValue) {
          let items = selectedPlanned[dateValue] || [];
          items.forEach(function (item) {
            if (!uniqueCustomerIds.includes(item.customerid)) {
              uniqueCustomerIds.push(item.customerid);
            }
          });
        });

        uniqueCustomerIds.forEach(function (cid) {
          formData.push({
            name: "customerid[]",
            value: cid,
          });
        });

        let index = 0;
        Object.keys(selectedPlanned).forEach(function (dateValue) {
          let items = selectedPlanned[dateValue] || [];
          items.forEach(function (item) {
            formData.push({
              name: `planned_detail[${index}][customerid]`,
              value: item.customerid,
            });
            formData.push({
              name: `planned_detail[${index}][user_id]`,
              value: item.user_id,
            });
            formData.push({
              name: `planned_detail[${index}][periode]`,
              value: dateValue,
            });
            index++;
          });
        });

        Swal.fire({
          title: "Loading...",
          text: "Mohon tunggu sebentar",
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        $.ajax({
          url: url,
          type: "POST",
          data: formData,
          dataType: "json",
          success: function (res) {
            if (res.code === 200) {
              Swal.fire({
                title: "Success",
                text: res.message || "Berhasil menyimpan Planned",
                icon: "success",
              }).then(() => {
                common.direct("setup_planned");
              });
            } else {
              Swal.fire({
                title: "Gagal",
                text: res.message || "Gagal menyimpan Planned",
                icon: "error",
              });
            }
          },
          error: function (xhr, status, error) {
            let errorMsg = "Terjadi kesalahan sistem.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMsg = xhr.responseJSON.message;
            }
            Swal.fire({
              title: "Error",
              text: errorMsg,
              icon: "error",
            });
          },
        });

        return false;
      },
    });

    loadSalesman({
      usersession: paramsession.username,
      idjabatan: paramsession.idjabatan,
      restrict_level: paramsession.restrict_level,
      salesmanid: param !== undefined ? param.salesmanid : "",
    });

    uiSelectSalesman.on("change", function () {
      selectedMedrep = uiSelectSalesman.select2("data")[0];
      let nama_salesman = "";
      if (selectedMedrep) {
        updateMainPlannedSummary();
        loadOutlet(selectedMedrep);
        nama_salesman = selectedMedrep?.nama_salesman || "";
      } else {
        $("#daterange-container, #planned-dates-container").hide();
      }
      $("#salesman_name").val(nama_salesman);
    });

    uiBtnCancel.click(function () {
      common.direct("setup_planned");
    });

    $("#periode").val(moment().format("YYYY-MM-DD"));

    $("#planned-daterange").daterangepicker({
      locale: {
        format: "YYYY-MM-DD",
        separator: " s/d ",
        applyLabel: "Pilih",
        cancelLabel: "Batal",
        daysOfWeek: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
        monthNames: [
          "Januari",
          "Februari",
          "Maret",
          "April",
          "Mei",
          "Juni",
          "Juli",
          "Agustus",
          "September",
          "Oktober",
          "November",
          "Desember",
        ],
        firstDay: 1,
      },
      autoUpdateInput: false,
      minDate: (isUpdate && param && param.periode)
        ? moment(param.periode)
        : moment().add(1, "days"),
    });

    $("#planned-daterange").on("apply.daterangepicker", function (ev, picker) {
      $(this).val(
        picker.startDate.format("YYYY-MM-DD") +
          " s/d " +
          picker.endDate.format("YYYY-MM-DD"),
      );
      generateDateButtons();
    });

    $("#planned-daterange").on("cancel.daterangepicker", function (ev, picker) {
      $(this).val("");
      generateDateButtons();
    });

    $(document)
      .off(
        "click",
        "#modal-outlet-accordion-container .outlet-accordion-item .panel-heading",
      )
      .on(
        "click",
        "#modal-outlet-accordion-container .outlet-accordion-item .panel-heading",
        function (e) {
          if ($(e.target).closest("input, button, a").length) return;

          let item = $(this).closest(".outlet-accordion-item");
          let collapse = item.find(".panel-collapse");
          let arrow = item.find(".accordion-arrow");

          collapse.slideToggle(200, function () {
            if (collapse.is(":visible")) {
              arrow.css("transform", "rotate(180deg)");
            } else {
              arrow.css("transform", "rotate(0deg)");
            }
          });
        },
      );

    $(document)
      .off("change", ".modal-professional-checkbox")
      .on("change", ".modal-professional-checkbox", function () {
        let wasChecked = $(this).is(":checked");

        let cid = String($(this).data("customerid"));
        let uid = String($(this).val());

        if (!selectedPlanned[activeModalDate]) {
          selectedPlanned[activeModalDate] = [];
        }

        if (wasChecked) {
          selectedPlanned[activeModalDate].push({
            customerid: cid,
            user_id: uid,
          });
        } else {
          selectedPlanned[activeModalDate] = selectedPlanned[
            activeModalDate
          ].filter(function (item) {
            return !(
              String(item.customerid) == cid && String(item.user_id) == uid
            );
          });
        }

        let parentPanel = $(this).closest(".outlet-accordion-item");
        let count = parentPanel.find(
          ".modal-professional-checkbox:checked",
        ).length;
        let badge = parentPanel.find(".selected-count-badge");
        badge.text(`${count} terpilih`);
        if (count > 0) {
          badge.removeClass("label-default").addClass("label-success");
        } else {
          badge.removeClass("label-success").addClass("label-default");
        }

        updateModalTotals();
      });

    $(document)
      .off("click", ".btn-date-planned")
      .on("click", ".btn-date-planned", function () {
        activeModalDate = $(this).data("date");
        let displayDate = moment(activeModalDate)
          .locale("id")
          .format("dddd, DD MMM YYYY");
        $("#modal-planned-date-display").text(displayDate);

        renderModalAccordion();
        $("#modalPlannedAccordion").modal("show");
      });

    $("#modal-search-outlet")
      .off("keyup")
      .on("keyup", function () {
        let query = $(this).val().toLowerCase();
        $("#modal-outlet-accordion-container .outlet-accordion-item").each(
          function () {
            let outletName = $(this).find(".panel-title").text().toLowerCase();
            let subtitle = $(this)
              .find(".outlet-subtitle")
              .text()
              .toLowerCase();
            if (
              outletName.indexOf(query) > -1 ||
              subtitle.indexOf(query) > -1
            ) {
              $(this).show();
            } else {
              $(this).hide();
            }
          },
        );
      });
  }

  function loadSalesman(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_salesman"),
      {
        idjabatan: data.idjabatan,
        usersession: data.usersession,
        restrict_level: data.restrict_level,
        salesmanid: data.salesmanid || "",
      },
      function (res) {
        uiSelectSalesman.empty();
        uiSelectSalesman.select2({
          placeholder: "Select TPE",
          allowClear: true,
          data: $.map(res.result, function (o) {
            o.id = o.salesmanid;
            o.text =
              o.salesmanid + " - " + o.nama_salesman + " - " + o.tipe_sales;
            return o;
          }),
        });

        if (isUpdate) {
          uiSelectSalesman.val(param.salesmanid).trigger("change");
        } else {
          uiSelectSalesman.val(null).trigger("change");
        }
        common.loadingClose();
      },
    );
  }

  function loadOutlet(data) {
    common.loading();
    $.post(
      common.baseURL("api_v1/call_outlet_planned"),
      {
        salesmanid: data.salesmanid,
      },
      function (res) {
        let dub = res.result ? res.result.dub || [] : [];
        if (!dub || (dub && dub.length == 0)) {
          Swal.fire({
            title: "Peringatan",
            html: `<b>${selectedMedrep.nama_salesman} - ${selectedMedrep.tipe_sales}</b> belum memiliki DUB.`,
            icon: "warning",
          });
          uiSelectSalesman.val(null).trigger("change");
          $("#daterange-container, #planned-dates-container").hide();
          common.loadingClose();
          return;
        }

        loadedOutlets = dub;
        $("#daterange-container, #planned-dates-container").show();

        if (isUpdate) {
          $.post(
            common.baseURL("setup_planned/get_detail"),
            { req_no: param.req_no },
            function (detailRes) {
              let planned = detailRes.result || [];

              selectedPlanned = {};
              let dates = [];
              planned.forEach(function (pl) {
                if (pl.periode) {
                  dates.push(pl.periode);
                  if (!selectedPlanned[pl.periode]) {
                    selectedPlanned[pl.periode] = [];
                  }
                  selectedPlanned[pl.periode].push({
                    customerid: String(pl.customerid),
                    user_id: String(pl.user_id),
                  });
                }
              });

              if (dates.length > 0) {
                dates.sort();
                let minDate = dates[0];
                let maxDate = dates[dates.length - 1];

                let picker = $("#planned-daterange").data("daterangepicker");
                if (picker) {
                  picker.setStartDate(minDate);
                  picker.setEndDate(maxDate);
                }
                $("#planned-daterange").val(minDate + " s/d " + maxDate);
                generateDateButtons();
              } else {
                generateDateButtons();
              }

              if (param.status == "1" && planned.length > 0) {
                let footer = $(".box-footer");
                if ($("#btn-form-approve").length === 0) {
                  footer.append(`
                    <button type="button" id="btn-form-reject" class="btn btn-danger" style="margin-left: 10px;">Reject</button>
                    <button type="button" id="btn-form-approve" class="btn btn-success" style="margin-left: 5px;">Approve</button>
                  `);

                  $("#btn-form-approve")
                    .off("click")
                    .on("click", function () {
                      handleFormApproveReject(param.req_no, 3);
                    });

                  $("#btn-form-reject")
                    .off("click")
                    .on("click", function () {
                      handleFormApproveReject(param.req_no, 5);
                    });
                }
              }

              common.loadingClose();
            },
          ).fail(function () {
            common.loadingClose();
          });
        } else {
          selectedPlanned = {};
          $("#planned-daterange").val("");
          generateDateButtons();
          common.loadingClose();
        }
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function groupDUBList(dubList) {
    let grouped = {};
    dubList.forEach(function (d) {
      let cid = d.customerid;
      if (!grouped[cid]) {
        grouped[cid] = {
          customerid: cid,
          nama_customer: d.nama_customer || "Outlet tidak diketahui",
          typeid: d.typeid || "",
          professionals: [],
        };
      }
      let isDuplicate = grouped[cid].professionals.some(function (p) {
        return p.user_id == d.user_id;
      });
      if (!isDuplicate) {
        grouped[cid].professionals.push(d);
      }
    });
    return grouped;
  }

  function generateDateButtons() {
    let daterangeVal = $("#planned-daterange").val();
    if (!daterangeVal) {
      $("#date-buttons-container").empty();
      return;
    }

    let parts = daterangeVal.split(" s/d ");
    if (parts.length != 2) {
      $("#date-buttons-container").empty();
      return;
    }

    let startVal = parts[0];
    let endVal = parts[1];

    let start = moment(startVal);
    let end = moment(endVal);

    if (end.isBefore(start)) {
      $("#date-buttons-container").empty();
      return;
    }

    let container = $("#date-buttons-container");
    container.empty();

    let dateButtonsGroup = $(
      '<div class="btn-group-vertical" style="width: 100%; gap: 10px; display: flex; flex-direction: column;"></div>',
    );

    let current = start.clone();
    while (current.isSameOrBefore(end)) {
      let dateStr = current.format("YYYY-MM-DD");
      let displayDate = current.locale("id").format("dddd, DD MMM YYYY");

      let count = selectedPlanned[dateStr]
        ? selectedPlanned[dateStr].length
        : 0;
      let badgeClass =
        count > 0 ? "badge-success bg-green" : "badge-default bg-gray";

      let btn = $(`
        <button type="button" class="btn btn-default btn-date-planned" data-date="${dateStr}" style="text-align: left; display: flex; justify-content: space-between; align-items: center; padding: 10px 15px; border: 1px solid #ddd; border-radius: 4px;">
          <span><i class="fa fa-calendar" style="margin-right: 8px; color: #3c8dbc;"></i> <b>${displayDate}</b></span>
          <span class="badge ${badgeClass} date-selected-count" style="font-size: 12px; padding: 4px 8px;">${count} terpilih</span>
        </button>
      `);

      dateButtonsGroup.append(btn);
      current.add(1, "days");
    }

    container.append(dateButtonsGroup);
    updateMainPlannedSummary();
  }

  function renderModalAccordion() {
    let container = $("#modal-outlet-accordion-container");
    container.empty();

    let grouped = groupDUBList(loadedOutlets);
    let currentlySelectedForDate = selectedPlanned[activeModalDate] || [];
    let checkedProfIds = {};
    currentlySelectedForDate.forEach(function (item) {
      let cidStr = String(item.customerid);
      if (!checkedProfIds[cidStr]) {
        checkedProfIds[cidStr] = [];
      }
      checkedProfIds[cidStr].push(String(item.user_id));
    });

    Object.keys(grouped).forEach(function (cid) {
      let group = grouped[cid];
      let checkedList = checkedProfIds[cid] || [];
      let checkedCount = checkedList.length;
      let badgeClass = checkedCount > 0 ? "label-success" : "label-default";

      let subtitle = group.professionals
        .map((p) => `${p.user_id} - ${p.nama_professional}`)
        .join(", ");

      let html = "";
      if (group.customerid) html += group.customerid;
      if (group.nama_customer) html += ` - ${group.nama_customer}`;
      if (group.typeid) html += ` - ${group.typeid}`;

      let accordionItem = $(`
        <div class="panel panel-default outlet-accordion-item" data-id="${cid}">
          <div class="panel-heading">
            <div class="panel-title-label">
              <h4 class="panel-title">
                ${html}
              </h4>
              <div class="outlet-subtitle" title="${subtitle}">
                ${subtitle || "Tidak ada professional"}
              </div>
            </div>
            <div class="panel-title-icon">
              <span class="label ${badgeClass} selected-count-badge">${checkedCount} terpilih</span>
              <i class="fa fa-chevron-down accordion-arrow"></i>
            </div>
          </div>
          <div class="panel-collapse">
            <div class="panel-body">
              <div class="professional-checkbox-group">
              </div>
            </div>
          </div>
        </div>
      `);

      let checkboxGroup = accordionItem.find(".professional-checkbox-group");
      group.professionals.forEach(function (p) {
        let isChecked = checkedList.includes(String(p.user_id));
        let checkAttr = isChecked ? "checked" : "";

        let checkboxItem = $(`
          <label class="professional-label">
            <input type="checkbox" class="modal-professional-checkbox" data-customerid="${cid}" value="${p.user_id}" ${checkAttr} style="margin-right: 5px; cursor: pointer;">
            <span class="professional-name">${p.user_id} - ${p.nama_professional}</span>
          </label>
        `);
        checkboxGroup.append(checkboxItem);
      });

      container.append(accordionItem);
    });

    updateModalTotals();
    $("#modal-search-outlet").val("").trigger("keyup");
  }

  function getTotalPlannedCount() {
    let total = 0;
    Object.keys(selectedPlanned).forEach(function (date) {
      total += selectedPlanned[date] ? selectedPlanned[date].length : 0;
    });
    return total;
  }

  function updateModalTotals() {
    let forDateCount = selectedPlanned[activeModalDate]
      ? selectedPlanned[activeModalDate].length
      : 0;
    $("#modal-date-total-badge").text(`${forDateCount} terpilih`);
    updateMainPlannedSummary();
  }

  function updateMainPlannedSummary() {
    $(".btn-date-planned").each(function () {
      let dateStr = $(this).data("date");
      let count = selectedPlanned[dateStr]
        ? selectedPlanned[dateStr].length
        : 0;
      let badge = $(this).find(".date-selected-count");
      badge.text(`${count} terpilih`);
      if (count > 0) {
        badge
          .removeClass("badge-default bg-gray")
          .addClass("badge-success bg-green");
      } else {
        badge
          .removeClass("badge-success bg-green")
          .addClass("badge-default bg-gray");
      }
    });

    let total = getTotalPlannedCount();
    let badgeText = `${total} terpilih`;
    $("#main-total-planned-badge").text(badgeText);
  }

  function handleFormApproveReject(reqNo, targetStatus) {
    let statusText = targetStatus == 3 ? "Approve" : "Reject";
    let inputPlaceholder =
      targetStatus == 3
        ? "Alasan persetujuan (opsional)"
        : "Alasan penolakan (wajib)";

    Swal.fire({
      title: statusText + " Request Planned",
      input: "textarea",
      inputLabel: "Masukkan keterangan/alasan:",
      inputPlaceholder: inputPlaceholder,
      showCancelButton: true,
      confirmButtonText: "Kirim",
      cancelButtonText: "Batal",
      confirmButtonColor: targetStatus == 3 ? "#28a745" : "#d33",
      inputValidator: (value) => {
        if (!value) {
          return `Alasan ${statusText} wajib diisi!`;
        }
      },
    }).then((result) => {
      if (result.isConfirmed) {
        common.loading();
        $.post(
          common.baseURL("setup_planned/update_status"),
          {
            req_no: reqNo,
            status: targetStatus,
            reason: result.value || "",
            usersession: paramsession.username,
          },
          function (res) {
            common.loadingClose();
            if (res.code === 200) {
              Swal.fire({
                title: "Success",
                text: "Status berhasil diperbarui",
                icon: "success",
              }).then(() => {
                common.direct("setup_planned");
              });
            } else {
              Swal.fire({
                title: "Gagal",
                text: res.message || "Gagal memperbarui status",
                icon: "error",
              });
            }
          },
        ).fail(function (xhr, status, error) {
          common.loadingClose();
          Swal.fire({
            title: "Error",
            text: "Terjadi kesalahan sistem: " + error,
            icon: "error",
          });
        });
      }
    });
  }
})();
