<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// result for db/ models to return controller
function result($data, $code = 200, $message = "Success")
{
    $response = new stdClass();
    if (isset($data)) {
        $response->code = $code;
        $response->message = $message;
        $response->result = $data;
    } else {
        $response->code = $code;
        $response->message = $message;
    }
    return $response;
}

// result for controller return client
function response($data, $code = 200, $message = "Success")
{
    header('Content-Type: application/json');
    $response = new stdClass();
    if (isset($data)) {
        $response->code = $code;
        $response->message = $message;
        $response->result = $data;
    } else {
        $response->code = $code;
        $response->message = $message;
        $response->result = $data;
    }
    echo json_encode($response);
}

function responseJSON($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
}

// VALIDATION VALUE

function validArrayValue($value, $key)
{
    return (isset($value[$key]) && !(is_null($value[$key]) || empty($value[$key])) && is_array($value[$key]));
}

function validKeyValue($value, $key)
{
    return (property_exists($value, $key)); //&& !(is_null($value->$key) || empty($value->$key))
}

function excludeValueForCreate($values, $index)
{
    foreach ($values[$index] as $key => $value) {
        unset($values[$index][$key]["id"]);
        unset($values[$index][$key]["deleted"]);
        unset($values[$index][$key]["selected"]);
    }
    return $values[$index];
}

function excludeValueForUpdate($value, $exclude)
{
    $result = new stdClass();
    $where = new stdClass();
    if (is_array($value)) {
        foreach ($exclude as $v) {
            if (isset($value[$v])) {
                if ("periode" == $v) {
                    $where[$v] = date("Y-m-d", strtotime($value[$v]));
                } else {
                    $where[$v] = $value[$v];
                }
                unset($value[$v]);
            }
        }
        unset($value["id"]);
        unset($value["deleted"]);
        unset($value["selected"]);
        $result->value = $value;
        $result->where = $where;
    } else if (is_object($value)){
        foreach ($exclude as $v) {
            if (isset($value->$v)) {
                if ("periode" == $v) {
                    $where->$v= date("Y-m-d", strtotime($value->$v));
                } else {
                    $where->$v = $value->$v;
                }
                unset($value->$v);
            }
        }
        unset($value->id);
        unset($value->deleted);
        unset($value->selected);
        $result = new stdClass();
        $result->value = $value;
        $result->where = $where;
    }
    return $result;
}

?>