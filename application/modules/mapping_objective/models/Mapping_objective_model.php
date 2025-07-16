<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapping_objective_model extends CI_Model
{
    function create($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $datetime = $datetime->datetime;

        $data_insert = [];
        if (isset($data['classid']) && isset($data['products'])) {
            foreach ($data['classid'] as $class) {
                $cl = explode('||', $class);

                foreach ($data['products'] as $product) {
                    $pd = explode('||', $product);
                    $data_insert[] = [
                        'productid' => $pd[0] ?? 0,
                        'nama_invoice' => $pd[1] ?? null,
                        'classid' => $cl[0] ?? 0,
                        'nama_class' => $cl[1] ?? null,
                        'objective' => $data['objective'] ?? null,
                        'min_order' => $data['min_order'] ?? null,
                        'keterangan' => $data['keterangan'] ?? null,
                        'start_periode' => $data['start_periode'],
                        'end_periode' => $data['end_periode'],
                        'created_by' => $data['usersession'],
                        'created_date' => $datetime,
                    ];
                }
            }
        }

        $status = [];
        if (count($data_insert) > 0) {
            $this->db->trans_start();
            $exeinsert = $this->db->insert_batch('mapping_objective', $data_insert);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Gagal insert_bath mapping_objective: ' . $exeinsert);
                $status[] = 'Gagal insert_bath mapping_objective: ' . $exeinsert;
            }
        }

        if (count($status) > 0) {
            return $status;
        } else {
            return true;
        }
    }

    function update($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $datetime = $datetime->datetime;

        $data_update = [];
        $pd = explode('||', $data['products']);
        if (isset($data['class'])) {
            foreach ($data['class'] as $class) {
                $cl = explode('||', $class);

                $data_update[] = [
                    'productid' => $pd[0] ?? 0,
                    'nama_invoice' => $pd[1] ?? null,
                    'classid' => $cl[0] ?? 0,
                    'nama_class' => $cl[1] ?? null,
                    'objective' => $data['objective'] ?? null,
                    'min_order' => $data['min_order'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'start_periode' => $data['start_periode'],
                    'end_periode' => $data['end_periode'],
                    'modified_by' => $data['usersession'],
                    'modified_date' => $datetime,
                ];
            }
        }

        $status = [];
        if (count($data_update) > 0) {
            $this->db->where('productid', $pd[0] ?? 0);
            $this->db->delete('mapping_objective');

            $this->db->trans_start();
            $exeinsert = $this->db->insert_batch('mapping_objective', $data_update);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Gagal insert_bath mapping_objective: ' . $exeinsert);
                $status[] = 'Gagal insert_bath mapping_objective: ' . $exeinsert;
            }
        }

        if (count($status) > 0) {
            return $status;
        } else {
            return true;
        }
    }

    public function delete($data)
    {
        $sql = 'select * from mapping_objective where siteid=? and productid=? and objective=? and start_periode=? and end_periode=?';
        $result = $this->db->query($sql, [$data['siteid'], $data['productid'], $data['objective'], $data['start_periode'], $data['end_periode']])->result_array();
        if (count($result) < 1) return false;
        
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('productid', $data['productid']);
        $this->db->where('objective', $data['objective']);
        $this->db->where('start_periode', $data['start_periode']);
        $this->db->where('end_periode', $data['end_periode']);
        $this->db->delete('mapping_objective');
    }

    public function load($data)
    { 
        $field = " a.* ";
        $table = " ( 
                select
                    mo.siteid,
                    mo.productid,
                    mo.nama_invoice,
                    mo.objective,
                    mo.min_order,
                    mo.start_periode,
                    mo.end_periode,
                    mo.keterangan,
                    pd.ext_id1,
                    pd.ext_id2,
                    pd.barcode,
                    pd.category_product,
                    pd.nama_brand,
                    pd.sat_kecil,
                    pd.isi_kecil,
                    pd.h_grosir,
                    pd.h_ritel,
                    pd.keterangan as product_keterangan,
                    GROUP_CONCAT(DISTINCT mo2.classid ORDER BY mo2.classid SEPARATOR '||') AS accountids,
                    GROUP_CONCAT(DISTINCT mo2.nama_class ORDER BY mo2.nama_class SEPARATOR '||') AS accounts
                from mapping_objective mo
                left join m_product pd on pd.productid = mo.productid
                left join mapping_objective mo2 on mo2.productid = mo.productid and mo2.siteid = mo.siteid
                where mo.start_periode <= '".($data['get_date1'] ?? today())."' and mo.end_periode >= '".($data['get_date2'] ?? today())."'
                group by mo.productid
            ) a
        ";
        return easy_pagging($data, $field, $table);
    }
}
