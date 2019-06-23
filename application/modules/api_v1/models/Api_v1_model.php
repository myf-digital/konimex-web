<?php

class Api_v1_model extends CI_Model
{
	
    function get_event_id()
    {
            $sql = "select *
					  from ref_event
					where publish=1;";
            $res_ss = $this->db->query($sql);
			if (count($res_ss->result_array()) > 0) {
                    return $res_ss->result_array()[0];
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
    }
	
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
                    $response = $res_ss->result_array()[0];
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
					  left join ref_rdg c on c.id_satker=a.id_satker
					  left join ref_event_rdg d on c.id_rdg=d.id_rdg
					where a.nip=? and d.id_event=1"; // and c.tanggal=?
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
            $sql = "select a.id_rdg, a.nama_rdg, a.tanggal, b.id_aspek, c.id_parent, c.aspek, c.id_tipe tipe_soal, c.listjawaban, c.nourut, d.value nilai
					from 
					ref_rdg a left join ref_matrix_aspek b on a.id_matrix=b.id_matrix
					left join ref_aspek c on c.id_aspek=b.id_aspek
					left join trx_hasil_penilaian d on a.id_rdg=d.id_rdg and b.id_aspek=d.id_aspek and d.nip = ?
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

    function get_data_hasil_evaluasi_grafik_event($data)
    {
        if (is_null($data["id_event"]))
		{
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker,avg(z.value) as value_avg
						from
						(
						select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(h.value as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(h.value as int) > 0 and a.publish = 1
						 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
							   f.id_aspek,g.id_parent,g.aspek
						 order by g.nourut
						 ) z group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker
						 ;";

            $res_ss = $this->db->query($sql);
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Materi not found!");				 
            }
        }else {
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker,avg(z.value) as value_avg
						from
						(
						select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(h.value as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(h.value as int) > 0 and a.id_event=?
						 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
							   f.id_aspek,g.id_parent,g.aspek
						 order by g.nourut
						 ) z group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker
						 ;";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
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

    function get_data_hasil_evaluasi_grafik_materi($data)
    {
        if (is_null($data["id_event"]))
		{
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker,avg(z.value) as value_avg
						from
						(
						select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(h.value as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(h.value as int) > 0 and a.publish = 1
						 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
							   f.id_aspek,g.id_parent,g.aspek
						 order by g.nourut
						 ) z group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker
						 ;";

            $res_ss = $this->db->query($sql);
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Materi not found!");				 
            }
        }else {
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker,avg(z.value) as value_avg
						from
						(
						select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(h.value as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(h.value as int) > 0 and a.id_event=?
						 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
							   f.id_aspek,g.id_parent,g.aspek
						 order by g.nourut
						 ) z group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker
						 ;";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
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

}