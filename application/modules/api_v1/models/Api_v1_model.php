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
                return result($response);
            } else {
                return result(new stdClass(), 200, "Siteid Invalid!");
            }
    }

	function get_salesman($data)
    {
		$strquery = "";
		if (!empty($data['salesmanid'])) {
			$strquery .= " and a.salesmanid = '".$data['salesmanid']."'";
		} else {
			$restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
			if ($restrict_query) {
				$strquery = " and a.salesmanid in (" . $restrict_query . ")";
			}
		}
		
		if (!empty($data['skip_req_dub'])) {
			$exclude_where = "";
			if (!empty($data['salesmanid'])) {
				$exclude_where = " AND salesmanid <> '" . $this->db->escape_str($data['salesmanid']) . "'";
			}
			$strquery .= " and a.salesmanid not in (select distinct salesmanid from req_dub where 1=1 $exclude_where)";
		}

		$sql = "select
					a.*,
					(
						select group_concat(distinct regional.nama_regional order by regional.nama_regional asc separator ', ')
						from m_salesman_area msa
						join m_area_regional regional on regional.regionalid = msa.regionalid
						where msa.salesmanid = a.salesmanid
					) as nama_regional,
					(
						select group_concat(distinct area.nama_area order by area.nama_area asc separator ', ')
						from m_salesman_area msa
						join m_area_areasite area on area.areaid = msa.areaid
						where msa.salesmanid = a.salesmanid
					) as nama_area,
					(
						select area.latitude
						from m_salesman_area msa
						join m_area_areasite area on area.areaid = msa.areaid
						where msa.salesmanid = a.salesmanid
						limit 1
					) as latitude,
					(
						select area.longitude
						from m_salesman_area msa
						join m_area_areasite area on area.areaid = msa.areaid
						where msa.salesmanid = a.salesmanid
						limit 1
					) as longitude,
					ifnull(rmt.target_dub,0) as target_dub
				from m_sales_salesman a
				left join app_role role on role.role_name = a.tipe_sales
				left join role_mapping_target rmt on rmt.role_id = role.role_id
					and rmt.tahun = YEAR(CURRENT_DATE) and rmt.bulan = MONTH(CURRENT_DATE)
				where a.tipe_sales <> 'ADMIN' and a.aktif = 1
				".$strquery."
				order by a.nama_salesman asc
			";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			return result($response);
		} else {
			return result(new stdClass(), 200, "Siteid Invalid!");
		}
    }

	function get_spesialisasi($data)
    {
		$where = "";
		if (!empty($data['q'])) {
			$where = " where a.name like '%" . $data['q'] . "%'";
		}

		$sql = "select a.*
				from ref_spesialisasi a
				$where
				order by a.id asc
			";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			return result($response);
		} else {
			return result(new stdClass(), 200, "Data not found");
		}
    }

	function get_professional($data)
    {
		$where = "";
		if (!empty($data['q'])) {
			$where = " and a.nama_professional like '%" . $data['q'] . "%'";
		}

		$sql = "select a.*, b.name as spesialisasi
				from ref_professional a
				left join ref_spesialisasi b on b.id = a.spesialisasi_id
				where a.siteid = 'HIMALAYA' and a.status = 3 $where
				order by a.id desc
				limit 25
			";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			return result($response);
		} else {
			return result(new stdClass(), 200, "Data not found");
		}
    }

	function get_all_gff_admin($data)
    {
		$restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
		if ($restrict_query) {
			$strquery = " and a.salesmanid in (" . $restrict_query . ")";
		} else {
			$strquery = "";
		}

		$sql = "select a.*
					from m_sales_salesman a
					where a.aktif='1' 
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
			return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
            }
    }
	
	function get_regional_restrict($data)
    {
		if ($data["restrict_level"]=='4'){
			$strquery = " where a.regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else if ($data["restrict_level"]=='3'){
			$strquery = " where a.regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else if ($data["restrict_level"]=='2'){
			$strquery = " where a.regionalid in (select distinct b.regionalid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else {
			$strquery = "";
		}

			$sql = "select a.*
						from m_area_regional a 
						$strquery
						order by a.regionalid asc
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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

	function get_area_restrict($data)
    {
		if ($data["restrict_level"]=='4'){
			$strquery = " and a.areaid in (select distinct b.areaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else if ($data["restrict_level"]=='3'){
			$strquery = " and a.areaid in (select distinct b.areaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		} else {
			$strquery = "";
		}

        $regionalids = $data["regionalid"] ?? null;
        if (empty($regionalids)) {
            $sql = "select a.* from m_area_areasite a where 1=1 $strquery order by a.nama_area asc";
            $res_ss = $this->db->query($sql);
        } else {
            $regional_arr = is_array($regionalids) ? $regionalids : explode(',', $regionalids);
            $this->db->select('a.*');
            $this->db->from('m_area_areasite a');
            $this->db->where_in('a.regionalid', $regional_arr);
            if ($strquery != "") {
                $this->db->where(substr(trim($strquery), 4));
            }
            $this->db->order_by('a.nama_area', 'asc');
            $res_ss = $this->db->get();
        }

        $response = $res_ss->result_array();
        return result($response);
	}

	function get_subarea_restrict($data)
    {
		if ($data["restrict_level"]=='4'){
			$strquery = " and a.subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else {
			$strquery = "";
		}

        $regionalids = $data["regionalid"] ?? null;
        $areaids = $data["areaid"] ?? null;

        if (empty($regionalids) || empty($areaids)) {
            return result(new stdClass(), 400, "Parameter not allowed");
        }

        $regional_arr = is_array($regionalids) ? $regionalids : explode(',', $regionalids);
        $area_arr = is_array($areaids) ? $areaids : explode(',', $areaids);

        $this->db->select('a.*');
        $this->db->from('m_area_subarea a');
        $this->db->where_in('a.regionalid', $regional_arr);
        $this->db->where_in('a.areaid', $area_arr);
        if ($strquery != "") {
            $this->db->where(substr(trim($strquery), 4));
        }
        $this->db->order_by('a.nama_area', 'asc');
        $res_ss = $this->db->get();

        if (count($res_ss->result_array()) > 0) {
            $response = $res_ss->result_array();
            return result($response);
        } else {
            return result(new stdClass(), 200, "Data Invalid!");
        }
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
            }
        }
    }

	function get_subarea($data)
    {
		if ($data["restrict_level"]=='4'){
			$strquery = " and a.subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else if ($data["restrict_level"]=='3'){
			$strquery = " and a.subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else if ($data["restrict_level"]=='2'){
			$strquery = " and a.subareaid in (select distinct b.subareaid from  
												app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
												where a.username='".$data["usersession"]."'
												)";
		}
		else {
			$strquery = "";
		}

        if ( is_null($data["regionalid"]) or is_null($data["areaid"]) ) {
            return result(new stdClass(), 400, "Parameter not allowed");
        } else {
            $sql = "select a.*
						from m_area_subarea a 
					where a.regionalid=? and a.areaid=?
					$strquery
						order by a.nama_area asc
						";
            $res_ss = $this->db->query($sql, array($data["regionalid"],$data["areaid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "Data Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid Invalid!");
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
                return result(new stdClass(), 200, "Siteid or PropinsiId Invalid!");
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
                return result(new stdClass(), 200, "PropinsiId or Kotaid Invalid!");
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
                return result(new stdClass(), 200, "PropinsiId or Kotaid or kecamatanid Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_tipesalesman($data)
    {
		$params = [];
		$where = '';
		if (!empty($data['param'])) {
			$paramGlobal = $this->db->query("select * from ref_param_global where key_param = ?", [$data['param']])->row();
			if ($paramGlobal) {
				$where = " and lower(a.role_name) in ?";
				$params[] = explode("|", strtolower($paramGlobal->value));
			}
		}
		$sql = "
			select
				a.role_name as idtipesales,
				concat(a.role_name, ' - ', a.description) as tipesales
			from app_role a
			where a.role_name not like '%admin%' and 
				a.description is not null
				and a.description <> '' $where
		";
		$res_ss = $this->db->query($sql, $params);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			return result($response);
		} else {
			return result(new stdClass(), 200, "data Invalid!");
		}
		// $data = [
		// 	['idtipesales' => 'MEDREP', 'tipesales' => 'MEDICAL REP'],
		// 	['idtipesales' => 'MRC', 'tipesales' => 'MEDICAL REP COORDINATOR'],
		// 	['idtipesales' => 'ASS', 'tipesales' => 'AREA SALES SUPERVISOR'],
		// 	['idtipesales' => 'ASM', 'tipesales' => 'AREA SALES MANAGER'],
		// 	['idtipesales' => 'SM', 'tipesales' => 'SALES MANAGER'],
		// 	['idtipesales' => 'GME', 'tipesales' => 'GENERAL MANAGER EB'],
		// 	['idtipesales' => 'ESO', 'tipesales' => 'ETHICAL SUPPORT OFFICER'],
		// 	['idtipesales' => 'PA', 'tipesales' => 'PENATA ADM'],
		// 	['idtipesales' => 'PM', 'tipesales' => 'PRODUCT MANAGER'],
		// 	['idtipesales' => 'PE', 'tipesales' => 'PRODUCT EXECUTIVE'],
		// ];
		// return result($data);
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_tracking($data) {
		$site = $this->get_siteid(); 
		$q = $this->db->query(" select ifnull(latitude_cell,0) latitude_cell, ifnull(longitude_cell,0) longitude_cell,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from t_tracker_salesman
								where siteid = '".($site->siteid ?? 'HIMALAYA')."' AND salesmanid = ? AND periode = ?
								order by DATE_FORMAT(createdate,'%H:%i') asc
								", array($data["sid"],$data["periode"]));
								
		if (count($q->result_array()) > 0) {
		$response = new stdClass();
		$response = $q->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
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
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_account_outlet()
    {
		$sql = " select classid as idaccount, nama_class as account from m_customer_class";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_product($data)
    {
		$where = '';
		if (isset($data['type']) && $data['type'] == 'mapping_objective') {
			$where = ' and productid not in (
				select productid
				from mapping_objective
				where date(start_periode) <= CURDATE() and date(end_periode) >= CURDATE()
				group by productid
			)';
		}
		$sql = " select * from m_product where status='A'" . $where;
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_product_filter_brand($data)
    {
		$sql = " select * from m_product where status='A' and brandid='".$data["brandid"]."'";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_brand()
    {
		$sql = " select brandid,brand from ref_brand order by urutan asc;";
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
		$response = new stdClass();
		$response = $res_ss->result_array();
		//parsing to result
		return result($response);
		} else {
		return result(new stdClass(), 200, "data Invalid!");
		}
	}

	function get_pjp_daily($vdate,$createby)
    {
		$this->db->select("*");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();

		$datesetupsite=date("Y-m-d", strtotime($data->tanggal));
		$reqdate=date("Y-m-d", strtotime($vdate));
		if ($datesetupsite >= $reqdate){
			return false;
		}else{
			$weekday = date('w', strtotime($vdate));
			$weeks = $data->aktif_week;

			if ($weekday==0){
				if ($weeks==4){
					$weeks = 1;
					$tsql = "update m_setup_site set aktif_week=1, tanggal=?;";
				}else{
					$weeks = $weeks+1;
					$tsql = "update m_setup_site set aktif_week=aktif_week+1, tanggal=?;";
				}
			}

			$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
			$this->db->query($sqldeleterrk, array($vdate));
			$sqldeletecrc = "delete from t_sales_crc where periode=? and date_update is null;";
			$this->db->query($sqldeletecrc, array($vdate));

			$sql = " insert into t_sales_rrk(periode,salesmanid,customerid,flag_proses,tgl_proses,user_create,date_create, minggu)
					select ?,a.salesmanid,a.customerid,'O',now(),?, now(),? from t_sales_setup_rrk a join m_sales_salesman b on a.salesmanid=b.salesmanid 
					where a.minggu=? and a.hari = ? and b.aktif='1'
					";
			$res_ss = $this->db->query($sql, array($vdate,$createby,$weeks,$weeks,$weekday));
			if (!$res_ss) {
				$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
				$this->db->query($sqldeleterrk, array($vdate));
				return false;
			} else {
				//delete data crc yang tidak di isi
				$sqldeletecrc = "delete from t_sales_crc where date_update is null;";
				$this->db->query($sqldeletecrc);

				//menambahkan price periode terakhir input product di crc
				$sqlcrc = "insert into t_sales_crc(periode,salesmanid,customerid,productid,brandid,harga,price)
								select ?,a.salesmanid, a.customerid, c.productid, d.brandid, d.h_ritel, ifnull(e.price,0)
								from t_sales_rrk a join m_customer_ob b on a.customerid = b.customerid and a.salesmanid=b.salesmanid
								join m_customer mc on b.customerid = mc.customerid
								join mapping_sku_active c on mc.classid = c.idaccount
								join m_product d on c.productid = d.productid
								left join t_stock_all_outlet e on a.salesmanid=e.salesmanid and a.customerid = e.customerid 
								and d.productid=e.productid and e.tahun=date_format(?,'%Y') and e.bulan=date_format(?,'%m')
							where a.periode=? and a.customerid not in (select distinct customerid from mapping_sku_active_last3months);
							";
				// add suggest last 3 months SKU Active 2022-02-08
				$sqlcrc_suggest = "replace into t_sales_crc(periode,salesmanid,customerid,productid,brandid,harga,price)
									select ?,a.salesmanid, a.customerid, c.productid, d.brandid, d.h_ritel, ifnull(e.price,0)
										from t_sales_rrk a join m_customer_ob b on a.customerid = b.customerid and a.salesmanid=b.salesmanid
										join mapping_sku_active_last3months c on b.customerid = c.customerid 
										join m_product d on c.productid = d.productid
										left join t_stock_all_outlet e on a.salesmanid=e.salesmanid and a.customerid = e.customerid 
										and d.productid=e.productid and e.tahun=date_format(?,'%Y') and e.bulan=date_format(?,'%m')
									where a.periode=?
									;
									";

				$sqlcrc_suggest_order = "replace into t_sales_crc(periode,salesmanid,customerid,productid,brandid,harga,price,qty_saran_order)
											select ?,b.salesmanid, b.customerid, c.productid, d.brandid, d.h_ritel, d.h_ritel, c.qty_saran_order
											from m_customer_ob b join m_customer mc on b.customerid = mc.customerid
											join (
													select z.customerid,z.salesmanid, z.productid, round(avg(z.qty_akhir ),0) as qty_saran_order 
													from t_sales_crc as z
													where z.salesmanid in (select salesmanid from m_sales_salesman where aktif=1) 
													and z.periode >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) and z.customerid <>''
													group by z.customerid,z.salesmanid, z.productid
												) c on b.customerid = c.customerid
											join m_product d on c.productid = d.productid
											join t_stock_all_outlet e on e.customerid = c.customerid and e.salesmanid=c.salesmanid 
											and d.productid=e.productid and e.last_update >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
											where c.customerid <>'';
								";
				$clearcrc="delete from t_sales_crc where date_update is null;";

				$this->db->query($clearcrc);
				$res_crc = $this->db->query($sqlcrc, array($vdate,$vdate,$vdate,$vdate));
				$res_crc_sugest = $this->db->query($sqlcrc_suggest, array($vdate,$vdate,$vdate,$vdate));
				$res_crc_sugest_order = $this->db->query($sqlcrc_suggest_order, array($vdate));
				
				if (!$res_crc){
					$sqldeletecrc = "delete from t_sales_crc where periode=? and date_update is null;";
					$this->db->query($sqldeletecrc, array($vdate));
					$sqldeleterrk = "delete from t_sales_rrk where periode=?;";
					$this->db->query($sqldeleterrk, array($vdate));
					return false;
				}else{
					if ($weekday=='0'){
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
	}

	function get_outlet_pjp($data)
    {
		$where = '';
		$where = " and a.customerid not in (select customerid from t_sales_setup_rrk where salesmanid = '".$data["salesmanid"]."')";
		if (isset($data['type']) && $data['type']) $where = '';

		$sql = " select a.customerid, a.kode_outlet, a.nama_customer outlet, b.nama_class account, a.mcc dc
			from m_customer a left join m_customer_class b on a.classid = b.classid
			join m_customer_ob c on a.customerid=c.customerid
			where c.salesmanid = '".$data["salesmanid"]."'" . $where
		;
		$res_ss = $this->db->query($sql);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			//parsing to result
			return result($response);
		} else {
			return result(new stdClass(), 200, "Siteid Invalid!");
		}
    }

	function get_outlet_dub($data)
    {
		$where = '';
		$salesmanid_val = '';
		$area = [];
		if (!empty($data['salesmanid'])) {
			$salesmanid_val = $data['salesmanid'];
			$sql_salesman = "
				select
					mss.*,
					mar.nama_regional,
					maa.nama_area,
					mas.nama_area as nama_subarea
				from m_sales_salesman mss
				left join m_area_regional mar on mar.regionalid = mss.regionalid
				left join m_area_areasite maa on maa.areaid = mss.areaid
				left join m_area_subarea mas on mas.subareaid = mss.subareaid
				where mss.salesmanid = ?
			";
			$salesman = $this->db->query($sql_salesman, [$data['salesmanid']])->row_array();
			if ($salesman) {
				if (!empty($salesman['regionalid'])) {
					$where .= ' and a.regionalid = "'.$salesman['regionalid']. '"';
					$area[] = $salesman['nama_regional'];
				}
				if (!empty($salesman['areaid'])) {
					$where .= ' and a.areaid = "'.$salesman['areaid']. '"';
					$area[] = $salesman['nama_area'];
				}
				if (!empty($salesman['subareaid'])) {
					$where .= ' and a.subareaid = "'.$salesman['subareaid']. '"';
					$area[] = $salesman['nama_subarea'];
				}
			}
		}

		$sql = "
			select
				a.customerid,
				a.nama_customer,
				b.typeid,
				b.nama_class,
				d.nama_regional,
				c.nama_area,
				e.nama_area as nama_subarea,
                ifnull(pro.list_professional, '') as list_professional,
                ifnull(group_concat(distinct ob.user_id separator '||'), '') as mapped_professionals
			from m_customer a 
			left join m_customer_class b on a.classid = b.classid
			left join m_area_regional d on a.regionalid = d.regionalid
			left join m_area_areasite c on a.areaid = c.areaid
			left join m_area_subarea e on a.subareaid = e.subareaid
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
            left join m_customer_ob ob on a.customerid = ob.customerid and ob.salesmanid = '".$this->db->escape_str($salesmanid_val)."'
			where a.customerid <> '' and pro.list_professional is not null $where
			group by a.customerid
		";
		$res = $this->db->query($sql);
		$data = $res->result_array();
		
		if (count($data) > 0) {
			return result($data);
		} else {
			$la = implode(" - ", $area);
			return result([], 404, "Outlet area (".$la.") tidak ditemukan!");
		}
    }

	function get_outlet_planned($data)
    {
		$sql = "
			select
				a.req_no,
				a.salesmanid,
				a.periode,
				a.customerid,
				a.user_id,
				b.nama_salesman,
				c.nama_customer,
				c.typeid,
				d.nama_professional,
				e.name as spesialisasi
			from req_pjp_daily_detail a
			left join m_sales_salesman b on b.salesmanid = a.salesmanid
			left join m_customer c on c.customerid = a.customerid
			left join ref_professional d on d.id = a.user_id
			left join ref_spesialisasi e on e.id = d.spesialisasi_id
			where  d.nama_professional is not null and a.salesmanid = ?
			order by a.periode asc
		";
		$res = $this->db->query($sql, [$data['salesmanid']]);

		$sqlDub = "
			select
				a.salesmanid,
				a.customerid,
				a.user_id,
				b.nama_salesman,
				c.nama_customer,
				c.typeid,
				d.nama_professional,
				e.name as spesialisasi
			from m_customer_ob a
			left join m_sales_salesman b on b.salesmanid = a.salesmanid
			left join m_customer c on c.customerid = a.customerid
			left join ref_professional d on d.id = a.user_id
			left join ref_spesialisasi e on e.id = d.spesialisasi_id
			where d.nama_professional is not null and a.salesmanid = ?
		";
		$dub = $this->db->query($sqlDub, [$data['salesmanid']]);
		$resDub = $dub->result_array();

		if (count($resDub) > 0) {
			return result([
				'dub' => $resDub,
				'planned' => $res->result_array(),
			]);
		} else {
			return result([
				'dub' => [],
				'planned' => [],
			], 200, 'Data tidak ditemukan!');
		}
    }

	function get_pjp_detail($data)
    {
		if (!$data['req_no']) return result(new stdClass(), 422, 'req_no is required');

		$table = 'req_pjp_daily_detail';
		if (isset($data['type']) && $data['type'] == 'weekly') {
			$table = 'req_pjp_weekly_detail';
		}
		
		$sql = 'select * from ' . $table . ' where req_no=?';
		$res_ss = $this->db->query($sql, [$data['req_no']]);
		if (count($res_ss->result_array()) > 0) {
			$response = new stdClass();
			$response = $res_ss->result_array();
			return result($response);
		} else {
			return result(new stdClass(), 404, 'Data not found');
		}
    }

	function get_outlet_within_radius($data)
    {
		$where = '';
		if ($data['selected_outlets'] && count($data['selected_outlets']) > 0) {
			$where = ' AND a.customerid NOT IN ('.implode(',',$data['selected_outlets']).')';
		}
		$sql = "
			SELECT
				a.customerid,
				a.kode_outlet,
				a.nama_customer outlet,
				b.nama_class account,
				a.mcc dc,
				a.latitude,
				a.longitude,
                (6371 * ACOS(
                    COS(RADIANS(?)) * COS(RADIANS(a.latitude)) * 
                    COS(RADIANS(a.longitude) - RADIANS(?)) + 
                    SIN(RADIANS(?)) * SIN(RADIANS(a.latitude))
                )) AS distance_km
			FROM m_customer a
			LEFT JOIN m_customer_class b ON b.classid = a.classid
			JOIN m_customer_ob c ON c.customerid = a.customerid
			WHERE a.latitude <> 0 AND a.latitude <> 0
				AND c.salesmanid = ?
				AND a.customerid NOT IN (SELECT customerid FROM t_sales_setup_rrk WHERE salesmanid = ?)
				".$where."
			HAVING distance_km <= ?
			ORDER BY distance_km ASC
			LIMIT 50
		";

        $query = $this->db->query($sql, [
			$data['lat_center'],
			$data['lng_center'],
			$data['lat_center'],
			$data['salesmanid'],
			$data['salesmanid'],
			$data['radius_km'],
		]);
		if (count($query->result_array()) > 0) {
			return result($query->result_array());
		} else {
			return result([], 404, 'Data not found');
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

	function get_ram_rsm($data)
    {
            $sql = " select a.username, a.name
							from app_resource a 
					where idjabatan=3;
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "data Invalid!");
            }
    }

	function get_aas_aam_tss_tsm($data)
    {
            $sql = " select a.username, a.name
							from app_resource a 
					where idjabatan=16;
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "data Invalid!");
            }
    }

	function get_fc($data)
    {
            $sql = " select a.username, a.name
							from app_resource a 
					where idjabatan=17;
						";
            $res_ss = $this->db->query($sql);
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "data Invalid!");
            }
    }

	function generate_absensi($vdate)
    {
		$sql = " replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, flag_adjust, keterangan, pjp, 
												effective_call, `call`, extra_call, invalid_call, crc, promo, competitor, `order`, sos, image)
					select a.date, a.salesman_id, case when a.type='IST' then 'S' 
													   when a.type='ICT' then 'C' 
													   when a.type='AHR' then 'H'
													   else 'HF' end tipe, 
							check_in checkin, check_out checkout, ifnull(b.flag_adjust,0) as flag_adjust,
							concat(ifnull(a.description,''),ifnull(concat('-',a.description_in),''),ifnull(concat('-',a.description_out),'')) as keterangan, 
							case when a.type='ICT' then (select count(1) from t_sales_rrk where periode=a.date and salesmanid=a.salesman_id) 
							when b.flag_adjust=1 then 0
							else 0 
							end _pjp, 0 _effective_call,
							0 _call, 0 _extra_call, 0 _invalid_call,0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos, a.image 
					from s_absensi a left join t_sales_absensi b on a.`date`=b.periode and a.salesman_id =b.salesmanid 
					where a.date between DATE_ADD(?, INTERVAL -7 DAY) and ?
					union
					select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, ifnull(b.flag_adjust,0) as flag_adjust,'' keterangan, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) else 0 end _pjp,
						(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid 
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid)) as _effective_call, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
								and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid))  else 0 end _call,
						case when b.flag_adjust=0 or b.flag_adjust=1 then 
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
							else (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid)
						end _extra_call,
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid not in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
						as _invalid_call,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
					(select count(1) from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid) _order,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos, '' image
					from t_sales_rrk_trans a left join t_sales_absensi b on a.periode=b.periode and a.salesmanid=b.salesmanid  
					where a.periode between  DATE_ADD(?, INTERVAL -7 DAY) and ?
					group by a.periode,a.salesmanid
					;
				";
			$res_ss = $this->db->query($sql, array($vdate,$vdate,$vdate,$vdate));
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function generate_absensi_monthly($vdate)
    {
		$sql = " replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, flag_adjust, keterangan, pjp, 
												effective_call, `call`, extra_call, invalid_call, crc, promo, competitor, `order`, sos, image)
					select a.date, a.salesman_id, case when a.type='IST' then 'S' 
													   when a.type='ICT' then 'C' 
													   when a.type='AHR' then 'H'
													   else 'HF' end tipe, 
							check_in checkin, check_out checkout, ifnull(b.flag_adjust,0) as flag_adjust,
							concat(ifnull(a.description,''),ifnull(concat('-',a.description_in),''),ifnull(concat('-',a.description_out),'')) as keterangan, 
							case when a.type='ICT' then (select count(1) from t_sales_rrk where periode=a.date and salesmanid=a.salesman_id) 
							when b.flag_adjust=1 then 0
							else 0 
							end _pjp, 0 _effective_call,
							0 _call, 0 _extra_call, 0 _invalid_call,0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos, a.image 
					from s_absensi a left join t_sales_absensi b on a.`date`=b.periode and a.salesman_id =b.salesmanid 
					where a.date between DATE_ADD(?, INTERVAL -35 DAY) and ?
					union
					select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, ifnull(b.flag_adjust,0) as flag_adjust,'' keterangan, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) else 0 end _pjp,
						(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid 
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid)) as _effective_call, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
								and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid))  else 0 end _call,
						case when b.flag_adjust=0 or b.flag_adjust=1 then 
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
							else (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid)
						end _extra_call,
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid not in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
						as _invalid_call,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
					(select count(1) from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid) _order,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos, '' image
					from t_sales_rrk_trans a left join t_sales_absensi b on a.periode=b.periode and a.salesmanid=b.salesmanid  
					where a.periode between  DATE_ADD(?, INTERVAL -35 DAY) and ?
					group by a.periode,a.salesmanid;
				";
			$res_ss = $this->db->query($sql, array($vdate,$vdate,$vdate,$vdate));
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}
	
	function generate_absensi_daily($vdate)
    {
		$sql = " replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, flag_adjust, keterangan, pjp, 
												effective_call, `call`, extra_call, invalid_call, crc, promo, competitor, `order`, sos, image)
					select a.date, a.salesman_id, case when a.type='IST' then 'S' 
													   when a.type='ICT' then 'C' 
													   when a.type='AHR' then 'H'
													   else 'HF' end tipe, 
							check_in checkin, check_out checkout, ifnull(b.flag_adjust,0) as flag_adjust,
							concat(ifnull(a.description,''),ifnull(concat('-',a.description_in),''),ifnull(concat('-',a.description_out),'')) as keterangan, 
							case when a.type='ICT' then (select count(1) from t_sales_rrk where periode=a.date and salesmanid=a.salesman_id) 
							when b.flag_adjust=1 then 0
							else 0 
							end _pjp, 0 _effective_call,
							0 _call, 0 _extra_call, 0 _invalid_call,0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos, a.image 
					from s_absensi a left join t_sales_absensi b on a.`date`=b.periode and a.salesman_id =b.salesmanid 
					where a.date between DATE_ADD(?, INTERVAL -2 DAY) and ?
					union
					select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, ifnull(b.flag_adjust,0) as flag_adjust,'' keterangan, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) else 0 end _pjp,
						(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid 
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid)) as _effective_call, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
								and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid))  else 0 end _call,
						case when b.flag_adjust=0 or b.flag_adjust=1 then 
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
							else (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid)
						end _extra_call,
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid not in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
						as _invalid_call,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
					(select count(1) from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid) _order,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos, '' image
					from t_sales_rrk_trans a left join t_sales_absensi b on a.periode=b.periode and a.salesmanid=b.salesmanid  
					where a.periode between  DATE_ADD(?, INTERVAL -2 DAY) and ?
					group by a.periode,a.salesmanid
					;
				";
			$res_ss = $this->db->query($sql, array($vdate,$vdate,$vdate,$vdate));
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function generate_stock($vdate)
    {
		$sql = " replace into t_stock_all_outlet (tahun,bulan,salesmanid,customerid,productid,qty_akhir,qty_ed,exp_date,price,update_by,last_update)
					select date_format(periode,'%Y'), date_format(periode,'%m'), salesmanid, customerid, productid, 
							qty_akhir, total_qty_exp, exp_date, price,modified_by,periode 
					from t_sales_crc where  date_update is not null
					and periode between DATE_FORMAT(DATE_ADD(?, INTERVAL -1 DAY),'%Y-%m-01') and LAST_DAY(DATE_ADD(?, INTERVAL -1 DAY))
					order by date_update asc;
				";
			$res_ss = $this->db->query($sql, array($vdate,$vdate));
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function generate_rekap_sos($vdate)
    {
		$sql = " replace into rekap_sos_detail (tahun,bulan,periode,salesmanid,customerid,customerid_m,type_sos,image,qty_sos_gsk,qty_sos_competitor,
												sos,qty_facing,qty_product_focus,product_focus,datecreate,created_by,created_date,modified_by,modified_date,transaction_id)
							select date_format(periode,'%Y'), date_format(periode,'%m'), periode,salesmanid,customerid,customerid_m,type_sos,image,qty_sos_gsk
							,qty_sos_competitor,ifnull(round(( qty_sos_gsk/qty_sos_competitor * 100 ),2),0) AS sos,
							qty_facing,qty_product_focus,ifnull(round(( qty_product_focus/qty_facing * 100 ),2),0) as product_focus,datecreate,created_by,created_date,modified_by,modified_date,transaction_id					
					from t_activity_sos where  qty_sos_gsk > 0
					and periode between DATE_FORMAT(?,'%Y-%m-01') and LAST_DAY(?)
					order by periode asc;
				";
			$res_ss = $this->db->query($sql, array($vdate,$vdate));
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}


	function get_stock_all_periode_fr_oos($vdate)
    {
		$sql_old = " replace into t_stock_all_outlet (tahun,bulan,salesmanid,customerid,productid,qty_akhir,qty_ed,exp_date,price,update_by,last_update,fr,oos)
					select a.tahun, a.bulan, a.salesmanid, a.customerid, a.productid, a.qty_akhir, a.qty_ed, a.exp_date, a.price, a.update_by, a.last_update, 
						   b.fr, case when c.oos is null then 0 else c.oos end oos
					from t_stock_all_outlet a
					left join 
					(select customerid, count(1) fr from t_sales_rrk_trans 
					where periode between DATE_FORMAT('$vdate','%Y-%m-01') and LAST_DAY('$vdate') and crc_time is not null
						group by customerid) b on a.customerid=b.customerid
					left join 
					(select customerid,productid,count(1) oos from t_sales_crc 
						where periode between DATE_FORMAT('$vdate','%Y-%m-01') and LAST_DAY('$vdate') and qty_akhir=0 and date_update is not null
						group by customerid,productid) c on a.customerid=c.customerid and a.productid=c.productid
					where a.tahun=date_format('$vdate','%Y') and a.bulan=date_format('$vdate','%m');
				";
		$sql = " replace into t_stock_all_outlet (tahun,bulan,salesmanid,customerid,productid,qty_akhir,qty_ed,exp_date,price,update_by,last_update,fr,oos,na)
					select a.tahun, a.bulan, a.salesmanid, a.customerid, a.productid, a.qty_akhir, a.qty_ed, a.exp_date, a.price, a.update_by, a.last_update, 
						   b.fr, case when c.oos is null then 0 else c.oos end oos, case when d.NotActive is null then 0 else d.NotActive end na
					from t_stock_all_outlet a 
					left join 
					(select customerid, salesmanid, productid, count(1) fr from t_sales_crc 
					where periode between DATE_FORMAT(DATE_ADD('$vdate', INTERVAL -1 DAY),'%Y-%m-01') and LAST_DAY(DATE_ADD('$vdate', INTERVAL -1 DAY)) and date_update is not null
						group by customerid, salesmanid, productid) b on b.customerid=a.customerid and b.salesmanid =a.salesmanid and b.productid = a.productid
					left join 
					(select customerid,salesmanid,productid,count(1) oos from t_sales_crc 
						where periode between DATE_FORMAT(DATE_ADD('$vdate', INTERVAL -1 DAY),'%Y-%m-01') and LAST_DAY(DATE_ADD('$vdate', INTERVAL -1 DAY)) 
						and qty_akhir=0 and date_update is not null and status_aktif ='A'
						group by customerid,salesmanid,productid) c on c.customerid=a.customerid and c.salesmanid = a.salesmanid and a.productid=c.productid
					left join 
					(select customerid,salesmanid,productid,count(1) NotActive from t_sales_crc 
						where periode between DATE_FORMAT(DATE_ADD('$vdate', INTERVAL -1 DAY),'%Y-%m-01') and LAST_DAY(DATE_ADD('$vdate', INTERVAL -1 DAY)) 
						and date_update is not null and status_aktif ='NA'
						group by customerid,salesmanid,productid) d on d.customerid=a.customerid and d.salesmanid = a.salesmanid and d.productid=a.productid
					where a.tahun=date_format(DATE_ADD('$vdate', INTERVAL -1 DAY),'%Y') and a.bulan=date_format(DATE_ADD('$vdate', INTERVAL -1 DAY),'%m');
				";
			$res_ss = $this->db->query($sql);
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function generate_stock_doi($vdate)
    {
		ini_set("memory_limit","10240M");
        ini_set('max_execution_time', '6000');
		
		$q = $this->db->query(" select date_format(periode,'%Y') tahun, date_format(periode,'%m') bulan, 
										date_format(CONCAT(LEFT(periode + INTERVAL 1 MONTH,7),'-01'),'%Y') tahunnext, 
										date_format(CONCAT(LEFT(periode + INTERVAL 1 MONTH,7),'-01'),'%m') bulannext, 
										date_format(CONCAT(LEFT(periode - INTERVAL 1 MONTH,7),'-01'),'%Y') tahunprev,
										date_format(CONCAT(LEFT(periode - INTERVAL 1 MONTH,7),'-01'),'%m') bulanprev, 
										salesmanid, customerid, productid, qty_akhir, qty_fix_order, price, modified_by, periode, date_update 
								from t_sales_crc where customerid in (select customerid from m_customer where typeid = 'TOKO PANEL')
								and date_update is not null and periode between DATE_ADD('$vdate', INTERVAL -7 DAY) and '$vdate'
								order by periode asc
								;");

		$q_sellin = $this->db->query(" select date_format(periode,'%Y') tahun, date_format(periode,'%m') bulan, 
								date_format(CONCAT(LEFT(periode + INTERVAL 1 MONTH,7),'-01'),'%Y') tahunnext, 
								date_format(CONCAT(LEFT(periode + INTERVAL 1 MONTH,7),'-01'),'%m') bulannext, 
								date_format(CONCAT(LEFT(periode - INTERVAL 1 MONTH,7),'-01'),'%Y') tahunprev,
								date_format(CONCAT(LEFT(periode - INTERVAL 1 MONTH,7),'-01'),'%m') bulanprev, 
								salesmanid, customerid, productid, qty_sell_in, modified_by, periode, date_update 
							from (					
							select a.tanggal as periode, a.salesmanid, a.customerid, b.productid, b.qty_kecil as qty_sell_in, a.salesmanid as modified_by, a.tanggalsave as date_update 
							from t_sales_master a left join t_sales_detail b on a.no_sales=b.no_sales 
							where a.customerid in (select customerid from m_customer where typeid = 'TOKO PANEL')
							and a.tanggal is not null and a.tanggal between DATE_ADD('$vdate', INTERVAL -7 DAY) and '$vdate'
							order by a.tanggalsave asc
							) a;
							");


			$data = $q->result_array();
			$data_sellin = $q_sellin->result_array();
			foreach ($data as $rowscrc){
			$exec_create_stock_akhir = $this->db->query(" 
					replace into t_stock_all_outlet_doi (tahun,bulan,salesmanid,customerid,productid,
					qty_stock_awal,qty_sell_in,qty_stock_akhir,qty_sell_out,harga,update_stock_awal,update_sell_in,update_stock_akhir)
					values('".$rowscrc['tahun']."','".$rowscrc['bulan']."','".$rowscrc['salesmanid']."','".$rowscrc['customerid']."','".$rowscrc['productid']."',
					qty_stock_awal,qty_sell_in,'".$rowscrc['qty_akhir']."',qty_sell_out,'".$rowscrc['price']."',update_stock_awal,update_sell_in,'".$rowscrc['date_update']."')
					; ");
					$this->db->last_query();
				if (!$exec_create_stock_akhir) {return false;}

			$exec_create_stock_awal = $this->db->query(" 
				replace into t_stock_all_outlet_doi (tahun,bulan,salesmanid,customerid,productid,
				qty_stock_awal,qty_sell_in,qty_stock_akhir,qty_sell_out,harga,update_stock_awal,update_sell_in,update_stock_akhir)
				values('".$rowscrc['tahunnext']."','".$rowscrc['bulannext']."','".$rowscrc['salesmanid']."','".$rowscrc['customerid']."','".$rowscrc['productid']."',
				'".$rowscrc['qty_akhir']."',qty_sell_in,qty_stock_akhir,qty_sell_out,'".$rowscrc['price']."','".$rowscrc['date_update']."',update_sell_in,update_stock_akhir)
				; ");
				$this->db->last_query();
			if (!$exec_create_stock_awal) {return false;}
			}

			foreach($data_sellin as $rowssellin){
				$exec_create_sellin = $this->db->query(" 
				replace into t_stock_all_outlet_doi (tahun,bulan,salesmanid,customerid,productid,
				qty_stock_awal,qty_sell_in,qty_stock_akhir,qty_sell_out,harga,update_stock_awal,update_sell_in,update_stock_akhir)
				values('".$rowssellin['tahunprev']."','".$rowssellin['bulanprev']."','".$rowssellin['salesmanid']."','".$rowssellin['customerid']."','".$rowssellin['productid']."',
				qty_stock_awal,'".$rowssellin['qty_sell_in']."',qty_stock_akhir,qty_sell_out,harga,update_stock_awal,'".$rowssellin['date_update']."',update_stock_akhir)
				; ");
				$this->db->last_query();
				if (!$exec_create_stock_awal) {return false;}
			}
		return true;

	}
	
	function get_available_sku_last3months()
    {
		$sqldelete = "truncate table mapping_sku_active_last3months;";
		$sql = "replace into mapping_sku_active_last3months(customerid,productid,created_by,created_date)
				select x.customerid, x.productid, 'System', now() as datenow from (
				select z.customerid, z.kode_outlet, z.nama_customer, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, z.classid, z.nama_class, z.brandid, z.brand, z.productid, z.nama_product,
						sum(z.jml_bulan) as jml_bulan, sum(z.available) as _available, sum(z.fr) fr, sum(z.oos) oos from 
							( 
							select y.tahun, y.bulan, y.customerid, y.kode_outlet, y.nama_customer, y.regionalid, y.nama_regional, y.areaid, y.nama_area, y.subareaid, y.city, 
									y.classid, y.nama_class, y.brandid, y.brand, y.productid, y.nama_product, 
									1 as jml_bulan, case when y.fr<>y.oos then 1 else 0 end available, sum(y.fr) as fr, sum(y.oos) as oos
							from (
									select a.tahun, a.bulan, a.customerid, b.kode_outlet,b.nama_customer,b.classid, h.nama_class, c.brandid, d.brand, b.regionalid, e.nama_regional, 
											b.areaid, f.nama_area, b.subareaid, g.nama_area city, a.productid, c.nama_invoice as nama_product, a.fr, a.oos,c.categoryid
									from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
										left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
										left join m_customer_class h on h.classid=b.classid 
									where a.last_update between last_day(now()) + interval 1 day - interval 4 month and last_day(now() - interval 1 month)
								) y 
								group by y.tahun, y.bulan, y.customerid, y.kode_outlet,y.nama_customer,y.classid, y.nama_class, y.brandid, y.brand, 
										 y.regionalid, y.nama_regional, y.areaid, y.nama_area, y.subareaid, y.city, y.productid, y.nama_product
						) z 
				group by z.customerid, z.kode_outlet, z.nama_customer, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, z.classid, z.nama_class, z.brandid, z.brand, z.productid, z.nama_product
				) x
				where x._available > 0 
				and x.customerid in (select customerid from t_stock_all_outlet 
										where last_update between last_day(now()) + interval 1 day - interval 12 month and last_day(now() - interval 1 month)
									group by customerid having count(distinct customerid,tahun,bulan)>=3
									)
				;
				";
			$res_del = $this->db->query($sqldelete);
			$res_ss = $this->db->query($sql);
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function get_available_sku_last3months_period()
    {
		$sql = "replace into mapping_sku_active_last3months_period(period,customerid,productid,created_by,created_date)
				select date_format(NOW(),'%Y-%m-%d') as period,x.customerid, x.productid, 'System', now() as datenow from (
				select z.customerid, z.kode_outlet, z.nama_customer, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, z.classid, z.nama_class, z.brandid, z.brand, z.productid, z.nama_product,
						sum(z.jml_bulan) as jml_bulan, sum(z.available) as _available, sum(z.fr) fr, sum(z.oos) oos from 
							( 
							select y.tahun, y.bulan, y.customerid, y.kode_outlet, y.nama_customer, y.regionalid, y.nama_regional, y.areaid, y.nama_area, y.subareaid, y.city, 
									y.classid, y.nama_class, y.brandid, y.brand, y.productid, y.nama_product, 
									1 as jml_bulan, case when y.fr<>y.oos then 1 else 0 end available, sum(y.fr) as fr, sum(y.oos) as oos
							from (
									select a.tahun, a.bulan, a.customerid, b.kode_outlet,b.nama_customer,b.classid, h.nama_class, c.brandid, d.brand, b.regionalid, e.nama_regional, 
											b.areaid, f.nama_area, b.subareaid, g.nama_area city, a.productid, c.nama_invoice as nama_product, a.fr, a.oos,c.categoryid
									from t_stock_all_outlet a left join m_customer b on a.customerid=b.customerid left join m_product c on a.productid=c.productid 
										left join ref_brand d on c.brandid=d.brandid left join m_area_regional e on b.regionalid=e.regionalid
										left join m_area_areasite f on b.areaid=f.areaid left join m_area_subarea g on b.subareaid=g.subareaid
										left join m_customer_class h on h.classid=b.classid 
									where a.last_update between last_day(now()) + interval 1 day - interval 4 month and last_day(now() - interval 1 month)
								) y 
								group by y.tahun, y.bulan, y.customerid, y.kode_outlet,y.nama_customer,y.classid, y.nama_class, y.brandid, y.brand, 
										 y.regionalid, y.nama_regional, y.areaid, y.nama_area, y.subareaid, y.city, y.productid, y.nama_product
						) z 
				group by z.customerid, z.kode_outlet, z.nama_customer, z.regionalid, z.nama_regional, z.areaid, z.nama_area, z.subareaid, z.city, z.classid, z.nama_class, z.brandid, z.brand, z.productid, z.nama_product
				) x
				where x._available > 0 
				and x.customerid in (select customerid from t_stock_all_outlet 
										where last_update between last_day(now()) + interval 1 day - interval 12 month and last_day(now() - interval 1 month)
									group by customerid having count(distinct customerid,tahun,bulan)>=3
									)
				;
				";
			$res_ss = $this->db->query($sql);
			if (!$res_ss) {
				return false;
			} else {
				return true;
			}
	}

	function get_crc_takout()
    {
		$sqlget = "select * from 
					t_sales_crc_202206 a left join mapping_sku_active_last3months b on a.customerid=b.customerid and a.productid=b.productid
					where a.periode between '2022-06-01' and '2022-06-18' and date_update is not null
					and b.productid is null and a.qty_akhir = 0;
				";
				
			$res_ss = $this->db->query($sqlget);
			$data_crc = $res_ss->result_array();
			foreach ($data_crc as $rowscrc){
				$sqldelete = "delete from 
					t_sales_crc_202206 where periode='".$rowscrc['periode']."' and customerid='".$rowscrc['customerid']."' and productid='".$rowscrc['productid']."';
				";
				$res = $this->db->query($sqldelete);
			}
			if (!$res) {
				return false;
			} else {
				return true;
			}
	}
	
	function get_productivity_md($data)
    {
            $sql = " select e.nama_regional, d.nama_area, c.nama_area city,
							b.salesmanid,b.nama_salesman,b.tipe_sales, 
							date_format('".$data["periode"]."','%d')-FLOOR(date_format('".$data["periode"]."','%d')/7)-(case when date_format('".$data["periode"]."','%d') > 15 
							then (select jml_libur from setup_jumlah_harilibur where tahun=date_format('".$data["periode"]."','%Y') and bulan=date_format('".$data["periode"]."','%m')) else 0 end) as 'MEDREP Aktif', 
							(select count(1) from t_sales_absensi where status='H' and salesmanid=a.salesmanid and periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."')) as 'MEDREP Hadir',
							(select count(1) from t_sales_absensi where status='C' and salesmanid=a.salesmanid and periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."')) as cuti,
							(select count(1) from t_sales_absensi where status='S' and salesmanid=a.salesmanid and periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."')) as sakit, 
							round((count(1)/(date_format('".$data["periode"]."','%d')-FLOOR(date_format('".$data["periode"]."','%d')/7)-(case when date_format('".$data["periode"]."','%d') > 25 
							then (select jml_libur from setup_jumlah_harilibur where tahun=date_format('".$data["periode"]."','%Y') and bulan=date_format('".$data["periode"]."','%m')) else 0 end)))*100,0) as persentasi,
							sum(a.pjp) as pjp, sum(a.effective_call) as effective_call, sum(a.call) as `call`, sum(a.extra_call) as extra_call, sum(a.invalid_call) as invalid_call,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid = 'HYPERMARKET'),'0') as sos_gsk_hyp,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid = 'MTI'),'0') as sos_gsk_mti,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid = 'SUPERMARKET'),'0') as sos_gsk_spm,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid = 'MINIMARKET'),'0') as sos_gsk_mini,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid = 'MODERN PHARMA'),'0') as sos_gsk_mph,
							ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
							from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
							left join m_customer_class y on x.classid=y.classid
							where a.tahun=date_format('".$data["periode"]."','%Y') and a.bulan=date_format('".$data["periode"]."','%m') and a.salesmanid=b.salesmanid and a.type_sos='P'
							and x.typeid in ('MINIMARKET','SUPERMARKET','MTI','HYPERMARKET','MODERN PHARMA')),'0') as sos_gsk_total,
							ifnull((
							select GROUP_CONCAT(x.reason_rrk SEPARATOR ' , ') from ( 
							SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_rrk 
							from t_sales_rrk_trans z left join t_sales_rrk_reason y on z.call_reasonid=y.call_reasonid 
							where z.periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."') 
							and z.call_reasonid is not null 
							group by z.salesmanid,y.reason ) x where x.salesmanid=a.salesmanid GROUP BY x.salesmanid
							),'-') as rrk_keterangan,
							sum(a.checkin_out_area) as out_area,
							(select avg(z.final_score) as final_score
							from evaluation_product_knowledge z left join product_knowledge_event x on z.id_event = x.id_event 
							where z.periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."')  and z.username = a.salesmanid
							group by z.username) as quiz,
							sum(a.order) as outlet_order, sum(a.total_order) as total_order
					from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1 left join m_area_subarea c on b.subareaid=c.subareaid 
					left join m_area_areasite d on c.areaid=d.areaid left join m_area_regional e on e.regionalid=d.regionalid 
					where a.periode between '".$data["periode"]."' and LAST_DAY('".$data["periode"]."') and b.tipe_sales not in ('ADMIN','FC')
					and a.salesmanid = ?
					group by e.nama_regional, d.nama_area, c.nama_area, b.salesmanid,b.nama_salesman,b.tipe_sales
					order by tipe_sales asc, nama_salesman asc;
						";
			$res_ss = $this->db->query($sql, array($data["salesmanid"]));
            if (count($res_ss->result_array()) > 0) {
                $response = new stdClass();
                $response = $res_ss->result_array();
                //parsing to result
                return result($response);
            } else {
                return result(new stdClass(), 200, "data Invalid!");
            }
    }

	function get_schedule_dub($date, $createby)
	{
		$time = strtotime($date);
		if (!$time) {
			if (strlen($date) == 7 && strpos($date, '-') !== false) {
				$time = strtotime($date . '-01');
			} else {
				$time = time();
			}
		}
		$tahun = (int) date('Y', $time);
		$bulan = (int) date('n', $time);

		$days_in_month = (int) date('t', $time);
		$weekends = 0;
		for ($d = 1; $d <= $days_in_month; $d++) {
			$date_str = sprintf("%04d-%02d-%02d", $tahun, $bulan, $d);
			$day_of_week = (int) date('w', strtotime($date_str));
			if ($day_of_week == 0 || $day_of_week == 6) {
				$weekends++;
			}
		}
		$running_hk = $days_in_month - $weekends;

		$prev_time = strtotime("-1 month", $time);
		$prev_tahun = (int) date('Y', $prev_time);
		$prev_bulan = (int) date('n', $prev_time);

		$dataRoles = [
			'MEDREP' => [
				'description' => 'MEDICAL REP',
				'target_dub' => 40,
			],
			'MRC' => [
				'description' => 'MEDICAL REP COORDINATOR',
				'target_dub' => 30,
			],
			'ASS' => [
				'description' => 'AREA SALES SUPERVISOR',
				'target_dub' => 30,
			],
			'ASM' => [
				'description' => 'AREA SALES MANAGER',
				'target_dub' => 20,
			],
			'SM' => [
				'description' => 'SALES MANAGER',
				'target_dub' => 20,
			],
		];
		$rolenames = array_keys($dataRoles);
		
		$sql_roles = "
			select * from app_role 
			where role_name in ('" . implode("','", $rolenames) . "')
		";
		$app_roles = $this->db->query($sql_roles)->result_array();
		
		$existing_role_names = array_column($app_roles, 'role_name');
		foreach ($dataRoles as $role_name => $r_info) {
			if (!in_array($role_name, $existing_role_names)) {
				$insert_data = [
					'role_name' => $role_name,
					'description' => $r_info['description'],
					'status' => 'RA',
					'created_by' => $createby,
					'created_date' => date('Y-m-d'),
				];
				$this->db->insert('app_role', $insert_data);
				$new_id = $this->db->insert_id();
				
				$app_roles[] = [
					'role_id' => $new_id,
					'role_name' => $role_name,
					'description' => $r_info['description'],
					'status' => 'RA',
				];
			}
		}
		
		$role_ids = array_column($app_roles, 'role_id');
		$target_map = [];
		if (!empty($role_ids)) {
			$sql_targets = "
				select role_id, tahun, bulan, target_dub, target_hk, target_call_dub, target_call_visit
				from role_mapping_target
				where role_id in (" . implode(",", $role_ids) . ")
				  and (
					(tahun = $tahun and bulan = $bulan)
					or (tahun = $prev_tahun and bulan = $prev_bulan)
				  )
			";
			$targets = $this->db->query($sql_targets)->result_array();
			foreach ($targets as $t) {
				$key = $t['role_id'] . '_' . $t['tahun'] . '_' . $t['bulan'];
				$target_map[$key] = $t;
			}
		}
		
		$result_roles = [];
		foreach ($app_roles as $ar) {
			$role_id = $ar['role_id'];
			$role_name = $ar['role_name'];
			
			$curr_key = $role_id . '_' . $tahun . '_' . $bulan;
			$prev_key = $role_id . '_' . $prev_tahun . '_' . $prev_bulan;
			
			$target_dub = null;
			$target_call_dub = null;
			$target_call_visit = null;
			$is_new_target = false;
			
			if (!empty($target_map[$curr_key])) {
				$t = $target_map[$curr_key];
				$target_dub = $t['target_dub'];
				$target_call_dub = $t['target_call_dub'];
				$target_call_visit = $t['target_call_visit'];
			} else {
				$is_new_target = true;
				if (!empty($target_map[$prev_key])) {
					$t = $target_map[$prev_key];
					$target_dub = $t['target_dub'];
					$target_call_dub = $t['target_call_dub'];
					$target_call_visit = $t['target_call_visit'];
				} else {
					$target_dub = !empty($dataRoles[$role_name]['target_dub']) ? $dataRoles[$role_name]['target_dub'] : 0;
					$target_call_dub = 2.5;
					$target_call_visit = 8;
				}
			}
			
			if (empty($target_dub)) {
				$target_dub = !empty($dataRoles[$role_name]['target_dub']) ? $dataRoles[$role_name]['target_dub'] : 0;
			}
			if (empty($target_call_dub)) {
				$target_call_dub = 2.5;
			}
			if (empty($target_call_visit)) {
				$target_call_visit = 8;
			}

			if ($is_new_target) {
				$insert_target = [
					'tahun' => $tahun,
					'bulan' => $bulan,
					'role_id' => $role_id,
					'target_dub' => $target_dub,
					'target_hk' => $running_hk,
					'target_call_dub' => $target_call_dub,
					'target_call_visit' => $target_call_visit,
					'created_by' => $createby,
					'created_date' => date('Y-m-d H:i:s'),
				];
				$this->db->insert('role_mapping_target', $insert_target);

				$prev_specialties = $this->db->query("
					select spesialisasi_id, nama_spesialisasi, target
					from m_sales_spesialis_target
					where role_id = ? and tahun = ? and bulan = ?
				", [$role_id, $prev_tahun, $prev_bulan])->result_array();

				if (!empty($prev_specialties)) {
					$this->db->where('role_id', $role_id);
					$this->db->where('tahun', $tahun);
					$this->db->where('bulan', $bulan);
					$this->db->delete('m_sales_spesialis_target');

					$insert_specialties = [];
					foreach ($prev_specialties as $ps) {
						$insert_specialties[] = [
							'tahun' => $tahun,
							'bulan' => $bulan,
							'role_id' => $role_id,
							'role_name' => $role_name,
							'spesialisasi_id' => $ps['spesialisasi_id'],
							'nama_spesialisasi' => $ps['nama_spesialisasi'],
							'target' => $ps['target'],
							'created_by' => $createby,
							'created_date' => date('Y-m-d H:i:s'),
						];
					}
					$this->db->insert_batch('m_sales_spesialis_target', $insert_specialties);
				}

				$prev_products = $this->db->query("
					select product_id, nama_invoice, target, target_qty
					from m_sales_produk_target
					where role_id = ? and tahun = ? and bulan = ?
				", [$role_id, $prev_tahun, $prev_bulan])->result_array();

				if (!empty($prev_products)) {
					$this->db->where('role_id', $role_id);
					$this->db->where('tahun', $tahun);
					$this->db->where('bulan', $bulan);
					$this->db->delete('m_sales_produk_target');

					$insert_products = [];
					foreach ($prev_products as $pp) {
						$insert_products[] = [
							'tahun' => $tahun,
							'bulan' => $bulan,
							'role_id' => $role_id,
							'role_name' => $role_name,
							'product_id' => $pp['product_id'],
							'nama_invoice' => $pp['nama_invoice'],
							'target' => $pp['target'],
							'target_qty' => $pp['target_qty'],
							'created_by' => $createby,
							'created_date' => date('Y-m-d H:i:s'),
						];
					}
					$this->db->insert_batch('m_sales_produk_target', $insert_products);
				}
			}

			$result_roles[] = [
				'role_id' => (string)$role_id,
				'role_name' => $role_name,
				'description' => $ar['description'],
				'status' => $ar['status'],
				'tahun' => (string) $tahun,
				'bulan' => (string) $bulan,
				'target_dub' => (string) $target_dub,
				'target_hk' => (string) $running_hk,
				'target_call_dub' => number_format((float) $target_call_dub, 1, '.', ''),
				'target_call_visit' => number_format((float) $target_call_visit, 1, '.', ''),
			];
		}
		
		if (count($result_roles) > 0) {
			return $result_roles;
		} else {
			return [];
		}
	}

    public function update_table($table, $data, $where)
    {
        $this->db->trans_begin();
        $this->db->where($where);
        $this->db->update($table, $data);

        $affected_rows = $this->db->affected_rows();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return result(null, 500, "Database error: " . $this->db->error()['message']);
        } else {
            $this->db->trans_commit();
            return result(array("affected_rows" => $affected_rows), 200, "Update successful.");
        }
    }

    public function execute_query_table($query_string)
    {
        $query = $this->db->query($query_string);
        if ($query === TRUE) {
            return result(array("affected_rows" => $this->db->affected_rows()), 200, "Query executed successfully.");
        } else if ($query === FALSE) {
            return result(null, 500, "Database error: " . $this->db->error()['message']);
        } else {
            return result($query->result_array(), 200, "Query executed successfully.");
        }
    }

    public function get_table($table, $columns = '*', $where = null)
    {
        $this->db->select($columns);
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get($table);

        if ($query === FALSE) {
            return result(null, 500, "Database error: " . $this->db->error()['message']);
        } else {
            return result($query->result_array(), 200, "Query executed successfully.");
        }
    }
}