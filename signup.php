<?php

include "api.php";

// Использование библиотек для рассылки
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__.'/Exception.php';
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';

// Если нажат Выход (из текущей учётной записи)
if (strpos($_SERVER['REQUEST_URI'], '?exit')){session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header('Location: /signup.php');}


$Fio_Err=$Phone_Err=$Email_Err='';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $Fio=$Phone=$Email='';

    // Первая проверка

    // Fioname
    if (empty($_POST['Fio'])) {$Fio_Err = 'Укажите ФИО или Название компании';} 
    else {$Fio = test_input($_POST['Fio']);}

    // Phone
    if (empty($_POST['Phone'])) {$Phone_Err = 'Укажите телефон';} 
    else {$Phone = test_input($_POST['Phone']);}

    // Email
    if (empty($_POST['Email'])) {$Email_Err = 'Укажите Email';} 
    elseif(!filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL)) {$Email_Err = 'Некорректный Email';}
    else {$Email = test_input($_POST['Email']);}

    // Login
    $Login = 'Пусто';

    // Password	
    $Password = 'Пусто';

    $Status = "Гость";

    $Datestamp = $_POST['Datestamp'];
    
    $var_array = [$Fio,$Phone,$Email,$Login,$Password,$Status];

    // Вторая проверка на пустые значения
    $Error = '';
    foreach ($var_array as $i){
        if (empty($i) and $i!='0'){
            $Error = 'Error!';
            break;
        }
    }

    if (empty($Error)){

        $conn = connect_to_db();

        $Fio = mysqli_real_escape_string($conn, $Fio);
        $Phone = mysqli_real_escape_string($conn, $Phone);
        $Email = mysqli_real_escape_string($conn, $Email);

        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT `Email` FROM `klienty` WHERE `Email` = '".$Email."'"));
        if ($check != null){
            $Email_Err = 'Ваш Email уже зарегистрирован в базе!';
        } else {
            $sql = "INSERT INTO `klienty` (`Fio`, `Phone`, `Email`, `Login`, `Password`, `Status`, `Datestamp`) VALUES ('$Fio', '$Phone', '$Email', '$Login', '$Password', '$Status', '$Datestamp')";

            $result = mysqli_query($conn, $sql);
    
            // -------------Отправка Mail без файла напрямую ------------- \\
            $mail_array = ["haytektm@gmail.com", "tekhaytek@mail.ru"];
            foreach ($mail_array as $email){
                $mailfrom = 'haytek.club@mail.ru';
                $subject = 'Новый пользователь Грузоперевозки 24/7';

                $message = "Фио: $Fio";
                $message .= "\n\nE-mail: $Email";
                $message .= "\n\nТелефон: $Phone";
                $message .= "\n\nДата: $Datestamp";

                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->CharSet = "utf-8";
                $mail->Host = 'smtp.mail.ru';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->Username = 'haytek.club@mail.ru';
                $mail->Password = 'h09fCxA7CyBg8UAKf91v';
                $mail->SMTPSecure = 'tsl';

                $mail->From = $mailfrom;
                $mail->FromName = $mailfrom;
                $mail->AddAddress($newemail, "");
                $mail->AddAddress($email, "");
                $mail->AddReplyTo($mailfrom);

                $mail->Body = stripslashes($message);
                $mail->Subject = stripslashes($subject);
                $mail->WordWrap = 80;
                if (!$mail->Send())
                    {
                        die('PHPMailer error: ' . $mail->ErrorInfo);
                    }                     
            }// foreach
                                     
            header('Location: /success_reg.html');

        }// if check
            mysqli_close($conn);
    }//if empty error

}


?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Регистрация</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/signup.css" rel="stylesheet">
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
<a id="Button1" href="./login.php" style="display:inline-block;width:100px;height:48px;z-index:3;">Вход</a>
<a id="Button3" href="?exit" style="display:inline-block;width:100px;height:48px;z-index:4;">Выйти</a>
</div>
</div>
</div>
</div>
<div class="layer">
	<div id="div-title"><h1>Регистрация</h1></div>
	<form name="Sign_Up" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Sign_Up">

	<input hidden type="date" id="Datestamp" name="Datestamp" value="<?php echo date('Y-m-d');?>" readonly spellcheck="false">

	<div class="col">
		<div class="span-div">
			<span><strong>ФИО</strong></span>
		</div>

		<div class="Elem" id="div-Fio">
			<input type="text" name="Fio" id="Fio" value="<?php echo $Fio_Err;?>" style="width:100%">
		</div>


		<div class="span-div">
			<span><strong>Телефон</strong></span>
		</div>

		<div class="Elem" id="div-Phone">
			<input type="text" name="Phone" id="Phone" value="<?php echo $Phone_Err;?>" style="width:100%">
		</div>


		<div class="span-div">
			<span><strong>E-mail</strong></span>
		</div>

		<div class="Elem" id="div-Email">
			<input type="email" name="Email" id="Email" value="<?php echo $Email_Err;?>" style="width:100%">
		</div>


		<div style="text-align: center;">
			<input type="submit" id="Confirm" value="Отправить">
		</div>

	</form>
</div>
</body>
</html>