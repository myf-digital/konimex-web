<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Rep_top_rating extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Rep_top_rating_model', 'top_rating');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function load()
    {
        $data = param_input();

        $result = $this->top_rating->load($data);

        $html ='<div class="box-body"><h3>Top Rating '.$data['top'].'</h3>';
        $html .= '<div class="container-table">';
        $html .= '<table class="table table-bordered table-condensed fixed-table">';
        $html .= '<tbody>';
        $html .= '<tr>';
        $html .= '<th style="width: 80px">No</th>';
        $html .= '<th style="width: 150px">ID Outlet</th>';
        $html .= '<th style="width: 200px">Kode Outlet</th>';
        $html .= '<th style="width: 300px">Outlet</th>';
        $html .= '<th style="width: 200px">Regional</th>';
        $html .= '<th style="width: 200px">Area</th>';
        // $html .= '<th style="width: 200px">City</th>';
        $html .= '<th style="width: 200px">Rating</th>';
        $html .= '</tr>';
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        $html .= '<div class="container-table-content">';
        $html .= '<table class="table table-bordered table-condensed fixed-table table-hover">';
        $html .= '<tbody>';
        $i=1;
        foreach ($result as $value) {
            $html .= '<tr class="clickable-row" data-id="'.$value['customerid'].'" data-nama="'.$value['nama_customer'].'" data-star="'.$value['rating_star'].'">';
            $html .= '<td style="width: 80px">'.$i.'</td>';
            $html .= '<td style="width: 150px">'.$value['customerid'].'</td>';
            $html .= '<td style="width: 200px">'.$value['kode_outlet'].'</td>';
            $html .= '<td style="width: 300px">'.$value['nama_customer'].'</td>';
            $html .= '<td style="width: 200px">'.$value['regional'].'</td>';
            $html .= '<td style="width: 200px">'.$value['area'].'</td>';
            // $html .= '<td style="width: 200px">'.$value['city'].'</td>';
            $html .= '<td style="width: 200px">'.$value['rating_star'].$this->star(round($value['rating_star'])).'</td>';
            $i++;
        }
        $html .= '</tbody>';
        $html .= '</table></div></div>';
        $html .= '<script type="text/javascript">
                $(".container-table-content").on("scroll", function() {
                    $(".container-table").scrollLeft($(this).scrollLeft());
                });
                $(".container-table").on("scroll", function() {
                    $(".container-table-content").scrollLeft($(this).scrollLeft());
                });
            </script>';
                
        echo $html;
    }

    public function load_detail()
    {
        $data = param_input();

        $outlet = [
        	'id' => $data['id'],
        	'nama' => $data['nama'],
        	'star' => $data['star']
        ];

        $result = $this->top_rating->load_detail($data);
        $resultCount = $this->top_rating->load_detail_count($data);

        $x = 0;
        foreach ($result as $value) {
           $result[$x]['images'] = $this->top_rating->load_detail_image($value['transaction_id']);
           $x++;
        }

        $option = [
            'id' => $data['id'],
            'filter' => $data['filter'],
            'resultCount' => $resultCount,
            'limit' => $data['limit'],
            'offset' => $data['offset']
        ];

        responseJSON(['result' => $result, 'outlet' => $outlet, 'option' => $option]);
    }

    public function load_detail_content()
    {
        $data = param_input();

        $result = $this->top_rating->load_detail($data);
        $resultCount = $this->top_rating->load_detail_count($data);

        $x = 0;
        foreach ($result as $value) {
           $result[$x]['images'] = $this->top_rating->load_detail_image($value['transaction_id']);
           $x++;
        }

        $option = [
            'id' => $data['id'],
            'filter' => $data['filter'],
            'resultCount' => $resultCount,
            'limit' => $data['limit'],
            'offset' => $data['offset']
        ];
        
        responseJSON(['result' => $result, 'option' => $option]);
    }

    public function star($star)
    {
        if ($star == 5) {
            $html = ' (';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= ')';

            return $html;
        } else if ($star == 4) {
            $html = ' (';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= ')';

            return $html;
        } else if ($star == 3) {
            $html = ' (';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= ')';

            return $html;
        } else if ($star == 2) {
            $html = ' (';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= ')';

            return $html;
        } else if ($star == 1) {
            $html = ' (';
            $html .= '<span class="fa fa-star checked"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= '<span class="fa fa-star"></span>';
            $html .= ')';

            return $html;
        }
    }

    public function savetoxlsx($data)
    {
        ini_set('memory_limit', '512M');

        $idjabatan = $this->uri->segment('3');
        $usersession = $this->uri->segment('4');
        $restrict_level = $this->uri->segment('5');

        $top = $this->uri->segment('6');
        $gff = $this->uri->segment('7');
        $search = $this->uri->segment('8');

        $data = [
            'search' => is_null($search) ? '' : $search,
            'top' => $top,
            'gff' => $gff
        ];

        $result = $this->top_rating->load($data);

        $filename = "Report_top_rating ".$top;

        $spreadsheet = new Spreadsheet();

        $header = ['No', 'ID Outlet', 'Kode Outlet', 'Outlet', 'Regional', 'Area', 'Rating'];

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'List Report Top Rating '.$top);

        $sheet->fromArray($header,NULL,'A2');

        $i = 1;
        $row = 3;
        foreach ($result as $value) {

            $content = [
                $i, 
                $value['customerid'],
                $value['kode_outlet'],
                $value['nama_customer'],
                $value['regional'],
                $value['area'],
                // $value['city'],
                round($value['rating_star'])
            ];

            $sheet->fromArray($content,NULL,'A'.$row);

            $i++;
            $row++;
        }
 
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');                              
    }
}