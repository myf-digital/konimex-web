(function () {
  const common = new Common();
  common.setTitle("Report Planned");

  let uiBtnPreview = $("#btn-preview-form");
  let uiBtnDownload = $("#btn-download-form");
  let uiSelectSalesID = $("#salesmanid-id");

  let calendarState = {
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    events: [],
  };

  const monthNames = [
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
  ];

  const dayNames = [
    "Minggu",
    "Senin",
    "Selasa",
    "Rabu",
    "Kamis",
    "Jumat",
    "Sabtu",
  ];

  initializeParam();

  function initializeParam() {
    uiBtnPreview.click(function () {
      open_preview();
    });

    uiBtnDownload.click(function () {
      save_xls();
    });

    load_salesid();
    initializeCalendar();
  }

  function open_preview() {
    let salesmanid = uiSelectSalesID.val();

    if (!salesmanid) {
      alert("Pilih MEDREP terlebih dahulu");
      return;
    }

    common.loading();
    $.ajax({
      type: "POST",
      dataType: "json",
      url: common.baseURL("rep_sales_planned/load_view_planned"),
      data: "salesmanid=" + salesmanid,
      success: function (res) {
        if (res.status) {
          $("#salesman-info-container").html(res.info_html);
          calendarState.events = res.events || [];
          renderCalendar();
        } else {
          alert(res.message || "Failed to load data");
        }
        common.loadingClose();
      },
      error: function () {
        common.loadingClose();
        alert("Load failed");
      },
    });
  }

  function save_xls() {
    let salesmanid = uiSelectSalesID.val();
    if (!salesmanid) {
      alert("Pilih MEDREP terlebih dahulu");
      return;
    }

    common.direct(
      "rep_sales_planned/download_to_excel_spreadsheet?salesmanid=" +
        salesmanid,
    );
  }

  function load_salesid() {
    common.loading();
    $.post(common.baseURL("api_v1/call_salesman"), function (res) {
      uiSelectSalesID.empty();
      uiSelectSalesID.select2({
        placeholder: "Select MEDREP",
        allowClear: true,
        data: $.map(res.result, function (o) {
          o.id = o.salesmanid; // replace name with the property used for the text
          o.text = o.salesmanid + " - " + o.nama_salesman;
          return o;
        }),
      });

      uiSelectSalesID.val(null).trigger("change");
      common.loadingClose();
    });
  }

  function initializeCalendar() {
    calendarState.events = [];
    calendarState.currentMonth = new Date().getMonth();
    calendarState.currentYear = new Date().getFullYear();

    $("#btn-prev-month")
      .off("click")
      .on("click", function (e) {
        e.preventDefault();
        navigateMonth(-1);
      });

    $("#btn-next-month")
      .off("click")
      .on("click", function (e) {
        e.preventDefault();
        navigateMonth(1);
      });

    $("#btn-today")
      .off("click")
      .on("click", function (e) {
        e.preventDefault();
        calendarState.currentMonth = new Date().getMonth();
        calendarState.currentYear = new Date().getFullYear();
        renderCalendar();
      });

    $("#btn-close-modal")
      .off("click")
      .on("click", function () {
        $("#planned-detail-modal").fadeOut(200);
      });

    $(window)
      .off("click.modalClose")
      .on("click.modalClose", function (event) {
        let modal = $("#planned-detail-modal");
        if (event.target == modal[0]) {
          modal.fadeOut(200);
        }
      });

    renderCalendar();
  }

  function navigateMonth(direction) {
    calendarState.currentMonth += direction;
    if (calendarState.currentMonth < 0) {
      calendarState.currentMonth = 11;
      calendarState.currentYear -= 1;
    } else if (calendarState.currentMonth > 11) {
      calendarState.currentMonth = 0;
      calendarState.currentYear += 1;
    }
    renderCalendar();
  }

  function renderCalendar() {
    let month = calendarState.currentMonth;
    let year = calendarState.currentYear;

    $("#calendar-month-year").text(`${monthNames[month]} ${year}`);

    let gridContainer = $("#calendar-grid-container");
    gridContainer.empty();

    dayNames.forEach(function (day) {
      gridContainer.append(`<div class="calendar-day-header">${day}</div>`);
    });

    let firstDayIndex = new Date(year, month, 1).getDay();
    let totalDays = new Date(year, month + 1, 0).getDate();
    let prevTotalDays = new Date(year, month, 0).getDate();

    for (let i = firstDayIndex; i > 0; i--) {
      let prevDay = prevTotalDays - i + 1;
      gridContainer.append(`
        <div class="calendar-day other-month">
          <div class="calendar-day-number">${prevDay}</div>
        </div>
      `);
    }

    for (let day = 1; day <= totalDays; day++) {
      let dateString = formatDateString(year, month, day);

      let dayEvents = calendarState.events.filter(function (event) {
        if (!event.periode) return false;
        let eventDateOnly = event.periode.split(" ")[0];
        return eventDateOnly === dateString;
      });

      let hasEvents = dayEvents.length > 0;
      let dayClass = "calendar-day";
      if (hasEvents) {
        dayClass += " has-event";
      }

      let today = new Date();
      if (
        today.getDate() === day &&
        today.getMonth() === month &&
        today.getFullYear() === year
      ) {
        dayClass += " today";
      }

      let dayHtml = `
        <div class="${dayClass}" data-date="${dateString}">
          <div class="calendar-day-number">${day}</div>
      `;

      if (hasEvents) {
        dayHtml += `
          <div class="calendar-event-badge">${dayEvents.length} Planned</div>
        `;
      }

      dayHtml += `</div>`;

      let dayEl = $(dayHtml);

      if (hasEvents) {
        dayEl.on("click", function () {
          showEventDetails(dateString, dayEvents);
        });
      }

      gridContainer.append(dayEl);
    }

    let totalCells = firstDayIndex + totalDays;
    let nextPadding = 42 - totalCells;
    if (nextPadding >= 7) {
      nextPadding = nextPadding % 7;
    }

    for (let i = 1; i <= nextPadding; i++) {
      gridContainer.append(`
        <div class="calendar-day other-month">
          <div class="calendar-day-number">${i}</div>
        </div>
      `);
    }
  }

  function formatDateString(year, month, day) {
    let m = String(month + 1).padStart(2, "0");
    let d = String(day).padStart(2, "0");
    return `${year}-${m}-${d}`;
  }

  function showEventDetails(dateString, events) {
    let parts = dateString.split("-");
    let d = parseInt(parts[2], 10);
    let m = parseInt(parts[1], 10) - 1;
    let y = parts[0];

    let formattedDate = `${d} ${monthNames[m]} ${y}`;
    $("#planned-modal-title").text(`Planned Visits - ${formattedDate}`);

    let modalBody = $("#planned-modal-body");
    modalBody.empty();

    let groupedEvents = {};
    events.forEach(function (event) {
      let key = event.customerid;
      if (!groupedEvents[key]) {
        groupedEvents[key] = {
          kode_outlet: event.kode_outlet,
          outlet: event.outlet,
          professionals: [],
        };
      }
      let profName = event.user_name
        ? event.user_name
        : "No Professional Mapped";
      if (!groupedEvents[key].professionals.includes(profName)) {
        groupedEvents[key].professionals.push(profName);
      }
    });

    Object.keys(groupedEvents).forEach(function (key) {
      let item = groupedEvents[key];
      let outletCode = item.kode_outlet ? `[${item.kode_outlet}] ` : "";
      let outletName = item.outlet || "Unknown Outlet";

      let profsHtml = "";
      item.professionals.forEach(function (prof) {
        profsHtml += `
          <div class="planned-item-professional" style="margin-top: 4px;">
            <i class="fa fa-user-md"></i> ${prof}
          </div>
        `;
      });

      let itemHtml = `
        <div class="planned-item">
          <div class="planned-item-outlet">${outletCode}${outletName}</div>
          ${profsHtml}
        </div>
      `;
      modalBody.append(itemHtml);
    });

    $("#planned-detail-modal").css("display", "flex").hide().fadeIn(200);
  }
})();
