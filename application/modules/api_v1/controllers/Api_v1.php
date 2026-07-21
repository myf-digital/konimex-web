<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Api_v1 extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->model('Api_v1_model', 'api_v1');
    }

    function index()
    {

    }

    function call_siteid()
    {
        //$data = param_input();
        $result = $this->api_v1->get_siteid();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_salesman()
    {
        $data = param_input();
        $result = $this->api_v1->get_salesman($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_spesialisasi()
    {
        $data = param_input();
        $result = $this->api_v1->get_spesialisasi($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_professional()
    {
        $data = param_input();
        $result = $this->api_v1->get_professional($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_gff_admin()
    {
        $data = param_input();
        $result = $this->api_v1->get_all_gff_admin($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_salesman_under_lead()
    {
        $data = param_input();
        $result = $this->api_v1->get_salesman($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_salesman_mapping_area()
    {
        $data = param_input();
        $result = $this->api_v1->get_salesman_mapping_area($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_regional()
    {
        //$data = param_input();
        $result = $this->api_v1->get_regional();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_regional_restrict()
    {
        $data = param_input();
        $result = $this->api_v1->get_regional_restrict($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_area()
    {
        $data = param_input();
        $result = $this->api_v1->get_area($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_area_restrict()
    {
        $data = param_input();
        $result = $this->api_v1->get_area_restrict($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_subarea_restrict()
    {
        $data = param_input();
        $result = $this->api_v1->get_subarea_restrict($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_city()
    {
        $data = param_input();
        $result = $this->api_v1->get_city($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_areakirim()
    {
        $data = param_input();
        $result = $this->api_v1->get_areakirim($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_subarea()
    {
        $data = param_input();
        $result = $this->api_v1->get_subarea($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_propinsi()
    {
        //$data = param_input();
        $result = $this->api_v1->get_propinsi();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_kota()
    {
        $data = param_input();
        $result = $this->api_v1->get_kota($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_kecamatan()
    {
        $data = param_input();
        $result = $this->api_v1->get_kecamatan($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_kelurahan()
    {
        $data = param_input();
        $result = $this->api_v1->get_kelurahan($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_paytype()
    {
        $data = param_input();
        $result = $this->api_v1->get_paytype();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_gudang()
    {
        $data = param_input();
        $result = $this->api_v1->get_gudang($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_statusaktif()
    {
        $data = param_input();
        $result = $this->api_v1->get_statusaktif();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_tipesalesman()
    {
        $data = param_input();
        $result = $this->api_v1->get_tipesalesman($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_tipetrans()
    {
        $data = param_input();
        $result = $this->api_v1->get_tipetrans();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_frequency()
    {
        $data = param_input();
        $result = $this->api_v1->get_frequency();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_area_by_sales()
    {
        $data = param_input();
        $result = $this->api_v1->get_area_by_sales($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_weeks()
    {
        $data = param_input();
        $result = $this->api_v1->get_weeks();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_days()
    {
        $data = param_input();
        $result = $this->api_v1->get_days();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_trackingsales()
    {
        $data = param_input();
        $result = $this->api_v1->get_tracking($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_param_key()
    {
        $data = param_input();
        $result = $this->api_v1->get_param_key($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_jabatan()
    {
        //$data = param_input();
        $result = $this->api_v1->get_jabatan();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_account_outlet()
    {
        //$data = param_input();
        $result = $this->api_v1->get_account_outlet();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_product()
    {
        $data = param_input();
        $result = $this->api_v1->get_product($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_product_filter_brand()
    {
        $data = param_input();
        $result = $this->api_v1->get_product_filter_brand($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_product_brand()
    {
        //$data = param_input();
        $result = $this->api_v1->get_brand();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_pjp_daily()
    {
        ini_set("memory_limit","512M");
        ini_set('max_execution_time', '0');
		
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        $createby = 'Scheduler';
        //$data = param_input();
        $resultsos = $this->api_v1->generate_rekap_sos($vdate);
        //$resultstock = $this->api_v1->generate_stock($vdate);
        $resultatt = $this->api_v1->generate_absensi($vdate);
        $result = $this->api_v1->get_pjp_daily($vdate,$createby);
        //$resultoos = $this->api_v1->get_stock_all_periode_fr_oos($vdate);
        //echo $this->db->last_query();
        if (true == $result) {
            if (true == $resultatt){
				return response("PJP Ok, Att Ok");    
            }else{
                return response("PJP Ok, Att NOk");
            }
        } else {
            if (true == $resultatt){
				return response("PJP NOk, Att Ok");    
            }else{
                return response("PJP NOK, Att NOk");
            }
        }

    }

    function call_outlet_pjp()
    {
        $data = param_input();
        $result = $this->api_v1->get_outlet_pjp($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_outlet_dub()
    {
        $data = param_input();
        $result = $this->api_v1->get_outlet_dub($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_outlet_planned()
    {
        $data = param_input();
        $result = $this->api_v1->get_outlet_planned($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_pjp_detail()
    {
        $data = param_input();
        $result = $this->api_v1->get_pjp_detail($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function outlet_pjp()
    {
        $data = param_input();
        $result = $this->api_v1->get_outlet_within_radius($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_ram_rsm()
    {
        $data = param_input();
        $result = $this->api_v1->get_ram_rsm($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_aas_aam_tss_tsm()
    {
        $data = param_input();
        $result = $this->api_v1->get_aas_aam_tss_tsm($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_fc()
    {
        $data = param_input();
        $result = $this->api_v1->get_fc($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_absensi()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        //$createby = 'Scheduler';
        //$data = param_input();
        $result = $this->api_v1->generate_absensi($vdate);
        if (true == $result) {
            return response("Ok");
        } else {
            return response("NOK");
        }
    }

    function call_absensi_monthly()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        //$createby = 'Scheduler';
        //$data = param_input();
        $result = $this->api_v1->generate_absensi_monthly($vdate);
        if (true == $result) {
            return response("Ok");
        } else {
            return response("NOK");
        }
    }
	
    function call_absensi_daily()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        //$createby = 'Scheduler';
        //$data = param_input();
        $result = $this->api_v1->generate_absensi_daily($vdate);
        if (true == $result) {
            return response("Ok");
        } else {
            return response("NOK");
        }
    }
    function call_generate_stock()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        //$createby = 'Scheduler';
        //$data = param_input();
        $result = $this->api_v1->generate_stock($vdate);
        if (true == $result) {
            return response("Ok");
        } else {
            return response("NOK");
        }
    }

    function generate_stock_doi()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
		
        $result = $this->api_v1->generate_stock_doi($vdate);
        
        return response($result->result);
        /*if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }*/
    }

    function call_recon_fr_oos()
    {
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        $resultstock = $this->api_v1->generate_stock($vdate);
        $result = $this->api_v1->get_stock_all_periode_fr_oos($vdate);
        //echo $this->db->last_query();
        if (true == $result) {
			return response("Recon Fr OOS Ok");    
        } else {
			return response("Recon Fr OOS NOK");
		}
    }

    function call_recon_available_sku_last3months()
    {
        $result = $this->api_v1->get_available_sku_last3months();
        //echo $this->db->last_query();
        if (true == $result) {
			return response("Recon avalable SKU last 3 months Ok");    
        } else {
			return response("Recon avalable SKU last 3 months NOK");
		}
    }

    function call_available_sku_last3months_period()
    {
        $result = $this->api_v1->get_available_sku_last3months_period();
        //echo $this->db->last_query();
        if (true == $result) {
			return response("Recon avalable SKU last 3 months Ok");    
        } else {
			return response("Recon avalable SKU last 3 months NOK");
		}
    }

    function call_delete_crc()
    {
        $result = $this->api_v1->get_crc_takout();
        //echo $this->db->last_query();
        if (true == $result) {
			return response("delete success");    
        } else {
			return response("delete failde");
		}
    }

    function call_productivity_md()
    {
        $data = param_input();
        $result = $this->api_v1->get_productivity_md($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_productivity_salesman()
    {
        $data = param_input();
        $result = $this->api_v1->get_productivity_salesman($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }
    
    function call_productivity_fc()
    {
        $data = param_input();
        $result = $this->api_v1->get_productivity_fc($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_schedule_dub()
    {
        ini_set("memory_limit","512M");
        ini_set('max_execution_time', '0');
		
        if (empty($_GET['periode'])) {
            $vdate = date("Y-m"); //format date yyyy-mm
        } else {
            $vdate = $_GET['periode']; //format date yyyy-mm
        }
        
        $createby = 'Scheduler';
        $result = $this->api_v1->get_schedule_dub($vdate, $createby);
        if (!empty($result) && count($result) > 0) {
			return response("Schedule DUB Done");    
        } else {
			return response("Schedule DUB Not Done");
        }
    }

    function call_update_table()
    {
        if (!$this->validate_token()) {
            return response(null, 401, "Unauthorized: Invalid X-Token.");
        }

        $data = param_input();
        if (empty($data['table']) || empty($data['data']) || empty($data['where'])) {
            return response(null, 400, "Missing required parameters: table, data, and where are required.");
        }

        $table = $data['table'];
        $update_data = $data['data'];
        $where = $data['where'];

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            return response(null, 400, "Invalid table name format.");
        }

        $result = $this->api_v1->update_table($table, $update_data, $where);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_get_table()
    {
        if (!$this->validate_token()) {
            return response(null, 401, "Unauthorized: Invalid X-Token.");
        }

        $data = param_input();
        
        if (!empty($data['query'])) {
            $query_str = $data['query'];
            $result = $this->api_v1->execute_query_table($query_str);
        } else if (!empty($data['table'])) {
            $table = $data['table'];
            $columns = isset($data['columns']) ? $data['columns'] : '*';
            $where = isset($data['where']) ? $data['where'] : null;

            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
                return response(null, 400, "Invalid table name format.");
            }

            $result = $this->api_v1->get_table($table, $columns, $where);
        } else {
            return response(null, 400, "Missing query or table parameters.");
        }

        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_download_log()
    {
        if (!$this->validate_token()) {
            return response(null, 401, "Unauthorized: Invalid token.");
        }

        $data = param_input();
        if (empty($data['filename'])) {
            $filename = $this->input->get('filename', TRUE);
        } else {
            $filename = $data['filename'];
        }

        if (empty($filename)) {
            return response(null, 400, "Filename is required.");
        }

        if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $filename) || strpos($filename, '..') !== FALSE) {
            return response(null, 400, "Invalid filename format.");
        }

        $filepath = APPPATH . 'logs/' . $filename;
        $real_filepath = realpath($filepath);
        $real_logs_dir = realpath(APPPATH . 'logs');

        if ($real_filepath === FALSE || strpos($real_filepath, $real_logs_dir) !== 0) {
            return response(null, 404, "Log file not found.");
        }

        $this->load->helper('download');
        force_download($real_filepath, NULL);
    }

    function list_uploads()
    {
        if (!$this->validate_token()) {
            return response(null, 401, "Unauthorized: Invalid token.");
        }

        $data = param_input();
        $path = '';
        if (!empty($data['path'])) {
            $path = $data['path'];
        } else {
            $path = $this->input->get('path', TRUE);
        }

        if ($path && strpos($path, '..') !== FALSE) {
            return response(null, 400, "Invalid path format.");
        }

        $base_dir = realpath(FCPATH . 'uploads');
        if ($base_dir === FALSE || !is_dir($base_dir)) {
            return response(null, 500, "Uploads directory not found.");
        }

        $target_dir = $base_dir;
        if (!empty($path)) {
            $target_dir = realpath($base_dir . DIRECTORY_SEPARATOR . $path);
            if ($target_dir === FALSE || strpos($target_dir, $base_dir) !== 0 || !is_dir($target_dir)) {
                return response(null, 404, "Directory not found.");
            }
        }

        $result = [];
        $this->scan_directory_recursive($base_dir, $target_dir, $result);

        return response($result, 200, "Success");
    }

    function call_download_upload()
    {
        if (!$this->validate_token()) {
            return response(null, 401, "Unauthorized: Invalid token.");
        }

        $data = param_input();
        if (empty($data['filename'])) {
            $filename = $this->input->get('filename', TRUE);
        } else {
            $filename = $data['filename'];
        }

        if (empty($filename)) {
            return response(null, 400, "Filename/path is required.");
        }

        if (!preg_match('/^[a-zA-Z0-9_\-\.\/]+$/', $filename) || strpos($filename, '..') !== FALSE) {
            return response(null, 400, "Invalid filename format.");
        }

        $uploads_dir = FCPATH . 'uploads';
        $real_uploads_dir = realpath($uploads_dir);
        $filepath = $real_uploads_dir . DIRECTORY_SEPARATOR . $filename;
        $real_filepath = realpath($filepath);

        if ($real_filepath === FALSE || strpos($real_filepath, $real_uploads_dir) !== 0) {
            return response(null, 404, "File not found.");
        }

        if (is_dir($real_filepath)) {
            return response(null, 400, "Cannot download a directory.");
        }

        $is_image = false;
        $mime = 'application/octet-stream';
        if (function_exists('mime_content_type')) {
            $detected_mime = mime_content_type($real_filepath);
            if ($detected_mime && strpos($detected_mime, 'image/') === 0) {
                $is_image = true;
                $mime = $detected_mime;
            }
        } else {
            $ext = strtolower(pathinfo($real_filepath, PATHINFO_EXTENSION));
            $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
            if (in_array($ext, $image_extensions)) {
                $is_image = true;
                switch ($ext) {
                    case 'jpg':
                    case 'jpeg':
                        $mime = 'image/jpeg';
                        break;
                    case 'png':
                        $mime = 'image/png';
                        break;
                    case 'gif':
                        $mime = 'image/gif';
                        break;
                    case 'bmp':
                        $mime = 'image/bmp';
                        break;
                    case 'webp':
                        $mime = 'image/webp';
                        break;
                    case 'svg':
                        $mime = 'image/svg+xml';
                        break;
                }
            }
        }

        if ($is_image) {
            header('Content-Type: ' . $mime);
            header('Content-Length: ' . filesize($real_filepath));
            readfile($real_filepath);
            exit;
        }

        $this->load->helper('download');
        force_download($real_filepath, NULL);
    }

    // helper
    private function validate_token()
    {
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth_header || stripos($auth_header, 'Basic ') !== 0) {
            return false;
        }

        $credentials = explode(':', base64_decode(substr($auth_header, 6)), 2);
        if (count($credentials) !== 2) {
            return false;
        }

        $username = $credentials[0];
        $password = $credentials[1];

        if (empty($username) || empty($password)) {
            return false;
        }

        $encript = md5($password);
        $this->db->select("resource_id");
        $this->db->from("app_resource");
        $this->db->where([
            'username' => $username,
            'password' => $encript,
        ]);
        
        $cek_user = $this->db->get()->num_rows();
        return ($cek_user > 0);
    }

    private function scan_directory_recursive($base_dir, $current_dir, &$result)
    {
        $items = scandir($current_dir);
        if ($items === FALSE) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $filepath = $current_dir . DIRECTORY_SEPARATOR . $item;
            $real_filepath = realpath($filepath);

            if ($real_filepath === FALSE || strpos($real_filepath, $base_dir) !== 0) {
                continue;
            }

            if ($item[0] === '.') {
                continue;
            }

            $relative_path = ltrim(substr($real_filepath, strlen($base_dir)), DIRECTORY_SEPARATOR);
            $relative_path = str_replace(DIRECTORY_SEPARATOR, '/', $relative_path);

            if (is_dir($real_filepath)) {
                $result[] = [
                    'path' => $relative_path,
                    'name' => $item,
                    'type' => 'directory',
                    'modified' => date('Y-m-d H:i:s', filemtime($real_filepath))
                ];
                $this->scan_directory_recursive($base_dir, $real_filepath, $result);
            } else {
                $result[] = [
                    'path' => $relative_path,
                    'name' => $item,
                    'type' => 'file',
                    'size' => filesize($real_filepath),
                    'modified' => date('Y-m-d H:i:s', filemtime($real_filepath))
                ];
            }
        }
    }
}

