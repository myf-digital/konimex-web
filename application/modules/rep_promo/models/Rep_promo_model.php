<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_promo_model extends CI_Model
{

    /*public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('mapping_promo_active');
        if (!empty($id)) {
            $data['idpromo'] = $id;
        }
        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        return $this->db->insert('mapping_promo_active', $data);
    }*/

    public function update($data)
    {
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);
        $this->db->where('idpromo', $data['idpromo']);
        return $this->db->update('mapping_promo_active', $data);
    }

    public function delete($data)
    {
        $this->db->where('idpromo', $data['idpromo']);
        return $this->db->delete('mapping_promo_active');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_promo($data)
    {
        $field = " a.* ";
        $table = " ( select promo, GROUP_CONCAT(a.idpromo) as idpromo, 
                     case when promo='Promo GSK' then '' else concat('(',DATE_FORMAT(a.start_periode,'%d-%m-%Y'),' s/d ',DATE_FORMAT(a.end_periode,'%d-%m-%Y'),')') end as periode  from 
                     mapping_promo_active a left join m_customer_class b on a.classid=b.classid
                     where a.classid = '".$data['classid']."' and (a.tipepromo = '".$data['tipepromo']."' or a.tipepromo is null or a.tipepromo = '') 
					 and a.end_periode >= DATE_FORMAT('".$data['start']."','%Y-%m-%d')
                     group by promo
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_account($data)
    {
        $field = " a.* ";
        $table = " ( select classid, nama_class from m_customer_class order by nama_class asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_regional($data)
    {
        $field = " a.* ";
        $table = " ( 
					 select regionalid,nama_regional,status from m_area_regional where status='1' order by nama_regional asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_area($data)
    {
        $field = " a.* ";
        $table = " ( select areaid,nama_area from m_area_areasite where regionalid='".$data['regionalid']."' order by nama_area asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_city($data)
    {
        $field = " a.* ";
        $table = " ( select subareaid,nama_area from m_area_subarea where regionalid='".$data['regionalid']."' order by nama_area asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function load_promo_old($data)
    {
        $field = " a.* ";
        $table = " ( select a.*,b.nama_class from 
                     mapping_promo_active a left join m_customer_class b on a.classid=b.classid
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

}
