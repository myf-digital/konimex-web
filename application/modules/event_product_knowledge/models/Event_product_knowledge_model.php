<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_product_knowledge_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('product_knowledge_event');
        if (!empty($id)) {
            $data['id_event'] = $id;
        }
        $data['start_period'] = $data['start_period'].' '.$data['start_time'];
        $data['end_period'] = $data['end_period'].' '.$data['end_time'];
	    unset($data['start_time'],$data['end_time']);

        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        return $this->db->insert('product_knowledge_event', $data);
    }

    public function update($data)
    {
		
        $data['start_period'] = $data['start_period'].' '.$data['start_time'];
        $data['end_period'] = $data['end_period'].' '.$data['end_time'];
	    unset($data['start_time'],$data['end_time']);
	
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);
        $this->db->where('id_event', $data['id_event']);
        return $this->db->update('product_knowledge_event', $data);
    }

    public function delete($data)
    {
        $this->db->where('id_event', $data['id_event']);
        return $this->db->delete('product_knowledge_event');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.* from product_knowledge_event a ) as a";
        return easy_pagging($data, $field, $table);
    }

}
