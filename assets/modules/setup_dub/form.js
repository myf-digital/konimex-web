(function () {
  const common = new Common();
  common.setTitle("Setup DUB");
  // declare dom
  let uiForm = $("#fm-add-setup-dub");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectSalesman = $("#salesmanid-id");
  let uiSearchOutlet = $("#customerid");
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

        let customer = document.getElementById("customerid_to");
        if (!customer || customer.options.length === 0) {
          Swal.fire({
            title: "Validation",
            html: "Outlet wajib dipilih.",
            icon: "warning",
          });
          return false;
        }

        let totalChecked = $(".professional-checkbox:checked").length;
        if (totalChecked === 0) {
          Swal.fire({
            title: "Validation",
            html: "DUB wajib dipilih.",
            icon: "warning",
          });
          return false;
        }

        if (totalChecked !== maxLimit && maxLimit > 0) {
          Swal.fire({
            title: "Validation",
            html: `Jumlah DUB wajib tepat ${maxLimit}. Saat ini Anda memilih ${totalChecked}.`,
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

        for (let i = 0; i < customer.options.length; i++) {
          formData.push({
            name: "customerid[]",
            value: customer.options[i].value,
          });
        }

        let index = 0;
        $(".professional-checkbox:checked").each(function () {
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
        loadOutlet(selectedMedrep);
      }
    });

    uiSearchOutlet.multiselect({
      search: {
        left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
        right:
          '<input type="text" name="q" class="form-control" placeholder="Search..." />',
      },
      fireSearch: function (value) {
        return value.length > 3;
      },
      submitAllLeft: false,
      submitAllRight: false,
      afterMoveToRight: function ($left, $right, $options) {
        updateProfessionalCheckboxes();
      },
      afterMoveToLeft: function ($left, $right, $options) {
        updateProfessionalCheckboxes();
      },
    });

    uiBtnCancel.click(function () {
      common.direct("setup_dub");
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
      orientation: "bottom right",
    });

    $(document).on("change", ".professional-checkbox", function () {
      let totalChecked = $(".professional-checkbox:checked").length;
      if (totalChecked > maxLimit && maxLimit > 0) {
        $(this).prop("checked", false);
        Swal.fire({
          title: "Batas Maksimum",
          text: `Maksimal professional yang boleh dipilih adalah ${maxLimit}. Anda sudah memilih ${totalChecked}.`,
          icon: "warning",
        });
      }
    });

    if (isUpdate && param.status == "1") {
      let footer = $(".box-footer");
      footer.append(`
        <button type="button" id="btn-form-reject" class="btn btn-danger" style="margin-left: 10px;">Reject</button>
        <button type="button" id="btn-form-approve" class="btn btn-success" style="margin-left: 5px;">Approve</button>
      `);

      $("#btn-form-approve").click(function () {
        handleFormApproveReject(param.req_no, 3);
      });

      $("#btn-form-reject").click(function () {
        handleFormApproveReject(param.req_no, 5);
      });
    }
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
        loadedOutlets = res.result || [];
        let { select, selectTo } = resetSelectOutlet();

        if (isUpdate) {
          $.post(
            common.baseURL("setup_dub/get_detail"),
            { req_no: param.req_no },
            function (detailRes) {
              let savedDetails = detailRes.result || [];
              let savedCustomerIds = [
                ...new Set(savedDetails.map((d) => d.customerid)),
              ];

              for (let i = 0; i < loadedOutlets.length; i++) {
                let item = loadedOutlets[i];
                let opt = document.createElement("option");
                opt.value = item.customerid;

                let html = "";
                if (item.customerid) html += item.customerid;
                if (item.nama_customer) html += ` - ${item.nama_customer}`;
                if (item.typeid) html += ` - ${item.typeid}`;
                if (item.nama_class) html += ` - ${item.nama_class}`;

                opt.innerHTML = html;
                opt.setAttribute("data-position", item.customerid);

                if (savedCustomerIds.includes(item.customerid)) {
                  selectTo.appendChild(opt);
                } else {
                  select.appendChild(opt);
                }
              }

              uiSearchOutlet.find("option").prop("selected", false);

              updateProfessionalCheckboxes(savedDetails);
              common.loadingClose();
            },
          );
        } else {
          let savedDetails = [];
          for (let i = 0; i < loadedOutlets.length; i++) {
            let item = loadedOutlets[i];
            let opt = document.createElement("option");
            opt.value = item.customerid;

            let html = "";
            if (item.customerid) html += item.customerid;
            if (item.nama_customer) html += ` - ${item.nama_customer}`;
            if (item.typeid) html += ` - ${item.typeid}`;
            if (item.nama_class) html += ` - ${item.nama_class}`;

            opt.innerHTML = html;
            opt.setAttribute("data-position", item.customerid);

            if (item.mapped_professionals) {
              selectTo.appendChild(opt);
              let profs = item.mapped_professionals.split("||");
              profs.forEach(function (userId) {
                savedDetails.push({
                  customerid: item.customerid,
                  user_id: userId
                });
              });
            } else {
              select.appendChild(opt);
            }
          }

          updateProfessionalCheckboxes(savedDetails);
          common.loadingClose();
        }
      },
    );
  }

  function resetSelectOutlet() {
    let select = document.getElementById("customerid");
    let selectTo = document.getElementById("customerid_to");

    let length = select.options.length;
    for (i = length - 1; i >= 0; i--) {
      select.options[i] = null;
    }

    let selectToLength = selectTo.options.length;
    for (i = selectToLength - 1; i >= 0; i--) {
      selectTo.options[i] = null;
    }

    return { select, selectTo };
  }

  function updateProfessionalCheckboxes(savedDetails) {
    let selectTo = document.getElementById("customerid_to");
    let container = $("#professional-container");
    let listDiv = $("#professional-list");

    if (!selectTo || selectTo.options.length === 0) {
      listDiv.empty();
      container.hide();
      return;
    }

    let checkedProfIds = {};
    $(".professional-checkbox:checked").each(function () {
      let customerId = $(this).data("customerid");
      let userId = $(this).val();
      if (!checkedProfIds[customerId]) {
        checkedProfIds[customerId] = [];
      }
      checkedProfIds[customerId].push(userId);
    });

    if (savedDetails && savedDetails.length > 0) {
      savedDetails.forEach((d) => {
        if (!checkedProfIds[d.customerid]) {
          checkedProfIds[d.customerid] = [];
        }
        if (d.user_id && !checkedProfIds[d.customerid].includes(d.user_id)) {
          checkedProfIds[d.customerid].push(d.user_id);
        }
      });
    }

    listDiv.empty();
    let hasProfessionals = false;

    for (let i = 0; i < selectTo.options.length; i++) {
      let cid = selectTo.options[i].value;
      let outlet =
        loadedOutlets.find((o) => o.customerid == cid) ||
        dataOutlets.find((o) => o.customerid == cid);

      if (outlet) {
        let outletName = outlet.nama_customer || `Customer ${cid}`;
        let listProfStr = outlet.list_professional || "";

        if (listProfStr.trim() !== "") {
          hasProfessionals = true;
          let profs = listProfStr.split("||");

          let customerBlock = $(
            '<div class="customer-prof-block" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;"></div>',
          );
          customerBlock.append(
            `<h4 style="margin: 0 0 8px 0; font-size: 14px; font-weight: bold; color: #333;">${outletName}</h4>`,
          );

          let checkboxGroup = $(
            '<div style="display: flex; flex-wrap: wrap; gap: 10px 20px; padding-left: 10px;"></div>',
          );

          profs.forEach((pStr) => {
            let parts = pStr.split(" - ");
            if (parts.length >= 1) {
              let userId = parts[0].trim();
              let profName = parts.slice(1).join(" - ").trim();

              let isChecked =
                checkedProfIds[cid] && checkedProfIds[cid].includes(userId);
              let checkAttr = isChecked ? "checked" : "";

              let checkboxItem = $(`
                <label style="font-weight: normal; cursor: pointer; display: inline-flex; align-items: center; margin-bottom: 0;">
                  <input type="checkbox" class="professional-checkbox" data-customerid="${cid}" value="${userId}" ${checkAttr} style="margin-right: 6px; cursor: pointer;">
                  <span>${userId} - ${profName}</span>
                </label>
              `);
              checkboxGroup.append(checkboxItem);
            }
          });

          customerBlock.append(checkboxGroup);
          listDiv.append(customerBlock);
        }
      }
    }

    if (hasProfessionals) {
      container.show();
    } else {
      container.hide();
    }
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
})();
