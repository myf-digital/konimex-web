<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
filter current must same lenngth with replace
*/
function easy_filter($data, $current = array(), $replace = array())
{
    if (isset($data['filterRules'])) {
        if (count($data['filterRules']) > 0) {
            $filter = json_decode($data['filterRules']);
            $values = array();
            foreach ($filter as $json) {
                $key = $json->field;
                for ($i = 0; $i < count($current); $i++) {
                    if ($current[$i] == $key) {
                        $json->field = $replace[$i];
                        break;
                    }
                }
                array_push($values, $json);
            }
            $data['filterRules'] = json_encode($values);
        }
    }
    // var_dump($data['filterRules']);
    return $data;
}

function easy_pagging($data, $fields, $table, $alias = array())
{
    $ci =& get_instance();

    $page  = isset($data['page']) ? (int)$data['page'] : 1;
    $limit = isset($data['rows']) ? (int)$data['rows'] : 30;
    $offset = ($page - 1) * $limit;

    $response = array();
    $values = array();
    $cond = '';

    // ================= FILTER =================
    if (!empty($data['filterRules'])) {
        $filter = json_decode($data['filterRules']);
        $loop = 0;

        foreach ($filter as $json) {
            $rules = get_object_vars($json);
            $optns = $rules['op'];
            $value = $rules['value'];
            $field = $rules['field'];

            if ($value !== '' && $value !== null) {
                $prefix = ($loop == 0) ? "WHERE" : "AND";

                switch ($optns) {
                    case 'contains':
                        $cond .= " $prefix $field LIKE ?";
                        $values[] = "%$value%";
                        break;

                    case 'greaterequal':
                        $cond .= " $prefix $field >= ?";
                        $values[] = $value;
                        break;

                    case 'lessequal':
                        $cond .= " $prefix $field <= ?";
                        $values[] = $value;
                        break;

                    case 'greater':
                        $cond .= " $prefix $field > ?";
                        $values[] = $value;
                        break;

                    case 'less':
                        $cond .= " $prefix $field < ?";
                        $values[] = $value;
                        break;

                    case 'notequal':
                        $cond .= " $prefix $field != ?";
                        $values[] = $value;
                        break;

                    case 'equal':
                        $cond .= " $prefix $field = ?";
                        $values[] = $value;
                        break;
                }

                $loop++;
            }
        }
    }

    // ================= SORT =================
    $sort  = $data['sort'] ?? '';
    $order = $data['order'] ?? '';
    $order_by = $sort ? "ORDER BY $sort $order" : "";

    // ================= COUNT =================
    $sql_count = "SELECT COUNT(1) as total FROM $table $cond";

    $count_values = $values;

    $response['total'] = $ci->db->query($sql_count, $count_values)->row()->total ?? 0;

    // ================= DATA =================
    $sql_rows = "SELECT $fields FROM $table $cond $order_by LIMIT ?, ?";

    $row_values = $values;
    $row_values[] = $offset;
    $row_values[] = $limit;

    $response['rows'] = $ci->db->query($sql_rows, $row_values)->result();

    return $response;
}

?>