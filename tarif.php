<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__.'/Exception.php';
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';

function ValidateEmail($email)
{
   $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
   return preg_match($pattern, $email);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formid']) && $_POST['formid'] == 'tarifform')
{
   $mailto = 'example@gmail.com';
   $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
   $subject = 'Тариф';
   $message = '';
   $success_url = './yuk247.php';
   $error_url = './tarif.php';
   $eol = "\n";
   $error = '';
   $internalfields = array ("submit", "reset", "send", "filesize", "formid", "captcha", "recaptcha_challenge_field", "recaptcha_response_field", "g-recaptcha-response", "h-captcha-response");

   $mail = new PHPMailer(true);
   try
   {
      $mail->IsSMTP();
      $mail->Host = 'SERVER';
      $mail->Port = PORT;
      $mail->SMTPAuth = true;
      $mail->Username = 'EXAMPLE@MAIL';
      $mail->Password = 'PASSWORD';
      $mail->SMTPSecure = 'tls';
      $mail->Subject = stripslashes($subject);
      $mail->From = $mailfrom;
      $mail->FromName = $mailfrom;
      $mailto_array = explode(",", $mailto);
      for ($i = 0; $i < count($mailto_array); $i++)
      {
         if(trim($mailto_array[$i]) != "")
         {
            $mail->AddAddress($mailto_array[$i], "");
         }
      }
      if (!ValidateEmail($mailfrom))
      {
         $error .= "The specified email address (" . $mailfrom . ") is invalid!\n<br>";
         throw new Exception($error);
      }
      $mail->AddReplyTo($mailfrom);
      $message .= $eol;
      $message .= "IP Address : ";
      $message .= $_SERVER['REMOTE_ADDR'];
      $message .= $eol;
      foreach ($_POST as $key => $value)
      {
         if (!in_array(strtolower($key), $internalfields))
         {
            if (is_array($value))
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
            }
            else
            {
               $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
            }
         }
      }
      $mail->CharSet = 'UTF-8';
      if (!empty($_FILES))
      {
         foreach ($_FILES as $key => $value)
         {
            if (is_array($_FILES[$key]['name']))
            {
               $count = count($_FILES[$key]['name']);
               for ($file = 0; $file < $count; $file++)
               {
                  if ($_FILES[$key]['error'][$file] == 0)
                  {
                     $mail->AddAttachment($_FILES[$key]['tmp_name'][$file], $_FILES[$key]['name'][$file]);
                  }
               }
            }
            else
            {
               if ($_FILES[$key]['error'] == 0)
               {
                  $mail->AddAttachment($_FILES[$key]['tmp_name'], $_FILES[$key]['name']);
               }
            }
         }
      }
      $mail->WordWrap = 80;
      $mail->Body = $message;
      $mail->Send();
      header('Location: '.$success_url);
   }
   catch (Exception $e)
   {
      $errorcode = file_get_contents($error_url);
      $replace = "##error##";
      $errorcode = str_replace($replace, $e->getMessage(), $errorcode);
      echo $errorcode;
   }
   exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Тарифы</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/tarif.css" rel="stylesheet">
</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="col-1">
<div id="wb_Gruzoperevozki" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Gruzoperevozki">Грузоперевозки 24/7</h1>
</div>
<div id="wb_Text19">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><strong>+993 64 93 04 67</strong></span>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./signup.php" style="display:inline-block;width:98px;height:35px;z-index:2;">Регистрация</a>
</div>
</div>
</div>
<div id="wb_LayoutGrid3">
<div id="LayoutGrid3">
<div class="row">
<div class="col-1">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:3;">
<h1 id="Heading1">Выберите для себя и своих партнеров наиболее выгодной тариф:</h1>
</div>
</div>
</div>
</div>
</div>
<div id="wb_Tarifform">
<form name="Tarifform" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" accept-charset="UTF-8" id="Tarifform">
<input type="hidden" name="formid" value="tarifform">
<div class="col-1">
<input type="text" id="Sumuser" style="display:none;width: 100%;height:26px;z-index:4;" name="Sumuser" value="" spellcheck="false">
<input type="text" id="Sumuser2" style="display:none;width: 100%;height:26px;z-index:5;" name="Sumuser2" value="" spellcheck="false">
</div>
<div class="col-2">
<div id="wb_Text2">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Количество пользователей</strong></span>
</div>
<div id="wb_LayoutGrid6">
<div id="LayoutGrid6">
<div class="row">
<div class="col-1">
<label for="RadioButton2" id="Label1" style="display:block;width:100%;line-height:16px;z-index:6;">1 пользователь - 350 тмт</label>
</div>
<div class="col-2">
<div id="wb_RadioButton2" style="display:inline-block;width:17px;height:20px;z-index:7;">
<input type="radio" id="RadioButton2" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton2"></label>
</div>
</div>
</div>
</div>
</div>
<div id="wb_LayoutGrid5">
<div id="LayoutGrid5">
<div class="row">
<div class="col-1">
<label for="RadioButton2" id="Label2" style="display:block;width:100%;line-height:16px;z-index:8;">2 пользователя - 550 тмт</label>
</div>
<div class="col-2">
<div id="wb_RadioButton1" style="display:inline-block;width:17px;height:20px;z-index:9;">
<input type="radio" id="RadioButton1" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton1"></label>
</div>
</div>
</div>
</div>
</div>
<div id="wb_LayoutGrid7">
<div id="LayoutGrid7">
<div class="row">
<div class="col-1">
<label for="RadioButton2" id="Label3" style="display:block;width:100%;line-height:16px;z-index:10;">3 пользователя - 750 тмт</label>
</div>
<div class="col-2">
<div id="wb_RadioButton3" style="display:inline-block;width:17px;height:20px;z-index:11;">
<input type="radio" id="RadioButton3" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton3"></label>
</div>
</div>
</div>
</div>
</div>
<div id="wb_LayoutGrid8">
<div id="LayoutGrid8">
<div class="row">
<div class="col-1">
<label for="RadioButton2" id="Label4" style="display:block;width:100%;line-height:16px;z-index:12;">4 пользователя - 950 тмт</label>
</div>
<div class="col-2">
<div id="wb_RadioButton4" style="display:inline-block;width:17px;height:20px;z-index:13;">
<input type="radio" id="RadioButton4" name="NewGroup" value="on" style="display:inline-block;"><label for="RadioButton4"></label>
</div>
</div>
</div>
</div>
</div>
<div id="wb_LayoutGrid9">
<div id="LayoutGrid9">
<div class="col-1">
<div id="wb_Text4">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Продолжительность</strong></span>
</div>
<select name="Kolmes" size="1" id="Kolmes" style="display:block;width: 100%;height:28px;z-index:15;">
<option selected value="0">Кол-во месяцев</option>
<option selected value="1">1 мес.</option>
<option selected value="2">2 мес.</option>
<option selected value="3">3 мес.</option>
<option selected value="4">4 мес.</option>
<option selected value="5">5 мес.</option>
<option selected value="6">6 мес.</option>
</select>
</div>
<div class="col-2">
<div id="wb_Text7">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Скидка %</strong></span>
</div>
<input type="text" id="Skidkapr" style="display:block;width: 100%;height:26px;z-index:17;" name="Skidkapr" value="" readonly spellcheck="false">
</div>
</div>
</div>
</div>
<div class="col-3">
<div id="wb_LayoutGrid10">
<div id="LayoutGrid10">
<div class="row">
<div class="col-1">
<div id="wb_Text3">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Итого:</strong></span>
</div>
<input type="text" id="Summa" style="display:block;width: 100%;height:26px;z-index:25;" name="Sum" value="" readonly spellcheck="false">
</div>
<div class="col-2">
<div id="wb_Text8">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Условия оплаты</strong></span>
</div>
<select name="Usloplata" size="1" id="Usloplata" style="display:block;width: 100%;height:28px;z-index:27;">
<option selected value="">На телефон</option>
<option selected>Перечислением на счет</option>
<option selected>Наличными в кассу</option>
</select>
</div>
</div>
</div>
</div>
<input type="text" id="Editbox4" style="display:block;width: 100%;height:26px;z-index:29;" name="Email" value="" spellcheck="false" placeholder="E-mail:">
<input type="text" id="Editbox2" style="display:block;width: 100%;height:26px;z-index:30;" name="Name" value="" spellcheck="false" placeholder="Имя:">
<input type="text" id="Editbox3" style="display:block;width: 100%;height:26px;z-index:31;" name="Phone" value="" spellcheck="false" placeholder="Телефон:">
<input type="submit" id="Button1" name="" value="Отправить" style="display:inline-block;width:102px;height:32px;z-index:32;">
</div>
<div class="col-4">
</div>
</form>
</div>
<div id="wb_Footer">
<div id="Footer">
<div class="row">
<div class="col-1">
<div id="wb_Text6">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">Наш сервис предоставляет услуги исключительно как справочник. Мы не оказываем услуги транспортного или экспедиционного характера</span>
</div>
</div>
<div class="col-2">
<div id="wb_Text5">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">744000 Address,Turkmenistan, Ashgabat<br>+99364 93 04 67<br>+99361 52 91 13<br>haytektm@gmail.com<br></span><span style="color:#FFFFFF;font-family:Arial;font-size:12px;"><br><strong>Copyright 2023 ES Haytek</strong></span><span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><br></span>
</div>
</div>
</div>
</div>
</div>
<script src="jquery-3.6.0.min.js"></script>
<script src="tarif.js"></script>
</body>
</html>