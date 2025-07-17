<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Mapping_objective extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mapping_objective_model', 'mapping_objective');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {   
        $data = param_input();
        responseJSON($this->mapping_objective->load($data));
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_update()
    {
        $this->template->show($this, 'form_update');
    }

    public function create()
    {
        $data = param_input();
        response($this->mapping_objective->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->mapping_objective->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->mapping_objective->delete($data));
    }
    
    public function savetoxlsx()
    {
        $start = $this->uri->segment('3');
        $end = $this->uri->segment('4');

        ini_set("memory_limit","2048M");
        ini_set('max_execution_time', '0');
            
        $filename = "Mapping_Objective_".date('Ymd_His').".xlsx";
        $query = "
            select
                mo.siteid,
                mo.productid,
                mo.nama_invoice,
                mo.objective,
                mo.min_order,
                mo.start_periode,
                mo.end_periode,
                mo.keterangan,
                pd.ext_id1,
                pd.ext_id2,
                pd.barcode,
                pd.category_product,
                pd.nama_brand,
                pd.sat_kecil,
                pd.isi_kecil,
                pd.h_grosir,
                pd.h_ritel,
                pd.keterangan as product_keterangan,
                GROUP_CONCAT(DISTINCT mo2.nama_class ORDER BY mo2.nama_class SEPARATOR '||') AS accounts
            from mapping_objective mo
            left join m_product pd on pd.productid = mo.productid
            left join mapping_objective mo2 on mo2.productid = mo.productid and mo2.siteid = mo.siteid
            where mo.start_periode <= '".($start ?? today())."' and mo.end_periode >= '".($end ?? today())."'
            group by mo.productid
        ";
        $result = $this->db->query($query)->result_array();
        
        $this->load->library('excel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcelActive = $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcelActive->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcelActive->mergeCells('A1:L1')->getStyle('A1:L1');
        $objPHPExcelActive->getStyle('A1:L1')->getFont()->setBold(true);
        $objPHPExcelActive
            ->setCellValue('A2', 'Periode Start')
            ->setCellValue('B2', 'Periode End')
            ->setCellValue('C2', 'ID Produk')
            ->setCellValue('D2', 'Nama Produk')
            ->setCellValue('E2', 'Barcode')
            ->setCellValue('F2', 'Brand')
            ->setCellValue('G2', 'Variant')
            ->setCellValue('H2', 'Objective')
            ->setCellValue('I2', 'Account')
            ->setCellValue('J2', 'Keterangan')
            ->setCellValue('K2', 'HJP')
            ->setCellValue('L2', 'HNA')
        ;
        $objPHPExcelActive->getStyle('A2:L2')->getFont()->setBold(true);

        $i = 3;
        foreach ($result as $val) {
            $objective = $val['objective'] && $val['objective'] == 'Order Reguler Min Order' ? $val['objective'] . '|| (' . $val['min_order'] . ')' : $val['objective'];
            $objPHPExcelActive
                ->setCellValue('A'.$i, $val['start_periode'])
                ->setCellValue('B'.$i, $val['end_periode'])
                ->setCellValue('C'.$i, $val['productid'])
                ->setCellValue('D'.$i, $val['nama_invoice'])
                ->setCellValue('E'.$i, $val['barcode'])
                ->setCellValue('F'.$i, $val['nama_brand'])
                ->setCellValue('G'.$i, $val['category_product'])
                ->setCellValue('H'.$i, str_replace('||', "\n", $objective))
                ->setCellValue('I'.$i, str_replace('||', "\n", $val['accounts']))
                ->setCellValue('J'.$i, $val['keterangan'])
                ->setCellValue('K'.$i, $val['h_grosir'])
                ->setCellValue('L'.$i, $val['h_ritel'])
            ;
            $objPHPExcelActive->getStyle('H'.$i)->getAlignment()->setWrapText(true);
            $objPHPExcelActive->getStyle('I'.$i)->getAlignment()->setWrapText(true);
            $objPHPExcelActive->getStyle('K'.$i)->getNumberFormat()->setFormatCode('#,##0');
            $objPHPExcelActive->getStyle('L'.$i)->getNumberFormat()->setFormatCode('#,##0');
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
}
