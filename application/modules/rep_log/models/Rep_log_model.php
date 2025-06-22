<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_log_model extends CI_Model
{
    function loadUserLog($data)
    {
        $sql = 'select a.*, b.name, c.jabatan, c.description from log_resource_activity a 
				left join app_resource b on a.username = b.username 
				left join ref_jabatan c on a.idjabatan = c.idjabatan 
				where log_type = "login" and a.periode between "'.$data['start'].'" and "'.$data['end'].'" ';

        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
