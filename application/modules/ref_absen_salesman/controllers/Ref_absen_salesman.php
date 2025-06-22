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
        $data = param_input();

        //file upload
        if (isset($_FILES["fileupload"])) {

        	$path = DIR_IMAGE_PATH.'absence/'.date('Ym');

        	if (!file_exists($path)) {
        		mkdir($path);
        	}

        	$file_name = $_FILES["fileupload"]["name"];
			$tmp = explode('.', $file_name);
			$file_extension = end($tmp);

	        $fileName = date('YmdHis').'-'.$data['salesmanid']; 
	        $config['upload_path'] = $path;
	        $config['allowed_types'] = 'jpg|png|jpeg';
	        $config['file_name'] = $fileName;
	        $config['max_size'] = '2048';
	        $config['max_width'] = '0';
	        $config['max_height'] = '0';
	         
	        $this->load->library('upload', $config);
	        $this->upload->initialize($config);

	        if(! $this->upload->do_upload('fileupload') )
	        {
	            echo $this->upload->display_errors();
	            return;
	        }

	        $data['image'] = 'absence/'.date('Ym').'/'.$fileName.'.'.$file_extension;
        }

        response($this->absen->create($data));
    }

    public function update()
    {
        $data = param_input();

        //file upload
        if (isset($_FILES["fileupload"])) {

        	$path = DIR_IMAGE_PATH.'absence/'.date('Ym');

        	if (!file_exists($path)) {
        		mkdir($path);
        	}

        	$file_name = $_FILES["fileupload"]["name"];
			$tmp = explode('.', $file_name);
			$file_extension = end($tmp);

	        $fileName = date('YmdHis').'-'.$data['salesmanid']; 
	        $config['upload_path'] = $path;
	        $config['allowed_types'] = 'jpg|png|jpeg';
	        $config['file_name'] = $fileName;
	        $config['max_size'] = '2048';
	        $config['max_width'] = '0';
	        $config['max_height'] = '0';
	         
	        $this->load->library('upload', $config);
	        $this->upload->initialize($config);

	        if(! $this->upload->do_upload('fileupload') )
	        {
	            echo $this->upload->display_errors();
	            return;
	        }

	        $data['image'] = 'absence/'.date('Ym').'/'.$fileName.'.'.$file_extension;
        }

        response($this->absen->update($data));
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
