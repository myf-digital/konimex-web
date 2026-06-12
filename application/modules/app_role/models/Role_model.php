<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_role');
        if (!empty($id)) {
            $data['role_id'] = $id;
        }
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        $data["created_by"] = $data["usersession"];
        
        $data['description'] = $data['role_name'] == 'other' ? null : $data['role_name_desc'];
        $data['role_name'] = $data['role_name'] == 'other' ? $data['role_name_desc'] : $data['role_name'];
        $payload = $this->generatePayload($data);
        return $this->db->insert('app_role', $payload);
    }

    public function update($data)
    {
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        $data["modified_by"] = $data["usersession"];

        $data['description'] = $data['role_name'] == 'other' ? null : $data['role_name_desc'];
        $data['role_name'] = $data['role_name'] == 'other' ? $data['role_name_desc'] : $data['role_name'];
        $payload = $this->generatePayload($data);
        $this->db->where('role_id', $data['role_id']);
        return $this->db->update('app_role', $payload);
    }

    public function delete($data)
    {
        $this->db->where('role_id', $data['role_id']);
        return $this->db->delete('app_role');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_role a order by a.modified_date desc, a.target_dub desc';
        return easy_pagging($data, $field, $table);
    }

    public function generatePayload($data)
    {
        $fields = [
            'role_name',
            'status',
            'target_dub',
            'created_by',
            'created_date',
            'modified_by',
            'modified_date'
        ];
        $payload = payload($fields, $data);
        $payload['description'] = $data['description'];
        return $payload;
    }
}
