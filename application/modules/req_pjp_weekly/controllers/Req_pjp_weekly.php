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
            
        $filename = "PJP_weekly_".$filename.".xlsx";
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
        $objPHPExcelActive->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcelActive->mergeCells('A1:L1')->getStyle('A1:L1');
        $objPHPExcelActive->getStyle('A1:L1')->getFont()->setBold(true);
        $objPHPExcelActive->setCellValue('A1', 'Keterangan hari : 0: Minggu, 1: Senin, 2: Selasa, 3:Rabu, 4:Kamis, 5:Jumat, 6:Sabtu; Week Active '.$weekaktif)
            ->setCellValue('A3', 'Kode Medrep')
            ->setCellValue('B3', 'Nama Medrep')
            ->setCellValue('C3', 'Status')
            ->setCellValue('D3', 'Reason')
            ->setCellValue('E3', 'Keterangan')
            ->setCellValue('F3', 'Regional')
            ->setCellValue('G3', 'Area')
            ->setCellValue('H3', 'Outlet')
            ->setCellValue('I3', 'Minggu 1')
            ->setCellValue('J3', 'Minggu 2')
            ->setCellValue('K3', 'Minggu 3')
            ->setCellValue('L3', 'Minggu 4')
        ;
        $objPHPExcelActive->getStyle('A3:L3')->getFont()->setBold(true);

		$sqldetail = '
            select
                a.*,
                b.kode_outlet,
                b.nama_customer,
                b.mcc as dc,
                c.nama_class as account
            from req_pjp_weekly_detail a
            join m_customer b on b.customerid = a.customerid
            left join m_customer_class c on c.classid = b.classid
            where a.req_no=?
        ';

        $i = 4;
        foreach ($lovpjp as $vpjp) {
            $detail = $this->db->query($sqldetail, [$vpjp['req_no']])->result_array();
            $pjp_detail = $this->format_pjp_detail($detail);

            $outlet = isset($pjp_detail['outlet']) ? implode('\n', array_unique($pjp_detail['outlet'])) : '';

            $objPHPExcelActive->setCellValue('A'.$i, $vpjp['salesmanid'])
                ->setCellValue('B'.$i, $vpjp['salesman_name'])
                ->setCellValue('C'.$i, $vpjp['status_label'])
                ->setCellValue('D'.$i, $vpjp['reason'])
                ->setCellValue('E'.$i, $vpjp['keterangan'])
                ->setCellValue('F'.$i, $vpjp['nama_regional'])
                ->setCellValue('G'.$i, $vpjp['nama_area'])
                ->setCellValue('H'.$i, str_replace('\n', "\n", $outlet))
                ->setCellValue('I'.$i, isset($pjp_detail['minggu_1']) ? implode(',', array_unique($pjp_detail['minggu_1'])) : '')
                ->setCellValue('J'.$i, isset($pjp_detail['minggu_2']) ? implode(',', array_unique($pjp_detail['minggu_2'])) : '')
                ->setCellValue('K'.$i, isset($pjp_detail['minggu_3']) ? implode(',', array_unique($pjp_detail['minggu_3'])) : '')
                ->setCellValue('L'.$i, isset($pjp_detail['minggu_4']) ? implode(',', array_unique($pjp_detail['minggu_4'])) : '')
            ;
            $objPHPExcelActive->getStyle('H'.$i)->getAlignment()->setWrapText(true);
            $i++;
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
        $outletids = [];
        foreach ($data as $d) {
            if (!in_array('customerid', $outletids)) {
                $outlet = [];
                $outletids[] = $d['customerid'];
                if ($d['kode_outlet'] && $d['kode_outlet'] != '-') $outlet[] = $d['kode_outlet'];
                if ($d['nama_customer']) $outlet[] = $d['nama_customer'];
                if ($d['account']) $outlet[] = $d['account'];
                if ($d['dc']) $outlet[] = $d['dc'];
                $result['outlet'][] = implode('-', $outlet);
            }
            if ($d['minggu'] == '1') $result['minggu_1'][] = $d['hari'];
            if ($d['minggu'] == '2') $result['minggu_2'][] = $d['hari'];
            if ($d['minggu'] == '3') $result['minggu_3'][] = $d['hari'];
            if ($d['minggu'] == '4') $result['minggu_4'][] = $d['hari'];
        }
        return $result;
    }
}
