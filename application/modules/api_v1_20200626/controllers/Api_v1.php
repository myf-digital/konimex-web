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
        $result = $this->api_v1->get_tipesalesman();
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
        //$data = param_input();
        $result = $this->api_v1->get_product();
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
        if (empty($_GET['periode'])){
            $vdate = date("Y-m-d"); //format date yyyy-mm-dd
        }else{
            $vdate = $_GET['periode']; //format date yyyy-mm-dd
        }
        
        $createby = 'Scheduler';
        //$data = param_input();
        $resultstock = $this->api_v1->generate_stock($vdate);
        $resultatt = $this->api_v1->generate_absensi($vdate);
        $result = $this->api_v1->get_pjp_daily($vdate,$createby);
        if (true == $result) {
            if (true == $resultatt){
            return response("Ok, Att Ok");
            }else{
            return response("Ok, Att NOk");
            }
        } else {
            if (true == $resultatt){
            return response("NOK, Att Ok");
            }else{
            return response("NOK, Att NOk");
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
    
}

