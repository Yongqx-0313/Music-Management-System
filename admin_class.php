<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where email = '".$email."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function login2(){
		extract($_POST);
			$qry = $this->db->query("SELECT *,concat(lastname,', ',firstname,' ',middlename) as name FROM users where email = '".$email."' and password = '".md5($password)."'  and type= 2 ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
				return 1;
		}else{
			return 3;
		}
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}
	function save_user(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(!empty($cpass) && !empty($password)){
					$data .= ", password=md5('$password') ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function signup(){
	extract($_POST);
	$data = "";
	foreach($_POST as $k => $v){
		if(!in_array($k, array('id','cpass','month','day','year')) && !is_numeric($k)){
			// ✅ Sanitize to prevent script injection
			$v = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

			if($k == 'password'){
				if(empty($v)) continue;
				$v = md5($v); // 🔒 Consider using password_hash() instead
			}

			// ✅ Add to SQL string
			if(empty($data)){
				$data .= " $k='$v' ";
			}else{
				$data .= ", $k='$v' ";
			}
		}
	}

	// ✅ Check email existence
	if(isset($email)){
		$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); // sanitize again
		$check = $this->db->query("SELECT * FROM users WHERE email = '$email' " . (!empty($id) ? " AND id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
	}

	// ✅ Upload image safely
	if(isset($_FILES['pp']) && $_FILES['pp']['tmp_name'] != ''){
		$safe_filename = preg_replace("/[^A-Za-z0-9.\-_]/", '', $_FILES['pp']['name']);
		$fnamep = strtotime(date('Y-m-d H:i')) . '_' . $safe_filename;
		$move = move_uploaded_file($_FILES['pp']['tmp_name'], 'assets/uploads/' . $fnamep);
		if ($move) {
			$data .= ", profile_pic = '$fnamep' ";
		}
	}

	// ✅ Insert or update
	if(empty($id)){
		$save = $this->db->query("INSERT INTO users SET $data");
	}else{
		$save = $this->db->query("UPDATE users SET $data WHERE id = $id");
	}

	if($save){
		if(empty($id)) $id = $this->db->insert_id;

		// ✅ Sanitize and store in session
		foreach ($_POST as $key => $value) {
			if(!in_array($key, array('id','cpass','password')) && !is_numeric($key)){
				$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
				if($key == 'pp') $key = 'profile_pic';
				if($key == 'cover') $key = 'cover_pic';
				$_SESSION['login_'.$key] = $value;
			}
		}
		$_SESSION['login_id'] = $id;

		if(isset($_FILES['pp']) && $_FILES['pp']['tmp_name'] != '')
			$_SESSION['login_profile_pic'] = $fnamep;

		if(!isset($type))
			$_SESSION['login_type'] = 2;

		return 1;
	}
}

	function update_user(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table')) && !is_numeric($k)){
				if($k =='password')
					$v = md5($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";

		}
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			
			return 1;
		}
	}
function delete_user(){
	$_POST = $this->sanitize_input($_POST);
	extract($_POST);

	// Get the type of user being deleted
	$getUser = $this->db->query("SELECT * FROM users WHERE id = $id");
	if ($getUser->num_rows == 0) return 0;

	$user = $getUser->fetch_assoc();

	// If the user is an admin (type = 1)
	if ($user['type'] == 1) {
		// Count how many admins are left
		$countAdmins = $this->db->query("SELECT COUNT(*) as total FROM users WHERE type = 1")->fetch_assoc()['total'];

		if ($countAdmins <= 1) {
			// Do not allow deleting the last admin
			return 2; // special code for "cannot delete last admin"
		}
	}

	// Proceed with deletion
	$delete = $this->db->query("DELETE FROM users WHERE id = $id");
	if ($delete)
		return 1;

	return 0;
}

	function save_genre(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
			}

		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_photo = '$fname' ";
		}
		if(empty($id)){
			if(empty($_FILES['cover']['tmp_name']))
			$data .= ", cover_photo = 'default_cover.jpg' ";
			$save = $this->db->query("INSERT INTO genres set $data");
		}else{
			$save = $this->db->query("UPDATE genres set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_genre(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$delete = $this->db->query("DELETE FROM genres where id = $id");
		if($delete){
			return 1;
		}
	}
	function save_music(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover','audio','item_code')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
			}
			$data .=",user_id = '{$_SESSION['login_id']}' ";
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_image = '$fname' ";
		}
		if(isset($_FILES['audio']) && $_FILES['audio']['tmp_name'] != ''){
			$audio = strtotime(date('y-m-d H:i')).'_'.$_FILES['audio']['name'];
			$move = move_uploaded_file($_FILES['audio']['tmp_name'],'assets/uploads/'. $audio);
			$data .= ", upath = '$audio' ";
		}
		if(empty($id)){
			if(empty($_FILES['cover']['tmp_name']))
			$data .= ", cover_image = 'default_cover.jpg' ";
			$save = $this->db->query("INSERT INTO uploads set $data");
		}else{
			$save = $this->db->query("UPDATE uploads set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_music(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM uploads where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_details(){
		extract($_POST);
		$get = $this->db->query("SELECT * FROM uploads where id = $id")->fetch_array();
		$data = array("cover_image"=>$get['cover_image'],"title"=>$get['title'],"artist"=>$get['artist']);
		return json_encode($data);
	}
	function save_playlist(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cover')) && !is_numeric($k)){
				#escape sql input
				$v = $this->db->real_escape_string($v);
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
			}
			$data .=",user_id = '{$_SESSION['login_id']}' ";
			if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", cover_image = '$fname' ";
		}
		if(empty($id)){
			if(empty($_FILES['cover']['tmp_name']))
			$data .= ", cover_image = 'play.jpg' ";
			$save = $this->db->query("INSERT INTO playlist set $data");
		}else{
			$save = $this->db->query("UPDATE playlist set $data where id = $id");
		}
		if($save){
			if(empty($id))
			$id = $this->db->insert_id;
			return $id;
		}
	}
	function delete_playlist(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM playlist where id = $id");
		if($delete){
			return 1;
		}
	}
	function find_music(){
    	$user_id = (int)$_SESSION['login_id'];
    	extract($_POST);
    	$sql = "SELECT id,title,upath,artist,cover_image FROM uploads WHERE user_id = $user_id AND (title LIKE '%$search%' OR artist LIKE '%$search%')";
    	$get = $this->db->query($sql);
    	$data = array();
    	while($row = $get->fetch_assoc()){
        $data[] = $row;
    	}
    	echo json_encode($data); // Use echo for AJAX
    	exit();
	}
	function save_playlist_items(){
		$_POST = $this->sanitize_input($_POST);
		extract($_POST);
		$ids=array();
		foreach($music_id as $k => $v){
			$data = " playlist_id = $playlist_id ";
			$data .= ", music_id = {$music_id[$k]} ";
			$check = $this->db->query("SELECT * FROM playlist_items where playlist_id = $playlist_id and  music_id = {$music_id[$k]}")->num_rows;
			if($check <= 0){
				if($save[] = $this->db->query("INSERT INTO playlist_items set $data ")){
					$ids[]=$music_id[$k];
				}
			}else{
				$save[] = 1;
			}

		}
		if(isset($save)){
			$this->db->query("DELETE FROM playlist_items where playlist_id = $playlist_id and music_id not in (".implode(',',$music_id).") ");
			return 1;
		}
	}

	private function sanitize_input($input){
		$sanitized = [];
		foreach($input as $key => $value){
			if ($key === 'description') {
        // Allow limited safe HTML
        $sanitized[$key] = strip_tags($value, '<p><br><b><i><strong><em><ul><li><ol>');
    }   else {
        // Escape all other fields for safety
        $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    	}
    }
    return $sanitized;
	}
}