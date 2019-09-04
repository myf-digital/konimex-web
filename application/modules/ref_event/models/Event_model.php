<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('ref_event');
        if (!empty($id)) {
            $data['id_event'] = $id;
        }
			$data_rdg = array();
			$data_peserta = array();
			if(isset($data['id_rdg'])){ $data_rdg = $data['id_rdg']; unset($data['id_rdg']); }
			if(isset($data['id_group'])){ $data_peserta = $data['id_group']; unset($data['id_group']); }
			$data['password'] = md5($data['password']);
			$this->db->insert('ref_event', $data);
			$idevent = $this->db->insert_id();
			for($i=0;$i<count($data_rdg);$i++){
				$data_array = array(
					"id_event" => (int) $idevent,
					"id_rdg" => (int) $data_rdg[$i],
					"nourut" => $i
				);
				$this->db->insert("ref_event_rdg", $data_array);
			}

			for($i=0;$i<count($data_peserta);$i++){
				$data_array = array(
					"id_event" => (int) $idevent,
					"id_group" => (int) $data_peserta[$i]
				);
				$this->db->insert("ref_event_peserta", $data_array);
			}
        
			if(!$idevent){
				return false;
			}else{
				return true;
			}
    }

    public function update($data)
    {
		$idevent = $data["id_event"];
		if (isset($data["id_rdg"])){

			$data_rdg = array();
			$data_rdg = $data['id_rdg'];
			unset($data["id_rdg"]);
			$this->db->where("id_event", $idevent);
			$this->db->delete("ref_event_rdg");
			
			for($i=0;$i<count($data_rdg);$i++){
				$data_array = array(
					"id_event" => (int) $idevent,
					"id_rdg" => (int) $data_rdg[$i],
					"nourut" => $i
				);
				$this->db->insert("ref_event_rdg", $data_array);
			}
		}
		
		if (isset($data["id_group"])){

			$data_peserta = array();
			$data_peserta = $data['id_group'];
			unset($data["id_group"]);
			$this->db->where("id_event", $idevent);
			$this->db->delete("ref_event_peserta");
			
			for($i=0;$i<count($data_peserta);$i++){
				$data_array = array(
					"id_event" => (int) $idevent,
					"id_group" => (int) $data_peserta[$i]
				);
				$this->db->insert("ref_event_peserta", $data_array);
			}
		}

		if($data['password']=="")
		{
			unset($data["password"]);
		}else {
			$data['password'] = md5($data['password']);
		}
        $this->db->where('id_event', $data['id_event']);
        return $this->db->update('ref_event', $data);
    }

    public function update_publish($data)
    {
		$idevent = $data["id_event"];
		if (isset($data["id_rdg_publish"])){
			$data_rdg = array();
			$data_rdg = $data['id_rdg_publish'];
			unset($data["id_rdg_publish"]);
			$data_update["publish"]=1;
			for($i=0;$i<count($data_rdg);$i++){
				$this->db->where("id_event", $idevent);
				$this->db->where("id_rdg", $data_rdg[$i]);
				$this->db->update("ref_event_rdg",$data_update);				
			}
		}
		
        return true;
    }

    public function delete($data)
    {
        $this->db->where('id_event', $data['id_event']);
        $this->db->delete('ref_event_rdg');

        $this->db->where('id_event', $data['id_event']);
        $this->db->delete('ref_event_peserta');
        
		$this->db->where('id_event', $data['id_event']);
        return $this->db->delete('ref_event');
    }

    public function load($data)
    {
        $field = " z.id_event, z.event, z.tanggal, z.start_reformat_tanggal, z.end_periode, z.end_reformat_tanggal,z.group_rdg,GROUP_CONCAT(y.id_group) group_peserta, z.group_rdg_publish ";
        $table = " (select a.id_event, a.event, a.tanggal, date_format(a.tanggal, '%d %M %Y') start_reformat_tanggal, 
						   a.end_periode, date_format(a.end_periode, '%d %M %Y') end_reformat_tanggal,
						   GROUP_CONCAT(distinct(b.id_rdg)) group_rdg, GROUP_CONCAT(distinct(c.id_rdg)) group_rdg_publish
					from ref_event a left join ref_event_rdg b on a.id_event=b.id_event
						 left join ref_event_rdg c on a.id_event=c.id_event and c.publish=1
					group by a.id_event, a.event, a.tanggal, a.end_periode) z
					left join ref_event_peserta y on z.id_event = y.id_event
					group by z.id_event, z.event, z.tanggal, z.start_reformat_tanggal, z.end_periode, z.end_reformat_tanggal,z.group_rdg,z.group_rdg_publish ";
        return easy_pagging($data, $field, $table);
    }

    public function load_mapping_rdg($data)
    {
		$field = "a.* ";
        $table = "(select a.*,  date_format(a.tanggal, '%d %M %Y') reformat_tanggal,
				  (SELECT matrix_table FROM ref_matrix_table where id_matrix=a.id_matrix) matrix_table,
				  (SELECT satker FROM ref_satuan_kerja where id_satker=a.id_satker) satker ";
        $table .= " from ref_rdg a) as a";
		//join ref_event_rdg b on a.id_rdg = b.id_rdg and b.id_event = ".$data['id_event']."
        return easy_pagging($data, $field, $table);
    }

}
