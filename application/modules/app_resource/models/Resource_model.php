<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resource_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_resource');
        if (!empty($id)) {
            $data['resource_id'] = $id;
        }
        $data["password"] = md5($data["password"]);

        $data_subarea = array();
        $data_area = array();
        $data_regionalid = array();

        if(isset($data['subareaid'])){ $data_subarea = $data['subareaid']; unset($data['subareaid']); }
        if(isset($data['areaid'])){ $data_area = $data['areaid']; unset($data['areaid']); }
        if(isset($data['regionalid'])){ $data_regionalid = $data['regionalid']; unset($data['regionalid']); }

        $this->db->insert('app_resource', $data);
        $idresource = $this->db->insert_id();
        
        if($idresource>0){
            if (count($data_subarea)>0)
            {
                for($i=0;$i<count($data_subarea);$i++){
                    $sqlinsert = "insert into app_restrict_location(resource_id,regionalid,areaid,subareaid)
                                    select $idresource,regionalid,areaid,subareaid from m_area_subarea where subareaid = ".$data_subarea[$i].";";
                    $execinst = $this->db->query($sqlinsert);
                }
                if (!$execinst){return false;}else{return true;}
            }else if(count($data_area)>0)
                {
                    for($i=0;$i<count($data_area);$i++){
                        $sqlinsert = "insert into app_restrict_location(resource_id,regionalid,areaid)
                        select $idresource,regionalid,areaid from m_area_areasite where areaid = ".$data_area[$i].";";
                        $execinst = $this->db->query($sqlinsert);
                    }
                    if (!$execinst){return false;}else{return true;}
            }else if(count($data_regionalid)>0){
                for($i=0;$i<count($data_regionalid);$i++){
                    $data_array = array(
                        "resource_id" => $idresource,
                        "regionalid" => $data_regionalid[$i]
                        );
                    $execinst = $this->db->insert('app_restrict_location', $data_array);
                }
                if (!$execinst){return false;}else{return true;}
            }else{
                return true;
            }
    
        }else{
            return false;
        }
    }

    public function update($data)
    {

        $data_subarea = array();
        $data_area = array();
        $data_regionalid = array();
        if(isset($data['subareaid'])){ $data_subarea = $data['subareaid']; unset($data['subareaid']); }
        if(isset($data['areaid'])){ $data_area = $data['areaid']; unset($data['areaid']); }
        if(isset($data['regionalid'])){ $data_regionalid = $data['regionalid']; unset($data['regionalid']); }

        $this->db->where('resource_id', $data['resource_id']);
        $this->db->update('app_resource', $data);
        
        $idresource = $data['resource_id'];

        if($idresource>0){
            if (count($data_subarea)>0)
            {
                $this->db->where('resource_id', $data['resource_id']);
                $this->db->delete('app_restrict_location');
                for($i=0;$i<count($data_subarea);$i++){
                    $sqlinsert = "insert into app_restrict_location(resource_id,regionalid,areaid,subareaid)
                                    select $idresource,regionalid,areaid,subareaid from m_area_subarea where subareaid = ".$data_subarea[$i].";";
                    $execinst = $this->db->query($sqlinsert);
                }
                if (!$execinst){return false;}else{return true;}
            }else if(count($data_area)>0)
                {
                    $this->db->where('resource_id', $data['resource_id']);
                    $this->db->delete('app_restrict_location');
                    for($i=0;$i<count($data_area);$i++){
                        $sqlinsert = "insert into app_restrict_location(resource_id,regionalid,areaid)
                        select $idresource,regionalid,areaid from m_area_areasite where areaid = ".$data_area[$i].";";
                        $execinst = $this->db->query($sqlinsert);
                    }
                    if (!$execinst){return false;}else{return true;}
            }else if(count($data_regionalid)>0){
                $this->db->where('resource_id', $data['resource_id']);
                $this->db->delete('app_restrict_location');
                for($i=0;$i<count($data_regionalid);$i++){
                    $data_array = array(
                        "resource_id" => $idresource,
                        "regionalid" => $data_regionalid[$i]
                        );
                    $execinst = $this->db->insert('app_restrict_location', $data_array);
                }
                if (!$execinst){return false;}else{return true;}
            }else{
                return true;
            }
    
        }else{
            return false;
        }
        
    }

    public function delete($data)
    {
        $this->db->where('resource_id', $data['resource_id']);
        $this->db->delete('app_restrict_location');
        $this->db->where('resource_id', $data['resource_id']);
        return $this->db->delete('app_resource');
    }

    public function load($data)
    {
		$field = " a.* ";
        $table = " (select a.resource_id,a.role_id,a.nip,a.name,a.email,a.telepon,a.username,a.password,a.type,a.status,a.idjabatan, b.role_name, c.jabatan, 
							GROUP_CONCAT(distinct(d.regionalid)) regional, GROUP_CONCAT(distinct(e.nama_regional)) nama_regional,
							GROUP_CONCAT(distinct(d.areaid)) area, GROUP_CONCAT(distinct(f.nama_area)) nama_area, 
							GROUP_CONCAT(d.subareaid) subarea, GROUP_CONCAT(g.nama_area) city
					from app_resource a left join app_role b on a.role_id = b.role_id left join ref_jabatan c on a.idjabatan=c.idjabatan
					left join app_restrict_location d on a.resource_id = d.resource_id 
					left join m_area_regional e on d.regionalid = e.regionalid
					left join m_area_areasite f on d.areaid = f.areaid 
					left join m_area_subarea g on d.subareaid = g.subareaid 
					group by a.resource_id,a.role_id,a.nip,a.name,a.email,a.telepon,a.username,a.password,a.type,a.status,a.idjabatan, b.role_name, c.jabatan 
					) a";
        return easy_pagging($data, $field, $table);
    }

	function get_area($data)
    {

        if (is_null($data["regionalid"]) or $data["regionalid"]==null) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
                    from m_area_areasite a 
                    where a.regionalid in ?
                    order by a.nama_area asc
                    ";
            $res_ss = $this->db->query($sql, array($data["regionalid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Data Invalid!");
            }
        }

    }

	function get_subarea($data)
    {
        if (is_null($data["areaid"]) or $data["areaid"]==null) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from m_area_subarea a 
					where a.areaid in ?
						order by a.nama_area asc
						";
            $res_ss = $this->db->query($sql, array($data["areaid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Data Invalid!");
            }
        }
    }


    function cekusername($user) {
		
		$this->db->select("username");
		$this->db->from("app_resource");
		$this->db->where( "username", $user);
		$num = $this->db->get()->num_rows();		
		return $num;
	}

}
