<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model
{

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
        $this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Insert New Outlet'");
        return $this->db->insert('m_customer', $data);
    }

    public function update($data)
    {
        //$this->db->where('siteid', $data['siteid']);
        //$this->db->where('customerid_m', $data['customerid_m']);
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);
		//$this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Update New Outlet'");
        $this->db->where('customerid', $data['customerid']);
        return $this->db->update('m_customer', $data);
    }

    public function delete($data)
    {
        $this->db->query("insert into m_customer_delete select *, '' deleted_by, now() deleted_date from m_customer where customerid='".$data['customerid']."'");
        //$data["deleted_by"] = $data["usersession"];
        //$datadb["deleted_date"] = $datetime->datetime;
        //$this->db->insert('m_customer_delete', $datajson);

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid_m', $data['customerid_m']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->delete('m_customer');

        $this->db->query("Update app_data_version set version=version+1, modified_date=now(), modified_by='Delete Outlet'");

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('customerid', $data['customerid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('t_sales_setup_rrk');
    }

    public function load($data)
    {
		if ($data["idjabatan"]=='2' or $data["idjabatan"]=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$data["usersession"]."') ";
		else if($data["idjabatan"]=='16' or $data["idjabatan"]=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$data["usersession"]."') ";
		}else{
            $strquery = "";
        }

        $field = "a.* ";
        $table = " (select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position
                                    from m_customer a left join m_area_regional b on a.regionalid=b.regionalid and a.customerid <>''
                                    left join m_area_areasite c on a.areaid = c.areaid
                                    left join m_area_subarea d on a.subareaid = d.subareaid
                                    left join m_customer_class e on a.classid = e.classid
                                    left join m_sales_salesman f on a.salesmanid = f.salesmanid
                                    where a.customerid <> '' ".$strquery."
                    ) a";
        return easy_pagging($data, $field, $table);
    }


    function savetoxls($data) {
		ini_set('memory_limit', '256M');
        $account = $data['account'];
        $filename = $data['filename'];

        if ($account=='' or empty($account) or $account=='null'){
            $account='All';
            $filename = 'All_Account';
        }
        //$salesmanid = $data['salesmanid'];

		if ($data["jabatan"]=='2' or $data["jabatan"]=='3') {
                if ($account=='All'){
                    $strsubquery = "";
                }else{
                    $strsubquery = " and a.classid='".$account."'";
                }
            
                $strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm 
                                                    where a.ram_rsm = '".$data["username"]."') ";
                $strquery = $strquery.$strsubquery;
            } else if($data["jabatan"]=='16' or $data["jabatan"]=='17') {
                if ($account=='All'){
                    $strsubquery = "";
                }else{
                    $strsubquery = " and a.classid='".$account."'";
                }
            
                $strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$data["username"]."') ";
                $strquery = $strquery.$strsubquery;
            }else{
                if ($account=='All'){
                    $strquery = "";
                }else{
                    $strquery = " and a.classid='".$account."'";
                }
            }

        $query = " select a.*, b.nama_regional, c.nama_area, d.nama_area as nama_subarea, e.nama_class as nama_account, f.nama_salesman gff_name, f.tipe_sales position
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
		$html ='<style>
				#table-wrapper {
					position:relative;
				}

				#table-scroll {
					height:300px;
					overflow:auto;  
					margin-top:20px;
				}

				#table-wrapper table {
					width:100%;
				}

				#table-wrapper table thead th .text {
					position:absolute;   
					top:-20px;
					z-index:2;
					height:20px;
					width:35%;
					border:1px solid;
				}
                </style>';
            
		$html .= '<table id="activity_table" border="1" class="table table-striped table-bordered table-condensed">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .='<th>OUTLETID_DRC</th>';
		$html .='<th>KODE OUTLET</th>';
		$html .='<th>Nama Outlet</th>';
		$html .='<th>Alamat</th>';
		$html .='<th>Regional</th>';
		$html .='<th>Area</th>';
		$html .='<th>SubArea/City</th>';
		$html .='<th>BU</th>';
		$html .='<th>Channel/Type</th>';
		$html .='<th>SubChannel/Account</th>';
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
                $html .='<td>'.$voutlet['mcc'].'</td>';
                $html .='<td>'.$voutlet['salesmanid'].'</td>';
                $html .='<td>'.$voutlet['gff_name'].'</td>';
                $html .='<td>'.$voutlet['position'].'</td>';
                $html .= '</tr>';
            }
		$html .= '</tbody>';
		$html .= '</table>';

        header('Content-Type: application/vnd.ms-excel');
        //header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=" . $filename);  //File name extension was wrong
		header('Cache-Control: max-age=0');
        header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);

		echo $html;		
	}		

}
