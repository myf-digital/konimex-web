<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	session
*/
function exist_session()
{
    $ci =& get_instance();
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION['user_credential'])) {
        return true;
    } else {
        redirect(base_url(""));
    }
}

function is_allow_page()
{
    $ci =& get_instance();
    if (!isset($_SESSION)) {
        session_start();
    }
	
    if (strtotime('now') > $_SESSION['expired_at']) {
        //logout
        unset($_SESSION["user_credential"]);
    } else {
        $_SESSION['expired_at'] = strtotime("+30 minutes");
    }
	
    if (isset($_SESSION['user_credential'])) {
        if (!empty($ci->uri->segment(1))) {
            $role = $_SESSION['role_id'];
            $sql = "SELECT count(1) as allow FROM app_role_menu a left join app_menu b on a.menu_id=b.menu_id WHERE a.role_id=? and b.module_name=?";
            $res = $ci->db->query($sql, array($role, $ci->uri->segment(1)))->result_array();
            if (1 == $res[0]["allow"]) {
                return true;
            } else {
                $ci->load->view('/errors/html/error_restrict');
                return false;
            }
        }
        // echo $ci->uri->segment(2);
        // echo $ci->uri->segment(3);
        // echo $ci->uri->uri_string();
    } else {
        redirect(base_url(""));
    }
}

function authUserApplication($data)
{
    $ci =& get_instance();
    $values = array($data['username'], $data['password']);
    $sql = "SELECT a.*, b.restrict_level, b.restrict_bu, (select aktif_week from m_setup_site) as week_aktif FROM app_resource a left join ref_jabatan b on a.idjabatan=b.idjabatan
			WHERE a.username=? AND a.password=md5(?) AND a.status='RA'";
    $res = $ci->db->query($sql, $values)->result_array();
    $form_response = new stdClass();
    if (1 == count($res)) {
        $_SESSION['user_login'] = $res[0]['username'];
        $_SESSION['resource_id'] = $res[0]['resource_id'];
        $_SESSION['user'] = $res[0]['name'];
        $_SESSION['nip'] = $res[0]['nip'];
        $_SESSION['user_credential'] = $res[0]['username'];
        $_SESSION['role_id'] = $res[0]['role_id'];
        $_SESSION['idjabatan'] = $res[0]['idjabatan'];
        $_SESSION['expired_at'] = strtotime("+30 minutes");
        unset($res[0]["password"]);
        $form_response->session = $res[0];
        $form_response->status_login = 200;
        $form_response->message = "Login success";

        //insert log
        log_activity('login');

        // save player id
        if (isset($data['player_id']) && !empty($data['player_id'])) {
            savePlayer($data);
        }

        return $form_response;
    } else if (1 < count($res)) { // duplicate user
        $form_response->status_login = 201;
        $form_response->message = "Duplicate user found!";
        return $form_response;
    } else { // wrong username and password
        $form_response->status_login = 202;
        $form_response->message = "Username and password not correct!";
        return $form_response;
    }
}

function savePlayer($data)
{
    $ci =& get_instance();
    $sql = "SELECT * FROM x_player WHERE account_id = ? LIMIT 1";
    $res = $ci->db->query($sql, [$data['username']])->result_array();
    if (1 == count($res)) {
        $payload = [
            'account_id' => $data['username'],
            'player_id' => $data['player_id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $ci->db->where('account_id', $data['username']);
        $ci->db->update('x_player', $payload);
    } else {
        $payload = [
            'account_id' => $data['username'],
            'player_id' => $data['player_id'],
        ];
        $ci->db->insert('x_player', $payload);
    }
}

function authUserApplicationMobile($data)
{
    $ci =& get_instance();
    $values = array($data['username'], $data['password']);
    $sql = "SELECT a.* FROM app_resource a WHERE a.username=? AND password=md5(?)";
    $res = $ci->db->query($sql, $values)->result_array();
    $form_response = new stdClass();
    if (1 == count($res)) {
        $form_response->status = 1;
        $form_response->status_login = 200;
        $form_response->user = $res[0];
        return $form_response;
    } else if (1 < count($res)) { // duplicate user
        $form_response->status = 0;
        $form_response->status_login = 801;
        return $form_response;
    } else {
        $form_response->status = 0;
        $form_response->status_login = 800;
        $form_response->message = "Username dan password salah!";
        return $form_response;
    }
}

function createMark($user)
{
    $user['created_by'] = $_SESSION['user_credential'];
    $user['created_date'] = date("Y-m-d H:i:s");
    return $user;
}

function updateMark($user)
{
    $user['modified_by'] = $_SESSION['user_credential'];
    $user['modified_date'] = date("Y-m-d H:i:s");
    return $user;
}

function logoutUserLogin()
{
    $ci =& get_instance();
    unset($_SESSION["user_credential"]);
    if (!isset($_SESSION)) {
        session_start();
    }

    //insert log
    log_activity('logout');

    $form_response = new stdClass();
    if (isset($_SESSION['user_credential'])) {
        $form_response->status = 1;
        $form_response->status_login = 203;
        $form_response->login_page = site_url();
    } else {
        $form_response->status = 1;
        $form_response->status_login = 200;
        $form_response->login_page = site_url();
    }
    return $form_response;
}

/*
	user menu generator
*/
function user_menu()
{
    $ci =& get_instance();
    $selected = $ci->uri->segment(1);
    $parent = selected_menu(find_menu($_SESSION['role_id']), $selected);
    $list_menu = '<ul class="sidebar-menu">';
    $list_menu .= build_tree_menu($parent);
    $list_menu .= '</ul';
    return $list_menu;
}

/*
	get menu grand by user login
*/
function find_menu($role_id)
{
    $ci =& get_instance();
    $values = array($role_id);
    $sql = "SELECT a.*, b.* FROM app_role_menu a JOIN app_menu b ON a.menu_id = b.menu_id WHERE a.role_id = ? AND b.status IS NULL ORDER BY b.seq_number";
    return $ci->db->query($sql, $values)->result_array();
}

function selected_menu($parent, $selected)
{
    $counter = 0;
    foreach ($parent as $element) {
        if ($element['module_name'] === $selected) {
            $parent[$counter]['selected'] = true;
            if ($element['parent_id'] > 0) {
                $parent = selected_menu_parent($parent, $element['parent_id']);
            }
            break;
        }
        $counter++;
    }
    return $parent;
}

function selected_menu_parent($parent, $parentId = 0)
{
    $counter = 0;
    foreach ($parent as $element) {
        if ($parentId == $element['menu_id']) {
            $parent[$counter]['selected'] = true;
            $parent = selected_menu_parent($parent, $element['parent_id']);
            break;
        }
        $counter++;
    }
    return $parent;

}

/*
	Generator menu
*/
function build_tree_menu(array $elements, $parentId = 0)
{
    $navigation = '';
    foreach ($elements as $element) {
        $menu_name = $element['menu_name'];
        $menu_icon = (empty($element['menu_icon']) ? "fa fa-circle-o" : "fa " . $element['menu_icon']);
        $module_name = $element['module_name'];
        if ($element['parent_id'] == $parentId) {
            if ("#" == trim($module_name)) {
                $module_name = "javascript:void(0)";
                // active treeview menu-open
                if (isset($element['selected']) && $element['selected']) {
                    $navigation .= "<li class='active treeview menu-open'>";
                } else {
                    $navigation .= "<li class='treeview'>";
                }
                $navigation .= "<a href='$module_name'>
                                        <i class='$menu_icon'></i><span>$menu_name</span>
                                        <span class='pull-right-container'>
                                            <i class='fa fa-angle-left pull-right'></i>
                                        </span>
                                    </a>";
                $navigation .= "<ul class='treeview-menu'>";
                $navigation .= build_tree_menu($elements, $element['menu_id']);
                $navigation .= "</ul></li>";
            } else {
                $module_name = site_url($element['module_name']);
                if (isset($element['selected']) && $element['selected']) {
                    $navigation .= "<li class='active'><a class='menu-item' href='$module_name' menu='$module_name'><i class='$menu_icon'></i><span>$menu_name</span></a></li>";
                } else {
                    $navigation .= "<li><a class='menu-item' href='$module_name' menu='$module_name'><i class='$menu_icon'></i><span>$menu_name</span></a></li>";
                }
            }
        }
    }
    return $navigation;
}

/*
    log_resource_activity
*/
function log_activity($type, $log = null)
{
    $ci =& get_instance();
    
    $data = [
        'periode' => date('Y-m-d'),
        'idjabatan' => $_SESSION['idjabatan'],
        'username' => $_SESSION['user_login'],
        'log' => $log,
        'log_type' => $type,
        'log_date' => date('Y-m-d H:i:s')
    ];

    return $ci->db->insert('log_resource_activity', $data);
}
?>