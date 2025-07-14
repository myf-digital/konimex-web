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
//    if (empty($data)) return array();
    $ci =& get_instance();
    $response = array();
    $values = array();
    $cond = '';
    if (isset($data['filterRules']) && !empty($data['filterRules'])) {
        $filter = json_decode($data['filterRules']);
        //var_dump($filter);
        $loop = 0;
        foreach ($filter as $json) {
            $rules = get_object_vars($json);
            $optns = $rules['op'];
            $value = $rules['value'];
            $field = $rules['field'];
            if (!empty($value)) {
                if ('contains' == $optns) {
                    $cond .= (0 == $loop ? "where $field like ? " : "and $field like ? ");
                    array_push($values, "%$value%");
                } else if ('greaterequal' == $optns) {
                    $cond .= (0 == $loop ? "where $field => ? " : "and $field > ? ");
                    array_push($values, "$value");
                } else if ('lessequal' == $optns) {
                    $cond .= (0 == $loop ? "where $field <= ? " : "and $field < ? ");
                    array_push($values, "$value");
                } else if ('greater' == $optns) {
                    $cond .= (0 == $loop ? "where $field > ? " : "and $field > ? ");
                    array_push($values, "$value");
                } else if ('less' == $optns) {
                    $cond .= (0 == $loop ? "where $field < ? " : "and $field < ? ");
                    array_push($values, "$value");
                } else if ('notequal' == $optns) {
                    $cond .= (0 == $loop ? "where $field != ? " : "and $field != ? ");
                    array_push($values, "$value");
                } else if ('equal' == $optns) {
                    $cond .= (0 == $loop ? "where $field = ? " : "and $field = ? ");
                    array_push($values, "$value");
                }
                $loop++; // flag where
            }
        }
        $cond = trim($cond);
    }
    $sort = (empty($data['sort']) ? "" : $data['sort']);
    $order = (empty($data['order']) ? "" : $data['order']);
    $order_by = (empty($sort) ? "" : "order by $sort $order");
    $fields = trim($fields);
    if (isset($data['page']) && isset($data['rows'])) {
        $sql_count = "select count(1) as total from $table $cond";
        $response['total'] = $ci->db->query(trim($sql_count), $values)->result_array()[0]["total"];
        // get row paging
        $offset = intval(($data['page'] - 1) * $data['rows']);
        array_push($values, $offset, intval($data['rows']));
        $sql_rows = "select $fields from $table $cond $order_by limit ?, ?";
        $response['rows'] = $ci->db->query(trim($sql_rows), $values)->result();
    } else {
        $sql_rows = "select $fields from $table $cond $order_by";
        $response['total'] = null;
        $response['rows'] = $ci->db->query(trim($sql_rows), $values)->result();
    }
    return $response;
}

?>