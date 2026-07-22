<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model
{

    public function create($data)
    {
        if (empty($data["siteid"])) {
            $data["siteid"] = "KNX01";
        }
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        $data["created_by"] = $data["usersession"];

        $sql = "select used+1 customerid_new from app_table_sequence where id=5";
        $newidcust = $this->db->query($sql)->row();
        $data['customerid'] =  $newidcust->customerid_new;

        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('ref_professional_mapping');

        $professionals = $data['professional'];
        if(!empty($professionals)){
            $dataProfessionals = $this->db->where_in('id', $professionals)->get('ref_professional')->result_array();
            $data_array = [];
            for($i=0; $i < count($dataProfessionals); $i++){
                $data_array[] = array(
                    "id_professional" => $dataProfessionals[$i]['id'],
                    "nama_professional" => $dataProfessionals[$i]['nama_professional'],
                    "customerid" => $data['customerid'],
                    "nama_customer" => $data['nama_customer'],
                );
            }
            $this->db->insert_batch('ref_professional_mapping', $data_array);
        }

        $payload = $this->generatePayload($data);

        //update sequence
        $this->db->query("update app_table_sequence set used=".$data['customerid']." where id=5");
        $this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Insert New Outlet'");
        return $this->db->insert('m_customer', $payload);
    }

    public function update($data)
    {
        if (empty($data["siteid"])) {
            $data["siteid"] = "KNX01";
        }
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        $data["modified_by"] = $data["usersession"];
        $professionals = $data['professional'];
        
        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('ref_professional_mapping');

        if(!empty($professionals)){
            $dataProfessionals = $this->db->where_in('id', $professionals)->get('ref_professional')->result_array();
            $data_array = [];
            for($i=0; $i < count($dataProfessionals); $i++){
                $data_array[] = array(
                    "id_professional" => $dataProfessionals[$i]['id'],
                    "nama_professional" => $dataProfessionals[$i]['nama_professional'],
                    "customerid" => $data['customerid'],
                    "nama_customer" => $data['nama_customer'],
                );
            }
            $this->db->insert_batch('ref_professional_mapping', $data_array);
        }

        $payload = $this->generatePayload($data);
		$this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Modify Outlet'");
		$this->db->where('customerid', $data['customerid']);
        return $this->db->update('m_customer', $payload);
    }

    public function generatePayload($data)
    {
        $payload = payload([
            "siteid",
            "customerid_m",
            "customerid",
            "kode_outlet",
            "cust_id_map",
            "nama_customer",
            "alamat",
            "kelurahanid",
            "kecamatanid",
            "kotaid",
            "propinsiid",
            "kodepos",
            "telp",
            "email",
            "segmentid",
            "typeid",
            "classid",
            "regionalid",
            "areaid",
            "subareaid",
            "latitude",
            "longitude",
            "created_by",
            "created_date",
            "modified_by",
            "modified_date",
        ], $data);
        return $payload;
    }

    public function update_location($data)
    {
        $payload = [
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ];
		$this->db->where('customerid', $data['customerid']);
        return $this->db->update('m_customer', $payload);
    }

    public function delete($data)
    {
        $data["deleted_by"] = $data["usersession"];
        
        $customer_fields = $this->db->list_fields('m_customer');
        $delete_fields = $this->db->list_fields('m_customer_delete');
        
        $common_fields = array_intersect($customer_fields, $delete_fields);
        $common_fields = array_diff($common_fields, ['deleted_by', 'deleted_date']);
        
        if (!empty($common_fields)) {
            $fields_str = implode(', ', $common_fields);
            $this->db->query("insert into m_customer_delete ($fields_str, deleted_by, deleted_date) 
                              select $fields_str, '".$this->db->escape_str($data["deleted_by"])."' deleted_by, now() deleted_date 
                              from m_customer where customerid='".$this->db->escape_str($data['customerid'])."'");
        }
        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('m_customer_ob');

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid_m', $data['customerid_m']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('m_customer');

        $this->db->query("delete from t_sales_rrk where customerid='".$data['customerid']."' and salesmanid='".$data['salesmanid']."' 
                            and periode=(select tanggal from m_setup_site);");
        $this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Delete Outlet'");

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid', $data['customerid']);
        return $this->db->delete('t_sales_setup_rrk');
    }

    public function load($data, $type = 'load')
    {
        if (empty($data['sort'])) {
            $data['sort'] = "ifnull(a.modified_date, a.created_date)";
            $data['order'] = "desc";
        }

		if (empty($data['account']) || $data['account'] == 'All') {
			$strsubquery = "";
		} else {
			$strsubquery = " and a.typeid='".$data['account']."'";
		}

        $strquery = "" . $strsubquery;
        if ($data["restrict_level"]=='4'){
            $strquery = " and a.subareaid in (
                select distinct b.subareaid
                from app_resource a 
                left join app_restrict_location b on a.resource_id=b.resource_id 
                where a.username='".$data["usersession"]."'
            )";
        } else if ($data["restrict_level"]=='3'){
            $strquery = " and a.areaid in (
                select distinct b.areaid
                from app_resource a 
                left join app_restrict_location b on a.resource_id=b.resource_id 
                where a.username='".$data["usersession"]."'
            )";
        } else if ($data["restrict_level"]=='2'){
            $strquery = " and a.regionalid in (
                select distinct b.regionalid
                from app_resource a 
                left join app_restrict_location b on a.resource_id=b.resource_id 
                where a.username='".$data["usersession"]."'
            )";
        }

        $field = "a.* ";

        $table = " (
            select
                a.*,
                b.nama_regional,
                c.nama_area,
                d.nama_area as nama_subarea,
                ifnull(f.tipe_sales,'') position,
                ifnull(pro.list_professional, '') as list_professional
            from m_customer a
            left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
            left join m_area_areasite c on a.areaid = c.areaid
            left join m_area_subarea d on a.subareaid = d.subareaid
            left join m_sales_salesman f on a.salesmanid = f.salesmanid
            left join (
                select 
                    rpm.customerid, 
                    group_concat(
                        concat(
                            rp.id,' - ',
                            rp.nama_professional,
                            case 
                                when (rs.name is not null and rs.name <> '') and (rp.type is not null and rp.type <> '') 
                                    then concat(' (', rs.name, ' - ', rp.type, ')')
                                when (rs.name is not null and rs.name <> '') 
                                    then concat(' (', rs.name, ')')
                                when (rp.type is not null and rp.type <> '') 
                                    then concat(' (', rp.type, ')')
                                else ''
                            end
                        ) separator '||'
                    ) as list_professional
                from ref_professional_mapping rpm
                join ref_professional rp on rp.id = rpm.id_professional and status = '3'
                left join ref_spesialisasi rs on rs.id = rp.spesialisasi_id
                group by rpm.customerid
            ) AS pro ON a.customerid = pro.customerid
            where a.customerid <> '' ".$strquery."
        ) a";

        if ($type == 'export') {
            $result = $this->db->query("select * from ".$table." order by ifnull(modified_date, created_date) desc");
            return $result->result_array();
        }
        
        // COUNT DATA
        $has_pro_filter = false;
        if (!empty($data['filterRules'])) {
            $filters = json_decode($data['filterRules'], true);
            if (is_array($filters)) {
                foreach ($filters as $f) {
                    if (isset($f['field']) && $f['field'] === 'list_professional') {
                        $has_pro_filter = true;
                        break;
                    }
                }
            }
        }

        $pro_join = "";
        if ($has_pro_filter) {
            $pro_join = " left join (
                select 
                    rpm.customerid, 
                    group_concat(
                        concat(
                            rp.id,' - ',
                            rp.nama_professional,
                            case 
                                when (rs.name is not null and rs.name <> '') and (rp.type is not null and rp.type <> '') 
                                    then concat(' (', rs.name, ' - ', rp.type, ')')
                                when (rs.name is not null and rs.name <> '') 
                                    then concat(' (', rs.name, ')')
                                when (rp.type is not null and rp.type <> '') 
                                    then concat(' (', rp.type, ')')
                                else ''
                            end
                        ) separator '||'
                    ) as list_professional
                from ref_professional_mapping rpm
                join ref_professional rp on rp.id = rpm.id_professional and status = '3'
                left join ref_spesialisasi rs on rs.id = rp.spesialisasi_id
                group by rpm.customerid
            ) AS pro ON a.customerid = pro.customerid ";
        }

        $count_table = " (
            select
                a.*,
                b.nama_regional,
                c.nama_area,
                d.nama_area as nama_subarea,
                ifnull(f.tipe_sales,'') position
                " . ($has_pro_filter ? ", ifnull(pro.list_professional, '') as list_professional" : "") . "
            from m_customer a
            left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
            left join m_area_areasite c on a.areaid = c.areaid
            left join m_area_subarea d on a.subareaid = d.subareaid
            left join m_sales_salesman f on a.salesmanid = f.salesmanid
            " . $pro_join . "
            where a.customerid <> '' ".$strquery."
        ) a";
        // COUNT DATA

        return easy_pagging($data, $field, $table, array(), $count_table);
    }

    public function mapping_customer_area()
    {
        $sql = "
            UPDATE m_customer c
            JOIN (
                SELECT msa.salesmanid, msa.regionalid, msa.areaid, msa.subareaid
                FROM m_salesman_area msa
                INNER JOIN (
                    SELECT salesmanid, MAX(id) AS max_id
                    FROM m_salesman_area
                    GROUP BY salesmanid
                ) msa_max ON msa.salesmanid = msa_max.salesmanid AND msa.id = msa_max.max_id
            ) sub ON c.salesmanid = sub.salesmanid
            SET c.regionalid = sub.regionalid,
                c.areaid = sub.areaid,
                c.subareaid = sub.subareaid
            WHERE (c.regionalid IS NULL OR c.regionalid = '' OR c.regionalid = 'null' OR c.regionalid = '0')
              AND (c.areaid IS NULL OR c.areaid = '' OR c.areaid = 'null' OR c.areaid = '0')
              AND (c.subareaid IS NULL OR c.subareaid = '' OR c.subareaid = 'null' OR c.subareaid = '0')
              AND c.salesmanid IS NOT NULL AND c.salesmanid != '' AND c.salesmanid != 'null'
        ";
        
        $this->db->query($sql);
        $affected_rows = $this->db->affected_rows();
        
        return [
            'status' => true,
            'message' => "Successfully updated " . $affected_rows . " customer area mapping(s)."
        ];
    }

    public function get_template_data($regionalid = null, $areaid = null, $subareaid = null, $usersession = null, $restrict_level = null)
    {
        $channels = $this->db->select('typeid, nama_type')->from('m_customer_type')->get()->result_array();

        $this->db->select('s.regionalid, r.nama_regional, s.areaid, a.nama_area, s.subareaid, s.nama_area as nama_subarea');
        $this->db->from('m_area_subarea s');
        $this->db->join('m_area_areasite a', 's.areaid = a.areaid AND s.regionalid = a.regionalid', 'left');
        $this->db->join('m_area_regional r', 's.regionalid = r.regionalid', 'left');

        if (!empty($regionalid)) {
            $reg_arr = explode(',', $regionalid);
            $this->db->where_in('s.regionalid', $reg_arr);
        }
        if (!empty($areaid)) {
            $area_arr = explode(',', $areaid);
            $this->db->where_in('s.areaid', $area_arr);
        }
        if (!empty($subareaid)) {
            $subarea_arr = explode(',', $subareaid);
            $this->db->where_in('s.subareaid', $subarea_arr);
        }

        $restrict_query = get_salesman_restrict($usersession, $restrict_level);
        if ($restrict_query) {
            $this->db->where("s.subareaid IN (
                SELECT DISTINCT subareaid 
                FROM m_salesman_area 
                WHERE salesmanid IN (" . $restrict_query . ")
            )", NULL, FALSE);
        }

        $this->db->order_by('r.nama_regional, a.nama_area, s.nama_area', 'ASC');
        $locations = $this->db->get()->result_array();

        // Get Specializations
        $specializations = $this->db->select('id, name')->from('ref_spesialisasi')->order_by('name', 'ASC')->get()->result_array();

        // Get Professional Types from ref_param_global
        $prof_types = [];
        $param_row = $this->db->get_where('ref_param_global', ['key_param' => 'professional_type'])->row_array();
        if ($param_row && !empty($param_row['value'])) {
            $prof_types = explode('|', $param_row['value']);
        }

        return [
            'channels' => $channels,
            'locations' => $locations,
            'specializations' => $specializations,
            'prof_types' => $prof_types
        ];
    }

    public function import_excel($fileTmp, $usersession, $restrict_level = null)
    {
        $this->db->trans_start();
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $header = $sheet[0];
            $header = array_values(array_filter($header, function ($h) {
                return !is_null($h) && $h !== '';
            }));

            $expected = [
                'NO',
                'KODE_OUTLET',
                'NAMA_OUTLET',
                'CHANNEL',
                'TELPON',
                'EMAIL',
                'REGIONAL',
                'AREA',
                'SUB_AREA',
                'ALAMAT',
                'NAMA_USER',
                'SPESIALISASI',
                'TIPE_USER',
            ];

            if (array_map('strtoupper', $header) !== $expected) {
                return ['status' => false, 'message' => 'Format header tidak sesuai'];
            }

            $successCount = 0;
            $cleared_customer_ids = [];
            foreach ($sheet as $index => $row) {
                if ($index == 0) continue;
                
                if (isset($row[0], $row[1]) && $row[0] == 'Contoh Data' && $row[1] == 'OTL001') {
                    continue;
                }

                if (!isset($row[2]) || empty(trim($row[2]))) {
                    continue;
                }

                $nama_customer = trim($row[2]);
                $channel_name = isset($row[3]) ? trim($row[3]) : '';
                $regional_name = isset($row[6]) ? trim($row[6]) : '';
                $area_name = isset($row[7]) ? trim($row[7]) : '';
                $sub_area_name = isset($row[8]) ? trim($row[8]) : '';

                $channel_id = null;
                if (!empty($channel_name)) {
                    $chan_row = $this->db->select('typeid')->from('m_customer_type')->where('LOWER(nama_type)', strtolower($channel_name))->get()->row_array();
                    if ($chan_row) {
                        $channel_id = $chan_row['typeid'];
                    }
                }

                $regional_id = null;
                if (!empty($regional_name)) {
                    $reg_row = $this->db->select('regionalid')->from('m_area_regional')->where('LOWER(nama_regional)', strtolower($regional_name))->get()->row_array();
                    if ($reg_row) {
                        $regional_id = $reg_row['regionalid'];
                    }
                }

                $area_id = null;
                if (!empty($area_name)) {
                    $area_row = $this->db->select('areaid')->from('m_area_areasite')->where('LOWER(nama_area)', strtolower($area_name))->get()->row_array();
                    if ($area_row) {
                        $area_id = $area_row['areaid'];
                    }
                }

                $sub_area_id = null;
                if (!empty($sub_area_name)) {
                    $sub_row = $this->db->select('subareaid')->from('m_area_subarea')->where('LOWER(nama_area)', strtolower($sub_area_name))->get()->row_array();
                    if ($sub_row) {
                        $sub_area_id = $sub_row['subareaid'];
                    }
                }

                if (empty($channel_id) || empty($regional_id)) {
                    continue;
                }

                $kode_outlet = isset($row[1]) ? trim($row[1]) : '';
                $telp = isset($row[4]) ? trim($row[4]) : '';
                $email = isset($row[5]) ? trim($row[5]) : '';
                $alamat = isset($row[9]) ? trim($row[9]) : '';
                $nama_professional = isset($row[10]) ? trim($row[10]) : '';

                $existing = $this->db->query("SELECT a.* FROM m_customer a WHERE a.nama_customer = ? ", [trim($row[2])])->row_array();

                $salesmanid = $usersession ?? 'admin';
                if ($existing) {
                    $customerid = $existing['customerid'];
                    $updateData = [
                        'kode_outlet' => $kode_outlet ? $kode_outlet : $existing['kode_outlet'],
                        'typeid' => $channel_id,
                        'telp' => $telp,
                        'email' => $email,
                        'regionalid' => $regional_id,
                        'areaid' => $area_id,
                        'subareaid' => $sub_area_id,
                        'alamat' => $alamat,
                        'modified_by' => $usersession,
                        'modified_date' => date('Y-m-d H:i:s')
                    ];
                    if ($salesmanid) {
                        $updateData['salesmanid'] = $salesmanid;
                    }
                    $this->db->where('customerid', $customerid);
                    $this->db->update('m_customer', $updateData);
                } else {
                    $sql_seq = "select used+1 customerid_new from app_table_sequence where id=5";
                    $seq_row = $this->db->query($sql_seq)->row();
                    $customerid = $seq_row->customerid_new;

                    $this->db->query("update app_table_sequence set used=".$customerid." where id=5");

                    $insertData = [
                        'siteid' => 'KNX01',
                        'customerid_m' => '',
                        'customerid' => $customerid,
                        'kode_outlet' => $kode_outlet ? $kode_outlet : $customerid,
                        'nama_customer' => $nama_customer,
                        'typeid' => $channel_id,
                        'telp' => $telp,
                        'email' => $email,
                        'regionalid' => $regional_id,
                        'areaid' => $area_id,
                        'subareaid' => $sub_area_id,
                        'alamat' => $alamat,
                        'salesmanid' => $salesmanid,
                        'created_by' => $usersession,
                        'created_date' => date('Y-m-d H:i:s')
                    ];
                    $this->db->insert('m_customer', $insertData);
                }

                if (!in_array($customerid, $cleared_customer_ids)) {
                    $this->db->where('customerid', $customerid);
                    $this->db->delete('ref_professional_mapping');
                    $cleared_customer_ids[] = $customerid;
                }

                if (!empty($nama_professional)) {
                    $prof_str = str_replace('|', ',', $nama_professional);
                    $prof_names = explode(',', $prof_str);
                    
                    $mapping_batch = [];
                    foreach ($prof_names as $prof_name) {
                        $prof_name = trim($prof_name);
                        if (empty($prof_name)) continue;

                        $spesialisasi_name_input = isset($row[11]) ? trim($row[11]) : '';
                        $spesialisasi_id = null;
                        $spesialisasi_name = null;
                        if (!empty($spesialisasi_name_input)) {
                            $spec_db = $this->db->select('id, name')->from('ref_spesialisasi')->where('LOWER(name)', strtolower($spesialisasi_name_input))->get()->row_array();
                            if ($spec_db) {
                                $spesialisasi_id = $spec_db['id'];
                                $spesialisasi_name = $spec_db['name'];
                            }
                        }

                        $type_input = isset($row[12]) ? trim($row[12]) : null;

                        $existing_prof = $this->db->get_where('ref_professional', ['nama_professional' => $prof_name])->row_array();

                        if ($existing_prof) {
                            $prof_id = $existing_prof['id'];
                            $updateProf = [];
                            if ($spesialisasi_id) {
                                $updateProf['spesialisasi_id'] = $spesialisasi_id;
                                $updateProf['spesialisasi_name'] = $spesialisasi_name;
                            }
                            if ($type_input) {
                                $updateProf['type'] = $type_input;
                            }
                            if (!empty($updateProf)) {
                                $this->db->where('id', $prof_id);
                                $this->db->update('ref_professional', $updateProf);
                            }
                        } else {
                            $profData = [
                                'siteid' => 'KNX01',
                                'nama_professional' => $prof_name,
                                'spesialisasi_id' => $spesialisasi_id,
                                'spesialisasi_name' => $spesialisasi_name,
                                'type' => $type_input,
                                'status' => 3,
                                'created_by' => $usersession,
                                'created_date' => date('Y-m-d H:i:s')
                            ];
                            $this->db->insert('ref_professional', $profData);
                            $prof_id = $this->db->insert_id();
                        }

                        $mapping_batch[] = [
                            'id_professional' => $prof_id,
                            'nama_professional' => $prof_name,
                            'customerid' => $customerid,
                            'nama_customer' => $nama_customer
                        ];
                    }
                    if (!empty($mapping_batch)) {
                        $this->db->insert_batch('ref_professional_mapping', $mapping_batch);
                    }
                }

                $successCount++;
            }

            $this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Import Excel Outlet'");
            $this->db->trans_complete();

            return ['status' => true, 'message' => "Import selesai. Berhasil upload {$successCount} outlet."];
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return ['status' => false, 'message' => "Error: " . $e->getMessage()];
        }
    }

    public function get_all_outlets_and_users($regionalid = null, $areaid = null, $subareaid = null, $usersession = null, $restrict_level = null)
    {
        $this->db->select('
            c.customerid,
            c.kode_outlet,
            c.nama_customer,
            ct.nama_type as channel,
            c.telp,
            c.email,
            r.nama_regional as regional,
            a.nama_area as area,
            s.nama_area as sub_area,
            c.alamat,
            p.nama_professional,
            p.spesialisasi_name,
            p.type as tipe_user
        ');
        $this->db->from('m_customer c');
        $this->db->join('m_customer_type ct', 'c.typeid = ct.typeid', 'left');
        $this->db->join('m_area_regional r', 'c.regionalid = r.regionalid', 'left');
        $this->db->join('m_area_areasite a', 'c.areaid = a.areaid', 'left');
        $this->db->join('m_area_subarea s', 'c.subareaid = s.subareaid', 'left');
        $this->db->join('ref_professional_mapping pm', 'c.customerid = pm.customerid', 'left');
        $this->db->join('ref_professional p', 'pm.id_professional = p.id', 'left');

        if (!empty($regionalid)) {
            $reg_arr = explode(',', $regionalid);
            $this->db->where_in('c.regionalid', $reg_arr);
        }
        if (!empty($areaid)) {
            $area_arr = explode(',', $areaid);
            $this->db->where_in('c.areaid', $area_arr);
        }
        if (!empty($subareaid)) {
            $subarea_arr = explode(',', $subareaid);
            $this->db->where_in('c.subareaid', $subarea_arr);
        }

        $restrict_query = get_salesman_restrict($usersession, $restrict_level);
        if ($restrict_query) {
            $this->db->where("c.subareaid IN (
                SELECT DISTINCT subareaid 
                FROM m_salesman_area 
                WHERE salesmanid IN (" . $restrict_query . ")
            )", NULL, FALSE);
        }

        $this->db->order_by('c.customerid', 'DESC');
        $this->db->order_by('p.id', 'ASC');
        return $this->db->get()->result_array();
    }
}

