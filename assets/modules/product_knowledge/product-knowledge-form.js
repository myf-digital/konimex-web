(function () {
  const common = new Common();
  common.setTitle("Product Knowledge");
  // declare dom
  let uiForm = $("#fm-product-knowledge");
  let uiBtnCancel = $("#btn-cancel-form");
  let uiSelectProduct = $("#product");

  // define from *-content.js
  let param = common.getCookie("module.product_knowledge.update");
  let paramsession = common.getCookie("session");
  let isUpdate = param !== undefined; // flag create update

  initialize();
  initializeParam();
  if (isUpdate) previewFiles(param);

  function initialize() {
    let url =
      param === undefined
        ? common.baseURL("product_knowledge/create")
        : common.baseURL("product_knowledge/update");
    uiForm.initForm({
      url: url,
      param: param,
      directUrl: "product_knowledge",
      beforeSubmit: function (form, options) {
        if (param !== undefined) {
          form.push({ name: "id", value: param.id });
        }
        form.push({ name: "usersession", value: paramsession.username });
        return true; // MANDATORY!
      },
    });

    uiBtnCancel.click(function () {
      common.direct("product_knowledge");
    });

    uiSelectProduct.select2({
      placeholder: "Select product",
      allowClear: true,
    });
  }

  function initializeParam() {
    common.loading();
    let resolver = new HttpResolver();
    $.when($.post(common.baseURL("ref_product/list")))
      .done(function (data, textStatus, jqXHR) {})
      .then(function (r1, r2) {
        common.loadingClose();
        setupForm(r1);
      })
      .fail(resolver.fail);
  }

  function setupForm(r1) {
    let rows = r1;
    uiSelectProduct.select2({
      placeholder: "Select product",
      allowClear: true,
      data: $.map(rows, function (o) {
        o.id = o.productid + "||" + o.nama_invoice; // replace name with the property used for the text
        o.text = o.nama_invoice + " (" + o.nama_brand + ")"; // replace name with the property used for the text
        return o;
      }),
    });
    if (isUpdate)
      uiSelectProduct
        .val(param.productid + "||" + param.nama_invoice)
        .trigger("change");
    else uiSelectProduct.val(null).trigger("change");
  }

  document.getElementById("fileInput").addEventListener("change", function () {
    const list = document.getElementById("fileList");
    list.innerHTML = "";
    for (let file of this.files) {
      const li = document.createElement("li");
      li.className = "list-group-item";
      li.textContent = file.name;
      list.appendChild(li);
    }
  });

  const elements = document.getElementsByClassName("btn-preview_file");
  for (let i = 0; i < elements.length; i++) {
    elements[i].addEventListener("click", function () {
      let pdf = $(this).attr("data-pdf");

      $("#fileModalLabel").html(`
        Judul: <b>${param.judul}</b><br>
        Brand: <b>${param.nama_brand}</b><br>
        Produk: <b>${param.productid} - ${param.nama_invoice}</b>
      `);
      $("#bodyFileModal").html(
        `<embed src="${pdf}" type="application/pdf" width="100%" height="500px" />`,
      );
      $("#viewFileModal").modal("show");
    });
  }

  function previewFiles(data) {
    if (data && data.files) {
      const files = data.files.split("||").map((f) => BASE_URL + f);

      const list = document.getElementById("fileListData");
      list.innerHTML = "";
      for (let file of files) {
        let ext = file.split(".").pop();

        const li = document.createElement("li");
        li.className = "list-group-item";
        if (ext.toLowerCase() == "pdf")
          li.innerHTML = `<a tabindex="0" class="pointer btn-preview_file" data-pdf="${file}">Show PDF</a>`;
        else li.innerHTML = `<img src="${file}" width="100">`;
        list.appendChild(li);
      }
    }
  }
})();
