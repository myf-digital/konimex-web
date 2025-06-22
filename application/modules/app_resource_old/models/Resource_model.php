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
        $data_area = array();
        $data_regionalid = "";
        if(isset($data['areaid'])){ $data_area = $data['areaid']; unset($data['areaid']); }
        if(isset($data['regionalid'])){ $data_regionalid = $data['regionalid']; unset($data['regionalid']); }
        $this->db->insert('app_resource', $data);
        $idresource = $this->db->insert_id();
        
        if($idresource>0){
            if ($data_regionalid!="")
            {
                if(count($data_area)>0)
                {
                    for($i=0;$i<count($data_area);$i++){
                        $data_array = array(
                            "resource_id" => $idresource,
                            "regionalid" => $data_regionalid,
                            "areaid" => $data_area[$i]
                            );
                        $execreturn = $this->db->insert('app_restrict_location', $data_array);
                    }
                }else{
                    $data_array = array(
                        "resource_id" => $idresource,
                        "regionalid" => $data_regionalid,
                        "areaid" => ""
                        );
                    $execreturn = $this->db->insert('app_restrict_location', $data_array);
        
                }
    
                if (!$execreturn){
                    return false;
                }else{
                    return true;
                }
            
            }else{
                return true;
            }
        }else{
            return false;
        }


        //return $this->db->insert('app_resource', $data);
    }

    public function update($data)
    {

        $data_area = array();
        $data_regionalid = "";
        if(isset($data['areaid'])){ $data_area = $data['areaid']; unset($data['areaid']); }
        if(isset($data['regionalid'])){ $data_regionalid = $data['regionalid']; unset($data['regionalid']); }

        $this->db->where('resource_id', $data['resource_id']);
        $this->db->update('app_resource', $data);
        
        $idresource = $data['resource_id'];
        
        if($idresource>0){
            if ($data_regionalid!="")
            {
                if(count($data_area)>0)
                {
                    $this->db->where('resource_id', $data['resource_id']);
                    $this->db->delete('app_restrict_location');

                    for($i=0;$i<count($data_area);$i++){
                        $data_array = array(
                            "resource_id" => $idresource,
                            "regionalid" => $data_regionalid,
                            "areaid" => $data_area[$i]
                            );
                        $execreturn = $this->db->insert('app_restrict_location', $data_array);
                    }
                }else{

                    $this->db->where('resource_id', $data['resource_id']);
                    $this->db->delete('app_restrict_location');

                    $data_array = array(
                        "resource_id" => $idresource,
                        "regionalid" => $data_regionalid,
                        "areaid" => ""
                        );
                    $execreturn = $this->db->insert('app_restrict_location', $data_array);
        
                }
    
                if (!$execreturn){
                    return false;
                }else{
                    return true;
                }
            
            }else{
                return true;
            }
        }
    }

    public function delete($data)
    {
        $this->db->where('resource_id', $data['resource_id']);
        return $this->db->delete('app_resource');
    }

    public function load($data)
    {
        $field = "a.*, b.role_name, c.jabatan";
        $table = 'app_resource a';
        $joins = ' left join app_role b on a.role_id = b.role_id left join ref_jabatan c on a.idjabatan=c.idjabatan';
        return easy_pagging($data, $field, $table.$joins);
    }

}
