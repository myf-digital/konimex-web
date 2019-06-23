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

    function materi()
    {
        $data = param_input();
        $result = $this->api_v1->materi($data);
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
		$now = date("Y-m-d h:i:sa");
		$eventpublish = $this->api_v1->get_event_id();
     
        // jawaban
        if (isset($param["options"]) && isset($param["saran"])) {
            $options = json_decode($param["options"]);
            foreach ($options as $key => $value) {
                $this->db->where('nip', $value->nip);
                $this->db->where('id_rdg', $value->id_rdg);
                $this->db->delete("trx_hasil_penilaian");
				$value->id_event = $eventpublish["id_event"];
            }
			
            $return = $this->api_v1->insert_batch_table("trx_hasil_penilaian", $options);
            $saran = json_decode($param["saran"]);
            $this->db->where('nip', $saran->nip);
            $this->db->where('id_rdg', $saran->id_rdg);
            $this->db->delete("trx_saran");
            $return_saran = $this->db->insert("trx_saran", $saran);
            if ($return && $return_saran) {
				return response("success");
			} else {
				return response("error", "401", "failed");
			}
            return response("success");
        } else {
            return response(new stdClass(), 400, "Parameter not allowed");
        }
    }
	
    function grafik_per_event()
    {
		$param = param_input();
		$now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"])) {
			$result = $this->api_v1->get_data_hasil_evaluasi_grafik_event($param);
			if (200 == $result->code) {
				return response($result->result);
			} else {
				return response($result->result, $result->code, $result->message);
			}
		}else{
			$param["id_event"] = null;
			$result = $this->api_v1->get_data_hasil_evaluasi_grafik_event($param);
			if (200 == $result->code) {
				return response($result->result);
			} else {
				return response($result->result, $result->code, $result->message);
			}
		}
    }

    function grafik_per_materi()
    {
		$param = param_input();
		$now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"]) and isset($param["id_rdg"])) {
			$result = $this->api_v1->get_data_hasil_evaluasi_grafik_materi($param);
			if (200 == $result->code) {
				return response($result->result);
			} else {
				return response($result->result, $result->code, $result->message);
			}
		}else if (!isset($param["id_event"]) and isset($param["id_rdg"])) {
			$param["id_event"] = null;
			$result = $this->api_v1->get_data_hasil_evaluasi_grafik_materi($param);
			if (200 == $result->code) {
				return response($result->result);
			} else {
				return response($result->result, $result->code, $result->message);
			}
		}else{
			$result = $this->api_v1->get_data_hasil_evaluasi_grafik_materi($param);
			if (200 == $result->code) {
				return response($result->result);
			} else {
				return response($result->result, $result->code, $result->message);
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

