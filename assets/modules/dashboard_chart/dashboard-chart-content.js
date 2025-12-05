(function () {
  // import commons
  const common = new Common();
  const commonGrid = new CommonGrid();
  common.setTitle('Dashboard Chart');

  // ui components
  let uiChartProductivity = $('#chartProductivity');
  let uiChartPerformance = $('#chartPerformance');
  let uiChartSummary = $('#chartSummary');
  let chartProductivity, chartChartPerformance, chartSummary;

  load();

  function load() {
    $.ajax({
      type: 'GET',
      dataType: 'json',
      beforeSend : function() {
        common.loading();
      },
      url: common.baseURL('dashboard_chart/load'),
      success: function(data) {
        loadChartProductivity(data.productivity);
        loadChartPerformance(data.performance);
        loadChartSummary(data.summary);
        common.loadingClose();
      },
      error:function(){
        alert('Load failed');
        common.loadingClose();
      }
    });
  }

  function loadChartProductivity(data) {
    if (chartProductivity) chartProductivity.destroy();

    chartProductivity = new Chart(uiChartProductivity, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Productivity PAR-MA',
          data: data.data,
          backgroundColor: 'rgba(54,162,235,0.6)',
          borderColor: '#2670d8',
          borderWidth: 2
        }]
      }
    });
  }

  function loadChartPerformance(data) {
    if (chartChartPerformance) chartChartPerformance.destroy();

    chartChartPerformance = new Chart(uiChartPerformance, {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [
          { label: 'Schedule', borderColor: '#008d4c', data: data.data_schedule },
          { label: 'Call', borderColor: '#00de93', data: data.data_call },
          { label: 'Extra Call', borderColor: '#f82347', data: data.data_extra },
          { label: 'CRC', borderColor: '#f7f02a', data: data.data_crc },
          { label: 'Order', borderColor: '#2a60f7', data: data.data_order },
        ]
      }
    });
  }

  function loadChartSummary(data) {
    if (chartSummary) chartSummary.destroy();

    chartSummary = new Chart(uiChartSummary, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [
          { label: 'Quota', data: data.data_quota, backgroundColor: 'rgba(255,159,64,0.7)' },
          { label: 'Actual', data: data.data_actual, backgroundColor: 'rgba(75,192,192,0.7)' },
        ]
      }
    });
  }
})();
