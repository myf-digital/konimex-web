<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_sales_salesman');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }

        $data_array_category = array(
            "categoryid" => "11",
            "salesmanid" => $data['salesmanid'],
            "nama_category" => "CATEGORY"." - ".$data['salesmanid']
        );
        $this->db->insert('m_sales_salesman_category', $data_array_category);

        $data['categoryid']="11";
        $data['password'] = md5($data['password']);
        return $this->db->insert('m_sales_salesman', $data);
    }

    public function update($data)
    {
        $data['password'] = md5($data['password']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->update('m_sales_salesman', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_salesman_category');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('m_sales_salesman');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " (
            select
                a.*,
                b.nama_regional,
                c.nama_area,
                d.nama_area as city,
                case when a.aktif = 1 then 'Active' when a.aktif = 0 then 'Not Active' end aktifstatus,
                mss.nama_salesman as supervisor
            from m_sales_salesman a 
            left join m_area_regional b on a.regionalid=b.regionalid
            left join m_area_areasite c on a.areaid=c.areaid
            left join m_area_subarea d on a.subareaid=d.subareaid
            left join m_sales_salesman mss on a.supervisorid=mss.salesmanid
        ) a";
        return easy_pagging($data, $field, $table);
    }

    function cekusergff($usergff) {
		
		$this->db->select("salesmanid");
		$this->db->from("m_sales_salesman");
		$this->db->where( "salesmanid", $usergff);
		$num = $this->db->get()->num_rows();		
		return $num;
	}

    function load_salesman($data)
    {
        $data = $this->db->query("
            SELECT 
                a.salesmanid as id,
                a.salesmanid,
                a.nama_salesman,
                a.tipe_sales,
                a.jabatan,
                a.kpp_area,
                COALESCE(NULLIF(a.supervisorid, 0), '') as pid,
                b.nama_regional,
                c.nama_area,
                d.nama_area as nama_subarea,
                'https://cdn-icons-png.flaticon.com/512/149/149071.png' as img
            FROM m_sales_salesman a
            LEFT JOIN m_area_regional b ON a.regionalid = b.regionalid
            LEFT JOIN m_area_areasite c ON a.areaid = c.areaid
            LEFT JOIN m_area_subarea d ON a.subareaid = d.subareaid
            WHERE a.salesmanid NOT IN (00332,09174,09159,09173)
        ")->result_array();
        return $data;
    }

    function buildTree(array $elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element['supervisorid'] == $parentId) {
                $children = $this->buildTree($elements, $element['salesmanid']);
                if ($children) {
                    $element['children'] = $children;
                } else {
                    $element['children'] = [];
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    function supervisor($data)
    {
        if (empty($data['tipe_sales'])) return [];

        $upperLevel = $this->getUpperLevel($data['tipe_sales']);
        if (empty($upperLevel)) return [];

		$this->db->from("m_sales_salesman");
        $this->db->where('aktif', '1');
        $this->db->where_in('tipe_sales', $upperLevel);
        $result = $this->db->get()->result_array();
        return $result;
    }

    function getUpperLevel($current)
    {
        $levels = [
            'level_1' => ['GME','PA','PM','PE','ESO'],
            'level_2' => ['SM'],
            'level_3' => ['ASM'],
            'level_4' => ['ASS','MRC'],
            'level_5' => ['MEDREP'],
        ];

        $keys = array_keys($levels);
        foreach ($levels as $key => $vals) {
            if (in_array($current, $vals)) {
                $index = array_search($key, $keys);
                return ($index > 0) ? $levels[$keys[$index - 1]] : [];
            }
        }
        return [];
    }
}
