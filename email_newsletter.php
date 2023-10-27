<?php

include "api.php";

session_start();
if (!in_array(trim($_SESSION['Status']), ["Менеджер","Администратор"])){header('Location: /yuk247.php');}

if (isset($_GET['id'])){
   $id = $_GET['id'];
   
   $conn = connect_to_db();

   $sql = "SELECT * FROM zayavki WHERE ID='$id'";
   $result = mysqli_query($conn, $sql);
   $row = mysqli_fetch_assoc($result);

if ($row['Application_type'] == "Груз"){

         // Формируем файл для рассылки Груз
        $text = "№ ".$id." ".$row['Application_type']."|";    
        $text .= $row['Cargo_name']."|";    
        $text .= $row['Cargo_category']."|";

        $text .= "Из ".$row['From_where']." ".$row['Adress_1']." в ".$row['Where_to']." ".$row['Adress_2']."|";
        $text .= "С ".DateTime::createFromFormat('Y-m-d', $row['Start_no_later'])->format('d.m.Y')." до ".DateTime::createFromFormat('Y-m-d', $row['Finish_no_later'])->format('d.m.Y')."|";
        $text .= "Вес груза в тонн: ".$row['Weight_in_ton']."|";
        $text .= "Размер груза в m3: ".$row['Size_in_m3']."|";
        $text .= "Нужно таможенное оформление?: ".$row['Need_customs']."|";
        $text .= "Цена предложения USD: ".$row['Price_USD']."|";
        $text .= "Условия оплаты: ".$row['Terms_Payment']."|";
        $text .= "Дополнительная информация: ".$row['Comment']."|";

        $text .= "http://oblakotm.com/yuk247.php|";
        $text .= "Наш сервис предоставляет услуги исключительно как справочник. Мы не оказываем услуги транспортного или экспедиционного характера|";
        $text .= "744000 Address,Turkmenistan, Ashgabat|";
        $text .= "+99364 93 04 67|";
        $text .= "+99361 52 91 13|";
        $text .= "haytektm@gmail.com|";
        $text .= "Copyright 2023 ES Haytek|";
        $text .= "Не отвечайте на это письмо, оно было создано автоматически|";

        // Записываем файл
	$file = fopen("mailer/new_application_".date('Y-m-d')."_".date('G-i-s')."_.txt","w") or die("Unable to open file!");
        fwrite($file, iconv("UTF-8", "WINDOWS-1251", $text));


	mysqli_close($conn);
	header('Location: /yuk247.php');
   } else {

// Формируем файл для рассылки Транспорт
        $text = "№ ".$id." ".$row['Application_type']."|";  
        $text .= $row['Type_transport']."|";
        $text .= "Из ".$row['From_where']." ".$row['Adress_1']." в ".$row['Where_to']." ".$row['Adress_2']."|";
        $text .= "С ".DateTime::createFromFormat('Y-m-d', $row['Start_no_later'])->format('d.m.Y')." до ".DateTime::createFromFormat('Y-m-d', $row['Finish_no_later'])->format('d.m.Y')."|";
        $text .= "Свободное место в m3: ".$row['Size_in_m3']."|";
        $text .= "Цена предложения USD: ".$row['Price_USD']."|";
        $text .= "Условия оплаты: ".$row['Terms_Payment']."|";
        $text .= "Дополнительная информация: ".$row['Comment']."|";

        $text .= "http://oblakotm.com/yuk247.php|";
        $text .= "Наш сервис предоставляет услуги исключительно как справочник. Мы не оказываем услуги транспортного или экспедиционного характера|";
        $text .= "744000 Address,Turkmenistan, Ashgabat|";
        $text .= "+99364 93 04 67|";
        $text .= "+99361 52 91 13|";
        $text .= "haytektm@gmail.com|";
        $text .= "Copyright 2023 ES Haytek|";
        $text .= "Не отвечайте на это письмо, оно было создано автоматически|";

        // Записываем файл
	$file = fopen("mailer/new_application_".date('Y-m-d')."_".date('G-i-s')."_.txt","w") or die("Unable to open file!");
        fwrite($file, iconv("UTF-8", "WINDOWS-1251", $text));

	mysqli_close($conn);
	header('Location: /yuk247.php');
   }
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>E-mail Рассылка</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/email_newsletter.css" rel="stylesheet">
</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Heading1">E-mail Рассылка</h1>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./yuk247.php" style="display:inline-block;width:100px;height:48px;z-index:1;">Главная</a>
<a id="Button1" href="./clients.php" style="display:inline-block;width:100px;height:48px;z-index:2;">Клиенты</a>
<a id="Button3" href="./logs.php" style="display:inline-block;width:100px;height:48px;z-index:3;">Логи</a>
<a id="Button4" href="./admin.php" style="display:inline-block;width:100px;height:48px;z-index:4;">Заявки</a>
</div>
</div>
</div>
</div>

<div id="wb_LayoutGrid2">
<form name="LayoutGrid2" method="get" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" accept-charset="UTF-8" target="_self" id="LayoutGrid2">
<div class="row">
<div class="col-1">
</div>
<div class="col-2">
<input type="text" id="Editbox1" style="display:block;width: 100%;height:56px;z-index:5;" name="id" value="" spellcheck="false" placeholder="Введите ID заявки">
<input type="submit" id="Button5" name="" value="Submit" style="display:inline-block;width:124px;height:52px;z-index:6;">
</div>
<div class="col-3">
</div>
</div>
</form>
</div>

</body>
</html>