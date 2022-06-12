<style>
    .btn {
  position: relative;
  bottom: -200;
  width: 100px;
  display: block;
  
  margin: 30px auto;
  padding: 10;
  text-decoration: none;

  overflow: hidden;

  border-width: 0;
  outline: none;
  border-radius: 2px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, .6);
  
  background-color: #2ecc71;
  color: #ecf0f1;
  
  transition: background-color .3s;
}

.btn:hover, .btn:focus {
  background-color: #27ae60;
}

.btn > * {
  position: absolute;
}

.btn span {
  display: block;
  padding: 12px 24px;
}

.btn:before {
  content: "";
  
  position: absolute;
  top: 50%;
  left: 50%;
  
  display: block;
  width: 0;
  padding-top: 0;
    
  border-radius: 100%;
  
  background-color: rgba(236, 240, 241, .3);
  
  -webkit-transform: translate(-50%, -50%);
  -moz-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  -o-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}

.btn:active:before {
  width: 120%;
  padding-top: 120%;
  
  transition: width .2s ease-out, padding-top .2s ease-out;
}

/* Styles, not important */
*, *:before, *:after {
  box-sizing: border-box;
}

html {
  position: relative;
  height: 100%;
}

body {
  position: absolute;
  top: 50%;		
  left: 50%;
  
  -webkit-transform: translate(-50%, -50%);
  -moz-transform: translate(-50%, -50%);
  -ms-transform: translate(-50%, -50%);
  -o-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
  
  background-color: #ecf0f1;
  color: #34495e;
  font-family: Trebuchet, Arial, sans-serif;
  text-align: center;
}


</style>

<div class="content" style="
    position: fixed;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
">
	<img src="" id="qr_img"  
			style="
			width: 3in;
			height: 3in;
			"/>
	<br>
</div>
<script>
	function QRLoad(code){
		document.getElementById("qr_img").src = '../../temp/'+code+'.png';
		// document.write('<p>'+code+'</p>');

		// alert(code);
	}
	function ButtonCreate(na){
		if(na == 0){
			document.write('<a class="btn" href="http://localhost:8085/bmis/pages/resident/resident.php">Back</a>');
		}
		if(na == 1){
			document.write('<a class="btn" href="http://localhost:8085/bmis/main/nonresident.php">Back</a>');
		}
	}
	//document.write('<a class="btn" href="http://localhost:8085/bmis/pages/resident/resident.php">Back</a>');
</script>


<?php 

require_once('../../config.php');
include('../../libs/phpqrcode/qrlib.php'); 

Class OverridePeople extends DBConnection {
    private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_people(){
    extract($_POST);
		$data = '';
		$f = $_GET['f'];
		$id = $_GET['id'];
		$firstname = $_GET['firstname'];
		$lastname = $_GET['lastname'];
		$middlename = $_GET['middlename'];
		$address = $_GET['address'];
		$na = !isset($_GET['na']) ? '0' : $_GET['na'];
		
		
		if($f == "create"){
			$code = $id.(mt_rand(0,99999999999));
			$i=0;
			while($i == 0){
				 
				$chk = $this->conn->query("SELECT * FROM people where code = $code ")->num_rows;
				if($chk <=0 ){
					$i = 1;
				}
			}
			$data .=" , code = '{$code}' ";

			if($chk > 0){
				return 3;
			}else{
				if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
					$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
					$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
					$data .=" , image_path = '{$fname}' ";
					
				}
				$qry = $this->conn->query("INSERT INTO `people` (`id`, `code`, `email`, `firstname`, `lastname`, `middlename`, `address`, `zone_id`, `city_id`, `contact`, `image_path`) 
                VALUES ('{$id}', '{$code}', 'test@sample.com', '{$firstname}', '{$lastname}', '{$middlename}', '{$address}', '2', '6', '1', 'uploads/1614241320_ava.jpg');");
				if($qry){
					$this->settings->set_flashdata('success','Person successfully saved.');
					// return 1;
				}else{
					//echo "error";
					return 2;
				}
			}
			
			if(!is_dir('../../temp/')) mkdir('../../temp/');
				$tempDir = '../../temp/'; 
			if(!is_file('../../temp/'.$code.'.png'))
				QRcode::png($code, $tempDir.''.$code.'.png', QR_ECLEVEL_L, 5);
			echo "<script type='text/javascript'>QRLoad('{$code}');</script>";
			echo "<script>ButtonCreate($na)</script>";
		}
		elseif($f == "update"){
			

				$qry = $this->conn->query(
					"UPDATE people

					SET 
					firstname = '{$firstname}',
					lastname = '{$lastname}',
					middlename = '{$middlename}',
					address = '{$address}'
					WHERE id = '{$id}'"
				);
				$code = 0;
				$chk = $this->conn->query("SELECT code FROM `people` WHERE id = '{$id}' ");
	
    				while($row = mysqli_fetch_array($chk)){
    				    $code = $row['code'];
    				}
				
				if($qry){
					$this->settings->set_flashdata('success','Person successfully updated.');
					echo "<script type='text/javascript'>QRLoad('{$code}');</script>";
					echo "<script>ButtonCreate($na)</script>";
					
					return 1;
				}else{
					return 2;
				}
			
		}
		
		
    }
}


// $action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new OverridePeople();
$auth->save_people();

?>


