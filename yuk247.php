<?php

include "api.php";
use PHPMailer\PHPMailer\PHPMailer;
require __DIR__.'/Exception.php';
require __DIR__.'/PHPMailer.php';
require __DIR__.'/SMTP.php';


session_start();
if (strpos($_SERVER['REQUEST_URI'], '?exit')){session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header('Location: /yuk247.php');}

$conn = connect_to_db();

date_default_timezone_set('Asia/Ashgabat');

check_token($conn);
save_logs($conn, 'yuk247.php');

list($count_all, $count_gruz, $count_allgruz, $count_transport, $count_alltransport, $count_open, $count_sovpad, $count_from_tkm, $count_to_tkm, $count_today, $date_today, $count_overdue) = filters_count_app($conn);

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['formname']) && $_POST['formname'] == "Contacts") {
    
    $message = "";

    foreach ($_POST as $key => $value){
        $message .= $key.": ".test_input($value)."\n";
    }

    $mail_array = ["haytektm@gmail.com", "tekhaytek@mail.ru"];
            foreach ($mail_array as $email){
                $mailfrom = 'haytek.club@mail.ru';
                $subject = 'Отзыв по Грузоперевозкам';

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
                        continue;
                    }                     
            }// foreach
                                     
            header('Location: /yuk247.php');
}


?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Грузоперевозки 24/7</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/yuk247.css" rel="stylesheet">

<!-- Yandex.Metrika counter -->
<noscript><div><img src="https://mc.yandex.ru/watch/95001728" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->





</head>
<body>
<div id="wb_Header">
<div id="Header">
<div class="col-1">
<div id="wb_Gruzoperevozki" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Gruzoperevozki">Грузоперевозки 24/7</h1>
</div>
<div id="wb_Gruzoperevozki_desc" style="display:inline-block;width:100%;z-index:1;">
<h1 id="Gruzoperevozki_desc">по Туркменистану и странам СНГ</h1>
</div>
<div id="wb_Phone">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><strong>+993 64 93 04 67</strong></span>
</div>
</div>
<div class="col-2">
<a id="Add_gruz" href="./addgruz.php" style="display:inline-block;width:177px;height:40px;z-index:3;">Добавить груз <?php echo $count_allgruz;?></a>
<a id="Add_transport" href="./addawto.php" style="display:inline-block;width:177px;height:40px;z-index:4;">Добавить транспорт <?php echo $count_alltransport;?></a>
</div>
<div class="col-3">
<!-- Username -->
<?php if(!empty($_SESSION["Username"])){echo '<button type="submit" name="username" style="background: transparent; color: white; border: 1px solid white; border-radius: 4px; min-width: 100px; height: 34px;">'.$_SESSION["Username"]." ".$_SESSION["Status"].'</button>';}?>
<!-- Login -->
<?php if(empty($_SESSION["Username"])){echo '<button type="submit" style="background: transparent; color: white; border: 1px solid white; border-radius: 4px; width: 100px; height: 34px;"><a href="/login.php">Вход</a></button>';}?>
<a id="Button2" href="./signup.php" style="display:inline-block;width:98px;height:35px;z-index:7;">Регистрация</a>
<!-- Админ -->
<?php if(in_array($_SESSION['Status'],['Менеджер','Администратор']) && !empty($_SESSION['Username'])){echo '<button type="submit" id="admin_link" style="background: transparent; color: white; border: 1px solid white; border-radius: 4px; width: 100px; height: 34px;"><a href="/admin.php">Админ</a></button>';}?>
<!-- Выйти -->
<?php if(!empty($_SESSION['Username'])){echo '<button type="submit" id="exit_link" style="background: transparent; color: white; border: 1px solid white; border-radius: 4px; width: 100px; height: 34px;"><a href="?exit">Выйти</a></button>';}?>
</div>
</div>
</div>
<div id="wb_Free_layout">
<div id="Free_layout">
<div class="col-1">
<nav id="wb_ThemeableMenu1" style="display:inline-block;width:100%;z-index:10;">
<div id="ThemeableMenu1" class="ThemeableMenu1" style="width:100%;height:auto !important;">
<div class="container">
<div class="navbar-header">
<button title="Hamburger Menu" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".ThemeableMenu1-navbar-collapse">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
</div>
<div class="ThemeableMenu1-navbar-collapse collapse">
<ul class="nav navbar-nav">
<li class="nav-item">
<a href="?showall" class="nav-link"><i class="fa fa-database"></i>Все заявки <?php echo $count_all;?></a>
</li>
<li class="nav-item dropdown">
<a href="#" class="dropdown-toggle" data-toggle="dropdown" ><i class="fa fa-unlock"></i>Открытые <?php echo $count_open;?><b class="caret"></b></a>
<ul class="dropdown-menu">
<li class="nav-item dropdown-item">
<a href="?showopen" class="nav-link"><i class="fa fa-database"></i>Все <?php echo $count_open;?></a>
</li>
<li class="nav-item dropdown-item">
<a href="?showgruz" class="nav-link"><i class="fa fa-archive"></i>Грузы <?php echo $count_gruz;?></a>
</li>
<li class="nav-item dropdown-item">
<a href="?showtransport" class="nav-link"><i class="fa fa-truck"></i>Транспорты  <?php echo $count_transport;?></a>
</li>
</ul>
</li>
<li class="nav-item">
<a href="?showsovpad" class="nav-link"><i class="fa fa-map-o"></i>Совпадения <?php echo $count_sovpad;?></a>
</li>
<li class="nav-item">
<a href="?showtoday" class="nav-link"><i class="fa fa-calendar"></i>На сегодня <?php echo $count_today;?></a>
</li>
<li class="nav-item">
<a href="./charts.php" target="_blank" class="nav-link"><i class="fa fa-bar-chart"></i>График</a>
</li>
<li class="nav-item">
<a href="./tarif.php" target="_blank" class="nav-link"><i class="fa fa-balance-scale"></i>Тарифы</a>
</li>
</ul>
</div>
</div>
</div>
</nav>
</div>
</div>
</div>
<div id="wb_Layout_table">
<div id="Layout_table">
<div class="row">
<div class="col-1">
<!-- Таблица -->
<div id="Html3" style="display:inline-block;width:100%;height:615px;overflow:scroll;z-index:11">
<?php
// Проверяет на мобильный телефон
$isMobile = is_mobile();
                    

$sql = check_filter($conn);

$result = mysqli_query($conn, $sql);



if ($isMobile){
    if (in_array(trim($_SESSION['Status']), ["Пользователь","Менеджер","Администратор"])){
      $contacts = True;
    } else {
      $contacts = False;
      $contacts_body = '<span class="hidden-contacts"><b>Контакты: </b>';
		if (empty($_SESSION['Username'])){$contacts_body .= 'Для отображения зарегистрируйтесь';} else {$contacts_body .= 'Истёк срок Вашего доступа';}
        $contacts_body .= "</span>";
    }

    while($row = mysqli_fetch_assoc($result)) {
      if ($contacts){
         $contacts_body = '
         <span><b>ФИО:</b> '.$row["Who_added"].'</span>
         <hr>
 
         <span><b>Телефон:</b> '.$row["Phone"].'</span>
         <hr>
 
         <span><b>E-mail:</b> '.$row["Email"].'</span>';
      }

        if($row['Start_no_later']<=$date_today and $row['Application_Status'] == 'Открыта'){$overdue_start = ' style="color: red;"';}else{$overdue_start = '';}
        if($row['Finish_no_later']<=$date_today and $row['Application_Status'] == 'Открыта'){$overdue_finish = ' style="color: red;"';}else{$overdue_finish = '';}
            
        echo '<div class="card">
                <span class="datetime">'.DateTime::createFromFormat('Y-m-d', $row["Datestamp"])->format('d.m.Y').'</span>
                <br>
                <span><b>№ '.$row["ID"].'</b></span>
                <br>';
                
                if ($row["Application_Status"] == "Открыта"){echo '<span style="color: green;"><b>Статус:</b> '.$row["Application_Status"].'</span><br>';
                
                } else {echo '<span style="color: red;"><b>Статус:</b> '.$row["Application_Status"].'</span><br>';}

                if ($row["Application_type"] == "Груз"){echo '
                
                <span style="color: #1E90FF;"><b>Тип:</b> '.$row["Application_type"].'</span>
                <hr>
                <span>'.$row["Cargo_name"].'</span>
                <hr>';
                
                } else {echo '
                <span style="color: #008000;"><b>Тип:</b> '.$row["Application_type"].'</span>
                <hr>
                ';}
                
                echo '
                <span><b>Категория груза / транспорта:</b> '.$row["Cargo_category"].$row['Type_transport'].'</span>
                <hr>
                <span><b>Страна отправки:</b> '.$row["From_where"].'</span>
                <hr>
                <span><b>Город и адрес отправки:</b> '.$row["Adress_1"].'</span>
                <hr>
                <span'.$overdue_start.'><b>Отправка не позже даты:</b> '.DateTime::createFromFormat('Y-m-d', $row["Start_no_later"])->format('d.m.Y').'</span>
                <hr>
                <span><b>Страна назначения:</b> '.$row["Where_to"].'</span>
                <hr>
                <span><b>Город и адрес назначения:</b> '.$row["Adress_2"].'</span>
                <hr>
                <span'.$overdue_finish.'><b>Прибытие не позже даты:</b> '.DateTime::createFromFormat('Y-m-d', $row["Finish_no_later"])->format('d.m.Y').'</span>
                <hr>
                <span><b>Вес груза в тоннах:</b> '.$row["Weight_in_ton"].'</span>
                <hr>
                <span><b>Размер груза в m<sup>3</sup>:</b> '.$row["Size_in_m3"].'</span>
                <hr>
                <span><b>Нужно таможенное оформление?:</b> '.$row["Need_Customs"].'</span>
                <hr>
                <span><b>Цена предложения USD:</b> '.$row["Price_USD"].'</span>
                <hr>
                <span><b>Условия оплаты:</b> '.$row["Terms_Payment"].'</span>
                <hr>
                <span><b>Дополнительная информация:</b> '.$row["Comment"].'</span>
                <hr>

                '.$contacts_body.'      

                </div>';
    }


} else {

    if (in_array(trim($_SESSION['Status']), ["Пользователь","Менеджер","Администратор"])){
      $contacts = True;
      $contacts_headers = "<th>ФИО</th><th>Телефон</th><th>E-mail</th>";
    } else {
      $contacts = False;
      $contacts_body = '<td>';
      if (empty($_SESSION['Username'])){echo $contacts_body .= 'Для отображения зарегистрируйтесь';} else {echo $contacts_body .= 'Истёк срок Вашего доступа';}
      
      $contacts_body .= '</td>';
      $contacts_headers = "<th>Контакты</th>";
    }

    echo  '<table class="sql_table">
            <thead>
                <tr>
                    <th>№</th><th>Дата заявки</th><th>Статус</th><th>Тип заявки</th><th>Наименование груза</th><th>Категория <br>груза / транспорта</th><th>Страна отправки</th><th>Город и адрес отправки</th><th>Отправка не позже даты</th><th>Страна назначения</th><th>Город и адрес назначения</th><th>Прибытие не позже даты</th><th>Вес груза в тоннах</th><th>Размер груза в m3</th><th>Нужно таможенное оформление?</th><th>Цена предложения USD</th><th>Условия оплаты</th><th>Дополнительная информация</th>'.$contacts_headers.'
            </tr></thead><tbody>';
    

    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      if ($contacts) {$contacts_body = '<td>'.$row["Who_added"].'</td> <td>'.$row["Phone"].'</td> <td>'.$row["Email"].'</td>';}
        
      if($row['Start_no_later']<$date_today && $row["Application_Status"]  == 'Открыта'){$overdue_start = ' style="color: red;"';}else{$overdue_start = '';}
      if($row['Finish_no_later']<$date_today && $row["Application_Status"]  == 'Открыта'){$overdue_finish = ' style="color: red;"';}else{$overdue_finish = '';}
      if($row['Finish_no_later'] == '0000-00-00'){$finish_no_later = '';}else{$finish_no_later = DateTime::createFromFormat('Y-m-d', $row["Finish_no_later"])->format('d.m.Y');}
      
      if ($row["Application_Status"]  == 'Открыта' and $row["Application_type"] == 'Транспорт'){$app_class = "open_trans";} 
      elseif ($row["Application_Status"]  == 'Закрыта' and $row["Application_type"] == 'Транспорт'){$app_class = "close_trans";} 
      elseif ($row["Application_Status"]  == 'Открыта' and $row["Application_type"] == 'Груз') {$app_class = "open_gruz";}  
      elseif ($row["Application_Status"]  == 'Закрыта' and $row["Application_type"] == 'Груз') {$app_class = "close_gruz";}

      if ($row["Application_type"] == 'Груз') {$type = $row["Cargo_category"];} else {$type = $row["Type_transport"];}

      echo '<tr class="'.$app_class.'"><td class="col-id">'.$row["ID"].'</td> <td>'.DateTime::createFromFormat('Y-m-d', $row["Datestamp"])->format('d.m.Y').'</td> <td>'.$row["Application_Status"].'</td> <td>'.$row["Application_type"].'</td> <td>'.$row["Cargo_name"].'</td> <td>'.$type.'</td> <td>'.$row["From_where"].'</td> <td>'.$row["Adress_1"].'</td> <td'.$overdue_start.'>'.DateTime::createFromFormat('Y-m-d', $row["Start_no_later"])->format('d.m.Y').'</td> <td>'.$row["Where_to"].'</td> <td>'.$row["Adress_2"].'</td> <td'.$overdue_finish.'>'.$finish_no_later.'</td> <td>'.$row["Weight_in_ton"].'</td> <td>'.$row["Size_in_m3"].'</td> <td>'.$row["Need_customs"].'</td> <td>'.$row["Price_USD"].'</td> <td>'.$row["Terms_Payment"].'</td> <td>'.$row["Comment"].'</td> '.$contacts_body.' </tr>';

    }//while
    
    echo '</tbody></table>';
}
?>

</div>
</div>
</div>
</div>
</div>
<div id="wb_Layout_head_possibilities">
<div id="Layout_head_possibilities">
<div class="row">
<div class="col-1">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:12;">
<h1 id="Heading1">Условия и возможности</h1>
</div>
</div>
</div>
</div>
</div>
<div id="wb_Layout_possibilities">
<div id="Layout_possibilities">
<div class="row">
<div class="col-1">
<div id="wb_Image1" style="display:inline-block;width:100%;height:auto;z-index:13;">
<img src="images/gruz.jpg" id="Image1" alt="" width="212" height="142">
</div>
<div id="wb_Text1" style="min-height: 250px;">
<span style="color:#5B4F46;font-family:Arial;font-size:15px;line-height:22px;"><strong>Разместите свой груз для поиска наилучшего предложения от наших перевозчиков. <br><br>При заполнении полей необходимо наиболее подробно и точно указать характеристики Вашего груза вес, размеры и т.д.</strong></span>
</div>
<a id="Button4" href="./addgruz.php" style="display:inline-block;width:131px;height:35px;z-index:15;">Добавить груз</a>
</div>
<div class="col-2">
<div id="wb_Image2" style="display:inline-block;width:100%;height:auto;z-index:16;">
<img src="images/awto.jpg" id="Image2" alt="" width="212" height="141">
</div>
<div id="wb_Text2" style="min-height: 250px;">
<span style="color:#5B4F46;font-family:Arial;font-size:15px;line-height:22px;"><strong>Вы можете разместить информацию о запланированной маршруте Вашего транспорта с точным указанием откуда и куда, а также дату отправки и прибытия транспорта.<br><br>Вы также можете указать дополнительно города и даты прохождения Вашим транспортом для получения заявки на догрузку.</strong></span>
</div>
<a id="Button5" href="./addawto.php" style="display:inline-block;width:144px;height:35px;z-index:18;">Добавить транспорт</a>
</div>
<div class="col-3">
<div id="wb_Image3" style="display:inline-block;width:100%;height:auto;z-index:19;">
<img src="images/parter.jpg" id="Image3" alt="" width="212" height="159">
</div>
<div id="wb_Text3" style="min-height: 250px;">
<span style="color:#5B4F46;font-family:Arial;font-size:15px;line-height:22px;"><strong>Мы не транспортная компания у нас нет цели выбирать для себя или для кого-то лучшее предложение в ущерб остальным пользователям сервиса.<br><br>Все пользователи нашего сервиса поставлены в равных условиях и преимущество имеет только тот, кто даёт наиболее точную и своевременную информацию о своих грузах или машинах.</strong></span>
</div>
<a id="Button6" href="./login.php" style="display:inline-block;width:125px;height:35px;z-index:21;">Вход</a>
</div>
<div class="col-4">
<div id="wb_Image4" style="display:inline-block;width:100%;height:auto;z-index:22;">
<img src="images/pay.jpg" id="Image4" alt="" width="212" height="143">
</div>
<div id="wb_Text4" style="min-height: 250px;">
<span style="color:#5B4F46;font-family:Arial;font-size:15px;line-height:22px;"><strong>Каждый пользователь сервиса получает после регистрации БЕСПЛАТНЫЙ доступ на 7 дней, а по истечению срока мы предлагаем перейти на ежемесячный платный тариф 350 тмт, где Вы получаете доступ к полной контактной информации участников. <br>Также Вы можете ознакомиться с </strong></span><span style="background-color:#228B22;color:#00008B;font-family:Arial;font-size:15px;line-height:22px;"><strong><a href="./tarif.php" target="_blank">тарифами </a></strong></span><span style="color:#5B4F46;font-family:Arial;font-size:15px;line-height:23px;"><strong>для наболее выгодного предложения.</strong></span>
</div>
<a id="Button7" href="./signup.php" style="display:inline-block;width:142px;height:35px;z-index:24;">Регистрация</a>
</div>
</div>
</div>
</div>
<div id="wb_Contacts_head">
<div id="Contacts_head">
<div class="row">
<div class="col-1">
<div id="wb_Heading2" style="display:inline-block;width:100%;z-index:25;">
<h1 id="Heading2">Контакт</h1>
</div>
</div>
</div>
</div>
</div>
<form name="Contacts" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Contacts">
<input type="hidden" name="formname" value="Contacts"><div id="wb_Contacts">
<div id="Contacts">
<div class="row">
<div class="col-1">
<div id="wb_Text7">
<span style="color:#3CB371;font-family:Arial;font-size:13px;"><strong><em>«Туркменистан убеждён: транспортная архитектура XXI столетия – это архитектура интеграционного прорыва, соединения пространств, регионов, промышленных, ресурсных, людских потенциалов.&quot; <br><br>&quot;Будущее – за комбинированной системой транспортного сообщения, с выходом на крупнейшие международные и региональные морские, речные, автомобильные, железнодорожные и воздушные узлы, их оптимальным сочетанием и использованием преимуществ каждого из них»,<br></em><br>Из выступления Президента Туркменистана Сердара Бердымухаммедова на Конференции в ОАЭ 21 ноября 2022 г.<br><br></strong></span><span style="color:#00008B;font-family:Arial;font-size:13px;"><strong><a href="https://turkmenistan.gov.tm/index.php/ru/post/68233/prezident-serdar-berdymuhamedov-transportnaya-logistika-turkmenistana-vazhnejshee-zveno-tranzitnoj-infrastruktury-evrazijskogo-kontinenta" id="link_to_news" target="_blank">Перейти на статью</a></strong></span><span style="color:#000000;font-family:Arial;font-size:13px;"><br></span>
</div>
</div>
<div class="col-2">
<input type="text" id="Editbox1" style="display:block;width: 100%;height:125px;z-index:27;" name="Review" value="" spellcheck="false" placeholder="Отзыв">
<input type="text" id="Editbox2" style="display:block;width: 100%;height:54px;z-index:28;" name="Phone" value="" spellcheck="false" placeholder="Телефон">
<input type="submit" id="Button8" name="" value="Отправить" style="display:inline-block;width:130px;height:41px;z-index:29;">
</div>
</div>
</div>
</div></form>
<div id="wb_Footer">
<div id="Footer">
<div class="col-1">
<div id="wb_Text6">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">Наш сервис предоставляет услуги исключительно как справочник. Мы не оказываем услуги транспортного или экспедиционного характера</span>
</div>
<a id="Button11" href="https://docs.google.com/document/d/e/2PACX-1vS07CgVaf6bizKyLcWwgSqbA9N62u1L__DtzKhENVPZU1ItsL4YWzDv-GuJmFakjlurVflJBMSL20wj/pub" target="_blank" style="display:inline-block;width:130px;height:41px;z-index:31;">Договор</a>
</div>
<div class="col-2">
<div id="wb_Text5">
<span style="color:#FFFFFF;font-family:Arial;font-size:13px;">744000 Address,Turkmenistan, Ashgabat<br>+99364 93 04 67<br>+99361 52 91 13<br>haytektm@gmail.com<br></span><span style="color:#FFFFFF;font-family:Arial;font-size:12px;"><br><strong>Copyright 2023 ES Haytek</strong></span><span style="color:#FFFFFF;font-family:Arial;font-size:13px;"><br></span>
</div>
</div>
<div class="col-3">
<a id="Button1" href="http://milli-audit.com/online.php" target="_blank" title="Юридическое сопровождение и консультация от Аудиторской компании &quot;Милли Аудит&quot;" style="display:inline-block;width:194px;height:35px;z-index:33;">Юридическая консультация</a>
<a id="Button3" href="http://baybaha.com/" target="_blank" title="Консультация по кредитованию от Оценочной компании &quot;Бай Баха&quot;" style="display:inline-block;width:194px;height:35px;z-index:34;">Получить кредит</a>
</div>
</div>
</div>
<script src="jquery-3.6.0.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="yuk247.js"></script>
</body>
</html><?php mysqli_close($conn); ?>