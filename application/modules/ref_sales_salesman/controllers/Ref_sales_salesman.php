<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_sales_salesman extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_salesman_model', 'sales_salesman');
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
        response($this->sales_salesman->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sales_salesman->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sales_salesman->delete($data));
    }

    public function clear_token()
    {
        $data = param_input();
        response($this->sales_salesman->clear_token($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->load($data));
    }

	public function cek_user_gff() {
		$usergff = $this->input->post("salesmanid");
		$cek = $this->sales_salesman->cekusergff($usergff);			
		if ($cek > 0) {
			$json = false;
		} else {
			$json = true; 
		}	
		echo json_encode($json);	
	}

    public function cek_user_gff_update() {
		$json = true; 
		echo json_encode($json);	
	}

    public function salesman_org()
    {
        $this->template->show($this, 'salesman_org');
    }

    public function sync_salesman_area()
    {
        responseJSON($this->sales_salesman->sync_all_salesman_area());
    }

    public function data_salesman()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->load_salesman($data));
    }

    public function supervisor()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->supervisor($data));
    }

    public function spesialisasi()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->spesialisasi($data));
    }

    public function products()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->products($data));
    }

    public function get_spesialisasi_targets()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->get_spesialisasi_targets($data));
    }

    public function get_product_targets()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->get_product_targets($data));
    }

    public function get_sales_targets()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->get_sales_targets($data));
    }

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function download_template()
    {
        $filePath = FCPATH . 'assets/templates/Template-Mapping-TPE.xlsx';
        if (file_exists($filePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            echo "Template file not found.";
        }
    }

    public function upload()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $this->db->save_queries = FALSE;

        $fileName = $_FILES['fileupload']['name'];
        $fileTmp  = $_FILES['fileupload']['tmp_name'];
        $fileSize = $_FILES['fileupload']['size'];

        if (!isset($fileTmp)) {
            responseJson(['status' => false, 'message' => "File tidak ditemukan"]);
            return;
        }

        $allowedExt = ['xls', 'xlsx'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            responseJson(['status' => false, 'message' => "File harus Excel (.xls atau .xlsx)"]);
            return;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);

        $allowedMime = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($mime, $allowedMime)) {
            responseJson(['status' => false, 'message' => "Format file tidak valid"]);
            return;
        }

        if ($fileSize > 10 * 1024 * 1024) { // 10MB
            responseJson(['status' => false, 'message' => "File terlalu besar (max 10MB)"]);
            return;
        }
        
        $this->load->database();
        $this->db->trans_start();
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmp);
            $sheet = $spreadsheet->getActiveSheet()->toArray();

            $header = $sheet[2];
            $header = array_values(array_filter($header, function ($h) {
                return !is_null($h) && $h !== '';
            }));

            $expected = [
                'AREA',
                'NIK',
                'NAMA',
                'JABATAN',
                'KPP / AREA',
                'PARENT',
            ];

            if (array_map('strtoupper', $header) !== $expected) {
                responseJSON(['status' => false, 'message' => 'Format header tidak sesuai']);
                return;
            }
            
            $this->db->from('m_sales_salesman');
		    $this->db->where( 'aktif', '1');
            $salesmans = $this->db->get()->result_array();

            $areaareasite = $this->db->query("
                select maa.*, mar.nama_regional
                from m_area_areasite maa
                left join m_area_regional mar on mar.regionalid = maa.regionalid
            ", [])->result_array();

            $areasubarea = $this->db->query("
                select mas.*, maa.nama_area as areasite, mar.nama_regional
                from m_area_subarea mas
                left join m_area_areasite maa on maa.areaid = mas.areaid
                left join m_area_regional mar on mar.regionalid = mas.regionalid
            ", [])->result_array();

            $this->db->from('m_area_regional');
            $this->db->where('nama_regional', 'Pusat');
            $arearegional = $this->db->get()->row();

            $currentArea = null;
            $insertData = [];
            $updateData = [];
            $existingInsertIds = [];
            $existingUpdateIds = [];
            $now = date('Y-m-d H:i:s');
            foreach ($sheet as $index => $row) {
                if ($index < 3) continue;

                $regional = null;
                $areasite = null;
                $subarea = null;

                $area     = trim($row[0] ?? '');
                $nik      = '0'.str_replace(',','',trim($row[1] ?? ''));
                $nama     = trim($row[2] ?? $row[3] ?? $row[4] ?? $row[5] ?? $row[6] ?? $row[7] ?? '');
                $jabatan  = trim($row[8] ?? '');
                $kppArea  = trim($row[9] ?? '');
                $parent   = '0'.str_replace(',','',trim($row[10] ?? ''));
                $tipe_sales  = $this->tipe_sales($jabatan);
                if ($tipe_sales == 'MR') $tipe_sales = 'MEDREP';

                if ($nik === '' || $nik === '0') {
                    continue;
                }

                if ($area !== '' && !is_numeric($area)) {
                    $currentArea = $area;
                }
                
                if ($currentArea == 'Pusat') {
                    $regional = $arearegional->regionalid ?? null;
                    $areasite = null;
                    $subarea = null;
                }

                foreach ($areaareasite as $as) {
                    if (strtolower($as['nama_area']) == strtolower($currentArea)) {
                        $regional = $as['regionalid'] ?? null;
                        $areasite = $as['areaid'] ?? null;
                        $subarea = null;
                        break;
                    }
                }

                foreach ($areasubarea as $a) {
                    if (strpos(strtolower($kppArea), strtolower($a['nama_area'])) !== false && strtolower($a['areasite']) == strtolower($currentArea)) {
                        $regional = $a['regionalid'] ?? null;
                        $areasite = $a['areaid'] ?? null;
                        $subarea = $a['subareaid'] ?? null;
                        break;
                    }
                }

                $isUpdate = false;
                foreach ($salesmans as $sales) {
                    if ($sales['salesmanid'] == $nik) {
                        $isUpdate = true;
                        break;
                    }
                }

                if ($isUpdate) {
                    if (!isset($existingUpdateIds[$nik])) {
                        $updateData[] = [
                            'salesmanid'=> $nik,
                            'supervisorid' => $parent,
                            'nama_salesman' => $nama,
                            'tipe_sales' => $tipe_sales,
                            'jabatan' => $jabatan,
                            'kpp_area' => $kppArea,
                            'regionalid' => $regional,
                            'areaid' => $areasite,
                            'subareaid' => $subarea,
                            'aktif' => 1,
                            'modified_date' => $now,
                        ];
                        $existingUpdateIds[$nik] = true;
                    }
                } else {
                    if (!isset($existingInsertIds[$nik])) {
                        $insertData[] = [
                            'salesmanid'=> $nik,
                            'supervisorid' => $parent,
                            'nama_salesman' => $nama,
                            'tipe_sales' => $tipe_sales,
                            'jabatan' => $jabatan,
                            'kpp_area' => $kppArea,
                            'regionalid' => $regional,
                            'areaid' => $areasite,
                            'subareaid' => $subarea,
                            'aktif' => 1,
                            'password' => md5('123456'),
                            'created_date' => $now,
                        ];
                        $existingInsertIds[$nik] = true;
                    }
                }
            }
            if (!empty($insertData)) {
                $this->db->insert_batch('m_sales_salesman', $insertData);
            }
            if (!empty($updateData)) {
                $this->db->update_batch('m_sales_salesman', $updateData, 'salesmanid');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                responseJson(['status' => false, 'message' => 'Gagal simpan data']);
                return;
            }

            $rowNumber = count($insertData) + count($updateData);
            responseJson([
                'status' => true,
                'message' => "Import selesai. Total row: {$rowNumber}",
                'insert' => count($insertData),
                'update' => count($updateData),
            ]);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            responseJson(['status' => false, 'message' => "Error: " . $e->getMessage()]);
        }
    }

    public function tipe_sales($name)
    {
        preg_match_all('/\b\w/', $name, $matches);
        $tipe_sales = strtoupper(implode('', $matches[0]));
        return $tipe_sales;
    }

    public function getLevel($value, $levels) {
        foreach ($levels as $key => $items) {
            if (in_array($value, $items)) {
                return $key;
            }
        }
        return null;
    }

    public function detail_mapping()
    {
        $salesmanid = $this->input->post('salesmanid');
        responseJSON($this->sales_salesman->detail_mapping($salesmanid));
    }
}
