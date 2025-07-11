<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Req_pjp_weekly extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Req_pjp_weekly_model', 'pjp_weekly');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {   
        $data = param_input();
        responseJSON($this->pjp_weekly->load($data));
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function update()
    {
        $data = param_input();
        response($this->pjp_weekly->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->pjp_weekly->delete($data));
    }
    
    public function savetoxlsx()
    {
        $salesmanid = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $restrict_level = $this->uri->segment('5');

        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');
        
        if ($salesmanid=='' or empty($salesmanid) or $salesmanid=='null'){
            $filename='All_Salesman';
            if ($restrict_level=='4') {
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$username."')
                        )";
            } else if ($restrict_level=='3') {
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$username."')
                        )";
            } else if ($restrict_level=='2') {
                $strquery = " where a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$username."')
                        ) ";
            } else {
                $strquery = "";
            }
        } else {
            $filename = $salesmanid;
            $strquery = "where a.salesmanid = '$salesmanid'";
        }
            
        $filename = "PJP_".$filename.".xlsx";
        $query = "select
                a.*,
                b.tipe_sales,
                c.nama_regional,
                d.nama_area,
                case
                    when a.status=5 then 'Rejected'
                    when a.status=3 then 'Approved'
                    else 'Pending'
                end as status_label
            from req_pjp_weekly a
            left join m_sales_salesman b on b.salesmanid=a.salesmanid
            left join m_area_regional c on c.regionalid = b.regionalid
            left join m_area_areasite d on d.areaid = b.areaid
            ".$strquery."
        ";

        $execquery = $this->db->query($query);
        $lovpjp = $execquery->result_array();
        
        $rowweek = $this->db->query("select aktif_week from m_setup_site limit 0,1")->row();
        $weekaktif = $rowweek->aktif_week;
        $this->load->library('excel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcelActive = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcelActive->mergeCells('A1:G1')->getStyle('A1:G1');
        $objPHPExcelActive->getStyle('A1:G1')->getFont()->setBold(true);
        $objPHPExcelActive->setCellValue('A1', 'Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu; Week Active '.$weekaktif)
            ->setCellValue('A3', 'Kode Salesman')
            ->setCellValue('B3', 'Nama Salesman')
            ->setCellValue('C3', 'Status')
            ->setCellValue('D3', 'Reason')
            ->setCellValue('E3', 'Keterangan')
            ->setCellValue('F3', 'Regional')
            ->setCellValue('G3', 'Area')
        ;
        $objPHPExcelActive->getStyle('A3:G3')->getFont()->setBold(true);

		$sqldetail = '
            select
                a.*,
                b.kode_outlet,
                b.nama_customer
            from req_pjp_weekly_detail a
            join m_customer b on b.customerid = a.customerid
            where a.req_no=?
        ';

        $i = 4;
        foreach ($lovpjp as $vpjp) {
            $objPHPExcelActive->setCellValue('A'.$i, $vpjp['salesmanid'])
                ->setCellValue('B'.$i, $vpjp['salesman_name'])
                ->setCellValue('C'.$i, $vpjp['status_label'])
                ->setCellValue('D'.$i, $vpjp['reason'])
                ->setCellValue('E'.$i, $vpjp['keterangan'])
                ->setCellValue('F'.$i, $vpjp['nama_regional'])
                ->setCellValue('G'.$i, $vpjp['nama_area']);

            $objPHPExcelActive->mergeCells('A'.($i+1).':G'.($i+1))
                ->getStyle('A'.($i+1).':G'.($i+1))
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcelActive->setCellValue('A'.($i+1), 'Detail PJP Weekly')
                    ->setCellValue('B'.($i+2), 'Kode Outlet')
                    ->setCellValue('C'.($i+2), 'Nama Outlet')
                    ->setCellValue('D'.($i+2), 'Minggu 1')
                    ->setCellValue('E'.($i+2), 'Minggu 2')
                    ->setCellValue('F'.($i+2), 'Minggu 3')
                    ->setCellValue('G'.($i+2), 'Minggu 4');

            $detail = $this->db->query($sqldetail, [$vpjp['req_no']])->result_array();
            $pjp_detail = $this->format_pjp_detail($detail);
            foreach ($pjp_detail as $x => $pd) {
                $objPHPExcelActive->setCellValue('B'.($i+$x+3), $pd['kode_outlet'] ?? '')
                    ->setCellValue('C'.($i+$x+3), $pd['nama_customer'] ?? '')
                    ->setCellValue('D'.($i+$x+3), implode(',', $pd['minggu_1']))
                    ->setCellValue('E'.($i+$x+3), implode(',', $pd['minggu_2']))
                    ->setCellValue('F'.($i+$x+3), implode(',', $pd['minggu_3']))
                    ->setCellValue('G'.($i+$x+3), implode(',', $pd['minggu_4']));
            }
            $i = $i + 5;
        }

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=0');
        // If you're serving to IE over SSL, then the following may be needed
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        unset($objPHPExcel);
        return true;
    }

    private function format_pjp_detail($data)
    {
        $result = [];
        foreach ($data as $d) {
            if (in_array($d['customerid'], array_column($result, 'customerid'))) {
                $idx = array_search($d['customerid'], array_column($result, 'customerid'));
                if ($d['minggu'] == '1') $result[$idx]['minggu_1'][] = $d['hari'];
                if ($d['minggu'] == '2') $result[$idx]['minggu_2'][] = $d['hari'];
                if ($d['minggu'] == '3') $result[$idx]['minggu_3'][] = $d['hari'];
                if ($d['minggu'] == '4') $result[$idx]['minggu_4'][] = $d['hari'];
            } else {
                $new_data = [
                    'customerid' => $d['customerid'],
                    'kode_outlet' => $d['kode_outlet'],
                    'nama_customer' => $d['nama_customer'],
                    'minggu_1' => [],
                    'minggu_2' => [],
                    'minggu_3' => [],
                    'minggu_4' => [],
                ];
                if ($d['minggu'] == '1') $new_data['minggu_1'][] = $d['hari'];
                if ($d['minggu'] == '2') $new_data['minggu_2'][] = $d['hari'];
                if ($d['minggu'] == '3') $new_data['minggu_3'][] = $d['hari'];
                if ($d['minggu'] == '4') $new_data['minggu_4'][] = $d['hari'];

                $result[] = $new_data;
            }
        }
        return $result;
    }
}
