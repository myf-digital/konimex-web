<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_return_detector extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_return_detector_model', 'rep_return');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    function load_view_detector_national() {
    	$brandid = $this->input->post("brandid");
    	$productid = $this->input->post("productid");

    	$now = date('Y-m');

    	$lastMonth = date('Y-m', strtotime('-1 month', strtotime($now)));
    	$threeMonth = date('Y-m', strtotime('-2 month', strtotime($now)));

    	$data = [
    		'brandid' => $brandid,
    		'productid' => $productid,
    		'inmonth' => $now,
    		'lastmonth' => $lastMonth,
    		'threemonth' => $threeMonth,
    	];

    	$detector = $this->rep_return->getDetectorNational($data);

    	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");

    	$nowHeaderArr = explode('-', $now);
    	$nowMonthHeaderArr = str_replace('0', '', $nowHeaderArr[1]);

    	$lastHeaderArr = explode('-', $lastMonth);
    	$lastMonthHeaderArr = str_replace('0', '', $lastHeaderArr[1]);

    	$threeHeaderArr = explode('-', $threeMonth);
    	$threeMonthHeaderArr = str_replace('0', '', $threeHeaderArr[1]);

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th rowspan="2">National</th>';
        $html .= '<th colspan="5">'.$bulan[$nowMonthHeaderArr].'-'.$nowHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$lastMonthHeaderArr].'-'.$lastHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$threeMonthHeaderArr].'-'.$threeHeaderArr[0].'</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$html .= '<tr>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td><a href="#" onclick="open_preview_regional(\''.$brandid.'\',\''.$productid.'\');">National</a></td>';
		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $now && $detect['header_left'] == 'in_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $lastMonth && $detect['header_left'] == 'in_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $threeMonth && $detect['header_left'] == 'in_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td>Last Month</td>';

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $now && $detect['header_left'] == 'last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $lastMonth && $detect['header_left'] == 'last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $threeMonth && $detect['header_left'] == 'last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		$html .= '</tr>';

		$html .= '<tr>';
		$html .= '<td>Last 3 Month</td>';

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $now && $detect['header_left'] == 'three_last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $lastMonth && $detect['header_left'] == 'three_last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		foreach ($detector as $key => $detect) {
			if ($detect['header'] == $threeMonth && $detect['header_left'] == 'three_last_month') {
				$mm = $detect['mm'] != null ? $detect['mm'] : '-';
				$hype = $detect['hype'] != null ? $detect['hype'] : '-';
				$mp = $detect['mp'] != null ? $detect['mp'] : '-';
				$mti = $detect['mti'] != null ? $detect['mti'] : '-';
				$sm = $detect['sm'] != null ? $detect['sm'] : '-';
				$html .= '<td>'.$mm.'</td>';
				$html .= '<td>'.$hype.'</td>';
				$html .= '<td>'.$mp.'</td>';
				$html .= '<td>'.$mti.'</td>';
				$html .= '<td>'.$sm.'</td>';
			}
		}

		$html .= '</tr>';

		$html .= '</tbody>';
		$html .= '</table></div></div></div>';
		$html .='<script>
					const common = new Common();

					let paramsession = common.getCookie("session");

					function open_preview_regional(brandid,productid) {

						common.loading();
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("rep_return_detector/load_view_detector_regional"),
							data : "brandid="+brandid+"&productid="+productid,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);

								common.loadingClose();
							},
							error:function(){
								alert("Load failed");

								common.loadingClose();
							}
						});
					}
				</script>
				';

		echo $html;
    }

    function load_view_detector_regional() {
    	$brandid = $this->input->post("brandid");
    	$productid = $this->input->post("productid");

    	$now = date('Y-m');

    	$lastMonth = date('Y-m', strtotime('-1 month', strtotime($now)));
    	$threeMonth = date('Y-m', strtotime('-2 month', strtotime($now)));

    	$data = [
    		'brandid' => $brandid,
    		'productid' => $productid,
    		'inmonth' => $now,
    		'lastmonth' => $lastMonth,
    		'threemonth' => $threeMonth,
    	];

    	$detector = $this->rep_return->getDetectorRegional($data);

    	$regional = $this->rep_return->get_regional();

    	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");

    	$nowHeaderArr = explode('-', $now);
    	$nowMonthHeaderArr = str_replace('0', '', $nowHeaderArr[1]);

    	$lastHeaderArr = explode('-', $lastMonth);
    	$lastMonthHeaderArr = str_replace('0', '', $lastHeaderArr[1]);

    	$threeHeaderArr = explode('-', $threeMonth);
    	$threeMonthHeaderArr = str_replace('0', '', $threeHeaderArr[1]);

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th rowspan="2">Region</th>';
        $html .= '<th colspan="5">'.$bulan[$nowMonthHeaderArr].'-'.$nowHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$lastMonthHeaderArr].'-'.$lastHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$threeMonthHeaderArr].'-'.$threeHeaderArr[0].'</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$html .= '<tr>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '</tr>';

		foreach ($regional as $value) {
			$html .= '<tr>';
			$html .= '<td><a href="#" onclick="open_preview_area(\''.$brandid.'\',\''.$productid.'\',\''.$value['regionalid'].'\');">'.$value['nama_regional'].'</a></td>';
			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last 3 Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['regionalid'] == $detect['regionalid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table></div></div></div>';
		$html .='<script>
					const common = new Common();

					let paramsession = common.getCookie("session");

					function open_preview_area(brandid,productid,regionalid) {

						common.loading();
				
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("rep_return_detector/load_view_detector_area"),
							data : "brandid="+brandid+"&productid="+productid+"&regionalid="+regionalid,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);

								common.loadingClose();
							},
							error:function(){
								alert("Load failed");

								common.loadingClose();
							}
						});
					}
				</script>
				';

		echo $html;
    	
    }

    function load_view_detector_area() {
    	$brandid = $this->input->post("brandid");
    	$productid = $this->input->post("productid");
    	$regionalid = $this->input->post("regionalid");

    	$now = date('Y-m');

    	$lastMonth = date('Y-m', strtotime('-1 month', strtotime($now)));
    	$threeMonth = date('Y-m', strtotime('-2 month', strtotime($now)));

    	$data = [
    		'brandid' => $brandid,
    		'productid' => $productid,
    		'inmonth' => $now,
    		'lastmonth' => $lastMonth,
    		'threemonth' => $threeMonth,
    		'regionalid' => $regionalid
    	];

    	$detector = $this->rep_return->getDetectorArea($data);

    	$area = $this->rep_return->get_area($data);

    	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");

    	$nowHeaderArr = explode('-', $now);
    	$nowMonthHeaderArr = str_replace('0', '', $nowHeaderArr[1]);

    	$lastHeaderArr = explode('-', $lastMonth);
    	$lastMonthHeaderArr = str_replace('0', '', $lastHeaderArr[1]);

    	$threeHeaderArr = explode('-', $threeMonth);
    	$threeMonthHeaderArr = str_replace('0', '', $threeHeaderArr[1]);

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th rowspan="2">Area</th>';
        $html .= '<th colspan="5">'.$bulan[$nowMonthHeaderArr].'-'.$nowHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$lastMonthHeaderArr].'-'.$lastHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$threeMonthHeaderArr].'-'.$threeHeaderArr[0].'</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$html .= '<tr>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '</tr>';

		foreach ($area as $value) {
			$html .= '<tr>';
			$html .= '<td><a href="#" onclick="open_preview_subarea(\''.$brandid.'\',\''.$productid.'\',\''.$value['areaid'].'\',\''.$regionalid.'\');">'.$value['nama_area'].'</a></td>';
			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last 3 Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['areaid'] == $detect['areaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table></div></div></div>';
		$html .='<script>
					const common = new Common();

					let paramsession = common.getCookie("session");

					function open_preview_subarea(brandid,productid,areaid,regionalid) {

						common.loading();
				
						$.ajax({
							type:"POST",
							dataType: "html",
							url: common.baseURL("rep_return_detector/load_view_detector_subarea"),
							data : "productid="+productid+"&areaid="+areaid+"&regionalid="+regionalid+"&brandid="+brandid,
							success:function(res){
								response = res;
								$(\'#tbl-content\').html(response);

								common.loadingClose();
							},
							error:function(){
								alert("Load failed");

								common.loadingClose();
							}
						});
					}

				</script>
				';

		echo $html;
    }

    function load_view_detector_subarea() {
    	$brandid = $this->input->post("brandid");
    	$productid = $this->input->post("productid");
    	$areaid = $this->input->post("areaid");
    	$regionalid = $this->input->post("regionalid");

    	$now = date('Y-m');

    	$lastMonth = date('Y-m', strtotime('-1 month', strtotime($now)));
    	$threeMonth = date('Y-m', strtotime('-2 month', strtotime($now)));

    	$data = [
    		'brandid' => $brandid,
    		'productid' => $productid,
    		'inmonth' => $now,
    		'lastmonth' => $lastMonth,
    		'threemonth' => $threeMonth,
    		'areaid' => $areaid,
    		'regionalid' => $regionalid
    	];

    	$detector = $this->rep_return->getDetectorSubArea($data);

    	$area = $this->rep_return->get_subarea($data);

    	$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");

    	$nowHeaderArr = explode('-', $now);
    	$nowMonthHeaderArr = str_replace('0', '', $nowHeaderArr[1]);

    	$lastHeaderArr = explode('-', $lastMonth);
    	$lastMonthHeaderArr = str_replace('0', '', $lastHeaderArr[1]);

    	$threeHeaderArr = explode('-', $threeMonth);
    	$threeMonthHeaderArr = str_replace('0', '', $threeHeaderArr[1]);

    	$html ='<div class="box-header">';
    	$html .='<div class="row">';
		$html .= '<div class="col-md-12">';
		$html .= '<table class="table table-striped table-bordered table-condensed report-table">';
		$html .= '<thead">';
		$html .= '<tr>';
        $html .= '<th rowspan="2">Sub Area</th>';
        $html .= '<th colspan="5">'.$bulan[$nowMonthHeaderArr].'-'.$nowHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$lastMonthHeaderArr].'-'.$lastHeaderArr[0].'</th>';
        $html .= '<th colspan="5">'.$bulan[$threeMonthHeaderArr].'-'.$threeHeaderArr[0].'</th>';
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';

		$html .= '<tr>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '<td>MM</td>';
		$html .= '<td>Hype</td>';
		$html .= '<td>MP</td>';
		$html .= '<td>MTI</td>';
		$html .= '<td>SM</td>';

		$html .= '</tr>';

		foreach ($area as $value) {
			$html .= '<tr>';
			$html .= '<td><a href="#" onclick="download_to_excel(\''.$brandid.'\',\''.$productid.'\',\''.$value['subareaid'].'\');">'.$value['nama_area'].'</a></td>';
			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'in_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';

			$html .= '<tr>';
			$html .= '<td>Last 3 Month</td>';

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $now && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $lastMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			foreach ($detector as $key => $detect) {
				if ($value['subareaid'] == $detect['subareaid']) {
					if ($detect['header'] == $threeMonth && $detect['header_left'] == 'three_last_month') {
						$mm = $detect['mm'] != null ? $detect['mm'] : '-';
						$hype = $detect['hype'] != null ? $detect['hype'] : '-';
						$mp = $detect['mp'] != null ? $detect['mp'] : '-';
						$mti = $detect['mti'] != null ? $detect['mti'] : '-';
						$sm = $detect['sm'] != null ? $detect['sm'] : '-';
						$html .= '<td>'.$mm.'</td>';
						$html .= '<td>'.$hype.'</td>';
						$html .= '<td>'.$mp.'</td>';
						$html .= '<td>'.$mti.'</td>';
						$html .= '<td>'.$sm.'</td>';
					}
				}
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '</table></div></div></div>';
		$html .='<script>
					const common = new Common();

					let paramsession = common.getCookie("session");

					function download_to_excel(brandid,productid,subareaid) {

						common.loading();

						common.direct("rep_return_detector/download_to_excel_spreadsheet?brandid="+brandid+"&productid="+productid+"&subareaid="+subareaid);
				
						common.loadingClose();
					}

				</script>
				';

		echo $html;
    }

    function download_to_excel_spreadsheet() {
    	ini_set('memory_limit', '128M');

    	$brandid = $this->input->get("brandid");
    	$productid = $this->input->get("productid");
    	$subareaid = $this->input->get("subareaid");

    	$brandid = str_replace('/', '', trim($brandid));	
    	$productid = str_replace('/', '', trim($productid));	
    	$subareaid = str_replace('/', '', trim($subareaid));

    	$data = [
    		'brandid' => $brandid,
    		'productid' => $productid,
    		'subareaid' => $subareaid,
    	];

    	$slob = $this->rep_return->getRekapSlob($data);	

		$filename = "Report_slob_".$subareaid."_".date('YmdHis');

		$spreadsheet = new Spreadsheet();
		$header = ['Periode', 'Regional', 'Area', 'SubArea/City', 'Channel', 'Account', 'Kode Outlet', 'Outlet', 'Product', 'Summary Qty ED', 'Value'];

		$spreadsheet->getActiveSheet()
		    ->fromArray($header,NULL,'A1');

		$bulan=array("","January","February","March","April","May","June","July","August","September","October","November","December");

		$row = 2;
		foreach ($slob as $value) {
			$month = str_replace('0', '', $value['bulan']);
			$periode = $bulan[$month].'-'.$value['tahun'];

		 	$content = [
		 		$periode,
		 		$value['regional'],
		 		$value['area'],
		 		$value['subarea'],
		 		$value['channel'],
		 		$value['account'],
		 		$value['kode_outlet'],
		 		$value['outlet'],
		 		$value['product'],
		 		$value['qty_ed'],
		 		$value['nilai']
		 	];

		 	$spreadsheet->getActiveSheet()
		    	->fromArray($content,NULL,'A'.$row);

		    $row++;
		}
 
		$writer = new Xlsx($spreadsheet);
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
    }
}