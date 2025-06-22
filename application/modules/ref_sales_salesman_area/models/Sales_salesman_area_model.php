<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_area_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_sales_salesman_area');
        if (!empty($id)) {
            $data['areaid'] = $id;
        }

        $data_area = array();
        if(isset($data['areaid'])){ $data_area = $data['areaid']; unset($data['areaid']); }
        for($i=0;$i<count($data_area);$i++){
            $data_array = array(
                "siteid" => $data['siteid'],
                "areaid" => $data_area[$i],
                "salesmanid" => $data['salesmanid']
            );
            $execreturn = $this->db->insert('m_sales_salesman_area', $data_array);
        }
        if (!$execreturn){
            return false;
        }else{
            return true;
        }
        //return $this->db->insert('m_sales_salesman_area', $data_array);
    }

    public function update($data)
    {
		$siteid = $data["siteid"];
		$salesmanid = $data["salesmanid"];
		if (isset($data["areaid"])){

			$this->db->where("siteid", $siteid);
			$this->db->where("salesmanid", $salesmanid);
			$this->db->delete("m_sales_salesman_area");

			$data_area = array();
			$data_area = $data['areaid'];
            unset($data["areaid"]);

			for($i=0;$i<count($data_area);$i++){
				$data_array = array(
                    "siteid" => $siteid,
                    "salesmanid" => $salesmanid,
                    "areaid" => $data_area[$i]
                );
                $execreturn = $this->db->insert('m_sales_salesman_area', $data_array);
			}
		}
        if (!$execreturn){
            return false;
        }else{
            return true;
        }
        //return $this->db->insert("m_sales_salesman_area", $data_array);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('m_sales_salesman_area');
    }

    public function load($data)
    {
        $field = " a.areaid, a.siteid, a.salesmanid, concat(b.nama_salesman,' - ',b.salesmanid) salesman, c.nama_area, GROUP_CONCAT(a.areaid) group_areaid,  GROUP_CONCAT(c.nama_area) group_areaname ";
        $table = " m_sales_salesman_area a ";
        $join = " left join m_sales_salesman b on a.salesmanid=b.salesmanid 
                  left join m_area_areasite c on c.siteid = a.siteid and c.areaid=a.areaid
                  group by a.siteid, a.salesmanid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
