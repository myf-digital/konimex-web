<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_rating extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_rating_model', 'rep_rating');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load()
    {
        $data = param_input();

        $result = $this->rep_rating->load($data);

        $no = 0;
        foreach ($result as $value) {
            $image = $this->rep_rating->load_outlet_image($value['customerid']);
            $result[$no]['image'] = $image != NULL ? $image->image : 'assets/images/DRC.png';
            $no++;
        }

        $resultCount = $this->rep_rating->load_count($data);

        responseJSON(['result' => $result, 'resultCount' => $resultCount, 'limit' => $data['limit'], 'offset' => $data['offset']]);
    }

    public function load_detail()
    {
        $data = param_input();

        $outlet = [
        	'id' => $data['id'],
        	'nama' => $data['nama'],
        	'star' => $data['star']
        ];

        $result = $this->rep_rating->load_detail($data);
        $resultCount = $this->rep_rating->load_detail_count($data);

        $x = 0;
        foreach ($result as $value) {
           $result[$x]['images'] = $this->rep_rating->load_detail_image($value['transaction_id']);
           $x++;
        }

        $option = [
            'id' => $data['id'],
            'filter' => $data['filter'],
            'resultCount' => $resultCount,
            'limit' => $data['limit'],
            'offset' => $data['offset'],
            'start' => $data['start'],
            'end' => $data['end']
        ];

        responseJSON(['result' => $result, 'outlet' => $outlet, 'option' => $option]);
    }

    public function load_detail_content()
    {
        $data = param_input();

        $result = $this->rep_rating->load_detail($data);
        $resultCount = $this->rep_rating->load_detail_count($data);

        $x = 0;
        foreach ($result as $value) {
           $result[$x]['images'] = $this->rep_rating->load_detail_image($value['transaction_id']);
           $x++;
        }

        $option = [
            'id' => $data['id'],
            'filter' => $data['filter'],
            'resultCount' => $resultCount,
            'limit' => $data['limit'],
            'offset' => $data['offset'],
            'start' => $data['start'],
            'end' => $data['end']
        ];
        
        responseJSON(['result' => $result, 'option' => $option]);
    }
}