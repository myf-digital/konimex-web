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

        $target_data = $data;
        $target_data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';

        unset($data['spesialisasiid']);
        unset($data['periode_spesialisasi']);
        unset($data['target_spesialisasi']);
        unset($data['productid']);
        unset($data['periode_product']);
        unset($data['target_product']);
        unset($data['periode_sales']);
        unset($data['total_target']);

        $data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        unset($data['usersession']);

        $this->db->insert('m_sales_salesman', $data);

        // Save targets
        $this->save_targets($target_data);

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

        $target_data = $data;
        $target_data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';

        unset($data['spesialisasiid']);
        unset($data['periode_spesialisasi']);
        unset($data['target_spesialisasi']);
        unset($data['productid']);
        unset($data['periode_product']);
        unset($data['target_product']);
        unset($data['periode_sales']);
        unset($data['total_target']);

        $data['usersession'] = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        unset($data['usersession']);

        $this->db->where('salesmanid', $salesmanid);
        $this->db->where('siteid', $data['siteid']);
        $this->db->update('m_sales_salesman', $data);

        // Save targets
        $this->save_targets($target_data);

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
        if (isset($data['periode_spesialisasi'])) {
            $periode_spesialisasi = $data['periode_spesialisasi'];
            if (!empty($periode_spesialisasi)) {
                $splitDate = explode('-', $periode_spesialisasi);
                $tahun = $splitDate[0] ?? date('Y');
                $bulan = $splitDate[1] ?? date('m');

                $this->db->where('salesmanid', $salesmanid);
                $this->db->where('tahun', $tahun);
                $this->db->where('bulan', $bulan);
                $this->db->delete('m_sales_spesialis_target');

                $spesialisasi_ids = $data['spesialisasiid'] ?? [];
                $target_spesialisasi = $data['target_spesialisasi'] ?? [];

                if (!empty($spesialisasi_ids)) {
                    $this->db->where_in('id', $spesialisasi_ids);
                    $specialties = $this->db->get('ref_spesialisasi')->result_array();
                    $specialtyMap = [];
                    foreach ($specialties as $s) {
                        $specialtyMap[$s['id']] = $s['name'];
                    }

                    $insertSpesialisasi = [];
                    foreach ($spesialisasi_ids as $spId) {
                        $tgt = isset($target_spesialisasi[$spId]) ? intval($target_spesialisasi[$spId]) : 0;
                        $insertSpesialisasi[] = [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'salesmanid' => $salesmanid,
                            'nama_salesman' => $nama_salesman,
                            'spesialisasi_id' => $spId,
                            'nama_spesialisasi' => $specialtyMap[$spId] ?? '',
                            'target' => $tgt,
                            'created_by' => $usersession,
                            'created_date' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($insertSpesialisasi)) {
                        $this->db->insert_batch('m_sales_spesialis_target', $insertSpesialisasi);
                    }
                }
            }
        }

        if (isset($data['periode_product'])) {
            $periode_product = $data['periode_product'];
            if (!empty($periode_product)) {
                $splitDate = explode('-', $periode_product);
                $tahun = $splitDate[0] ?? date('Y');
                $bulan = $splitDate[1] ?? date('m');

                $this->db->where('salesmanid', $salesmanid);
                $this->db->where('tahun', $tahun);
                $this->db->where('bulan', $bulan);
                $this->db->delete('m_sales_produk_target');

                $product_ids = $data['productid'] ?? [];
                $target_product = $data['target_product'] ?? [];

                if (!empty($product_ids)) {
                    $this->db->where_in('productid', $product_ids);
                    $products = $this->db->get('m_product')->result_array();
                    $productMap = [];
                    foreach ($products as $p) {
                        $productMap[$p['productid']] = $p['nama_invoice'];
                    }

                    $insertProduct = [];
                    foreach ($product_ids as $prodId) {
                        $tgt = isset($target_product[$prodId]) ? intval($target_product[$prodId]) : 0;
                        $insertProduct[] = [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'salesmanid' => $salesmanid,
                            'nama_salesman' => $nama_salesman,
                            'product_id' => $prodId,
                            'nama_invoice' => $productMap[$prodId] ?? '',
                            'target' => $tgt,
                            'created_by' => $usersession,
                            'created_date' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($insertProduct)) {
                        $this->db->insert_batch('m_sales_produk_target', $insertProduct);
                    }
                }
            }
        }

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

    public function spesialisasi($data)
    {
        return $this->db->from('ref_spesialisasi')->order_by('name', 'ASC')->get()->result_array();
    }

    public function products($data)
    {
        if (!empty($data['q'])) {
            $this->db->group_start();
            $this->db->like('productid', $data['q']);
            $this->db->or_like('nama_invoice', $data['q']);
            $this->db->group_end();
        }
        $this->db->order_by('nama_invoice', 'ASC');
        $this->db->limit(50);
        return $this->db->from('m_product')->get()->result_array();
    }

    public function get_spesialisasi_targets($data)
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
        return $this->db->get('m_sales_spesialis_target')->result_array();
    }

    public function get_product_targets($data)
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
        return $this->db->get('m_sales_produk_target')->result_array();
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
            SELECT salesmanid, nama_salesman, tipe_sales, jabatan
            FROM m_sales_salesman
            WHERE salesmanid = ?
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
                tahun,
                bulan,
                nama_spesialisasi,
                target
            FROM m_sales_spesialis_target
            WHERE salesmanid = ?
            ORDER BY tahun DESC, bulan DESC, nama_spesialisasi ASC
        ", [$salesmanid])->result_array();

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
                tahun,
                bulan,
                product_id,
                nama_invoice,
                target
            FROM m_sales_produk_target
            WHERE salesmanid = ?
            ORDER BY tahun DESC, bulan DESC, nama_invoice ASC
        ", [$salesmanid])->result_array();

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
}

