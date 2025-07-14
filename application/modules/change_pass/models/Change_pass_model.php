<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_pass_model extends CI_Model
{

    public function update($data)
    {
        $this->db->where('idjabatan', $data['idjabatan']);
        return $this->db->update('app_resource', $data);
    }

}
