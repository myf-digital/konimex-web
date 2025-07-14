<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_mypassword_model extends CI_Model
{
	public function checkPasswordOld($resourceid, $password){
		$query = $this->db->get_where('app_resource', array('resource_id' => $resourceid, 'password' => md5($password)));

		if($query->num_rows() > 0){
            return true;
        } else {
        	return false;
        }
	}

	public function update($data) {
		$resourceid = $_SESSION['resource_id'];
		$checkPassword = $this->checkPasswordOld($resourceid, $data['old_password']);

		$form_response = new stdClass();
		if (!$checkPassword) {
			$form_response->status = 'error';
			$form_response->message = 'Password lama salah';
			return $form_response;
		}

		if ($data['new_password'] != $data['confir_password']) {
			$form_response->status = 'error';
			$form_response->message = 'Password baru tidak sama dengan password konfirmasi';
			return $form_response;
		}

		$this->db->where('resource_id', $resourceid);
		$result = $this->db->update('app_resource', array('password' => md5($data['new_password'])));

		if ($result) {
			$form_response->status = 'ok';
			$form_response->message = 'Berhasil mengganti password';
			return $form_response;
		} else {
			$form_response->status = 'error';
			$form_response->message = 'Gagal';
			return $form_response;
		}
	}
}
