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
        if (is_null($data["nip"]) or is_null($data["password"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_event,e.event,e.tanggal start_periode,e.end_periode,c.nip,c.nama_karyawan,c.id_satker,d.kode_satker,d.satker,e.password
						from ref_event_peserta a left join ref_group_mapping b on a.id_group=b.id_group
						left join ref_karyawan c on c.id_karyawan = b.id_karyawan left join ref_satuan_kerja d on d.id_satker=c.id_satker
						left join ref_event e on a.id_event = e.id_event
					where a.id_event in (select id_event from ref_event where now() between tanggal and end_periode) 
						and (c.nip=? or replace(c.nama_karyawan,' ','')=replace(?,' ',''))
						and  e.password in (md5(?)) ";
            $res_ss = $this->db->query($sql, array($data["nip"], $data["nip"], $data["password"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "NIP or Password Invalid!");
            }
        }
    }

    function select_event($data)
    {
        if (is_null($data["nip"]) or is_null($data["id_event"]) or is_null($data["password"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_event,e.event,e.tanggal start_periode,e.end_periode,c.nip,c.nama_karyawan,c.id_satker,d.kode_satker,d.satker,e.password
						from ref_event_peserta a left join ref_group_mapping b on a.id_group=b.id_group
						left join ref_karyawan c on c.id_karyawan = b.id_karyawan
						left join ref_satuan_kerja d on d.id_satker=c.id_satker
						left join ref_event e on a.id_event = e.id_event
					where a.id_event in (select id_event from ref_event where now() between tanggal and end_periode) 
						and (c.nip=? or replace(c.nama_karyawan,' ','')=replace(?,' ','')) and e.password = md5(?)";
            $res_ss = $this->db->query($sql, array($data["nip"], $data["nip"], $data["password"]));
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
        if (is_null($data["nip"]) or is_null($data["id_event"])) {
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
						  and d.id_event=? ;"; // (select id_event from ref_event where now() between tanggal and end_periode) and c.tanggal=?
            $res_ss = $this->db->query($sql, array($data["nip"], $data["nip"], $data["id_event"]));
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
        if (is_null($data["idrdg"]) or is_null($data["nip"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_rdg, a.nama_rdg, a.tanggal, b.id_aspek, c.id_parent, c.aspek, c.id_tipe tipe_soal, c.listjawaban, c.nourut, d.value nilai
					from 
					ref_rdg a left join ref_matrix_aspek b on a.id_matrix=b.id_matrix
					left join ref_aspek c on c.id_aspek=b.id_aspek
					left join trx_hasil_penilaian d on a.id_rdg=d.id_rdg and b.id_aspek=d.id_aspek and d.nip = ?
					where a.id_rdg=?
					order by a.id_rdg, c.nourut";
            $res_ss = $this->db->query($sql, array($data["nip"], $data["idrdg"]));
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
        if (is_null($data["idrdg"]) or is_null($data["satker"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.id_rdg, b.nama_rdg, b.tanggal, a.id_aspek, c.aspek, c.id_tipe tipe_soal, c.listjawaban, c.nourut, d.value nilai
					from 
					ref_map_rdg a left join ref_rdg b on a.id_rdg=b.id_rdg
					left join ref_aspek c on a.id_aspek=c.id_aspek
					left join trx_hasil_penilaian d on a.id_rdg=d.id_rdg and a.id_aspek=d.id_aspek and d.nip = ?
					where a.id_rdg=? 
					order by a.id_rdg, c.nourut";
            $res_ss = $this->db->query($sql, array($data["nip"], $data["idrdg"]));
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
        if (is_null($data["id_event"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker,z.value as value_avg
						from
						(
						select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
							   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
							   f.id_aspek,g.id_parent,g.aspek,avg(ifnull(cast(ifnull(h.value,0) as int),0)) as value
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
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_data_hasil_evaluasi_grafik_materi($data)
    {
        if (is_null($data["id_event"]) and $data["id_rdg"]) {
            return result(new stdClass(), 201, "Event materi not found...!");

        } else if ($data["id_event"] and $data["id_rdg"]) {
            $sql = "select z.id_event,z.event,z.tanggal,z.id_rdg,z.nama_rdg,z.id_satker,z.kode_satker,z.satker, z.id_parent, z.id_matrix, y.id_aspek, x.aspek,
						   z.value as value_avg
					from
					(
					select a.id_event,a.event,a.tanggal,a.end_periode,b.id_rdg,
						   c.nama_rdg,c.id_satker,d.kode_satker,d.satker,c.id_matrix,e.matrix_table,c.tanggal tanggal_rdg,
						   f.id_aspek,if(g.id_parent is null or g.id_parent=0,g.id_aspek,g.id_parent) id_parent,g.aspek,avg(ifnull(cast(ifnull(h.value,0) as int),0)) as value
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

            $res_ss = $this->db->query($sql, array($data["id_event"], $data["id_rdg"]));
            if (count($res_ss->result_array()) > 0) {
                $response = $res_ss->result_array();
                return result($response);
            } else {
                return result(new stdClass(), 201, "Event Materi not found!");
            }
        } else {
            return result(new stdClass(), 201, "Event Materi not found!");
        }
    }

    function load_all_event()
    {
        $sql = "select * from ref_event order by id_event desc";
        $res = $this->db->query($sql)->result_array();
        if (count($res) > 0) {
            $response = $res;
            return result($response);
        } else {
            return result(array(), 404, "Event not found!");
        }
    }

    function load_aspek($data)
    {
        $sql = "select * from ref_event_rdg where id_event=?";
        $res = $this->db->query($sql, array($data["id_event"]))->result_array();
        if (count($res) > 0) {
            $param = array();
            $flag = "";
            foreach ($res as $value) {
                array_push($param, $value['id_rdg']);
                $flag .= "?, ";
            }
            $flag = substr($flag, 0, strlen($flag) - 2);
            $sql = "select * from ref_rdg where id_rdg in($flag)";
            $res = $this->db->query($sql, $param)->result_array();
            $response = $res;
            return result($response);
        } else {
            return result(array(), 404, "Aspek(RDG) not found!");
        }
    }

    function get_headers_rekap_hasil_evaluasi_event($data)
    {
        if (is_null($data["id_event"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "
					select @rownum:=@rownum+1 as indexno,z.id_aspek,z.title,if(z.colspan<>0,z.colspan+1,z.colspan) colspan,if(z.colspan=0,2,0) rowspan from 
					(
					select null id_merix, null id_aspek, null id_parent, null nourut, 'No' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Materi' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Satker' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Waktu' title, 0 colspan
					union 
					(select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, 
							 ifnull(e.id_parent, (select count(1) from ref_aspek where id_parent=e.id_aspek)) as colspan
					from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
						 left join ref_rdg c on b.id_rdg = c.id_rdg
						 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
						 left join ref_aspek e on d.id_aspek=e.id_aspek
					where a.id_event=? and id_parent is null
					order by e.nourut asc)
					) z , (SELECT @rownum:=0) r						 
				";

            $sqlchild = "
							select @rownum:=@rownum+1 as indexno,z.title,z.colspan,0 rowspan from 
							(
							(select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, 0 as colspan
							from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
								 left join ref_rdg c on b.id_rdg = c.id_rdg
								 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
								 left join ref_aspek e on d.id_aspek=e.id_aspek
							where a.id_event=? and id_parent = ?
							order by e.nourut asc)
							union
							select  null id_matrix, null id_aspek, null id_parent, null nourut, 'Total' title, 0 as colspan
							) z , (SELECT @rownum:=0) r
						";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
			$finalarray = array();
            if (count($res_ss->result_array()) > 0) {
				
				foreach ($res_ss->result_array() as $row){
					$res_child = $this->db->query($sqlchild, array($data["id_event"],$row["id_aspek"]));
					if($row["colspan"]!=0) 
					{ $child = $res_child->result_array();}else{ $child=array(); }
					$resarray = array(
									"indexno" => $row["indexno"],
									"title" => $row["title"],
									"rowspan" => $row["rowspan"],
									"colspan" => $row["colspan"],
									"child" => $child
									);
					array_push($finalarray,$resarray);
				}
                //$response = new stdClass();
                //$response = $res_ss->result_array();
                //$response = $finalarray;
				
                //parsing to result
                return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_rows_rekap_hasil_evaluasi_event($data)
    {
        if (is_null($data["id_event"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "	select @rownum:=@rownum+1 as indexno, z.id_rdg, z.materi, z.satker, z.tanggal  from 
							(
							  select  c.id_rdg, c.nama_rdg materi, d.satker, c.tanggal
										from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
											 left join ref_rdg c on b.id_rdg = c.id_rdg
								 left join ref_satuan_kerja d on c.id_satker = d.id_satker
										where a.id_event=?
							  ) z , (SELECT @rownum:=0) r
					";

            $sqlaspek = "
							select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, if(e.listjawaban<>'#',1,0) pertanyaan
							from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
								 left join ref_rdg c on b.id_rdg = c.id_rdg
								 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
								 left join ref_aspek e on d.id_aspek=e.id_aspek
							where a.id_event=? and if(e.listjawaban<>'#',1,0)=1
							order by e.nourut asc
						";
			
			$sqlnilai = "
							select round(avg(value),2) nilai from trx_hasil_penilaian 
							where id_event=? and id_rdg=? and id_aspek=?;
						";

			$sqlnilaitotal = "
								select round(avg(value),2) nilai_total from trx_hasil_penilaian a left join ref_aspek b on a.id_aspek=b.id_aspek
								where a.id_event=? and a.id_rdg=? and b.id_parent=?;
							";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
			$finalarray = array();
			$aspekarray = array();
            if (count($res_ss->result_array()) > 0) {
				//$finalarray = $res_ss->result_array();
				foreach ($res_ss->result_array() as $row){
					$res_aspek = $this->db->query($sqlaspek, array($data["id_event"]));
					$resarray = array(
									"indexno" => $row["indexno"],
									//"id_rdg" => $row["id_rdg"],
									"materi" => $row["materi"],
									"satker" => $row["satker"],
									"tanggal" => $row["tanggal"],
									);
					if (count($res_aspek->result_array()) > 0) 
					{	
						$idparent = "";
						foreach ($res_aspek->result_array() as $rowaspek)
						{	
							if ($idparent!=$rowaspek["id_parent"] and $idparent!="")
							{
								$res_nilaitot = $this->db->query($sqlnilaitotal, array($data["id_event"],$row["id_rdg"],$idparent));
								$vnilaitotal = $res_nilaitot->row();
								$resarray["Total ".$idparent] = $vnilaitotal->nilai_total;
							}
								$res_nilai = $this->db->query($sqlnilai, array($data["id_event"],$row["id_rdg"],$rowaspek["id_aspek"]));
								$vnilai = $res_nilai->row();
								$resarray[$rowaspek["title"]] = $vnilai->nilai;
								$idparent = $rowaspek["id_parent"];
						}
					}
					array_push($finalarray,$resarray);
				}
                //$response = new stdClass();
                //$response = $res_ss->result_array();
                //$response = $finalarray;
                //parsing to result
                return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_headers_rekap_hasil_evaluasi_rdg($data)
    {
        if (is_null($data["id_event"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "
					select @rownum:=@rownum+1 as indexno,z.id_aspek,z.title,if(z.colspan<>0,z.colspan+1,z.colspan) colspan,if(z.colspan=0,2,0) rowspan from 
					(
					select null id_merix, null id_aspek, null id_parent, null nourut, 'No' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Responden' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Satker' title, 0 colspan
					union select null id_merix, null id_aspek, null id_parent, null nourut, 'Waktu' title, 0 colspan
					union 
					(select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, 
							 ifnull(e.id_parent, (select count(1) from ref_aspek where id_parent=e.id_aspek)) as colspan
					from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
						 left join ref_rdg c on b.id_rdg = c.id_rdg
						 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
						 left join ref_aspek e on d.id_aspek=e.id_aspek
					where a.id_event=? and id_parent is null
					order by e.nourut asc)
					) z , (SELECT @rownum:=0) r						 
				";

            $sqlchild = "
							select @rownum:=@rownum+1 as indexno,z.title,z.colspan,0 rowspan from 
							(
							(select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, 0 as colspan
							from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
								 left join ref_rdg c on b.id_rdg = c.id_rdg
								 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
								 left join ref_aspek e on d.id_aspek=e.id_aspek
							where a.id_event=? and id_parent = ?
							order by e.nourut asc)
							union
							select  null id_matrix, null id_aspek, null id_parent, null nourut, 'Total' title, 0 as colspan
							) z , (SELECT @rownum:=0) r
						";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
			$finalarray = array();
            if (count($res_ss->result_array()) > 0) {
				
				foreach ($res_ss->result_array() as $row){
					$res_child = $this->db->query($sqlchild, array($data["id_event"],$row["id_aspek"]));
					if($row["colspan"]!=0) 
					{ $child = $res_child->result_array();}else{ $child=array(); }
					$resarray = array(
									"indexno" => $row["indexno"],
									"title" => $row["title"],
									"rowspan" => $row["rowspan"],
									"colspan" => $row["colspan"],
									"child" => $child
									);
					array_push($finalarray,$resarray);
				}
                //$response = new stdClass();
                //$response = $res_ss->result_array();
                //$response = $finalarray;
				
                //parsing to result
                return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_rows_rekap_hasil_evaluasi_rdg($data)
    {
        if (is_null($data["id_event"]) or is_null($data["id_rdg"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "
						select @rownum:=@rownum+1 as indexno, z.id_rdg, z.materi, z.nip, z.responden, z.satker, z.tanggal  from 
						(
						  select  a.id_rdg, d.nama_rdg materi, e.nip, e.nama_karyawan responden, f.satker, d.tanggal
									from trx_hasil_penilaian a  left join ref_event b on a.id_event=b.id_event
							 left join ref_event_rdg c on b.id_event=c.id_event
										 left join ref_rdg d on a.id_rdg = d.id_rdg
							 left join ref_karyawan e on a.nip = e.nip
							 left join ref_satuan_kerja f on e.id_satker = f.id_satker
									where a.id_event=? and a.id_rdg=? group by a.id_rdg, d.nama_rdg, e.nama_karyawan, f.satker, d.tanggal
						) z , (SELECT @rownum:=0) r

					";

            $sqlaspek = "
							select  distinct c.id_matrix, d.id_aspek, e.id_parent, e.nourut, e.aspek title, if(e.listjawaban<>'#',1,0) pertanyaan
							from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
								 left join ref_rdg c on b.id_rdg = c.id_rdg
								 left join ref_matrix_aspek d on c.id_matrix = d.id_matrix
								 left join ref_aspek e on d.id_aspek=e.id_aspek
							where a.id_event=? and if(e.listjawaban<>'#',1,0)=1
							order by e.nourut asc
						";
			
			$sqlnilai = "
							select round(avg(value),2) nilai from trx_hasil_penilaian 
							where id_event=? and id_rdg=? and nip=? and id_aspek=?;
						";

			$sqlnilaitotal = "
								select round(avg(value),2) nilai_total from trx_hasil_penilaian a left join ref_aspek b on a.id_aspek=b.id_aspek
								where a.id_event=? and a.id_rdg=? and a.nip=? and b.id_parent=?;
							";

            $res_ss = $this->db->query($sql, array($data["id_event"],$data["id_rdg"]));
			$finalarray = array();
			$aspekarray = array();
            if (count($res_ss->result_array()) > 0) {
				//$finalarray = $res_ss->result_array();
				foreach ($res_ss->result_array() as $row){
					$res_aspek = $this->db->query($sqlaspek, array($data["id_event"]));
					$resarray = array(
									"indexno" => $row["indexno"],
									//"id_rdg" => $row["id_rdg"],
									"responden" => $row["responden"],
									"satker" => $row["satker"],
									"tanggal" => $row["tanggal"],
									);
					if (count($res_aspek->result_array()) > 0) 
					{	
						$idparent = "";
						foreach ($res_aspek->result_array() as $rowaspek)
						{	
							if ($idparent!=$rowaspek["id_parent"] and $idparent!="")
							{
								$res_nilaitot = $this->db->query($sqlnilaitotal, array($data["id_event"],$row["id_rdg"],$row["nip"],$idparent));
								$vnilaitotal = $res_nilaitot->row();
								$resarray["Total ".$idparent] = $vnilaitotal->nilai_total;
							}
								$res_nilai = $this->db->query($sqlnilai, array($data["id_event"],$row["id_rdg"],$row["nip"],$rowaspek["id_aspek"]));
								$vnilai = $res_nilai->row();
								$resarray[$rowaspek["title"]] = $vnilai->nilai;
								$idparent = $rowaspek["id_parent"];
						}
					}
					array_push($finalarray,$resarray);
				}
                //$response = new stdClass();
                //$response = $res_ss->result_array();
                //$response = $finalarray;
                //parsing to result
                return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_headers_rekap_saran_rdg($data)
    {
        if (is_null($data["id_event"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "
					select @rownum:=@rownum+1 as indexno,z.title, 0 colspan,0 rowspan from 
					(
					select 'No' title, 0 colspan
					union select 'Responden' title, 0 colspan
					union select 'Satker' title, 0 colspan
					union select 'Waktu' title, 0 colspan
					union select 'Saran' title, 0 colspan
					) z , (SELECT @rownum:=0) r
				";

            $res_ss = $this->db->query($sql, array($data["id_event"]));
			$finalarray = array();
            if (count($res_ss->result_array()) > 0) {
				foreach ($res_ss->result_array() as $row){
					$resarray = array(
									"indexno" => $row["indexno"],
									"title" => $row["title"],
									"rowspan" => $row["rowspan"],
									"colspan" => $row["colspan"],
									"child" => ""									
									);
					array_push($finalarray,$resarray);
				}

                return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }

    function get_rows_rekap_saran_rdg($data)
    {
        if (is_null($data["id_event"]) or is_null($data["id_rdg"])) {
            return result(new stdClass(), 201, "Event not found...!");
        } else {
            $sql = "
						select @rownum:=@rownum+1 as indexno, z.id_rdg, z.materi, z.nip, z.responden, z.satker, z.tanggal, z.saran  from 
						(
						  select  a.id_rdg, d.nama_rdg materi, e.nip, e.nama_karyawan responden, f.satker, d.tanggal, a.saran
									from trx_saran a left join ref_event b on a.id_event=b.id_event
										 left join ref_event_rdg c on b.id_event=c.id_event
										 left join ref_rdg d on a.id_rdg = d.id_rdg
										 left join ref_karyawan e on a.nip = e.nip
										 left join ref_satuan_kerja f on e.id_satker = f.id_satker
									where a.id_event=? and a.id_rdg=? group by a.id_rdg, d.nama_rdg, e.nama_karyawan, f.satker, d.tanggal, a.saran
						) z , (SELECT @rownum:=0) r

					";

            $res_ss = $this->db->query($sql, array($data["id_event"],$data["id_rdg"]));
			$finalarray = array();
            if (count($res_ss->result_array()) > 0) {
				foreach ($res_ss->result_array() as $row){
					$resarray = array(
									"indexno" => $row["indexno"],
									//"id_rdg" => $row["id_rdg"],
									"responden" => $row["responden"],
									"satker" => $row["satker"],
									"tanggal" => $row["tanggal"],
									"saran" => $row["saran"]
									);
					array_push($finalarray,$resarray);
				}
				return result($finalarray);
            } else {
                return result(new stdClass(), 201, "Event not found!");
            }
        }
    }



}