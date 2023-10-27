<?php

include "api.php";

// Если нажат Выход из учётной записи
if (strpos($_SERVER['REQUEST_URI'], '?exit')){session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header('Location: /login.php');}


// Функция для генерация токена
function gen_token() {
	$token = sprintf(
		'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff)
	);
 
	return $token;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

$Login_Err=$Password_Err='';

$Login = test_input($_POST['Login']);
$Password = md5($_POST['Password']);

$conn = connect_to_db();

$sql = "SELECT Login, Password, Status, Email, Phone, Fio FROM klienty WHERE Login = '$Login'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0){
   $row = mysqli_fetch_array($result);
   if ($Password == $row['Password']){
      $new_token = gen_token();

      session_start();
      $_SESSION["Username"] = $row['Login'];
      $_SESSION["FIO"] = $row['Fio'];
      $_SESSION["Status"] = $row['Status'];
      $_SESSION["Email"] = $row['Email'];
      $_SESSION["Phone"] = $row['Phone'];
      $_SESSION["Token"] = $new_token;

      $sql = "UPDATE klienty SET Token = '$new_token' WHERE Login = '$Login'";
      $result = mysqli_query($conn, $sql);

      header('Location: /yuk247.php');

   } else {$Password_Err = 'Пароль неверный!';}

} else {$Login_Err = 'Пользователь не найден';}


mysqli_close($conn);
}//If server

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Вход</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/login.css" rel="stylesheet">
</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
<div id="wb_Gruzoperevozki" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Gruzoperevozki">Грузоперевозки 24/7</h1>
</div>
<div id="wb_Text19">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><strong>+993 64 93 04 67</strong></span>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./yuk247.php" style="display:inline-block;width:100px;height:48px;z-index:2;">Главная</a>
<a id="Button4" href="./signup.php" style="display:inline-block;width:100px;height:48px;z-index:3;">Регистрация</a>
<a id="Button3" href="?exit" style="display:inline-block;width:100px;height:48px;z-index:4;">Выйти</a>
</div>
</div>
</div>
</div>
<div class="layer">
	<div id="div-title"><h1>Вход</h1></div>
	<form name="LayoutGridGruz" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Zayavka_Gruz">

		<div>
			<span><strong>Логин</strong></span>
		</div>

		<div class="Elem" id="div-Login">
			<input type="text" name="Login" id="Login" placeholder="<?php echo $Login_Err;?>" value="<?php if(empty($Login_Err)){echo $Login;}?>" style="width:100%">
		</div>


		<div>
			<span><strong>Пароль</strong></span>
		</div>

		<div class="Elem" id="div-Password">
			<input type="password" name="Password" id="Password" placeholder="<?php echo $Password_Err;?>" style="width:100%">
		</div>


		<div style="text-align: center;">
			<input type="submit" id="Confirm" value="Войти">
		</div>

	</form>
</div>
</body>
</html>