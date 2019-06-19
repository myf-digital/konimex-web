<?php

class Api_v1_model extends CI_Model
{
    function login($data)
    {
        if (is_null($data["nip"]) or is_null($data["password"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.nip,a.nama_karyawan,a.id_satker,b.kode_satker,b.satker
					  from ref_karyawan a left join ref_satuan_kerja b on a.id_satker=b.id_satker
					where a.nip=?";
            $res_ss = $this->db->query($sql, array($data["nip"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "NIP not found!");
            }
        }
    }	

    function materi($data)
    {
        if (is_null($data["nip"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.nip,a.nama_karyawan,a.id_satker,b.kode_satker,b.satker, c.id_rdg, c.nama_rdg, c.tanggal, 
					  (select count(1) from trx_hasil_penilaian where nip=a.nip and id_rdg=c.id_rdg) as review
					  from ref_karyawan a left join ref_satuan_kerja b on a.id_satker=b.id_satker
					  left join ref_rdg c on a.id_satker=c.id_satker
					where a.nip=?";
            $res_ss = $this->db->query($sql, array($data["nip"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "NIP not found!");
            }
        }
    }

    function get_data_aspek($data)
    {
        if (is_null($data["idrdg"]) or is_null($data["nip"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_rdg, b.nama_rdg, b.tanggal, a.id_aspek, c.aspek, c.id_tipe tipe_soal, c.listjawaban, c.nourut, d.value nilai
					from 
					ref_map_rdg a left join ref_rdg b on a.id_rdg=b.id_rdg
					left join ref_aspek c on a.id_aspek=c.id_aspek
					left join trx_hasil_penilaian d on a.id_rdg=d.id_rdg and a.id_aspek=d.id_aspek and d.nip = ?
					where a.id_rdg=?
					order by a.id_rdg, c.nourut";
            $res_ss = $this->db->query($sql, array($data["nip"],$data["idrdg"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Materi not found!");
            }
        }
    }

    function get_data_hasil_evaluasi($data)
    {
        if (is_null($data["idrdg"]) or is_null($data["satker"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_rdg, b.nama_rdg, b.tanggal, a.id_aspek, c.aspek, c.id_tipe tipe_soal, c.listjawaban, c.nourut, d.value nilai
					from 
					ref_map_rdg a left join ref_rdg b on a.id_rdg=b.id_rdg
					left join ref_aspek c on a.id_aspek=c.id_aspek
					left join trx_hasil_penilaian d on a.id_rdg=d.id_rdg and a.id_aspek=d.id_aspek and d.nip = ?
					where a.id_rdg=? 
					order by a.id_rdg, c.nourut";
            $res_ss = $this->db->query($sql, array($data["nip"],$data["idrdg"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Materi not found!");
            }
        }
    }

    function insert_batch_table($table_name, $data)
    {
        return $this->db->insert_batch($table_name, $data);
    }

   function delete_table($table_name, $where)
    {
        if (isset($where)) {
            $this->db->delete($table_name, $where);
        }
    }

    function get_table($table)
    {
        $sql = "select * from $table";
        $return = $this->db->query($sql)->result_array();
        return $return;
    }

    function insert_table_upload($table, $values)
    {
        $db = $this->db;
        $db->trans_start();
        if ("t_ekspedisi_setup_site" == $table) {
            $this->insert_setup_site($db, $table, $values);
        } else if ("t_ekspedisi_periode" == $table) {
            $this->insert_periode($db, $table, $values);
        } else if ("t_ekspedisi_rute" == $table) {
            $this->insert_rute($db, $table, $values);
        } else if ("t_ekspedisi_master" == $table) {
            $this->insert_master($db, $table, $values);
        } else if ("t_ekspedisi_detail_product" == $table) {
            $this->insert_detail_product($db, $table, $values);
        } else if ("t_ekspedisi_detail_rekap_product" == $table) {
            $this->insert_detail_rekap_product($db, $table, $values);
        } else if ("t_ekspedisi_serah_terima" == $table) {
            $this->insert_serah_terima($db, $table, $values);
        } else if ("t_ekspedisi_tagihan" == $table) {
            $this->insert_tagihan($db, $table, $values);
        } else if ("t_ekspedisi_kilometer" == $table) {
            $this->insert_kilometer($db, $table, $values);
        } else if ("t_ekspedisi_biaya_jenis" == $table) {
            $this->insert_biaya_jenis($db, $table, $values);
        }
        $db->trans_complete();
    }

    function insert_setup_site($table, $values)
    {
        $exclude = array('siteid');
        $sql = "select count(1) as total from $table where siteid=?";
        foreach ($values as $value) {
            $res = $this->db->query($sql, array($value->siteid))->row();
            if (0 === $res->total) {
                $this->db->insert($table, $value);
            } else {
                $result = excludeValueForUpdate($value, $exclude);
                $this->update_table($table, $result->value, $result->where);
            }
        }
    }

    function insert_periode($table, $values)
    {
        $exclude = array('siteid', 'driverid', 'periode');
        $sql = "select count(1) as total from $table where siteid=? and driverid=? and periode=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->driverid) && isset($value->periode)) {
                $res = $this->db->query($sql, array($value->siteid, $value->driverid, $value->periode))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_rute($table, $values)
    {
        $exclude = array('siteid', 'driverid', 'periode', 'customerid');
        $sql = "select count(1) as total from $table where siteid=? and driverid=? and periode=? and customerid=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->driverid) && isset($value->periode) && isset($value->customerid)) {
                $res = $this->db->query($sql, array($value->siteid, $value->driverid, $value->periode, $value->customerid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_master($table, $values)
    {
        $exclude = array('siteid', 'periode', 'no_pengiriman', 'no_sales');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and no_pengiriman=? and no_sales=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->no_pengiriman) && isset($value->no_sales)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->no_pengiriman, $value->no_sales))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_detail_product($table, $values)
    {
        $exclude = array('siteid', 'periode', 'driverid', 'no_pengiriman', 'no_sales', 'productid');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and driverid=? and no_pengiriman=? and no_sales=? and productid=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->driverid) && isset($value->no_pengiriman) && isset($value->no_sales) && isset($value->productid)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->driverid, $value->no_pengiriman, $value->no_sales, $value->productid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_detail_rekap_product($table, $values)
    {
        $exclude = array('siteid', 'periode', 'driverid', 'productid');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and driverid=? and productid=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->driverid) && isset($value->productid)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->driverid, $value->productid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_serah_terima($table, $values)
    {
        $exclude = array('siteid', 'periode', 'driverid', 'no_pengiriman', 'productid');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and driverid=? and no_pengiriman=? and productid=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->driverid) && isset($value->no_pengiriman) && isset($value->productid)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->driverid, $value->no_pengiriman, $value->productid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_tagihan($table, $values)
    {
        $exclude = array('siteid', 'periode', 'no_pengiriman', 'no_sales');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and no_pengiriman=? and no_sales=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->no_pengiriman) && isset($value->no_sales)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->no_pengiriman, $value->no_sales))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            }
        }
    }

    function insert_kilometer($table, $values)
    {
        $exclude = array('siteid', 'periode', 'driverid');
        $sql = "select count(1) as total from $table where siteid=? and periode=? and driverid=?";
        foreach ($values as $value) {
            if (isset($value->siteid) && isset($value->periode) && isset($value->driverid)) {
                $res = $this->db->query($sql, array($value->siteid, $value->periode, $value->driverid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            } else {
            }
        }
    }

    function insert_biaya_jenis($table, $values)
    {
        $exclude = array('biayaid');
        $sql = "select count(1) as total from $table where biayaid=?";
        foreach ($values as $value) {
            if (isset($value->biayaid)) {
                $res = $this->db->query($sql, array($value->biayaid))->row();
                if (0 == $res->total) {
                    $this->db->insert($table, $value);
                } else {
                    $result = excludeValueForUpdate($value, $exclude);
                    $this->update_table($table, $result->value, $result->where);
                }
            } else {
            }
        }
    }

    function upload($param)
    {
        $res = new stdClass();
        $res->result = false;
        if (isset($_FILES['upload_file']) && isset($param["siteid"]) && isset($param["companyid"]) && isset($param["periode"])
            && !empty($param["siteid"]) && !empty($param["companyid"]) && !empty($param["periode"])) {
            $data = $this->uploaderfiler->upload($_FILES['upload_file'], filer_properties());
            if ($data['isComplete']) {
                $files = $data['data'];
                foreach ($files['metas'] as $key => $value) {
                    $data_file = array(
                        'file_name' => $value['name'],
                        'siteid' => $param['siteid'],
                        'companyid' => $param['companyid'],
                        'periode' => $param['periode'],
                        'status' => 0,
                        'message' => "success",
                        'created_date' => date("Y-m-d h:i:sa")
                    );
                    $this->db->insert('t_upload_task', $data_file);
                }
                $res->result = true;
            }
            if ($data['hasErrors']) {
                $errors = $data['errors'];
                $res->result = false;
                $res->message = $data['errors'];
            }
        } else {
            $res->result = false;
            $message = isset($param["siteid"]) ? "" : "siteid not set ";
            $message .= empty($param["siteid"]) ? "siteid empty " : "";
            $message .= isset($param["companyid"]) ? "" : "companyid not set ";
            $message .= empty($param["companyid"]) ? "companyid empty " : "";
            $message .= isset($param["periode"]) ? "" : "periode not set ";
            $message .= empty($param["periode"]) ? "periode empty " : "";
            $res->message = $message;
        }
        return $res;
    }


}