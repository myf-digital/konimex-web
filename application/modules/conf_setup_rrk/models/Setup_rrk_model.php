<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_rrk_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('t_sales_rrk_setup');
        if (!empty($id)) {
            $data[''] = $id;
        }

        $data_week = array();
        if(isset($data['minggu'])){ $data_week = $data['minggu']; unset($data['minggu']); }

        $data_days = array();
        if(isset($data['day'])){ $data_days = $data['day']; unset($data['day']); }
        if(count($data_days)>1){
            $days1 = $data_days[0];
            $days2 = $data_days[1];
        }else{
            $days = $data_days[0];
        }
        
        for($i=0;$i<count($data_week);$i++){
            if(count($data_days)>1){
                for($a=0;$a<count($data_days);$a++){
                    $data_array = array(
                    "siteid" => $data['siteid'],
                    "salesmanid" => $data['salesmanid'],
                    "areaid" => $data['areaid'],
                    "minggu" => $data_week[$i],
                    "day" => $data_days[$a],
                    "repeat_minggu" => $data['frvisit']
                    );
                    $execreturn = $this->db->insert('t_sales_rrk_setup', $data_array);
                }
            }else{
                    $data_array = array(
                        "siteid" => $data['siteid'],
                        "salesmanid" => $data['salesmanid'],
                        "areaid" => $data['areaid'],
                        "minggu" => $data_week[$i],
                        "day" => $data_days[0],
                        "repeat_minggu" => $data['frvisit']
                        );
                    $execreturn = $this->db->insert('t_sales_rrk_setup', $data_array);
            }
        }
        if (!$execreturn){
            return false;
        }else{
            return true;
        }

        return $this->db->insert('t_sales_rrk_setup', $data);
    }

    public function update($data)
    {
        $this->db->where('', $data['']);
        return $this->db->update('t_sales_rrk_setup', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('areaid', $data['areaid']);
        return $this->db->delete('t_sales_rrk_setup');
    }

    public function load($data)
    {
        $field = " a.siteid, GROUP_CONCAT(case when a.minggu=1 then 'Weeks 1' when a.minggu=2 then 'Weeks 2' when a.minggu=3 then 'Weeks 3' when a.minggu=4 then 'Weeks 4' end) groupminggu, 
                    GROUP_CONCAT(case when a.day=1 then 'Monday' when a.day=2 then 'Tuesday' when a.day=3 then 'Wednesday' when a.day=4 then 'Thursday' when a.day=5 then 'Friday' 
                   when a.day=6 then 'Saturday' when a.day=7 then 'Sunday' end) groupday, 
                   a.salesmanid, concat(b.nama_salesman,' - ',a.salesmanid) salesman, a.areaid, c.nama_area, a.repeat_minggu ";
        $table = " t_sales_rrk_setup a";
        $join = " left join m_sales_salesman b on a.salesmanid=b.salesmanid ";
        $join .= " left join m_area_areasite c on a.areaid=c.areaid ";
        $join .= " group by a.siteid, a.salesmanid, a.areaid order by a.minggu asc";
        return easy_pagging($data, $field, $table.$join);
    }

}
