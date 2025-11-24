<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_absen_salesman extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ref_absen_salesman_model', 'absen');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function create()
    {
		$message = 'Success';
        $data = param_input();

        //file upload
        if (isset($_FILES["fileupload"])) {
			if ($_FILES['fileupload']['size'] > (2 * 1024 * 1024)) {
				return response("File terlalu besar. Maksimal 2MB.");
			}

			$path = FCPATH.'uploads/absence/'.date('Ym').'/';
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}

			$file_name = $_FILES["fileupload"]["name"];
			$tmp = explode('.', $file_name);
			$file_extension = end($tmp);

	        $fileName = date('YmdHis').'-'.$data['salesmanid'].'.'.$file_extension; 
			$config['upload_path']   = $path;
			$config['allowed_types'] = 'jpg|jpeg|png|webp|heic|heif';
			$config['max_size']      = 2048;
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('fileupload')) {
				$dataUpload = $this->upload->data();

				$source = $dataUpload['full_path'];
				$dest   = $dataUpload['file_path'] . $fileName;

				$this->load->helper('image');

				$compressed = compress_image($source, $dest);
				if ($compressed) {
	        		$data['image'] = $compressed;
				} else {
					$message = "Gagal compress.";
				}

			} else {
				$message = $this->upload->display_errors();
			}
        }

		if ($message != 'Success') {
			return response($message);
		}

        response($this->absen->create($data), 200, $message);
    }

    public function update()
    {
		$message = 'Success';
        $data = param_input();

        //file upload
        if (isset($_FILES["fileupload"])) {
			if ($_FILES['fileupload']['size'] > (2 * 1024 * 1024)) {
				return response("File terlalu besar. Maksimal 2MB.");
			}

			$path = FCPATH.'uploads/absence/'.date('Ym').'/';
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}

			$file_name = $_FILES["fileupload"]["name"];
			$tmp = explode('.', $file_name);
			$file_extension = end($tmp);

	        $fileName = date('YmdHis').'-'.$data['salesmanid'].'.'.$file_extension; 
			$config['upload_path']   = $path;
			$config['allowed_types'] = 'jpg|jpeg|png|webp|heic|heif';
			$config['max_size']      = 2048;
			$this->load->library('upload', $config);

			if ($this->upload->do_upload('fileupload')) {
				$dataUpload = $this->upload->data();

				$source = $dataUpload['full_path'];
				$dest   = $dataUpload['file_path'] . $fileName;

				$this->load->helper('image');

				$compressed = compress_image($source, $dest);
				if ($compressed) {
	        		$data['image'] = $compressed;
				} else {
					$message = "Gagal compress.";
				}

			} else {
				$message = $this->upload->display_errors();
			}
        }

		if ($message != 'Success') {
			return response($message);
		}

        response($this->absen->update($data), 200, $message);
    }

    public function delete()
    {
        $data = param_input();
        response($this->absen->delete($data));
    }

    public function load()
    {
        $data = param_input();
		echo $this->db->last_query();
        responseJSON($this->absen->load($data));
    } 

}
