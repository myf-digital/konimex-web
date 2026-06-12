<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model
{

    public function create($data)
    {
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
        $this->db->query("insert into m_customer_delete select *, '".$data["deleted_by"]."' deleted_by, now() deleted_date from m_customer where customerid='".$data['customerid']."'");
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
		if ($data['account'] == 'All') {
			$strsubquery = "";
		} else {
			$strsubquery = " and a.classid='".$data['account']."'";
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
                e.nama_class as nama_account,
                ifnull(f.tipe_sales,'') position,
                ifnull(pro.list_professional, '') as list_professional
            from m_customer a
            left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
            left join m_area_areasite c on a.areaid = c.areaid
            left join m_area_subarea d on a.subareaid = d.subareaid
            left join m_customer_class e on a.classid = e.classid
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
                left join ref_professional rp on rp.id = rpm.id_professional
                left join ref_spesialisasi rs on rs.id = rp.spesialisasi_id
                group by rpm.customerid
            ) AS pro ON a.customerid = pro.customerid
            where a.customerid <> '' ".$strquery."
            order by ifnull(a.modified_date, a.created_date) desc
        ) a";

        if ($type == 'export') {
            $result = $this->db->query("select * from ".$table);
            return $result->result_array();
        }
        
        return easy_pagging($data, $field, $table);
    }


    function savetoxls($data) {
		ini_set('memory_limit', '1024M');
        $account = $data['account'];
        $filename = $data['filename'];

        if ($account=='' or empty($account) or $account=='null'){
            $account='All';
            $filename = 'All_Account';
        }
        if ($data["restrict_level"]=='4'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            
            $strquery = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["username"]."'
                                                )";
            $strquery = $strquery.$strsubquery;
        }
        else if ($data["restrict_level"]=='3'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            $strquery = " and a.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["username"]."'
                                                )";
            
            $strquery = $strquery.$strsubquery;
        }
        else if ($data["restrict_level"]=='2'){
            if ($account=='All'){
                $strsubquery = "";
            }else{
                $strsubquery = " and a.classid='".$account."'";
            }
            $strquery = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["username"]."'
                                                ) ";
            $strquery = $strquery.$strsubquery;
        }
        else {
            $strquery = "";
        }

        $query = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position,
                            case when a.customerid_m <>'' then 'Noo' else '-' END as flag_noo
                                    from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                                    left join m_area_areasite c on a.areaid = c.areaid
                                    left join m_area_subarea d on a.subareaid = d.subareaid
                                    left join m_customer_class e on a.classid = e.classid
                                    left join m_sales_salesman f on a.salesmanid = f.salesmanid
					where a.customerid <> '' ".$strquery."
                  ";

        $execquery = $this->db->query($query);
        $lovoutlet = $execquery->result_array();
		
		$filename = "Outlet_".$filename.".xls";
		$html ='';
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th>OUTLET ID</th>';
		$html .='<th>KODE OUTLET</th>';
		$html .='<th>Nama Outlet</th>';
		$html .='<th>Alamat</th>';
		$html .='<th>Regional</th>';
		$html .='<th>Area</th>';
		$html .='<th>SubArea/City</th>';
		$html .='<th>BU</th>';
		$html .='<th>Channel/Type</th>';
		$html .='<th>SubChannel/Account</th>';
		$html .='<th>Type</th>';
		$html .='<th>DC</th>';
		$html .='<th>MEDREP</th>';
		$html .='<th>MEDREP Name</th>';
		$html .='<th>Position</th>';
		$html .= '</tr></thead>';
        $html .= '<tbody>';
        
        foreach ($lovoutlet as $voutlet) {
                $html .= '<tr>';
                $html .='<td>'.$voutlet['customerid'].'</td>';
                $html .='<td>'.$voutlet['kode_outlet'].'</td>';
                $html .='<td>'.$voutlet['nama_customer'].'</td>';
                $html .='<td>'.$voutlet['alamat'].'</td>';
                $html .='<td>'.$voutlet['nama_regional'].'</td>';
                $html .='<td>'.$voutlet['nama_area'].'</t>';
                $html .='<td>'.$voutlet['nama_subarea'].'</td>';
                $html .='<td>'.$voutlet['segmentid'].'</td>';
                $html .='<td>'.$voutlet['typeid'].'</td>';
                $html .='<td>'.$voutlet['nama_account'].'</td>';
                $html .='<td>'.$voutlet['spot_id'].'</td>';
                $html .='<td>'.$voutlet['mcc'].'</td>';
                $html .='<td>'.$voutlet['salesmanid'].'</td>';
                $html .='<td>'.$voutlet['gff_name'].'</td>';
                $html .='<td>'.$voutlet['position'].'</td>';
                $html .= '</tr>';
            }
		$html .= '</tbody>';
		$html .= '</table>';

        header('Content-Type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);

		echo $html;		
	}		

}
