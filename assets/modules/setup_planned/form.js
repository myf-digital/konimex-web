(function () {
  const common = new Common();
  common.setTitle("Setup Planned");
  // declare dom
  let uiForm = $("#fm-add-setup-planned");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectSalesman = $("#salesmanid-id");
  let uiSearchOutlet = $("#customerid");
  let maxLimit = 0;

  let param = common.getCookie("module.setup_planned.update");
  let paramsession = common.getCookie("session");

  let isUpdate = param !== undefined;
  let selectedMedrep = null;
  let loadedOutlets = [];

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

        let totalPlanned = 0;
        $(".visit-datepicker").each(function () {
          if ($(this).val()) {
            totalPlanned++;
          }
        });

        if (totalPlanned === 0) {
          Swal.fire({
            title: "Validation",
            html: "Periode wajib dipilih.",
            icon: "warning",
          });
          return false;
        }

        if (totalPlanned !== maxLimit && maxLimit > 0) {
          Swal.fire({
            title: "Validation",
            html: `Jumlah Planned wajib tepat ${maxLimit}. Saat ini Anda mengisi ${totalPlanned}.`,
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
        $(".visit-datepicker").each(function () {
          if ($(this).val()) {
            let customerId = $(this).data("customerid");
            if (!uniqueCustomerIds.includes(customerId)) {
              uniqueCustomerIds.push(customerId);
            }
          }
        });

        uniqueCustomerIds.forEach(function (cid) {
          formData.push({
            name: "customerid[]",
            value: cid,
          });
        });

        let index = 0;
        $(".visit-datepicker").each(function () {
          let dateValue = $(this).val();
          if (dateValue) {
            let customerId = $(this).data("customerid");
            let userId = $(this).data("userid");

            formData.push({
              name: `planned_detail[${index}][customerid]`,
              value: customerId,
            });
            formData.push({
              name: `planned_detail[${index}][user_id]`,
              value: userId,
            });
            formData.push({
              name: `planned_detail[${index}][periode]`,
              value: dateValue,
            });
            index++;
          }
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
      if (selectedMedrep) {
        maxLimit = parseInt(selectedMedrep.target_dub) || 0;
        if (maxLimit < 1) {
          Swal.fire({
            title: "Validation",
            html: `Target Planned <b>${selectedMedrep.tipe_sales}</b> belum diatur.`,
            icon: "error",
          });
          return;
        }
        loadOutlet(selectedMedrep);
      }
    });

    uiBtnCancel.click(function () {
      common.direct("setup_planned");
    });

    $(".datepicker").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      todayHighlight: true,
      orientation: "bottom right",
    });

    $(document).on("change", ".visit-datepicker", function () {
      let totalFilled = 0;
      $(".visit-datepicker").each(function () {
        if ($(this).val()) {
          totalFilled++;
        }
      });
      if (totalFilled > maxLimit && maxLimit > 0) {
        $(this).val("");
        $(this).datepicker("update");
        Swal.fire({
          title: "Batas Maksimum",
          text: `Maksimal professional yang boleh direncanakan visit adalah ${maxLimit}. Anda sudah mengisi ${totalFilled} tanggal.`,
          icon: "warning",
        });
      }
      updateTableTotals();
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
          common.loadingClose();
          return;
        }

        if (isUpdate) {
          $.post(
            common.baseURL("setup_planned/get_detail"),
            { req_no: param.req_no },
            function (detailRes) {
              let planned = detailRes.result || [];
              renderDUBTemplate(dub, planned);
              common.loadingClose();
            },
          ).fail(function () {
            common.loadingClose();
          });
        } else {
          renderDUBTemplate(dub, []);
          common.loadingClose();
        }
      },
    ).fail(function () {
      common.loadingClose();
    });
  }

  function renderDUBTemplate(dub, planned) {
    let grouped = {};
    dub.forEach(function (d) {
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

    let listDiv = $("#professional-list");
    listDiv.empty();

    let hasData = false;

    let table = $(`
      <table class="table table-bordered table-striped" style="margin-bottom: 0; background-color: #fff;">
        <thead>
          <tr class="bg-f9">
            <th class="th-detail w-50">Outlet (Customer)</th>
            <th class="th-detail w-25">Daftar User Binaan (DUB)</th>
            <th class="th-detail w-25">Periode Visit</th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
          <tr class="bg-f9 font-weight-bold">
            <td class="td-footer">Total</td>
            <td class="td-footer" id="total-dub-cell">0</td>
            <td class="td-footer" id="total-filled-cell">0</td>
          </tr>
        </tfoot>
      </table>
    `);
    let tbody = table.find("tbody");

    Object.keys(grouped).forEach(function (cid) {
      hasData = true;
      let group = grouped[cid];
      let rowSpan = group.professionals.length;

      group.professionals.forEach(function (p, index) {
        let matchedPlanned = planned.find(function (pl) {
          return pl.customerid == p.customerid && pl.user_id == p.user_id;
        });

        let isChecked = matchedPlanned !== undefined;
        let checkAttr = isChecked ? "checked" : "";
        let dateVal = isChecked ? matchedPlanned.periode || "" : "";

        let tr = $("<tr></tr>");

        if (index === 0) {
          tr.append(`
            <td rowspan="${rowSpan}" style="vertical-align: middle; font-weight: bold; background-color: #fff !important;">
              <span class="label label-primary" style="margin-right: 5px;">${cid}</span>
              ${group.nama_customer} ${group.typeid ? `(${group.typeid})` : ""}
            </td>
          `);
        }

        tr.append(`
          <td style="vertical-align: middle;">
            <span><b>${p.user_id}</b> - ${p.nama_professional}</span>
          </td>
          <td style="vertical-align: middle;">
            <div class="input-group date" style="width: 100%;">
              <div class="input-group-addon" style="padding: 4px 8px;">
                <span class="glyphicon glyphicon-calendar"></span>
              </div>
              <input type="text" class="form-control visit-datepicker" 
                     data-customerid="${p.customerid}" 
                     data-userid="${p.user_id}" 
                     value="${dateVal}" 
                     placeholder="YYYY-MM-DD" 
                     style="height: 30px; padding: 4px 8px; font-size: 12px; background-color: #fff; cursor: pointer;" 
                     readonly>
            </div>
          </td>
        `);

        tbody.append(tr);
      });
    });

    if (hasData) {
      listDiv.append(table);

      listDiv.find(".visit-datepicker").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true,
        clearBtn: true,
        orientation: "bottom right",
      });

      updateTableTotals();

      $("#professional-container").show();
    } else {
      $("#professional-container").hide();
    }
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

  function updateTableTotals() {
    let totalDUB = $(".visit-datepicker").length;
    let totalFilled = 0;
    $(".visit-datepicker").each(function () {
      if ($(this).val()) {
        totalFilled++;
      }
    });

    $("#total-dub-cell").text(totalDUB);
    if (maxLimit > 0) {
      $("#total-filled-cell").text(totalFilled + " / " + maxLimit);
    } else {
      $("#total-filled-cell").text(totalFilled);
    }
  }
})();
