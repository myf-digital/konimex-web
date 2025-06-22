<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_resource extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('resource_model', 'resource');
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
        response($this->resource->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->resource->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->resource->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->resource->load($data));
    }

    function call_area()
    {
        $data = param_input();
        $result = $this->resource->get_area($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

    function call_subarea()
    {
        $data = param_input();
        $result = $this->resource->get_subarea($data);
        if (200 == $result->code) {
            return response($result->result);
        } else {
            return response($result->result, $result->code, $result->message);
        }
    }

	public function cek_username() {
		$user = $this->input->post("username");
		$cek = $this->resource->cekusername($user);			
		if ($cek > 0) {
			$json = false;
		} else {
			$json = true; 
		}	
		echo json_encode($json);	
	}

    public function cek_username_update() {
		$json = true; 
		echo json_encode($json);	
	}

}
