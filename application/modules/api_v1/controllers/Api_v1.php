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
        if (isset($param["id_event"])) {
            // $eventpublish = $this->api_v1->get_event_id();
            $eventpublish = $param["id_event"];
        } else {
            return response(new stdClass(), 400, "Parameter not allowed");
            die();
        }

        // jawaban
        if (isset($param["options"]) && isset($param["saran"])) {
            $options = json_decode($param["options"]);
            foreach ($options as $key => $value) {
                $this->db->where('nip', $value->nip);
                $this->db->where('id_rdg', $value->id_rdg);
                $this->db->delete("trx_hasil_penilaian");
                $value->id_event = $eventpublish;
            }

            $return = $this->api_v1->insert_batch_table("trx_hasil_penilaian", $options);
            $saran = json_decode($param["saran"]);
            $this->db->where('nip', $saran->nip);
            $this->db->where('id_rdg', $saran->id_rdg);
            $this->db->where('id_event', $saran->id_event);
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
        // $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"])) {
            $result = $this->api_v1->get_data_hasil_evaluasi_grafik_event($param);
            if (200 == $result->code) {
                return response($result->result);
            } else {
                return response($result->result, $result->code, $result->message);
            }
        } else {
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
            $resultresponden = $this->api_v1->get_rows_jumlah_responden($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response->chart = $result->result;
				$response->responden = $resultresponden->result;
                return response($response);
            } else {
                return response($result->result, $result->code, $result->message);
            }
        } else if (!isset($param["id_event"]) and isset($param["id_rdg"])) {
            $param["id_event"] = null;
            $result = $this->api_v1->get_data_hasil_evaluasi_grafik_materi($param);
            if (200 == $result->code) {
                return response($result->result);
            } else {
                return response($result->result, $result->code, $result->message);
            }
        } else {
            $result = $this->api_v1->get_data_hasil_evaluasi_grafik_materi($param);
            if (200 == $result->code) {
                return response($result->result);
            } else {
                return response($result->result, $result->code, $result->message);
            }
        }
    }

    function load_all_event()
    {
        $result = $this->api_v1->load_all_event();
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function load_aspek()
    {
        $param = param_input();
        if (isset($param["id_event"])) {
            $result = $this->api_v1->load_aspek($param);
            if (200 == $result->code) {
                return response($result->result);
            } else {
                return response($result->result, $result->code, $result->message);
            }
        } else {
            return response(new stdClass(), 400, "Parameter not allowed");
        }
    }

    function get_data_rekap_per_event()
    {
        $param = param_input();
        $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"])) {
            $result = $this->api_v1->get_headers_rekap_hasil_evaluasi_event($param);
            $resultrows = $this->api_v1->get_rows_rekap_hasil_evaluasi_event($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response);
            } else {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response, $result->code, $result->message);
            }
        }
    }

    function get_data_rekap_per_rdg()
    {
        $param = param_input();
        $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"]) && isset($param["id_rdg"])) {
            $result = $this->api_v1->get_headers_rekap_hasil_evaluasi_rdg($param);
            $resultrows = $this->api_v1->get_rows_rekap_hasil_evaluasi_rdg($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response);
            } else {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response, $result->code, $result->message);
            }
        }
    }

    function get_data_saran_per_rdg()
    {
        $param = param_input();
        $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"]) && isset($param["id_rdg"])) {
            $result = $this->api_v1->get_headers_rekap_saran_rdg($param);
            $resultrows = $this->api_v1->get_rows_rekap_saran_rdg($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response);
            } else {
				$response->header = $result->result;
				$response->rows = $resultrows->result;
                return response($response, $result->code, $result->message);
            }
        }
    }

    function get_data_jumlah_peserta_responden()
    {
        $param = param_input();
        $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"]) && isset($param["id_rdg"])) {
            $result = $this->api_v1->get_rows_jumlah_responden($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response = $result->result;
                return response($response);
            } else {
				$response = $result->result;
                return response($response, $result->code, $result->message);
            }
        }
    }

    function get_data_saran_per_materi()
    {
        $param = param_input();
        $now = date("Y-m-d h:i:sa");
        // jawaban
        if (isset($param["id_event"]) && isset($param["id_rdg"])) {
            $result = $this->api_v1->get_rows_saran($param);
			$response = new stdClass();
            if (200 == $result->code) {
				$response = $result->result;
                return response($response);
            } else {
				$response = $result->result;
                return response($response, $result->code, $result->message);
            }
        }
    }

}

