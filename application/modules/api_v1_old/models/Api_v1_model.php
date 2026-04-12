<?php

class Api_v1_model extends CI_Model
{

    function get_siteid()
    {
            $sql = "select a.*
						from m_setup_site a 
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
    }

	function get_salesman($data)
    {

			if ($data["idjabatan"]=='2' or $data["idjabatan"]=='3')
				$strquery = " where a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$data["usersession"]."') ";
			else if($data["idjabatan"]=='16'  or $data["idjabatan"]=='17'){
				$strquery = " where a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$data["usersession"]."') ";
			}else{
				$strquery = "";
			}

            $sql = "select a.*
						from m_sales_salesman a 
					".$strquery."
						order by a.nama_salesman asc
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
    }

	function get_salesman_mapping_area($data)

    {
        if (is_null($data["siteid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            	$sql = "select a.*
						from m_sales_salesman a 
						where a.siteid=? ";
			if (is_null(@$data["salesmanid"])){
				$sql .=	" and a.salesmanid not in (select distinct salesmanid from m_sales_salesman_area) order by a.nama_salesman asc ";
				$res_ss = $this->db->query($sql, array($data["siteid"]));
			}else{
				$sql .=	" and a.salesmanid = ? order by a.nama_salesman asc ";
				$res_ss = $this->db->query($sql, array($data["siteid"],$data["salesmanid"]));
			}
					
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
    }

	function get_regional()
    {
            $sql = "select a.*
						from m_area_regional a 
						order by a.regionalid asc
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
    }

	function get_regional_bysiteid($data)
    {
        if (is_null($data["siteid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from m_area_regional a 
					where a.siteid=?
						order by a.regionalid asc
						";
            $res_ss = $this->db->query($sql, array($data["siteid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
    }

	function get_area($data)
    {
            $sql = "select a.*
						from m_area_areasite a 
					where a.regionalid = ?
						order by a.nama_area asc
						";
			
			if (is_null(@$data["regionalid"])) 
			{
				$res_ss = $this->db->query($sql, array("regionalid"=>"%"));
			}else{
				$res_ss = $this->db->query($sql, array($data["regionalid"]));
			}
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
	}

	function get_city($data)
    {
        if (is_null($data["siteid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from ref_city a 
					where a.siteid=? and a.idregional in (?) and a.idarea in (?) 
						order by a.city asc
						";
			
			if (is_null(@$data["idarea"])) 
			{
				$res_ss = $this->db->query($sql, array($data["siteid"],$data["idregional"],"idarea"=>"%"));
			}else{
				$res_ss = $this->db->query($sql, array($data["siteid"],$data["idregional"],$data["idarea"]));
			}
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
	}

	function get_area_by_sales($data)
    {
        if (is_null($data["siteid"]) or is_null($data["salesmanid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*, b.nama_area
						from m_sales_salesman_area a left join m_area_areasite b on a.areaid=b.areaid
					where a.siteid=? and a.salesmanid = ? 
						  and a.areaid not in (select areaid from t_sales_rrk_setup where salesmanid=?)
						order by b.nama_area asc
						";
			
			$res_ss = $this->db->query($sql, array($data["siteid"],$data["salesmanid"],$data["salesmanid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
    }

	function get_areakirim($data)
    {
        if (is_null($data["siteid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from m_area_kirim a 
					where a.siteid=?
						order by a.nama_areakirim asc
						";
            $res_ss = $this->db->query($sql, array($data["siteid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
    }

	function get_subarea($data)
    {
        if ( is_null($data["regionalid"]) or is_null($data["areaid"]) ) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from m_area_subarea a 
					where a.regionalid=? and a.areaid=?
						order by a.nama_area asc
						";
            $res_ss = $this->db->query($sql, array($data["regionalid"],$data["areaid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Data Invalid!");
            }
        }
    }

	function get_propinsi()
    {
            $sql = "select a.propinsiid, a.nama_propinsi
						from m_area_propinsi a 
						order by a.nama_propinsi asc
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
    }

	function get_propinsi_bysiteid($data)
    {
        if (is_null($data["siteid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.propinsiid, a.nama_propinsi, a.siteid
						from m_area_propinsi a 
					where a.siteid=?
						order by a.nama_propinsi asc
						";
            $res_ss = $this->db->query($sql, array($data["siteid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
        }
    }

	function get_kota($data)
    {
        if (is_null($data["propinsiid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.kotaid, a.propinsiid, a.nama_kota, a.siteid
						from m_area_kota a join m_area_propinsi b on a.propinsiid = b.propinsiid 
					where a.propinsiid=?
						order by a.nama_kota asc
						";
            $res_ss = $this->db->query($sql, array($data["propinsiid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid or PropinsiId Invalid!");
            }
        }
    }

	function get_kecamatan($data)
    {
        if (is_null($data["propinsiid"]) or is_null($data["kotaid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
			$sql = "select a.kecamatanid, 
						   a.kotaid, 
						   a.propinsiid, 
						   a.nama_kecamatan, 
						   a.siteid
						from m_area_kecamatan a 
					where a.propinsiid=? and a.kotaid=?
						order by a.nama_kecamatan asc
						";
            $res_ss = $this->db->query($sql, array($data["propinsiid"], $data["kotaid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "PropinsiId or Kotaid Invalid!");
            }
        }
    }

	function get_kelurahan($data)
    {
        if (is_null($data["propinsiid"]) or is_null($data["kotaid"]) or is_null($data["kecamatanid"])) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
			$sql = "select a.kelurahanid, 
						   a.kotaid, 
						   a.propinsiid, 
						   a.kecamatanid, 
						   a.siteid,
						   a.nama_kelurahan
						from m_area_kelurahan a 
					where a.propinsiid=? and a.kotaid=? and a.kecamatanid=?
						order by a.nama_kelurahan asc
						";
            $res_ss = $this->db->query($sql, array($data["propinsiid"], $data["kotaid"], $data["kecamatanid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "PropinsiId or Kotaid or kecamatanid Invalid!");
            }
        }
	}

	function get_paytype()
    {
		$sql = " select 'T' as idpay, 'Cash' as pay
				 union
				 select 'K' as idpay, 'Kredit' as pay
				";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_gudang($data)
    {
		$sql = " select a.*
					from m_gudang a 
				where a.siteid=?
					order by a.gudang_name asc
				";
		$res_ss = $this->db->query($sql, array($data["siteid"]));
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_statusaktif()
    {
		$sql = " select '1' as idaktif, 'Active' as status
				 union
				 select '0' as idaktif, 'Not Active' as status
				";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_tipesalesman()
    {
		$sql = " select 'MERCHANDISER' as idtipesales, 'MERCHANDISER' as tipesales
				union
				select 'MEDREP MT' as idtipesales, 'MEDREP MT' as tipesales
				union
				select 'SPG MOBILE' as idtipesales, 'SPG MOBILE' as tipesales
				union
				select 'SPG STAY' as idtipesales, 'SPG STAY' as tipesales
				union
				select 'MEDREP GT' as idtipesales, 'MEDREP GT' as tipesales
				";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_tipetrans()
    {
		$sql = " select 'S' as idtipetrans, 'Sales' as tipetrans
				 union
				 select 'R' as idtipetrans, 'Retur' as tipetrans
				";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_frequency()
    {
		$sql = " select 'F1' as idfrequency, 'F1' as frequency 
				union
				select 'F2' as idfrequency, 'F2' as frequency 
				union
				select 'F4' as idfrequency, 'F4' as frequency 
				union
				select 'F8' as idfrequency, 'F8' as frequency 
		";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_weeks()
    {
		$sql = "select '1' as idweeks, 'Weeks 1' as weeks 
				union
				select '2' as idweeks, 'Weeks 2' as weeks 
				union
				select '3' as idweeks, 'Weeks 3' as weeks 
				union
				select '4' as idweeks, 'Weeks 4' as weeks 
		";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_days()
    {
		$sql = "select '1' as iddays, 'Senin' as days 
				union
				select '2' as iddays, 'Selasa' as days 
				union
				select '3' as iddays, 'Rabu' as days 
				union
				select '4' as iddays, 'Kamis' as days 
				union
				select '5' as iddays, 'Jumat' as days 
				union
				select '6' as iddays, 'Sabtu' as days 
				union
				select '0' as iddays, 'Minggu' as days 
		";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_tracking($data) {
		$siteid = $this->get_siteid(); 
		$q = $this->db->query(" select ifnull(latitude_cell,0) latitude_cell, ifnull(longitude_cell,0) longitude_cell,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from t_tracker_salesman
								where siteid = '".$siteid."' AND salesmanid = ? AND periode = ?
								order by DATE_FORMAT(createdate,'%H:%i') asc
								", array($data["sid"],$data["periode"]));
								
		if (count($q->result_array()) > 0) {
		$response = new stdClass();
		$response = $q->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_param_key($data)
    {
		$sql = " select * from ref_param_global where key_param = ? ";
		$res_ss = $this->db->query($sql, array($data["parkey"]));
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_jabatan()
    {
		$sql = " select * from ref_jabatan";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_account_outlet()
    {
		$sql = " select * from ref_account_outlet";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_product()
    {
		$sql = " select * from m_product where status='A'";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_brand()
    {
		$sql = " select brandid,brand from ref_brand";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 201, "data Invalid!");
		}
	}

	function get_pjp_daily($vdate,$createby)
    {
		$this->db->select("*");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();

		$weekday = date('w', strtotime($vdate));
		$weeks = $data->aktif_week;
		
		$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
		$this->db->query($sqldeleterrk, array($vdate));
		$sqldeletecrc = "delete from t_sales_crc where periode=? and date_update is null;";
		$this->db->query($sqldeletecrc, array($vdate));

		$sql = " insert into t_sales_rrk(periode,salesmanid,customerid,flag_proses,tgl_proses,user_create,date_create, minggu)
				 select ?,salesmanid,customerid,'O',now(),?, now(),? from t_sales_setup_rrk where minggu=? and hari = ? and salesmanid not in ('GSKMD001','GSKSPGM001','GSKSPGM002','GSKSPGM003')
				";
		$res_ss = $this->db->query($sql, array($vdate,$createby,$weeks,$weeks,$weekday));
		if (!$res_ss) {
			$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
			$this->db->query($sqldeleterrk, array($vdate));
			return false;
		} else {
			$sqlcrc = "insert into t_sales_crc(periode,salesmanid,customerid,productid,brandid,harga)
							select ?, a.salesmanid, a.customerid, c.productid, d.brandid, d.h_ritel
							from t_sales_rrk a join m_customer b on a.customerid = b.customerid and a.salesmanid=b.salesmanid
							join mapping_sku_active c on b.classid = c.idaccount
							join m_product d on c.productid = d.productid
						where a.periode=?;
						";
			$res_crc = $this->db->query($sqlcrc, array($vdate,$vdate));
			
			if (!$res_crc){
				$sqldeletecrc = "delete from t_sales_crc where periode=? and date_update is null;";
				$this->db->query($sqldeletecrc, array($vdate));
				$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
				$this->db->query($sqldeleterrk, array($vdate));
				return false;
			}else{
				if ($weekday=='0'){
					if ($weeks=='4'){
						$tsql = "update m_setup_site set aktif_week=1, tanggal=?;";
					}else{
						$tsql = "update m_setup_site set aktif_week=aktif_week+1, tanggal=?;";
					}
					$tres = $this->db->query($tsql,array($vdate));
					if(!$tres){
						$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
						$this->db->query($sqldeleterrk, array($vdate));
						$sqldeletecrc = "delete from t_sales_crc where periode=?;";
						$this->db->query($sqldeletecrc, array($vdate));
						return false;
					}else{
						return true;
					}
				}else{
					$this->db->query("Update app_data_version set version=1, modified_date=now(), modified_by='Scheduler PJP'");
            		$tsql = "update m_setup_site set tanggal=?;";
					$tres = $this->db->query($tsql,array($vdate));
					if(!$tres){
						$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
						$this->db->query($sqldeleterrk, array($vdate));
						$sqldeletecrc = "delete from t_sales_crc where periode=?;";
						$this->db->query($sqldeletecrc, array($vdate));
						return false;
					}else{
						return true;
					}

				}
			}
		}
	}

	function get_outlet_pjp($data)
    {

            $sql = " select a.customerid, a.kode_outlet, a.nama_customer outlet, b.nama_class account, a.mcc dc
						from m_customer a left join m_customer_class b on a.classid = b.classid
						where a.salesmanid = '".$data["salesmanid"]."' and a.customerid not in (select customerid from t_sales_setup_rrk where salesmanid = '".$data["salesmanid"]."')
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 201, "Siteid Invalid!");
            }
    }

	function get_crc_daily($data)
    {
		
		$sql = " insert into t_sales_rrk(periode,salesmanid,customerid,flag_proses,tgl_proses,user_create,date_create, minggu)
				 select date_format(NOW(),'%Y-%m-%d'),salesmanid,customerid,'O',now(),'System', now(),? from t_sales_setup_rrk where minggu=? and hari = ?
				";
		$res_ss = $this->db->query($sql, array($data["minggu"],$data["minggu"],$data["hari"]));
		if (!$res_ss) {
			return false;
		} else {
			return true;
		}
	}


}