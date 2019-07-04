<?php

class Api_v1_model extends CI_Model
{
	
    function get_event_id()
    {
            $sql = "select *
					  from ref_event
					where now() between tanggal and end_periode;";
            $res_ss = $this->db->query($sql);
			if (count($res_ss->result_array()) > 0) {
                    return $res_ss->result_array()[0];
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
    }
	
    function login($data)
    {
        if (is_null($data["nip"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_event,e.event,e.tanggal start_periode,e.end_periode,c.nip,c.nama_karyawan,c.id_satker,d.kode_satker,d.satker,e.password
						from ref_event_peserta a left join ref_group_mapping b on a.id_group=b.id_group
						left join ref_karyawan c on c.id_karyawan = b.id_karyawan left join ref_satuan_kerja d on d.id_satker=c.id_satker
						left join ref_event e on a.id_event = e.id_event
					where a.id_event in (select id_event from ref_event where now() between tanggal and end_periode) 
						and (c.nip=? or replace(c.nama_karyawan,' ','')=replace(?,' ',''))";
            $res_ss = $this->db->query($sql, array($data["nip"],$data["nip"]));
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

    function select_event($data)
    {
        if (is_null($data["nip"]) or is_null($data["id_event"]) or is_null($data["password"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_event,e.event,e.tanggal start_periode,e.end_periode,c.nip,c.nama_karyawan,c.id_satker,d.kode_satker,d.satker,e.password
						from ref_event_peserta a left join ref_group_mapping b on a.id_group=b.id_group
						left join ref_karyawan c on c.id_karyawan = b.id_karyawan
						left join ref_satuan_kerja d on d.id_satker=c.id_satker
						left join ref_event e on a.id_event = e.id_event
					where a.id_event in (select id_event from ref_event where now() between tanggal and end_periode) 
						and (c.nip=? or replace(c.nama_karyawan,' ','')=replace(?,' ','')) and e.password = md5(?)";
            $res_ss = $this->db->query($sql, array($data["nip"],$data["nip"],$data["password"]));
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
        if (is_null($data["nip"]) or is_null($data["id_event"]) or is_null($data["password"])) 
		{
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.nip,a.nama_karyawan,e.id_satker,f.kode_satker,f.satker, e.id_rdg, e.nama_rdg, e.tanggal,g.id_event,g.event, 
						(select count(1) from trx_hasil_penilaian where id_rdg=e.id_rdg) as review
						from ref_karyawan a left join ref_group_mapping z on a.id_karyawan = z.id_karyawan
						left join ref_satuan_kerja b on a.id_satker=b.id_satker
						left join ref_event_peserta c on c.id_group=z.id_group
						left join ref_event_rdg d on c.id_event=d.id_event
						left join ref_rdg e on d.id_rdg=e.id_rdg
						left join ref_satuan_kerja f on e.id_satker=f.id_satker
						left join ref_event g on g.id_event = c.id_event
					where (a.nip=? or replace(a.nama_karyawan,' ','')=replace(?,' ','')) 
						  and d.id_event=? and g.password = md5(?); "; // (select id_event from ref_event where now() between tanggal and end_periode) and c.tanggal=?
            $res_ss = $this->db->query($sql, array($data["nip"],$data["nip"],$data["id_event"],$data["password"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "NIP or Password invalid !");
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
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(ifnull(h.value,0) as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(ifnull(h.value,0) as int) >= 0 and a.id_event = (select id_event from ref_event where now() between tanggal and end_periode)
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
							   f.id_aspek,g.id_parent,g.aspek,sum(ifnull(cast(ifnull(h.value,0) as int),0)) as value
						 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
							  left join ref_rdg c on b.id_rdg=c.id_rdg
							  left join ref_satuan_kerja d on c.id_satker=d.id_satker
							  left join ref_matrix_table e on c.id_matrix=e.id_matrix
							  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
							  left join ref_aspek g on f.id_aspek=g.id_aspek
							  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
						 where cast(ifnull(h.value,0) as int) >= 0 and a.id_event=?
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
        if (is_null($data["id_event"]) and $data["id_rdg"])
		{
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker, z.id_parent, z.id_matrix, y.id_aspek, x.aspek,
						   avg(z.value) as value_avg
					from
					(
					select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
						   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
						   f.id_aspek,ifnull(g.id_parent,g.id_aspek) id_parent,g.aspek,sum(ifnull(cast(ifnull(h.value,0) as int),0)) as value
					 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
						  left join ref_rdg c on b.id_rdg=c.id_rdg
						  left join ref_satuan_kerja d on c.id_satker=d.id_satker
						  left join ref_matrix_table e on c.id_matrix=e.id_matrix
						  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
						  left join ref_aspek g on f.id_aspek=g.id_aspek
						  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
					 where cast(ifnull(h.value,0) as int) >= 0 and a.id_event = (select id_event from ref_event where now() between tanggal and end_periode) 
							and c.id_rdg=?
					 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
						   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
						   f.id_aspek,g.id_parent,g.aspek
					 order by g.nourut
					 ) z join ref_matrix_grafik y on z.id_matrix=y.id_matrix  and z.id_parent=y.id_aspek
					 left join ref_aspek x on z.id_parent=x.id_aspek
					 group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker, z.id_parent, z.id_matrix, y.id_aspek
					;";

            $res_ss = $this->db->query($sql,array($data["id_rdg"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Grafik Materi not found!");				 
            }
        }else if ($data["id_event"] and $data["id_rdg"]) {
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker, z.id_parent, z.id_matrix, y.id_aspek, x.aspek,
						   avg(z.value) as value_avg
					from
					(
					select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
						   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
						   f.id_aspek,ifnull(g.id_parent,g.id_aspek) id_parent,g.aspek,sum(ifnull(cast(ifnull(h.value,0) as int),0)) as value
					 from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
						  left join ref_rdg c on b.id_rdg=c.id_rdg
						  left join ref_satuan_kerja d on c.id_satker=d.id_satker
						  left join ref_matrix_table e on c.id_matrix=e.id_matrix
						  left join ref_matrix_aspek f on c.id_matrix=f.id_matrix
						  left join ref_aspek g on f.id_aspek=g.id_aspek
						  left join trx_hasil_penilaian h on b.id_event=h.id_event and c.id_rdg=h.id_rdg and g.id_aspek=h.id_aspek
					 where cast(ifnull(h.value,0) as int) >= 0 and a.id_event=? and c.id_rdg=?
					 group by a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
						   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal,
						   f.id_aspek,g.id_parent,g.aspek
					 order by g.nourut
					 ) z join ref_matrix_grafik y on z.id_matrix=y.id_matrix  and z.id_parent=y.id_aspek
					 left join ref_aspek x on z.id_parent=x.id_aspek
					 group by z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker, z.id_parent, z.id_matrix, y.id_aspek
					;";

            $res_ss = $this->db->query($sql, array($data["id_event"],$data["id_rdg"]));
			if (count($res_ss->result_array()) > 0) {
                    $response = new stdClass();
                    $response = $res_ss->result_array();
                    //parsing to result
                    return result($response);
            } else {
                return result(new stdClass(), 201, "Grafik Materi not found!");				 
            }
		}else{
			return result(new stdClass(), 201, "Grafik Materi not found!");
		}
    }

}