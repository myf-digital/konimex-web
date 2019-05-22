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

    function login()
    {
        $data = param_input();
        $result = $this->api_v1->login($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function aspek()
    {
        $param = param_input();

        $result = $this->api_v1->get_data_aspek($param);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function synchronize_penilaian()
    {
		$param = param_input();
		$today = date("Y-m-d h:i:sa");

        // jawaban
        if (validArrayValue($param, "jawaban")) {
			
            foreach ($param["jawaban"] as $key => $value) {
				$this->db->where('nip', $value["nip"]);
				$this->db->where('id_rdg', $value["id_rdg"]);
				$this->db->delete("trx_hasil_penilaian");
            }
			$return = $this->api_v1->insert_batch_table("trx_hasil_penilaian", $param["jawaban"]);

            foreach ($param["saran"] as $key => $value) {
				$this->db->where('nip', $value["nip"]);
				$this->db->where('id_rdg', $value["id_rdg"]);
				$this->db->delete("trx_saran");
            }
            $returnsaran = $this->api_v1->insert_batch_table("trx_saran", $param["saran"]);
            if ($return) {
				return response("success");
			} else {
				return response("error", "401", "failed");
			}

        }
    }
	
    function writeLog($json, $res)
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $time = @date('[d/M/Y:H:i:s]');
        $path = 'log_trans/' . date("Yn");
        if (!is_dir($path)) {
            mkdir($path, 0755, TRUE);
        }
        $filelog = $path . '/log_api_ekp_' . date("j.n.Y") . '.txt';
        file_put_contents($filelog, $time . $ip . " " . $res . " Json : " . json_encode($json) . "\n", FILE_APPEND | LOCK_EX);
    }

}

