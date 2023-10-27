<?php 

include "api.php";

session_start();

date_default_timezone_set('Asia/Ashgabat');

$conn = connect_to_db();
check_token($conn);
save_logs($conn, "addawto.php");

//------------------Отправка заявки-----------------------


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $Start_no_later_error = "";
        $Finish_no_later_error = "";
        $Who_added_error = "";
        $Phone_error = "";
        $Email_error = "";

        $error = "";

        //Проверяем на незаполненные поля
        if (empty($_POST['Start_no_later'])){$Start_no_later_error = "Заполните поле!"; $error = "Error!";}
        if (empty($_POST['Finish_no_later'])){$Finish_no_later_error = "Заполните поле!"; $error = "Error!";}
        if (empty($_POST['Who_added'])){$Who_added_error = "Заполните поле!"; $error = "Error!";}
        if (empty($_POST['Phone'])){$Phone_error = "Заполните поле!"; $error = "Error!";}
        if (empty($_POST['Email'])){$Email_error = "Заполните поле!"; $error = "Error!";}
        

        if(empty($error)){

	$form_data = array();
        $text = '';

	//Проверяем данные
	foreach ($_POST as $name => $value) {
		$form_data[$name] = test_input($value);
	}


        // Втавляем запись со второстепенными полями
	mysqli_query($conn, "INSERT INTO zayavki (`DATESTAMP`,`TIME`,`IP`,`BROWSER`)
	           VALUES ('".date("Y-m-d")."',
	           '".date("G:i:s")."',
	           '".$_SERVER['REMOTE_ADDR']."',
	           '".$_SERVER['HTTP_USER_AGENT']."')")or die('Failed to insert data into table!<br>'.mysqli_error($conn)); 

        // Берём последний id, т.е. id только что отправленной записи
	$id = mysqli_insert_id($conn);

        // Добавляем к нашей записи основные поля из заявки
	foreach($form_data as $name=>$value){
		mysqli_query($conn, "UPDATE zayavki SET $name='".mysqli_real_escape_string($conn, $value)."' WHERE ID=$id") or die('Failed to update table!<br>'.mysqli_error($conn));
	}
        
        // Формируем файл для рассылки
        $text = "№ ".$id." ".$_POST['Application_type']."|";  

        $text .= $_POST['Type_transport']."|";
        $text .= "Из ".$_POST['From_where']." ".$_POST['Adress_1']." в ".$_POST['Where_to']." ".$_POST['Adress_2']."|";

        $text .= "С ".DateTime::createFromFormat('Y-m-d', $_POST['Start_no_later'])->format('d.m.Y')." до ".DateTime::createFromFormat('Y-m-d', $_POST['Finish_no_later'])->format('d.m.Y')."|";

        $text .= "Свободное место в m3: ".$_POST['Size_in_m3']."|";
        $text .= "Цена предложения USD: ".$_POST['Price_USD']."|";
        $text .= "Условия оплаты: ".$_POST['Terms_Payment']."|";
        $text .= "Дополнительная информация: ".$_POST['Comment']."|";

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
         
	header('Location: /yuk247.php');
      }

}

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Добавить Транспорт</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/addawto.css" rel="stylesheet">
</head>
<body>
<div id="wb_LayoutGrid4">
<div id="LayoutGrid4">
<div class="col-1">
<div id="wb_Gruzoperevozki" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Gruzoperevozki">Грузоперевозки 24/7</h1>
</div>
<div id="wb_Gruzoperevozki_desc" style="display:inline-block;width:100%;z-index:1;">
<h1 id="Gruzoperevozki_desc">по Туркменистану и странам СНГ</h1>
</div>
<div id="wb_Text4">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><strong>+993 64 93 04 67</strong></span>
</div>
</div>
<div class="col-2">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:3;">
<h1 id="Heading1">Добавить заявку на Транспорт</h1>
</div>
</div>
<div class="col-3">
<div id="wb_Text1">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">Укажите пожалуйста тип транспорта и другие необходимые данные, как можно подробнее!</span>
</div>
</div>
</div>
</div>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
</div>
</div>
</div>
</div>
<form name="Add_Transport" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" accept-charset="UTF-8" id="Add_Transport">
<div id="wb_Add_Transport_layout">
<div id="Add_Transport_layout">
<div class="row">
<div class="col-1">
<input type="text" id="Editbox1" style="display:none;width: 100%;height:31px;z-index:5;" name="Application_type" value="Транспорт" readonly spellcheck="false">
<input type="text" id="Editbox2" style="display:none;width: 100%;height:31px;z-index:6;" name="Application_Status" value="Открыта" readonly spellcheck="false">
<div id="wb_Text2">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Тип транспорта</strong></span>
</div>
<select name="Type_transport" size="1" id="Type_transport" style="display:block;width: 100%;height:32px;z-index:8;">
<option value="">Выберите тип транспорта</option>
<option value="Тент">Тент</option>
<option value="Рефрижератор">Рефрижератор</option>
<option value="Изотремический">Изотремический</option>
<option value="Полуприцеп">Полуприцеп</option>
<option value="JUMBO">JUMBO</option>
<option value="Контейнеровоз">Контейнеровоз</option>
<option value="Бортовая платформа">Бортовая платформа</option>
<option value="Открытая платформа">Открытая платформа</option>
<option value="Контейнер">Контейнер</option>
<option value="Судно">Судно</option>
<option value="Авиатранспорт">Авиатранспорт</option>
</select>
<div id="wb_Text3">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Место отправки транспорта</strong></span>
</div>
<select name="From_where" size="1" id="From_where" style="display:block;width: 100%;height:32px;z-index:10;">
<option value="">Выбрать страну</option>
<option value="Австрия">Австрия</option>
<option value="Азербайджан">Азербайджан</option>
<option value="Армения">Армения</option>
<option value="Афганистан">Афганистан</option>
<option value="Белоруссия">Белоруссия</option>
<option value="Бельгия">Бельгия</option>
<option value="Болгария">Болгария</option>
<option value="Германия">Германия</option>
<option value="Грузия">Грузия</option>
<option value="Иран">Иран</option>
<option value="Казахстан">Казахстан</option>
<option value="Киргизия">Киргизия</option>
<option value="Китай">Китай</option>
<option value="Латвия">Латвия</option>
<option value="Литва">Литва</option>
<option value="Молдова">Молдова</option>
<option value="Монголия">Монголия</option>
<option value="ОАЭ">ОАЭ</option>
<option value="Польша">Польша</option>
<option value="РФ">РФ</option>
<option value="Румыния">Румыния</option>
<option value="Сербия">Сербия</option>
<option value="Таджикистан">Таджикистан</option>
<option value="Туркменистан">Туркменистан</option>
<option value="Турция">Турция</option>
<option value="Узбекистан">Узбекистан</option>
<option value="Украина">Украина</option>
<option value="Франция">Франция</option>
<option value="Эстония">Эстония</option>
<option value="Южная Корея">Южная Корея</option>
<option value="Другое">Другое</option>
</select>
<div id="wb_Text5">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Город, адрес и т.д. (забр.)</strong></span>
</div>
<input type="text" id="Adress_1" style="display:block;width: 100%;height:31px;z-index:12;" name="Adress_1" value="" spellcheck="false">
<div id="wb_Text6">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Дата отправки транспорта не позже</strong></span>
</div>
<input type="text" id="Start_no_later" style="display:block;width: 100%;height:31px;z-index:14;" name="Start_no_later" value="" spellcheck="false" placeholder="<?php echo $Start_no_later_error;?>" onfocus="(this.type='date')">
</div>
<div class="col-2">
<div id="wb_Text7">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Место прибытия транспорта</strong></span>
</div>
<select name="Where_to" size="1" id="Where_to" style="display:block;width: 100%;height:32px;z-index:16;">
<option value="">Выбрать страну</option>
<option value="Австрия">Австрия</option>
<option value="Азербайджан">Азербайджан</option>
<option value="Армения">Армения</option>
<option value="Афганистан">Афганистан</option>
<option value="Белоруссия">Белоруссия</option>
<option value="Бельгия">Бельгия</option>
<option value="Германия">Германия</option>
<option value="Грузия">Грузия</option>
<option value="Иран">Иран</option>
<option value="Казахстан">Казахстан</option>
<option value="Киргизия">Киргизия</option>
<option value="Китай">Китай</option>
<option value="Латвия">Латвия</option>
<option value="Литва">Литва</option>
<option value="Молдова">Молдова</option>
<option value="Монголия">Монголия</option>
<option value="ОАЭ">ОАЭ</option>
<option value="Польша">Польша</option>
<option value="РФ">РФ</option>
<option value="Таджикистан">Таджикистан</option>
<option value="Туркменистан">Туркменистан</option>
<option value="Турция">Турция</option>
<option value="Узбекистан">Узбекистан</option>
<option value="Украина">Украина</option>
<option value="Франция">Франция</option>
<option value="Эстония">Эстония</option>
<option value="Южная Корея">Южная Корея</option>
<option value="Другое">Другое</option>
</select>
<div id="wb_Text8">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Город, адрес и т.д. (приб.)</strong></span>
</div>
<input type="text" id="Adress_2" style="display:block;width: 100%;height:31px;z-index:18;" name="Adress_2" value="" spellcheck="false">
<div id="wb_Text9">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Дата прибытия транспорта</strong></span>
</div>
<input type="text" id="Finish_no_later" style="display:block;width: 100%;height:31px;z-index:20;" name="Finish_no_later" value="" spellcheck="false" placeholder="<?php echo $Finish_no_later_error;?>" onfocus="(this.type='date')">
<div id="wb_Text10">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Свободное место в m3</strong></span>
</div>
<input type="number" id="Size_in_m3" style="display:block;width: 100%;height:31px;z-index:22;" name="Size_in_m3" value="0" spellcheck="false" step="0.01">
<div id="wb_Text13">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Цена предложения в USD</strong></span>
</div>
<input type="number" id="Price_USD" style="display:block;width: 100%;height:31px;z-index:24;" name="Price_USD" value="0" spellcheck="false">
<div id="wb_Text14">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Условия оплаты</strong></span>
</div>
<select name="Terms_Payment" size="1" id="Terms_Payment" style="display:block;width: 100%;height:32px;z-index:26;">
<option value=""></option>
<option value="Только предоплата">Только предоплата</option>
<option value="Можно частично">Можно частично</option>
</select>
</div>
<div class="col-3">
<div id="wb_Text15">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Дополнительная информация</strong></span>
</div>
<input type="text" id="Comment" style="display:block;width: 100%;height:97px;z-index:28;" name="Comment" value="" spellcheck="false">
<div id="wb_Text16">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Кто добавил</strong></span>
</div>
<input type="text" id="Who_added" style="display:block;width: 100%;height:31px;z-index:30;" name="Who_added" value="<?php echo $_SESSION['FIO'];?>" spellcheck="false" placeholder="<?php echo $Who_added_error;?>">
<div id="wb_Text17">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>Телефон</strong></span>
</div>
<input type="text" id="Phone" style="display:block;width: 100%;height:31px;z-index:32;" name="Phone" value="<?php echo $_SESSION['Phone'];?>" spellcheck="false" placeholder="<?php echo $Phone_error;?>">
<div id="wb_Text18">
<span style="color:#000000;font-family:Arial;font-size:13px;"><strong>E-mail</strong></span>
</div>
<input type="text" id="Email" style="display:block;width: 100%;height:31px;z-index:34;" name="Email" value="<?php echo $_SESSION['Email'];?>" spellcheck="false" placeholder="<?php echo $Email_error;?>">
<input type="submit" id="Button2" name="" value="Добавить" style="display:inline-block;width:98px;height:35px;z-index:35;">
<a id="Button3" href="./yuk247.php" style="display:inline-block;width:98px;height:35px;z-index:36;">Вернуться</a>
</div>
</div>
</div>
</div>
</form>
<div id="wb_LayoutGrid3">
<div id="LayoutGrid3">
<div class="row">
<div class="col-1">
</div>
</div>
</div>
</div>
<div id="wb_Footer">
<div id="Footer">
<div class="row">
<div class="col-1">
<div id="wb_Text11">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">744000 Address,Turkmenistan, Ashgabat<br>+99364 93 04 67<br>+99361 52 91 13<br>haytektm@gmail.com<br></span><span style="color:#FFFFFF;font-family:Arial;font-size:12px;"><br><strong>Copyright 2023 ES Haytek</strong></span><span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><br></span>
</div>
</div>
</div>
</div>
</div>
</body>
</html><?php mysqli_close($conn); ?>