<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Request_new_outlet_model extends CI_Model
{
    function seo_friendly_url($string){
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
        return strtolower(trim($string, '-'));
    }
    
    function seo_url($str){
        $string = strtolower($str);
        $string = preg_replace('/[^A-Za-z0-9\ ]/','', $string);
        $string = preg_replace('!\s+!', ' ', $string);
        $string = str_replace(' ','-',$string);
        return trim($string,'-');
    }
    
    function to_prety_url($str){
        if($str !== mb_convert_encoding( mb_convert_encoding($str, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32') )
            $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
        $str = htmlentities($str, ENT_NOQUOTES, 'UTF-8');
        $str = preg_replace('`&([a-z]{1,2})(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig);`i', '\1', $str);
        $str = html_entity_decode($str, ENT_NOQUOTES, 'UTF-8');
        $str = preg_replace(array('`[^a-z0-9]`i','`[-]+`'), '-', $str);
        $str = strtolower( trim($str, '-') );
        return $str;
    }

    function str_valid($str){
        $str = str_replace("'","`",str_replace('"','`',trim($str)));
        $str = strtoupper( trim($str) );
        return $str;
    }

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        unset($data["customerid_m"]);
        unset($data["siteid"]);
        $data["created_by"] = $data["usersession"];

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);
        $sql = "select used+1 customerid_new from app_table_sequence where id=5";
        $newidcust = $this->db->query($sql)->row();
        $data['customerid'] =  $newidcust->customerid_new;
        //update sequence
        $this->db->query("update app_table_sequence set used=".$data['customerid']." where id=5");
        return $this->db->insert('m_customer', $data);
    }

    public function get_customer_ob($customerid)
    {
        $this->db->select('*');
		$this->db->from('m_customer_ob');
        $this->db->where('customerid', $customerid);
        return $this->db->get()->result();
    }

    public function update($data)
    {
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);
        
        $data["kode_outlet"] = $this->str_valid($data["kode_outlet"]);
        $data["nama_customer"] = $this->str_valid($data["nama_customer"]);
        $data["alamat"] = $this->str_valid($data["alamat"]);

        $datacustid = $data['customerid'];
        $datacustidm = $data['customerid_m'];

        $sql = "select used+1 customerid_new from app_table_sequence where id=5";
        $newidcust = $this->db->query($sql)->row();
        $data['customerid'] =  $newidcust->customerid_new;
        //update sequence
        $this->db->query("update app_table_sequence set used=".$data['customerid']." where id=5");

        $data['customerid_m'] = $datacustidm;

        $this->db->where('customerid_m', $datacustidm);
        $this->db->where('customerid', $datacustid);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->update('m_customer', $data); 

        $data_custob = array(
            "customerid" => $data['customerid'],
            "salesmanid" => $data['salesmanid'],
            "created_date" =>  $datetime->datetime,
            "created_by" => $data["modified_by"]
            );
        $this->db->insert('m_customer_ob', $data_custob);

        // notif onesignal
        if (count($data_custob) > 0) {
            $x_players = get_x_player([$data_custob['salesmanid']]);
            if (count($x_players) > 0) {
                foreach ($x_players as $xp) {
                    if (isset($xp->account_id)) {
                        send_onesignal_api([
                            'player_ids' => $xp->player_id,
                            'external_ids' => $xp->account_id,
                            'title' => 'Approve Outlet',
                            'message' => 'Outlet ' . ($data['kode_outlet'] ? '(' . $data['kode_outlet'] : '') . ($data['nama_customer'] ? ' ' . $data['nama_customer'] : '') . ') berhasil di Approve' . ($data['modified_by'] ? ' (' . $data['modified_by'] . ')' : ''),
                            'data' => array_merge(['type' => 'Approve Outlet'], [
                                'siteid' => $data['siteid'] ?? '',
                                'kode_outlet' => $data['kode_outlet'] ?? '',
                                'nama_customer' => $data['nama_customer'] ?? '',
                                'salesmanid' => $data['salesmanid'] ?? '',
                            ]),
                            'url' => '/ref_customer',
                        ]);
                    }
                }
            }
        }
    }

    public function delete($data)
    {
        $this->db->where('customerid_m', $data['customerid_m']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('m_customer');
    }

    public function load($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $field = "a.* ";
        $table = " (
            select 
                a.*, 
                b.nama_regional, 
                c.nama_area, 
                e.nama_class as nama_account, 
                f.aktif_week, 
                g.nama_salesman gff_name, 
                g.tipe_sales position
            from m_customer a 
            left join m_area_regional b on a.regionalid=b.regionalid
            left join m_area_areasite c on a.areaid = c.areaid
            left join m_customer_class e on a.classid = e.classid
            left join m_setup_site f on a.siteid=f.siteid
            left join m_sales_salesman g on a.salesmanid=g.salesmanid
            where a.customerid = ''
            ".$strquery."
            ) a";
        return easy_pagging($data, $field, $table);
    }

}
