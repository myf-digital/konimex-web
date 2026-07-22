<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_model extends CI_Model
{

    public function create($data)
    {
        $this->db->trans_start();

        $id = IDGenerator::getInstance()->nextID('m_sales_salesman');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }

        $salesmanid = $data['salesmanid'];
        $data_array_category = array(
            "categoryid" => "11",
            "salesmanid" => $salesmanid,
            "nama_category" => "CATEGORY" . " - " . $salesmanid
        );
        $this->db->insert('m_sales_salesman_category', $data_array_category);

        $data['categoryid'] = "11";
        $data['password'] = md5($data['password']);

        $this->save_targets($data);
        $this->save_salesman_area($data);

        if (isset($data['regionalid'])) {
            $data['regionalid'] = is_array($data['regionalid']) ? implode(',', $data['regionalid']) : $data['regionalid'];
        }
        if (isset($data['areaid'])) {
            $data['areaid'] = is_array($data['areaid']) ? implode(',', $data['areaid']) : $data['areaid'];
        }
        if (isset($data['subareaid'])) {
            $data['subareaid'] = is_array($data['subareaid']) ? implode(',', $data['subareaid']) : $data['subareaid'];
        }

        unset($data['periode_sales']);
        unset($data['total_target']);

        $data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        unset($data['usersession']);

        $this->db->insert('m_sales_salesman', $data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function update($data)
    {
        $this->db->trans_start();

        $salesmanid = $data['salesmanid'];
        if (!empty($data['password'])) {
            $data['password'] = md5($data['password']);
        } else {
            unset($data['password']);
        }

        $this->save_targets($data);
        $this->save_salesman_area($data);

        if (isset($data['regionalid'])) {
            $data['regionalid'] = is_array($data['regionalid']) ? implode(',', $data['regionalid']) : $data['regionalid'];
        }
        if (isset($data['areaid'])) {
            $data['areaid'] = is_array($data['areaid']) ? implode(',', $data['areaid']) : $data['areaid'];
        }
        if (isset($data['subareaid'])) {
            $data['subareaid'] = is_array($data['subareaid']) ? implode(',', $data['subareaid']) : $data['subareaid'];
        }

        unset($data['periode_sales']);
        unset($data['total_target']);

        $data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        unset($data['usersession']);

        $this->db->where('salesmanid', $salesmanid);
        $this->db->where('siteid', $data['siteid']);
        $this->db->update('m_sales_salesman', $data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($data)
    {
        $this->db->trans_start();

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_salesman_category');

        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_spesialis_target');

        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_produk_target');

        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('target_tpe');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->delete('m_sales_salesman');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " (
            select
                a.*,
                (
                    select group_concat(distinct b.nama_regional order by b.nama_regional asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_regional b on msa.regionalid = b.regionalid
                    where msa.salesmanid = a.salesmanid
                ) as nama_regional,
                (
                    select group_concat(distinct c.nama_area order by c.nama_area asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_areasite c on msa.areaid = c.areaid
                    where msa.salesmanid = a.salesmanid
                ) as nama_area,
                (
                    select group_concat(distinct d.nama_area order by d.nama_area asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_subarea d on msa.subareaid = d.subareaid
                    where msa.salesmanid = a.salesmanid
                ) as nama_subarea,
                (
                    select count(1) 
                    from tokens t 
                    where t.account_id = a.salesmanid 
                      and t.expires_at > now()
                ) as has_token,
                case when a.aktif = 1 then 'Active' when a.aktif = 0 then 'Not Active' end aktifstatus,
                mss.nama_salesman as supervisor
            from m_sales_salesman a 
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
                (
                    select group_concat(distinct b.nama_regional order by b.nama_regional asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_regional b on msa.regionalid = b.regionalid
                    where msa.salesmanid = a.salesmanid
                ) as nama_regional,
                (
                    select group_concat(distinct c.nama_area order by c.nama_area asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_areasite c on msa.areaid = c.areaid
                    where msa.salesmanid = a.salesmanid
                ) as nama_area,
                (
                    select group_concat(distinct d.nama_area order by d.nama_area asc separator ', ') 
                    from m_salesman_area msa
                    join m_area_subarea d on msa.subareaid = d.subareaid
                    where msa.salesmanid = a.salesmanid
                ) as nama_subarea,
                'https://cdn-icons-png.flaticon.com/512/149/149071.png' as img
            FROM m_sales_salesman a
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

    public function save_targets($data)
    {
        $salesmanid = $data['salesmanid'] ?? null;
        if (empty($salesmanid)) {
            return;
        }

        $nama_salesman = $data['nama_salesman'] ?? '';
        if (empty($nama_salesman)) {
            $salesman = $this->db->select('nama_salesman')
                                 ->where('salesmanid', $salesmanid)
                                 ->get('m_sales_salesman')
                                 ->row_array();
            if ($salesman) {
                $nama_salesman = $salesman['nama_salesman'];
            }
        }

        $usersession = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        if (isset($data['periode_sales'])) {
            $periode_sales = $data['periode_sales'];
            if (!empty($periode_sales)) {
                $splitDate = explode('-', $periode_sales);
                $tahun = $splitDate[0] ?? date('Y');
                $bulan = $splitDate[1] ?? date('m');

                $target_tpe = $this->db->select('id')
                                     ->where('salesmanid', $salesmanid)
                                     ->where('tahun', $tahun)
                                     ->where('bulan', $bulan)
                                     ->get('target_tpe')
                                     ->row_array();

                if ($target_tpe) {
                    $this->db->where('id', $target_tpe['id']);
                    $this->db->update('target_tpe', [
                        'total_target' => $data['total_target'] ?? 0,
                        'modified_by' => $usersession,
                        'modified_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->db->insert('target_tpe', [
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'salesmanid' => $salesmanid,
                        'nama_salesman' => $nama_salesman,
                        'total_target' => $data['total_target'] ?? 0,
                        'created_by' => $usersession,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
    }

    public function save_salesman_area($data) {
        $salesmanid = $data['salesmanid'] ?? null;
        if (empty($salesmanid)) {
            return;
        }

        $this->db->where('salesmanid', $salesmanid);
        $this->db->delete('m_salesman_area');

        // Filter out '0' or empty string values
        $subareaids = isset($data['subareaid']) ? (is_array($data['subareaid']) ? $data['subareaid'] : [$data['subareaid']]) : [];
        $subareaids = array_filter($subareaids, function($v) { return $v !== '0' && $v !== 0 && !empty($v); });

        $areaids = isset($data['areaid']) ? (is_array($data['areaid']) ? $data['areaid'] : [$data['areaid']]) : [];
        $areaids = array_filter($areaids, function($v) { return $v !== '0' && $v !== 0 && !empty($v); });

        $regionalids = isset($data['regionalid']) ? (is_array($data['regionalid']) ? $data['regionalid'] : [$data['regionalid']]) : [];
        $regionalids = array_filter($regionalids, function($v) { return $v !== '0' && $v !== 0 && !empty($v); });

        $insertData = [];
        if (!empty($subareaids)) {
            $this->db->select('regionalid, areaid, subareaid');
            $this->db->where_in('subareaid', $subareaids);
            $subareas = $this->db->get('m_area_subarea')->result_array();
            
            foreach ($subareas as $sa) {
                $insertData[] = [
                    'salesmanid' => $salesmanid,
                    'regionalid' => $sa['regionalid'],
                    'areaid'     => $sa['areaid'],
                    'subareaid'  => $sa['subareaid'],
                ];
            }
        } 
        else if (!empty($areaids)) {
            $this->db->select('regionalid, areaid');
            $this->db->where_in('areaid', $areaids);
            $areas = $this->db->get('m_area_areasite')->result_array();

            foreach ($areas as $ar) {
                $insertData[] = [
                    'salesmanid' => $salesmanid,
                    'regionalid' => $ar['regionalid'],
                    'areaid'     => $ar['areaid'],
                    'subareaid'  => null,
                ];
            }
        } 
        else if (!empty($regionalids)) {
            foreach ($regionalids as $regId) {
                $insertData[] = [
                    'salesmanid' => $salesmanid,
                    'regionalid' => $regId,
                    'areaid'     => null,
                    'subareaid'  => null,
                ];
            }
        }

        if (!empty($insertData)) {
            $this->db->insert_batch('m_salesman_area', $insertData);
        }
    }

    public function sync_all_salesman_area() {
        $this->db->trans_start();
        
        $this->db->select('salesmanid, regionalid, areaid, subareaid');
        $salesmen = $this->db->get('m_sales_salesman')->result_array();
        
        $count = 0;
        foreach ($salesmen as $s) {
            $data = [
                'salesmanid' => $s['salesmanid'],
                'regionalid' => !empty($s['regionalid']) ? explode(',', $s['regionalid']) : [],
                'areaid'     => !empty($s['areaid']) ? explode(',', $s['areaid']) : [],
                'subareaid'  => !empty($s['subareaid']) ? explode(',', $s['subareaid']) : [],
            ];
            
            $this->save_salesman_area($data);
            $count++;
        }
        
        $this->db->trans_complete();
        return [
            'status' => $this->db->trans_status(),
            'message' => 'Successfully synced ' . $count . ' salesmen areas'
        ];
    }


    public function get_sales_targets($data)
    {
        if (empty($data['salesmanid']) || empty($data['periode'])) {
            return [];
        }
        $parts = explode('-', $data['periode']);
        $tahun = intval($parts[0]);
        $bulan = intval($parts[1]);

        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('tahun', $tahun);
        $this->db->where('bulan', $bulan);
        return $this->db->get('target_tpe')->result_array();
    }

    public function detail_mapping($salesmanid)
    {
        if (empty($salesmanid)) {
            return [
                'status' => false,
                'message' => 'salesmanid wajib diisi'
            ];
        }

        $salesman = $this->db->query("
            SELECT
                a.salesmanid,
                a.nama_salesman,
                a.tipe_sales,
                a.jabatan,
                a.supervisorid,
                mss.nama_salesman as supervisor
            FROM m_sales_salesman a
            LEFT JOIN m_sales_salesman mss on mss.salesmanid = a.supervisorid
            WHERE a.salesmanid = ?
        ", [$salesmanid])->row_array();
        if (empty($salesman)) {
            return [
                'status' => false,
                'message' => "Data salesman ($salesmanid) tidak ditemukan"
            ];
        }

        $cur_year = intval(date('Y'));
        $cur_month = intval(date('m'));

        $role_targets = $this->db->query("
            SELECT 
                rmt.tahun,
                rmt.bulan,
                rmt.target_hk,
                rmt.target_dub,
                rmt.target_call_dub,
                rmt.target_call_visit
            FROM app_role r
            JOIN role_mapping_target rmt ON r.role_id = rmt.role_id
            WHERE r.role_name = ?
            ORDER BY rmt.tahun DESC, rmt.bulan DESC
        ", [$salesman['tipe_sales']])->result_array();

        $role_active = [];
        $role_history = [];
        foreach ($role_targets as $rt) {
            if (intval($rt['tahun']) == $cur_year && intval($rt['bulan']) == $cur_month) {
                $role_active[] = $rt;
            } else {
                $role_history[] = $rt;
            }
        }

        $spesialis_targets = $this->db->query("
            SELECT 
                mst.tahun,
                mst.bulan,
                mst.nama_spesialisasi,
                mst.target
            FROM m_sales_spesialis_target mst
            WHERE mst.role_name = ?
            ORDER BY mst.tahun DESC, mst.bulan DESC, mst.nama_spesialisasi ASC
        ", [$salesman['tipe_sales']])->result_array();

        $spesialis_active = [];
        $spesialis_history = [];
        foreach ($spesialis_targets as $st) {
            if (intval($st['tahun']) == $cur_year && intval($st['bulan']) == $cur_month) {
                $spesialis_active[] = $st;
            } else {
                $spesialis_history[] = $st;
            }
        }

        $produk_targets = $this->db->query("
            SELECT 
                mpt.tahun,
                mpt.bulan,
                mpt.product_id,
                mpt.nama_invoice,
                mpt.target,
                mpt.target_qty
            FROM m_sales_produk_target mpt
            WHERE mpt.role_name = ?
            ORDER BY mpt.tahun DESC, mpt.bulan DESC, mpt.nama_invoice ASC
        ", [$salesman['tipe_sales']])->result_array();

        $produk_active = [];
        $produk_history = [];
        foreach ($produk_targets as $pt) {
            if (intval($pt['tahun']) == $cur_year && intval($pt['bulan']) == $cur_month) {
                $produk_active[] = $pt;
            } else {
                $produk_history[] = $pt;
            }
        }

        $sales_targets = $this->db->query("
            SELECT 
                tahun,
                bulan,
                salesmanid,
                nama_salesman,
                total_target
            FROM target_tpe
            WHERE salesmanid = ?
            ORDER BY tahun DESC, bulan DESC, nama_salesman ASC
        ", [$salesmanid])->result_array();

        $sales_active = [];
        $sales_history = [];
        foreach ($sales_targets as $st) {
            if (intval($st['tahun']) == $cur_year && intval($st['bulan']) == $cur_month) {
                $sales_active[] = $st;
            } else {
                $sales_history[] = $st;
            }
        }

        return [
            'status' => true,
            'message' => 'success',
            'salesman' => $salesman,
            'role' => [
                'active' => $role_active,
                'history' => $role_history
            ],
            'spesialis' => [
                'active' => $spesialis_active,
                'history' => $spesialis_history
            ],
            'produk' => [
                'active' => $produk_active,
                'history' => $produk_history
            ],
            'sales' => [
                'active' => $sales_active,
                'history' => $sales_history
            ]
        ];
    }

    public function clear_token($data)
    {
        $salesmanid = $data['salesmanid'];
        $this->db->where('account_id', $salesmanid);
        return $this->db->delete('tokens');
    }
}

