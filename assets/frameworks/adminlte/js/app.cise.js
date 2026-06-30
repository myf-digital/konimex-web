/*
 * table of content
 * - commons
 * - form
 * - model
 * - filter
 *
 *
 * */

// commons
(function ($) {
  Common = function () {};

  Common.prototype.setTitle = function (title) {
    document.title = title !== undefined ? title : "";
  };

  // covert from json to object
  Common.prototype.fromJson = function (json) {
    return json === undefined ? json : JSON.parse(json);
  };

  // covert from object to json
  Common.prototype.toJson = function (value) {
    return value === undefined ? value : JSON.stringify(value);
  };

  // cookies
  Common.prototype.setCookie = function (key, val) {
    // Cookies.set(key, val, {expires: 1});
    Cookies.set(key, val);
  };

  Common.prototype.getCookie = function (key) {
    return this.fromJson(Cookies.get(key));
  };

  Common.prototype.removeCookie = function (key) {
    Cookies.remove(key);
  };

  // delete dialog
  Common.prototype.dialogDelete = function (callback) {
    $.confirm({
      title: "Confirmation!",
      content: "Are you sure delete this row?",
      buttons: {
        confirm: {
          btnClass: "btn-red",
          action: callback,
        },
        cancel: function () {
          $.alert("Delete cancel!");
        },
      },
    });
  };

  // delete dialog
  Common.prototype.dialogReject = function (callback) {
    $.confirm({
      title: "Confirmation!",
      content: "Rejected request?",
      buttons: {
        confirm: {
          btnClass: "btn-red",
          action: callback,
        },
        cancel: function () {
          $.alert("Rejected cancel!");
        },
      },
    });
  };

  // notify dialog
  Common.prototype.dialogNotifyEmail = function (callback) {
    $.confirm({
      title: "Konfirmasi...",
      content: "Kirim pemberitahuan kepada admin ?",
      buttons: {
        confirm: {
          btnClass: "btn-blue",
          action: callback,
        },
        cancel: function () {
          $.alert("Pemberitahuan email batal!");
        },
      },
    });
  };

  // approve dialog
  Common.prototype.dialogApprove = function (callback) {
    $.confirm({
      title: "Confirmation...!",
      content: "Riset Approved ?",
      buttons: {
        confirm: {
          btnClass: "btn-green",
          action: callback,
        },
        cancel: function () {
          $.alert("Approve canceled!");
        },
      },
    });
  };

  // loader
  Common.prototype.loading = function () {
    $.alert({
      lazyOpen: true,
      title: "Loading... ",
      content: "Loading... ",
      columnClass: "col-md-2 col-md-offset-5 col-xs-3 col-xs-offset-5",
      //containerFluid: true,
      isAjax: true,
      buttons: {
        confirm: function () {
          $.alert("Confirmed!");
        },
        close: function () {
          $.alert("Canceled!");
        },
      },
    });
  };

  Common.prototype.loadingClose = function () {
    setTimeout(function () {
      let dom = $("div.jconfirm");
      dom.remove();
    }, 200);
  };

  // url
  /**
	kalo pake index.php di ci ambil index 0
	*/
  Common.prototype.baseURL = function (path = "") {
    let base_url = window.location.origin;
    let pathArray = window.location.pathname.split("/");
    return base_url + "/" + pathArray[0] + "/" + path + "/";
  };

  Common.prototype.direct = function (path = "") {
    window.location = this.baseURL(path);
  };

  // http
  Common.prototype.get = function (url, callback) {
    $.getJSON(url, callback);
  };

  Common.prototype.post = function (url, data, callback) {
    $.post(url, JSON.stringify(data), callback, "json");
  };

  Common.prototype.post = function (url, data, callback, serialize) {
    if (serialize) {
      $.post(url, JSON.stringify(data), callback, "json");
    } else {
      $.post(url, data, callback, "json");
    }
  };

  Common.prototype.removeFilter = function (names) {
    names.forEach(function (value, index, arr) {
      $("input[name='" + value + "'].datagrid-filter").remove();
    });
  };

  Common.prototype.replaceFormValue = function (form = [], kv = []) {
    form.forEach(function (value, index, arr) {
      kv.forEach(function (rv, rindex, rarr) {
        if (value.name === rv.key) {
          form[index].value = rv.value;
        }
      });
    });
    return form;
  };

  Common.prototype.replaceGridFilterPrefix = function (form, pref) {
    if (form.filterRules !== undefined) {
      let rows = JSON.parse(form.filterRules);
      let result = [];
      rows.forEach(function (value, index, arr) {
        value.field = pref + "." + value.field;
        result.push(value);
      });
      form.filterRules = JSON.stringify(result);
    }
    return form;
  };

  Common.prototype.replaceGridFilter = function (form, names, alias) {
    if (form.filterRules !== undefined) {
      let rows = JSON.parse(form.filterRules);
      let result = [];
      rows.forEach(function (value, index, arr) {
        for (var i = 0; i < names.length; i++) {
          if (value.field === names[i]) {
            value.field = alias[i];
            break;
          }
        }
        result.push(value);
      });
      form.filterRules = JSON.stringify(result);
    }
    return form;
  };

  Common.prototype.formatListArea = function (val) {
    if (val) {
      let list = val.split(", ");

      let result = "";
      if (list.length > 1) {
        result += '<ul style="padding-left: 15px;">';
        list.forEach((item) => {
          result += `<li>${item}</li>`;
        });
        result += "</ul>";
      } else {
        result = list.join(", ");
      }

      return result;
    }
    return "";
  };

  Common.prototype.pluginFilerOption = function () {
    return {
      limit: 1000,
      maxSize: 1000,
      showThumbs: true,
      addMore: true,
      theme: "default",
      templates: {
        box: '<ul class="jFiler-item-list"></ul>',
        item: '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <li>{{fi-progressBar}}</li>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
        itemAppend:
          '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <div class="jFiler-item-status"></div>\
                                    <div class="jFiler-item-info">\
                                        <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                    </div>\
                                    {{fi-image}}\
                                </div>\
                                <div class="jFiler-item-assets jFiler-row">\
                                    <ul class="list-inline pull-left">\
                                        <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                    </ul>\
                                    <ul class="list-inline pull-right">\
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                    </ul>\
                                </div>\
                            </div>\
                        </div>\
                    </li>',
        progressBar: '<div class="bar-filler"></div>',
        itemAppendToEnd: true,
        removeConfirmation: true,
        _selectors: {
          list: ".jFiler-item-list",
          item: ".jFiler-item",
          progressBar: ".bar-filler",
          remove: ".jFiler-item-trash-action",
        },
      },
    };
  };
})(jQuery);

// http
(function ($) {
  HttpResolver = function () {};

  HttpResolver.prototype.fail = function (data, textStatus, jqXHR) {
    $("div.jconfirm").remove();
    $.alert({
      title: textStatus + ", " + jqXHR,
      content: data.responseText,
      columnClass:
        "col-xs-6 col-xs-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3",
      containerFluid: true,
    });
  };
})(jQuery);

// form
(function ($) {
  const common = new Common();

  $.fn.initForm = function (options) {
    // Establish our default settings
    var settings = $.extend(
      {
        //uiForm : null,
        url: null,
        param: {},
        rules: {},
        message: {},
        beforeSubmit: function (form, opt) {
          return true;
        },
        afterSuccess: null,
        directUrl: null,
        initEasyui: true,
      },
      options,
    );

    if (settings.uiForm === null) {
      throw new Error("Form not defined");
    }
    if (settings.url === null) {
      throw new Error("Url not defined");
    }

    return this.each(function () {
      if (settings.initEasyui) {
        $(this).form("load", settings.param);
      }
      $(this).validate({
        // onfocusout: false,
        focusCleanup: false,
        rules: settings.rules,
        messages: settings.message,
        submitHandler: function (form) {
          let loading = $.alert({
            title: "Loading... ",
            content: "Loading... ",
            columnClass: "col-md-2 col-md-offset-5 col-xs-3 col-xs-offset-5",
            containerFluid: true,
            isAjax: true,
            isAjaxLoading: true,
          });
          $(form).ajaxSubmit({
            url: settings.url,
            dataType: "json",
            beforeSubmit: settings.beforeSubmit,
            error: function showInfo(xhr, status, text) {
              let htmlString = $.parseHTML(xhr.responseText);
              $.alert({
                title: xhr.status + ", " + text,
                content: htmlString,
                columnClass:
                  "col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2",
                containerFluid: true,
              });
              loading.close();
            },
            success: function showResponse(text, status, xhr) {
              let response = xhr.responseJSON;
              if (200 === response.code && null != settings.directUrl) {
                common.direct(settings.directUrl);
              } else if (
                200 === response.code &&
                null != settings.afterSuccess
              ) {
                settings.afterSuccess(response);
              } else if (200 === response.code) {
                console.warn(
                  "no action defined, or url redirect after success request not set!",
                );
              } else {
                $.alert({
                  title: "Error ",
                  content:
                    response && response.message
                      ? response.message
                      : status + " : " + xhr,
                  columnClass:
                    "col-xs-8 col-xs-offset-2 col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2",
                  containerFluid: true,
                });
              }
              loading.close();
            },
          });
          return false;
        },
      });
    });
  };
})(jQuery);

// model filter rule
(function ($) {
  FilterRule = function (field, op, value) {
    this.field = field;
    this.op = operation(op);
    this.value = value;
  };

  function operation(op) {
    if ("=" === op) {
      return "equal";
    } else if ("!=" === op) {
      return "notequal";
    } else if (">" === op) {
      return "greater";
    } else if ("<" === op) {
      return "less";
    } else if (">=" === op) {
      return "greaterequal";
    } else if ("<=" === op) {
      return "lessequal";
    } else if ("like" === op) {
      return "contains";
    } else {
      return op;
    }
  }
})(jQuery);

// model filter
(function ($) {
  Filter = function (page, rows, sort, order) {
    filterRules = [];
    if (page !== undefined && isNaN(page) && page < 1) {
      throw "page number min 1";
    }
    if (rows !== undefined && isNaN(rows) && rows < 1) {
      throw "rows number min 1";
    }
    this.page = page;
    this.rows = rows;
    this.sort = sort;
    this.order = order;

    ((this.set = function (page, rows) {
      if (page !== undefined && isNaN(page) && page < 1) {
        throw "page number min 1";
      }
      if (rows !== undefined && isNaN(rows) && rows < 1) {
        throw "rows number min 1";
      }
      this.page = page;
      this.rows = rows;
    }),
      (this.sortOrder = function (sort, order) {
        this.sort = sort;
        this.order = order;
      }),
      (this.add = function (field, op, value) {
        if (this.filterRules == undefined) {
          this.filterRules = [];
        }
        this.filterRules.push(new FilterRule(field, op, value));
      }),
      (this.build = function () {
        let value = {
          page: undefined,
          rows: undefined,
          sort: undefined,
          order: undefined,
          filterRules: undefined,
        };
        if (this.page === undefined) {
          delete value.page;
        } else {
          value.page = this.page;
        }
        if (this.rows === undefined) {
          delete value.rows;
        } else {
          value.rows = this.rows;
        }
        if (this.sort === undefined) {
          delete value.sort;
        } else {
          value.sort = this.sort;
        }
        if (this.order === undefined) {
          delete value.order;
        } else {
          value.order = this.order;
        }
        if (this.filterRules === undefined) {
          delete value.filterRules;
        } else {
          value.filterRules = JSON.stringify(this.filterRules);
        }
        return value;
      }));
    return this;
  };

  Filter = function (page, rows) {
    filterRules = [];
    if (page !== undefined && isNaN(page) && page < 1) {
      throw "page number min 1";
    }
    if (rows !== undefined && isNaN(rows) && rows < 1) {
      throw "rows number min 1";
    }
    this.page = page;
    this.rows = rows;
    this.sort = undefined;
    this.order = undefined;

    ((this.set = function (page, rows) {
      if (page !== undefined && isNaN(page) && page < 1) {
        throw "page number min 1";
      }
      if (rows !== undefined && isNaN(rows) && rows < 1) {
        throw "rows number min 1";
      }
      this.page = page;
      this.rows = rows;
    }),
      (this.sortOrder = function (sort, order) {
        this.sort = sort;
        this.order = order;
      }),
      (this.add = function (field, op, value) {
        if (this.filterRules == undefined) {
          this.filterRules = [];
        }
        this.filterRules.push(new FilterRule(field, op, value));
      }),
      (this.build = function () {
        let value = {
          page: undefined,
          rows: undefined,
          sort: undefined,
          order: undefined,
          filterRules: undefined,
        };
        if (this.page === undefined) {
          delete value.page;
        } else {
          value.page = this.page;
        }
        if (this.rows === undefined) {
          delete value.rows;
        } else {
          value.rows = this.rows;
        }
        if (this.sort === undefined) {
          delete value.sort;
        } else {
          value.sort = this.sort;
        }
        if (this.order === undefined) {
          delete value.order;
        } else {
          value.order = this.order;
        }
        if (this.filterRules === undefined) {
          delete value.filterRules;
        } else {
          value.filterRules = JSON.stringify(this.filterRules);
        }
        return value;
      }));

    return this;
  };

  Filter = function () {
    ((this.set = function (page, rows) {
      if (page !== undefined && isNaN(page) && page < 1) {
        throw "page number min 1";
      }
      if (rows !== undefined && isNaN(rows) && rows < 1) {
        throw "rows number min 1";
      }
      this.page = page;
      this.rows = rows;
      this.sort = undefined;
      this.order = undefined;
    }),
      (this.sortOrder = function (sort, order) {
        this.sort = sort;
        this.order = order;
      }),
      (this.add = function (field, op, value) {
        if (this.filterRules == undefined) {
          this.filterRules = [];
        }
        this.filterRules.push(new FilterRule(field, op, value));
      }),
      (this.build = function () {
        let value = {
          page: undefined,
          rows: undefined,
          sort: undefined,
          order: undefined,
          filterRules: undefined,
        };
        if (this.page === undefined) {
          delete value.page;
        } else {
          value.page = this.page;
        }
        if (this.rows === undefined) {
          delete value.rows;
        } else {
          value.rows = this.rows;
        }
        if (this.sort === undefined) {
          delete value.sort;
        } else {
          value.sort = this.sort;
        }
        if (this.order === undefined) {
          delete value.order;
        } else {
          value.order = this.order;
        }
        if (this.filterRules === undefined) {
          delete value.filterRules;
        } else {
          value.filterRules = JSON.stringify(this.filterRules);
        }
        return value;
      }));
    return this;
  };
})(jQuery);

// grid basic
(function ($) {
  $.fn.grid = function (options) {
    var setting = $.extend(
      {
        columns: [],
        rows: [],
      },
      options,
    );
    this.rows = setting.rows;

    $(this).html("");

    this.rowAt = function (index) {
      return setting.rows[index];
    };

    return this.each(function () {
      let header = "<thead><tr>";
      for (let a in setting.columns) {
        header += "<th>" + setting.columns[a].title + "</th>";
      }
      header += "</tr></thead>";

      let rows = "<tbody>";
      for (let a in setting.rows) {
        rows += "<tr>";
        let row = setting.rows[a];
        for (let i in setting.columns) {
          let properties = setting.columns[i];
          if (properties.format !== undefined) {
            let val = row[properties.field];
            rows += "<td>" + properties.format(row, val) + "</td>";
          } else {
            rows += "<td>" + row[properties.field] + "</td>";
          }
        }
        rows += "</tr>";
      }
      rows += "</tbody>";
      let tmplate = header + rows;

      $(this).html(tmplate);
    });
  };
})(jQuery);

//
