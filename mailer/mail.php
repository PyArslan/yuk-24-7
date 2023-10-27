<?php

/* настройка cron. Нужно вести такую команду в панели управления cron на хостинге:

/usr/local/bin/wget -q http://oblakotm.com/mailer/mail.php

wget - браузер, с его помощью можно автономно открывать php страницы запуская их код 

Расшифровка команды:

/usr/local/bin/путь_до_программы_исполнителя -метод(-q это тихий) http(s)://домен/путь_до_исполяемого_файла

Указать время в cron:
* - каждый, например день: * означает запуск каждый день
*(слеш без ковычек)n - например минуты: означает запуск каждые n минут

*/

// Настройка параметров скрипта: Максимальное время выполнения и Текущие время и дата
ini_set('max_execution_time', '3600');
date_default_timezone_set('Asia/Ashgabat');

// Подключение нужных для скрипта файлов которые лежат с ним в одной папке
use PHPMailer\PHPMailer\PHPMailer;
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';



// создаём переменную с функцией открытия проверочного файла
$check = fopen("check.txt","r") or die(0);

// если в проверочном  файле значение OFF то начинается след. проверка
if(fread($check, filesize("check.txt")) == "OFF"){
	fclose($check);

	$files_array = array();

	// сканирование директории и поиск файлов с заявкой для рассылки
	$dir = scandir(getcwd());
	foreach ($dir as $file) {
	    if(strpos($file, "ew_application")){
	        array_push($files_array, $file);
	        break;
	    }
	}


	// если массив в файлами не пустой то начинается выполнение рассылки
	if (!empty($files_array)){
		// меняется статус в проверочном файле (чтобы следующий запуск этого скрипта не создал дубликат задачи а просто свернулся)
		$check = fopen("check.txt","w");
		fwrite($check, "ON");
		fclose($check);

		$date_today = date('d.m.Y G:i:s');

		// Запись в лог файл сведения о начале рассылки
		$log = fopen("log.txt","a");
		fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\n---------".$date_today."--------\n\nНачало отправки рассылки"));
		fclose($log);

	    // ----------- SQL -------------- \\
		
	    $servername = "127.0.0.1:3306";
	    $username = "root";
	    $password = "";
	    $dbname = "haytek_gruz247";

	    $emails_array = array();

	    $conn = new mysqli($servername, $username, $password, $dbname);

	    if ($conn->connect_error) {
	    	$log = fopen("log.txt","a");
			fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\nConnection failed: ".$conn->connect_error));
			fclose($log);
	      	die("Connection failed: " . $conn->connect_error);
	    }

	    $sql = "SELECT Email FROM Klienty WHERE Email != '' ORDER BY ID";

	    $result = $conn->query($sql);
	    if ($result->num_rows > 0) {
	     
	        while($row = $result->fetch_assoc()) {
	            array_push($emails_array, $row['Email']);
	        }
	    }
		
		$len = (int) count($emails_array);

		$firsthalf = array_slice($emails_array, 0, $len / 2);
		$secondhalf = array_slice($emails_array, $len / 2);

	    // ------------- Sending ------------- \\
	    
	    foreach ($files_array as $file_name){
	    	// чтение файла с заявкой
	        $file = fopen($file_name, "r") or die("Unable to open file!");
	        $file = fread($file,filesize($file_name));

	        // преобразование строки в массив через символ | -> A|B|C = ['A','B','C']
	        $file = explode("|", $file);

	        // проходится по email
	        foreach ($firsthalf as $email){

	            $mailfrom = 'MY_EMAIL_1@gmail.com';

				$id = mb_convert_encoding($file[0], "UTF-8", "WINDOWS-1251");
	            $subject = "Новая заявка по грузоперевозкам $id";

	            // Формируется текст письма
	            $message = "";
	            foreach ($file as $value) {
	                $message .= mb_convert_encoding($value, "UTF-8", "WINDOWS-1251")."\n\n";
	            }
	            
	            // Настройка PHPMailer и SMTP
	            $mail = new PHPMailer();
	            $mail->IsSMTP();
	            $mail->CharSet = "utf-8";
	            $mail->Host = 'smtp.gmail.com';
	            $mail->Port = 587;
	            $mail->SMTPAuth = true;
	            $mail->Username = 'haytek.server@gmail.com';
	            $mail->Password = 'PASSWORD';
	            $mail->SMTPSecure = 'tsl';

	            $mail->From = $mailfrom;
	            $mail->FromName = $mailfrom;
	            $mail->AddAddress($newemail, "");

	            // Указывается Email здесь, на данный момент берёт значение $email из массива со всеми E-mail'ами
	            $mail->AddAddress(strtolower(trim($email)), "");

	            $mail->AddReplyTo($mailfrom);

	            $mail->Body = stripslashes($message);
	            $mail->Subject = stripslashes($subject);
	            $mail->WordWrap = 80;

	            if (!$mail->Send())
	            {
	            	// Запись в лог файл сведения об ошибке
	            	$log = fopen("log.txt","a");
					fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\n1 часть - Ошибка при отправке $email, \n\n".$mail->ErrorInfo.",  \n\n$message"));
					fclose($log);
	                continue;
	            } else {sleep(2);}
	            
	        }// for each emails
	 

			foreach ($secondhalf as $email){

	            $mailfrom = 'MY_EMAIL_2@gmail.com';

				$id = mb_convert_encoding($file[0], "UTF-8", "WINDOWS-1251");
	            $subject = "Новая заявка по грузоперевозкам $id";

	            // Формируется текст письма
	            $message = "";
	            foreach ($file as $value) {
	                $message .= mb_convert_encoding($value, "UTF-8", "WINDOWS-1251")."\n\n";
	            }
	            
	            // Настройка PHPMailer и SMTP
	            $mail = new PHPMailer();
	            $mail->IsSMTP();
	            $mail->CharSet = "utf-8";
	            $mail->Host = 'smtp.gmail.com';
	            $mail->Port = 587;
	            $mail->SMTPAuth = true;
	            $mail->Username = 'MY_EMAIL_2@gmail.com';
	            $mail->Password = 'PASSWORD';
	            $mail->SMTPSecure = 'tsl';

	            $mail->From = $mailfrom;
	            $mail->FromName = $mailfrom;
	            $mail->AddAddress($newemail, "");

	            // Указывается Email здесь, на данный момент берёт значение $email из массива со всеми E-mail'ами
	            $mail->AddAddress(strtolower(trim($email)), "");

	            $mail->AddReplyTo($mailfrom);

	            $mail->Body = stripslashes($message);
	            $mail->Subject = stripslashes($subject);
	            $mail->WordWrap = 80;

	            if (!$mail->Send())
	            {
	            	// Запись в лог файл сведения об ошибке
	            	$log = fopen("log.txt","a");
					fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\n2 часть - Ошибка при отправке $email, \n\n".$mail->ErrorInfo.",  \n\n$message"));
					fclose($log);
	                continue;
	            } else {sleep(2);}
	            
	        }// for each emails

	        unlink($file_name);

	    }// for each files

	    $check = fopen("check.txt","w");
		fwrite($check, "OFF");
		fclose($check);

		// Запись в лог файл сведения об успехе
		$log = fopen("log.txt","a");
		fwrite($log, iconv("UTF-8", "WINDOWS-1251", "\n\nРассылка успешно завершена"));
		fclose($log);
	}
	
} else {exit();}

?>
