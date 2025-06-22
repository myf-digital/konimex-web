<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_crc_model extends CI_Model
{
	public function load_outlet($data)
    {

		if ($data['restrict_level']=='4'){
            $strqueryarea = " and a.subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."'
                                                )";
			
		}
		else if ($data['restrict_level']=='3'){
            $strqueryarea = " and a.areaid in (select distinct b.areaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."'
                                                )";
		}
		else if ($data['restrict_level']=='2'){
            $strqueryarea = " and a.regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."'
                                                ) ";
		}
		else {
			$strqueryarea ="";
		}

		/*old
    	if ($data['restrict_level']=='4'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$data['usersession']."')
				)";
		}
		else if ($data['restrict_level']=='3'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$data['usersession']."')
					)";
}
		else if ($data['restrict_level']=='2'){
			$strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
				app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
				where a.username='".$data['usersession']."')
				) ";
		}
		else {
			$strquery = "";
		}
		*/
		
        $field = " a.* ";
        $table = " ( select a.customerid, a.nama_customer from m_customer a where classid = '".$data['classid']."' ".$strqueryarea."
                     order by nama_customer
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

    function getPeriodeRekap($data) {

		$query = $this->db->query("select periode from t_sales_crc where customerid='".$data['customerid']."' and periode between '".$data['start']."' and '".$data['end']."' group by periode");

		return $query->result_array();
	}

	function getCustomerStockRekap($data) {

		$query = $this->db->query("select qty_akhir, total_qty_exp, price, productid, periode from t_sales_crc where customerid='".$data['customerid']."' and periode between '".$data['start']."' and '".$data['end']."' ");
			//var_dump($this->db->last_query());die;
		return $query->result_array();
	}

	function getMappingProduct($data) {

		$query = $this->db->query("select a.productid, b.nama_invoice product_name from t_sales_crc a left join m_product b on a.productid=b.productid 
									where a.customerid='".$data['customerid']."' and a.periode between '".$data['start']."' and '".$data['end']."' and date_update is not null");

		return $query->result_array();
	}

}