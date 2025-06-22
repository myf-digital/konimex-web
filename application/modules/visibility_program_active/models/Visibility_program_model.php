<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visibility_program_model extends CI_Model
{

    public function create($data)
    {
        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $arrclassid = array();
        if(isset($data['classid'])){ $arrclassid= $data['classid']; unset($data['classid']); }

        if(isset($data['regionalid'])){ $varregionalid= $data['regionalid']; unset($data['regionalid']); }
        if(isset($data['areaid'])){ $varareaid= $data['areaid']; unset($data['areaid']); }

        $arrsubareaid = array();
        if(isset($data['subareaid'])){ $arrsubareaid= $data['subareaid']; unset($data['subareaid']); }
        
        $execquery=$this->db->insert('visibility_scheme_program', $data);
        $data['id_program']=$this->db->insert_id();

        for($i=0;$i<count($arrclassid);$i++){
            $data_array = array(
                "id_program" => $data['id_program'],
                "classid" => $arrclassid[$i]
                );
            $this->db->insert('visibility_mapping_class', $data_array);
        }

        for($i=0;$i<count($arrsubareaid);$i++){
            $data_array = array(
                "id_program" => $data['id_program'],
                "regionalid" => $varregionalid,
                "areaid" => $varareaid,
                "subareaid" => $arrsubareaid[$i]
                );
            $this->db->insert('visibility_mapping_area', $data_array);
        }

        return $execquery;
    }

    public function update($data)
    {
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $this->db->where('id_program', $data['id_program']);
        $this->db->delete('visibility_mapping_class');

        $this->db->where('id_program', $data['id_program']);
        $this->db->delete('visibility_mapping_area');

        $arrclassid = array();
        if(isset($data['classid'])){ $arrclassid= $data['classid']; unset($data['classid']); }

        if(isset($data['regionalid'])){ $varregionalid= $data['regionalid']; unset($data['regionalid']); }
        if(isset($data['areaid'])){ $varareaid= $data['areaid']; unset($data['areaid']); }

        $arrsubareaid = array();
        if(isset($data['subareaid'])){ $arrsubareaid= $data['subareaid']; unset($data['subareaid']); }

        for($i=0;$i<count($arrclassid);$i++){
            $data_array = array(
                "id_program" => $data['id_program'],
                "classid" => $arrclassid[$i]
                );
            $this->db->insert('visibility_mapping_class', $data_array);
        }

        for($i=0;$i<count($arrsubareaid);$i++){
            $data_array = array(
                "id_program" => $data['id_program'],
                "regionalid" => $varregionalid,
                "areaid" => $varareaid,
                "subareaid" => $arrsubareaid[$i]
                );
            $this->db->insert('visibility_mapping_area', $data_array);
        }

        $this->db->where('id_program', $data['id_program']);
        return $this->db->update('visibility_scheme_program', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_program', $data['id_program']);
        $this->db->delete('visibility_mapping_class');

        $this->db->where('id_program', $data['id_program']);
        $this->db->delete('visibility_mapping_area');
        
        $this->db->where('id_program', $data['id_program']);
        return $this->db->delete('visibility_scheme_program');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( 
                    select a.id_program, a.program_name, a.description, a.start_period, a.end_period, 
                            a.qty_submit_photo, GROUP_CONCAT(DISTINCT b.classid) account, GROUP_CONCAT(DISTINCT c.nama_class) as account_name,
                            d.regionalid, e.nama_regional, d.areaid, e.nama_area, GROUP_CONCAT(DISTINCT d.subareaid) as subareaid,GROUP_CONCAT(DISTINCT e.city) as city 
                    from visibility_scheme_program a 
                    left join visibility_mapping_class b on b.id_program=a.id_program
                    left join m_customer_class c on c.classid = b.classid 
                    left join visibility_mapping_area d on d.id_program=a.id_program
                    left join v_mapping_area e on e.subareaid=d.subareaid
                    group by a.program_name, a.description, a.start_period, a.end_period, a.qty_submit_photo
                    order by a.id_program desc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

}
