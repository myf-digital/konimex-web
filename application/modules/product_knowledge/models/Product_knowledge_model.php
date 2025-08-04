<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_knowledge_model extends CI_Model
{
    public function create($data)
    {
        $this->load->library('upload');

        if (isset($data['brand'])) {
            $brand = explode('||', $data['brand']);
            $data['brandid'] = $brand[0] ?? 0;
            $data['brand'] = $brand[1] ?? null;
        }
        $payload = payload([
            'siteid',
            'judul',
            'brandid',
            'brand',
        ], $data);

        $payload["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $payload["created_date"] = $datetime->datetime;

        $files = $data['files'];
        $count = count($files['name']);

        if ($count > 0) {
            $path = FCPATH . 'uploads/files/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $fileUploaded = [];
        for ($i = 0; $i < $count; $i++) {
            $_FILES['file']['name'] = $files['name'][$i];
            $_FILES['file']['type'] = $files['type'][$i];
            $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['file']['error'] = $files['error'][$i];
            $_FILES['file']['size'] = $files['size'][$i];

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            if (!in_array(strtolower($ext), $allowed)) continue; // skip invalid

            $config['upload_path'] = './uploads/product_knowledge/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $upload = $this->upload->data();
                $fileUploaded[] = 'uploads/product_knowledge/' . $upload['file_name'];
            } else {
                log_message('error', 'Upload Product Knowledge error: ' . $this->upload->display_errors('', ''));
            }
        }
        if (count($fileUploaded) > 0) $payload['files'] = implode('||', $fileUploaded);

        return $this->db->insert('product_knowledge', $payload);
    }

    public function update($data)
    {
        $this->load->library('upload');

        if (isset($data['brand'])) {
            $brand = explode('||', $data['brand']);
            $data['brandid'] = $brand[0] ?? 0;
            $data['brand'] = $brand[1] ?? null;
        }
        $payload = payload([
            'siteid',
            'judul',
            'brandid',
            'brand',
        ], $data);
	
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        
        $files = $data['files'];
        $count = count($files['name']);

        if ($count > 0) {
            $path = FCPATH . 'uploads/product_knowledge/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        $fileUploaded = [];
        for ($i = 0; $i < $count; $i++) {
            $_FILES['file']['name'] = $files['name'][$i];
            $_FILES['file']['type'] = $files['type'][$i];
            $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['file']['error'] = $files['error'][$i];
            $_FILES['file']['size'] = $files['size'][$i];

            $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            if (!in_array(strtolower($ext), $allowed)) continue; // skip invalid

            $config['upload_path'] = './uploads/product_knowledge/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf';
            $config['max_size'] = 10240; // 10MB
            $config['encrypt_name'] = TRUE;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $upload = $this->upload->data();
                $fileUploaded[] = 'uploads/product_knowledge/' . $upload['file_name'];
            } else {
                log_message('error', 'Upload Product Knowledge error: ' . $this->upload->display_errors('', ''));
            }
        }
        if (count($fileUploaded) > 0) $payload['files'] = implode('||', $fileUploaded);

        $this->db->where('id', $data['id']);
        return $this->db->update('product_knowledge', $payload);
    }

    public function delete($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->delete('product_knowledge');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.* from product_knowledge a ) as a";
        return easy_pagging($data, $field, $table);
    }

}
