<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

interface CallbackController
{
    public function request($action, $param);
}

class BaseController extends CI_Controller
{

    public function __construct($model = "")
    {
        parent::__construct();
        //exist_session();
    }

    public function index()
    {

    }

    function validate()
    {
        $param = param_input();
        if ($param && $param["action"]) {
            return true;
        } else {
            response(null, 404, "Action not found");
        }
    }

}