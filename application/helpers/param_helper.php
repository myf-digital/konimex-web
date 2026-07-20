<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

function param_input()
{
    $ci =& get_instance();
    $param = $ci->input->post(NULL, TRUE);
    if (!$param) {
        $param = json_decode(file_get_contents('php://input'), true);;
    }
    return $param;
}

function upload_wrapper($input_name)
{
    $CI = &get_instance();
    $temp_array = array();
    foreach ($_FILES[$input_name] as $key => $val) {
        $i = 0;
        foreach ($val as $new_key) {
            $temp_array[$i][$key] = $new_key;
            $i++;
        }
    }
    $i = 0;
    foreach ($temp_array as $key => $val) {
        $_FILES['file' . $i] = $val;
        $i++;
    }
    #clear the original array;
    unset($_FILES[$input_name]);
    // return $temp_array;
    $config = array(
        'upload_path' => dirname($_SERVER["SCRIPT_FILENAME"]) . "/uploads/",
        'upload_url' => base_url() . "uploads/",
        'allowed_types' => "gif|jpg|png|jpeg|pdf|doc|docx|xml",
        'overwrite' => TRUE,
        'max_size' => "10000KB",
        'max_height' => "768",
        'max_width' => "1024"
    );
    $CI->load->library('upload', $config);
    foreach ($_FILES as $key => $value) {
        if (!empty($value['name'])) {
            if ($CI->upload->do_upload($key)) {
                $CI->upload->data();
            }
        }
    }
}

function filer_properties(){
    return array(
        'limit' => null, //Maximum Limit of files. {null, Number}
        'maxSize' => 10, //Maximum Size of files {null, Number(in MB's)}
        'extensions' => null, //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
        'required' => false, //Minimum one file is required for upload {Boolean}
        'uploadDir' => DIR_DOC, //Upload directory {String}
        'title' => array('{{file_name}}_{{timestamp}}', 100), //New file name {null, String, Array} *please read documentation in README.md
        'removeFiles' => true, //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
        'replace' => false, //Replace the file if it already exists {Boolean}
        'onRemove' => 'onFilesRemoveCallback' //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
    );
}

if (!function_exists('img_url_to_sheet')) {
	function img_url_to_sheet($sheet, $imageUrl, $cellCoordinate) {
		$message = null;
		try {
			// Verify URL is valid
			if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
				$message = "Error addImageFromUrlToSheet: Invalid URL";
			}
			
			// Get image contents
			$imageData = @file_get_contents($imageUrl);
			if ($imageData === false) {
				$message = "Error addImageFromUrlToSheet: Could not download image";
			}
			
			// Create temporary file
			$tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
			if (file_put_contents($tempFile, $imageData) === false) {
				$message = "Error addImageFromUrlToSheet: Could not create temporary file";
			}
			
			// Verify it's a valid image
			if (!@getimagesize($tempFile)) {
				$message = "Error addImageFromUrlToSheet: Downloaded file is not a valid image";
			}

			if ($message) {
				log_message("error", $message);
				return $message;
			}
			
			// Add image to worksheet
			$drawing = new Drawing();
			$drawing->setPath($tempFile);
			$drawing->setCoordinates($cellCoordinate);
			$drawing->setWorksheet($sheet);
			$drawing->setWidth(125);

			return $drawing;
		} catch (Exception $e) {
			// Clean up temp file if it exists
			if (isset($tempFile) && file_exists($tempFile)) {
				@unlink($tempFile);
			}
			$message = "Error addImageFromUrlToSheet: " . $e->getMessage();
			log_message("error", $message);
			return $message;
		}
	}
}

if (!function_exists('today')) {
	function today() {
        return date('Y-m-d');
    }
}

if (!function_exists('payload')) {
    function payload($fields = [], $data = []) {
        $result = [];
        foreach ($fields as $f) {
            if (isset($data[$f]) && $data[$f]) $result[$f] = $data[$f];
        }
        return $result;
    }
}

if (!function_exists('get_salesman_restrict')) {
    function get_salesman_restrict($usersession, $restrict_level) {
        $CI =& get_instance();
        $CI->load->database();

        $restrictions = $CI->db->query("
            select b.regionalid, b.areaid, b.subareaid 
            from app_resource a 
            join app_restrict_location b on a.resource_id=b.resource_id 
            where a.username=?
        ", [$usersession])->result_array();

        if (empty($restrictions)) {
            return null;
        }

        $where = [];
        foreach ($restrictions as $r) {
            $rowCond = [];
            if (!empty($r['subareaid'])) {
                $rowCond[] = "msa.subareaid = '" . $CI->db->escape_str($r['subareaid']) . "'";
            } else if (!empty($r['areaid'])) {
                $rowCond[] = "msa.areaid = '" . $CI->db->escape_str($r['areaid']) . "'";
            } else if (!empty($r['regionalid'])) {
                $rowCond[] = "msa.regionalid = '" . $CI->db->escape_str($r['regionalid']) . "'";
            }
            if (!empty($rowCond)) {
                $where[] = "(" . implode(" AND ", $rowCond) . ")";
            }
        }

        if (empty($where)) {
            return null;
        }

        return "
            select distinct msa.salesmanid 
            from m_salesman_area msa
            where " . implode(" OR ", $where) . "
        ";
    }
}
?>
