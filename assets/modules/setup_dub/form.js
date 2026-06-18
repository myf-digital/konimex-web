(function () {
  const common = new Common();
  common.setTitle("Setup DUB");
  // declare dom
  let uiForm = $("#fm-add-setup-dub");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectSalesman = $("#salesmanid-id");
  let maxLimit = 0;

  let param = common.getCookie("module.setup_dub.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined;
  let selectedMedrep = null;
  let loadedOutlets = [];

  initialize();

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("setup_dub/create")
        : common.baseURL("setup_dub/update");
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

        let checkedProfessionals = $(".professional-checkbox:checked");
        if (checkedProfessionals.length === 0) {
          Swal.fire({
            title: "Validation",
            html: "DUB wajib dipilih.",
            icon: "warning",
          });
          return false;
        }

        if (checkedProfessionals.length !== maxLimit && maxLimit > 0) {
          Swal.fire({
            title: "Validation",
            html: `Jumlah DUB wajib tepat ${maxLimit}. Saat ini Anda memilih ${checkedProfessionals.length}.`,
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

        let selectedCustomerIds = [];
        checkedProfessionals.each(function () {
          let cid = $(this).data("customerid");
          if (!selectedCustomerIds.includes(cid)) {
            selectedCustomerIds.push(cid);
          }
        });

        selectedCustomerIds.forEach(function (cid) {
          formData.push({
            name: "customerid[]",
            value: cid,
          });
        });

        let index = 0;
        checkedProfessionals.each(function () {
          let customerId = $(this).data("customerid");
          let userId = $(this).val();

          formData.push({
            name: `dub_detail[${index}][customerid]`,
            value: customerId,
          });
          formData.push({
            name: `dub_detail[${index}][user_id]`,
            value: userId,
          });
          index++;
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
                text: res.message || "Berhasil menyimpan DUB",
                icon: "success",
              }).then(() => {
                common.direct("setup_dub");
              });
            } else {
              Swal.fire({
                title: "Gagal",
                text: res.message || "Gagal menyimpan DUB",
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
      if (selectedMedrep) {
        maxLimit = parseInt(selectedMedrep.target_dub) || 0;
        if (maxLimit < 1) {
          Swal.fire({
            title: "Validation",
            html: `Target DUB <b>${selectedMedrep.tipe_sales}</b> belum diatur.`,
            icon: "error",
          });
          return;
        }
        updateTotalSelectedCount();
        loadOutlet(selectedMedrep);
      }
    });

    $("#search-outlet").on("keyup", function () {
      let query = $(this).val().toLowerCase();
      $(".outlet-accordion-item").each(function () {
        let outletName = $(this).find(".panel-title").text().toLowerCase();
        let subtitle = $(this).find(".outlet-subtitle").text().toLowerCase();
        if (outletName.indexOf(query) > -1 || subtitle.indexOf(query) > -1) {
          $(this).show();
        } else {
          $(this).hide();
        }
      });
    });

    $(document).on(
      "click",
      ".outlet-accordion-item .panel-heading",
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

    $(document).on("change", ".professional-checkbox", function () {
      let totalChecked = $(".professional-checkbox:checked").length;
      if (totalChecked > maxLimit && maxLimit > 0) {
        $(this).prop("checked", false);
        Swal.fire({
          title: "Batas Maksimum",
          text: `Maksimal professional yang boleh dipilih adalah ${maxLimit}. Anda sudah memilih ${totalChecked}.`,
          icon: "warning",
        });
        return;
      }

      let parentPanel = $(this).closest(".outlet-accordion-item");
      let count = parentPanel.find(".professional-checkbox:checked").length;
      let badge = parentPanel.find(".selected-count-badge");
      badge.text(`${count} terpilih`);
      if (count > 0) {
        badge.removeClass("label-default").addClass("label-success");
      } else {
        badge.removeClass("label-success").addClass("label-default");
      }

      updateTotalSelectedCount();
    });

    uiBtnCancel.click(function () {
      common.direct("setup_dub");
    });

    $("#periode").val(moment().format("YYYY-MM-DD"));
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
        skip_req_dub: 1,
      },
      function (res) {
        uiSelectSalesman.empty();
        uiSelectSalesman.select2({
          placeholder: "Select MEDREP",
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
      common.baseURL("api_v1/call_outlet_dub"),
      {
        salesmanid: data.salesmanid,
      },
      function (res) {
        if (res.code == 200) {
          loadedOutlets = res.result || [];
        } else {
          loadedOutlets = [];
          Swal.fire({
            title: "Informasi",
            text: res.message || "Gagal memuat outlet",
            icon: "warning",
          });
          common.loadingClose();
          return;
        }

        if (isUpdate) {
          $.post(
            common.baseURL("setup_dub/get_detail"),
            { req_no: param.req_no },
            function (detailRes) {
              let savedDetails = detailRes.result || [];
              renderOutletCheckboxes(savedDetails);

              $("#btn-form-reject, #btn-form-approve").remove();
              if (param.status == "1" && savedDetails.length > 0) {
                let footer = $(".box-footer");
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

              common.loadingClose();
            },
          );
        } else {
          let savedDetails = [];
          for (let i = 0; i < loadedOutlets.length; i++) {
            let item = loadedOutlets[i];
            if (item.mapped_professionals) {
              let profs = item.mapped_professionals.split("||");
              profs.forEach(function (userId) {
                savedDetails.push({
                  customerid: item.customerid,
                  user_id: userId,
                });
              });
            }
          }

          renderOutletCheckboxes(savedDetails);
          common.loadingClose();
        }
      },
    );
  }

  function renderOutletCheckboxes(savedDetails) {
    let container = $("#outlet-accordion-container");
    container.empty();

    let checkedProfIds = {};
    if (savedDetails && savedDetails.length > 0) {
      savedDetails
        .filter((sd) => sd.user_id && sd.user_id != "0" && sd.user_id != "-")
        .forEach((d) => {
          if (!checkedProfIds[d.customerid]) {
            checkedProfIds[d.customerid] = [];
          }
          if (d.user_id && !checkedProfIds[d.customerid].includes(d.user_id)) {
            checkedProfIds[d.customerid].push(d.user_id);
          }
        });
    }

    loadedOutlets.forEach((item) => {
      let cid = item.customerid;
      let listProfStr = item.list_professional || "";
      let subtitle = listProfStr
        .split("||")
        .map((p) => p.trim())
        .join(", ");

      let checkedList = checkedProfIds[cid] || [];
      let checkedCount = checkedList.length;
      let badgeClass = checkedCount > 0 ? "label-success" : "label-default";

      let html = "";
      if (item.customerid) html += item.customerid;
      if (item.nama_customer) html += ` - ${item.nama_customer}`;
      if (item.typeid) html += ` - ${item.typeid}`;

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
      if (listProfStr.trim() !== "") {
        let profs = listProfStr.split("||");
        profs.forEach((pStr) => {
          let parts = pStr.split(" - ");
          if (parts.length >= 1) {
            let userId = parts[0].trim();
            let profName = parts.slice(1).join(" - ").trim();

            let isChecked = checkedList.includes(userId);
            let checkAttr = isChecked ? "checked" : "";

            let checkboxItem = $(`
              <label class="professional-label">
                <input type="checkbox" class="professional-checkbox" data-customerid="${cid}" value="${userId}" ${checkAttr}>
                <span class="professional-name">${userId} - ${profName}</span>
              </label>
            `);
            checkboxGroup.append(checkboxItem);
          }
        });
      } else {
        checkboxGroup.html(
          '<span class="no-data-text">Tidak ada professional untuk outlet ini.</span>',
        );
      }

      container.append(accordionItem);
    });

    $("#search-outlet").trigger("keyup");
    updateTotalSelectedCount();
  }

  function handleFormApproveReject(reqNo, targetStatus) {
    let statusText = targetStatus == 3 ? "Approve" : "Reject";
    let inputPlaceholder =
      targetStatus == 3
        ? "Alasan persetujuan (opsional)"
        : "Alasan penolakan (wajib)";

    Swal.fire({
      title: statusText + " Request DUB",
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
          common.baseURL("setup_dub/update_status"),
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
                common.direct("setup_dub");
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

  function updateTotalSelectedCount() {
    let totalChecked = $(".professional-checkbox:checked").length;
    let badgeText = `${totalChecked} terpilih`;
    if (maxLimit > 0) {
      badgeText = `${totalChecked}/${maxLimit} terpilih`;
    }
    $("#total-selected-badge").text(badgeText);
  }
})();
