<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template
{
    var $ci;

    function __construct()
    {
        $this->ci =& get_instance();
    }

    /* mvc */
    function load($tpl_view, $body_view = null, $data = null)
    {
        if (!is_null($body_view)) {
            if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view)) {
                $body_view_path = $tpl_view . '/' . $body_view;
            } else if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view . '.php')) {
                $body_view_path = $tpl_view . '/' . $body_view . '.php';
            } else if (file_exists(APPPATH . 'views/' . $body_view)) {
                $body_view_path = $body_view;
            } else if (file_exists(APPPATH . 'views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            } else {
                show_error('Unable to load the requested file: ' . $tpl_view . '/' . $body_view . '.php');
            }
            $body = $this->ci->load->view($body_view_path, $data, TRUE);
            if (is_null($data)) {
                $data = array('body' => $body);
            } else if (is_array($data)) {
                $data['body'] = $body;
            } else if (is_object($data)) {
                $data->body = $body;
            }
        }
        $this->ci->load->view('templates/' . $tpl_view, $data);
    }

    /* hmvc */
    function show($controller, $body_view = null, $data = null)
    {
        $tpl_view = 'default';
        if (!is_null($body_view)) {
            if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view)) {
                $body_view_path = $tpl_view . '/' . $body_view;
            } else if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view . '.php')) {
                $body_view_path = $tpl_view . '/' . $body_view . '.php';
            } else if (file_exists(APPPATH . 'views/' . $body_view)) {
                $body_view_path = $body_view;
            } else if (file_exists(APPPATH . 'views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            } else if (file_exists(APPPATH . 'modules/' . $controller->router->fetch_class() . '/views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            }

            $body = $this->ci->load->view($body_view_path, $data, TRUE);

            if (is_null($data)) {
                $data = array('body' => $body);
            } else if (is_array($data)) {
                $data['body'] = $body;
            } else if (is_object($data)) {
                $data->body = $body;
            }
        }
        // nav mode
        $sql = "SELECT a.* FROM app_config a WHERE a.configuration=?";
        $res = $this->ci->db->query($sql, array("APP.TEMPLATE.NAV.MENU.SIDEBAR"))->result_array();
        if (1 == count($res)) {
            $data['navmode'] = (0 == $res[0]['value'] ? "hold-transition skin-blue sidebar-mini" : "skin-blue sidebar-mini tooltip-f sidebar-collapse");
        } else {
            $data['navmode'] = "hold-transition skin-blue sidebar-mini";
        }
        // onesignal
        $this->ci->load->config('onesignal');
        $data['onesignal_app_id'] = $this->ci->config->item('onesignal_app_id');

        $data['header'] = $this->ci->load->view('templates/header', $data, TRUE);
        $data['toolbar'] = $this->ci->load->view('templates/toolbar', $data, TRUE);
        $data['sidebar'] = $this->ci->load->view('templates/sidebar', $data, TRUE);
        $data['contents'] = $this->ci->load->view('templates/contents', $data, TRUE);
        $data['content_footer'] = $this->ci->load->view('templates/content_footer', $data, TRUE);
        $data['footer'] = $this->ci->load->view('templates/footer', $data, TRUE);
        if(is_allow_page()){
            $this->ci->load->view('templates/' . $tpl_view, $data);
        }
    }

    function show_login($controller, $body_view = null, $data = null)
    {
        $tpl_view = 'default';
        if (!is_null($body_view)) {
            if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view)) {
                $body_view_path = $tpl_view . '/' . $body_view;
            } else if (file_exists(APPPATH . 'views/' . $tpl_view . '/' . $body_view . '.php')) {
                $body_view_path = $tpl_view . '/' . $body_view . '.php';
            } else if (file_exists(APPPATH . 'views/' . $body_view)) {
                $body_view_path = $body_view;
            } else if (file_exists(APPPATH . 'views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            } else if (file_exists(APPPATH . 'modules/' . $controller['controller'] . '/views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            } else {
                show_error('Unable to load the requested file: ' . $body_view . '/' . $body_view . '.php');
            }
            $body = $this->ci->load->view($body_view_path, $data, TRUE);
            if (is_null($data)) {
                $data = array('body' => $body);
            } else if (is_array($data)) {
                $data['body'] = $body;
            } else if (is_object($data)) {
                $data->body = $body;
            }
        }
        $template_dir = 'template_login_lte';
        $data['header'] = $this->ci->load->view($template_dir . '/header', $data, TRUE);
        $data['contents'] = $this->ci->load->view($template_dir . '/contents', $data, TRUE);
        $this->ci->load->view($template_dir . '/' . $tpl_view, $data);
    }

}