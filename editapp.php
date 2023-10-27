<?php

include "api.php";

session_start();
if (!in_array($_SESSION['Status'],['Менеджер','Администратор'])){header('Location: /yuk247.php');}
date_default_timezone_set("Asia/Ashgabat");

$conn = connect_to_db();

$id = $_GET['edit'];

$sql = "SELECT * FROM zayavki WHERE ID='$id'";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_array($result);
mysqli_close($conn);

//----------------------------------------------


if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $id = $_POST['ID'];


   $Application_Status = test_input($_POST['Application_Status']);
   $Cargo_name = test_input($_POST['Cargo_name']);
   $Cargo_category = test_input($_POST['Cargo_category']);
   $From_where = test_input($_POST['From_where']);
   $Adress_1 = test_input($_POST['Adress_1']);
   $Start_no_later = test_input($_POST['Start_no_later']);
   $Where_to = test_input($_POST['Where_to']);
   $Adress_2 = test_input($_POST['Adress_2']);
   $Finish_no_later = test_input($_POST['Finish_no_later']);
   $Weight_in_ton = test_input($_POST['Weight_in_ton']);
   $Size_in_m3 = test_input($_POST['Size_in_m3']);
   $Need_customs = test_input($_POST['Need_customs']);
   $Price_USD = test_input($_POST['Price_USD']);
   $Terms_Payment = test_input($_POST['Terms_Payment']);
   $Type_transport = test_input($_POST['Type_transport']);
   $Comment = test_input($_POST['Comment']);
   $Who_added = test_input($_POST['Who_added']);
   $Phone = test_input($_POST['Phone']);
   $Email = test_input($_POST['Email']);
   $Datestamp = test_input($_POST['Datestamp']);

   $conn = connect_to_db();

   $Application_Status = mysqli_real_escape_string($conn, $Application_Status);
   $Cargo_name = mysqli_real_escape_string($conn, $Cargo_name);
   $Cargo_category = mysqli_real_escape_string($conn, $Cargo_category);
   $From_where = mysqli_real_escape_string($conn, $From_where);
   $Adress_1 = mysqli_real_escape_string($conn, $Adress_1);
   $Start_no_later = mysqli_real_escape_string($conn, $Start_no_later);
   $Where_to = mysqli_real_escape_string($conn, $Where_to);
   $Adress_2 = mysqli_real_escape_string($conn, $Adress_2);
   $Finish_no_later = mysqli_real_escape_string($conn, $Finish_no_later);
   $Weight_in_ton = mysqli_real_escape_string($conn, $Weight_in_ton);
   $Size_in_m3 = mysqli_real_escape_string($conn, $Size_in_m3);
   $Need_customs = mysqli_real_escape_string($conn, $Need_customs);
   $Price_USD = mysqli_real_escape_string($conn, $Price_USD);
   $Terms_Payment = mysqli_real_escape_string($conn, $Terms_Payment);
   $Type_transport = mysqli_real_escape_string($conn, $Type_transport);
   $Comment = mysqli_real_escape_string($conn, $Comment);
   $Who_added = mysqli_real_escape_string($conn, $Who_added);
   $Phone = mysqli_real_escape_string($conn, $Phone);
   $Email = mysqli_real_escape_string($conn, $Email);
   $Datestamp = mysqli_real_escape_string($conn, $Datestamp);

   $sql = "UPDATE Zayavki SET Application_Status = '$Application_Status', Cargo_name = '$Cargo_name', Cargo_category = '$Cargo_category', From_where = '$From_where', Adress_1 = '$Adress_1', Start_no_later = '$Start_no_later', Where_to = '$Where_to', Adress_2 = '$Adress_2', Finish_no_later = '$Finish_no_later', Weight_in_ton = '$Weight_in_ton', Size_in_m3 = '$Size_in_m3', Need_customs = '$Need_customs', Price_USD = '$Price_USD', Terms_Payment = '$Terms_Payment', Comment = '$Comment', Who_added = '$Who_added', Phone = '$Phone', Email = '$Email', Datestamp = '$Datestamp', Type_transport = '$Type_transport' WHERE ID='$id'";


   $result = mysqli_query($conn, $sql);
   mysqli_close($conn);
   header('Location: admin.php');
}

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Редактирование заявки</title>
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/editapp.css" rel="stylesheet">
 </head>
<body>

<form name="Update_form" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Update_form">

<div style="width: 100%; text-align: center;"><h1>№<?php echo ' '.$id.' '.$row['Application_type'].' '.$row['From_where'].'-'.$row['Where_to'];?></h1></div>

<input hidden id="ID" name="ID" type="text" value="<?php echo $id; ?>">

<div class="col">

<div>
<span style="font-family:Arial;"><strong>Дата заявки</strong></span>
</div>

<input type="date" id="Datestamp" style="display: block; width: 100%; z-index: 116;" name="Datestamp" value="<?php echo $row['Datestamp']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Статус</strong></span>
</div>

<select name="Application_Status" size="1" id="Application_Status" style="display:block;width: 100%;z-index:88;">
<option selected="selected"><?php echo $row['Application_Status']; ?></option>
<option value="Открыта">Открыта</option>
<option value="Закрыта">Закрыта</option>
</select>


<div>
<span style="font-family:Arial;"><strong>Наименование груза</strong></span>
</div>

<input type="text" id="Cargo_name" style="display:block;width: 100%;z-index:116;" name="Cargo_name" value="<?php echo $row['Cargo_name']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Категория груза / транспорта</strong></span>
</div>

<?php if($row['Application_type'] == "Груз"){echo '

<select name="Cargo_category" size="1" id="Cargo_category" style="display:block;width: 100%;z-index:88;">
<option selected="selected">'.$row['Cargo_category'].'</option>
<option selected value="Обычный">Обычный</option>
<option value="Бьющийся">Бьющийся</option>
<option value="Консолидированный">Консолидированный</option>
<option value="Скоропортящийся">Скоропортящийся</option>
<option value="Опасный">Опасный</option>
</select>

';}

else {echo '

<select name="Type_transport" size="1" id="Type_transport" style="display:block;width: 100%;z-index:88;">
<option selected="selected">'.$row['Type_transport'].'</option>
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

';}?>

<div>
<span style="font-family:Arial;"><strong>Страна отправки</strong></span>
</div>

<select name="From_where" size="1" id="From_where" style="display:block;width: 100%;z-index:88;">
<option selected="selected"><?php echo $row['From_where']; ?></option>
<option>Австрия</option>
<option>Азербайджан</option>
<option>Армения</option>
<option>Афганистан</option>
<option>Белоруссия</option>
<option>Бельгия</option>
<option>Болгария</option>
<option>Германия</option>
<option>Грузия</option>
<option>Иран</option>
<option>Казахстан</option>
<option>Киргизия</option>
<option>Китай</option>
<option>Латвия</option>
<option>Литва</option>
<option>Молдова</option>
<option>ОАЭ</option>
<option>Польша</option>
<option>РФ</option>
<option>Румыния</option>
<option>Сербия</option>
<option>Таджикистан</option>
<option>Туркменистан</option>
<option>Турция</option>
<option>Узбекистан</option>
<option>Украина</option>
<option>Франция</option>
<option>Эстония</option>
<option>Южная Корея</option>
<option>Другое</option>
</select>


<div>
<span style="font-family:Arial;"><strong>Город и адрес отправки</strong></span>
</div>

<input type="text" id="Adress_1" style="display:block;width: 100%;z-index:116;" name="Adress_1" value="<?php echo $row['Adress_1'];?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Отправка не позже даты</strong></span>
</div>

<input type="date" id="Start_no_later" style="display:block;width: 100%;z-index:116;" name="Start_no_later" value="<?php echo $row['Start_no_later']; ?>" spellcheck="false">


</div><!-- col-1 -->
<div class="col">


<div>
<span style="font-family:Arial;"><strong>Страна назначения</strong></span>
</div>

<select name="Where_to" size="1" id="Where_to" style="display:block;width: 100%;z-index:88;">
<option selected="selected"><?php echo $row['Where_to']; ?></option>
<option>Австрия</option>
<option>Азербайджан</option>
<option>Армения</option>
<option>Афганистан</option>
<option>Белоруссия</option>
<option>Бельгия</option>
<option>Болгария</option>
<option>Германия</option>
<option>Грузия</option>
<option>Иран</option>
<option>Казахстан</option>
<option>Киргизия</option>
<option>Китай</option>
<option>Латвия</option>
<option>Литва</option>
<option>Молдова</option>
<option>ОАЭ</option>
<option>Польша</option>
<option>РФ</option>
<option>Румыния</option>
<option>Сербия</option>
<option>Таджикистан</option>
<option>Туркменистан</option>
<option>Турция</option>
<option>Узбекистан</option>
<option>Украина</option>
<option>Франция</option>
<option>Эстония</option>
<option>Южная Корея</option>
<option>Другое</option>
</select>


<div>
<span style="font-family:Arial;"><strong>Город и адрес назначения</strong></span>
</div>

<input type="text" id="Adress_2" style="display:block;width: 100%;z-index:116;" name="Adress_2" value="<?php echo $row['Adress_2']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Прибытие не позже даты</strong></span>
</div>

<input type="date" id="Finish_no_later" style="display:block;width: 100%;z-index:116;" name="Finish_no_later" value="<?php echo $row['Finish_no_later']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Вес груза в тоннах</strong></span>
</div>

<input type="number" id="Weight_in_ton" style="display:block;width: 100%;z-index:116;" name="Weight_in_ton" value="<?php if (!empty($row['Weight_in_ton'])){echo $row['Weight_in_ton'];} else {echo 0;} ?>" step="0.01" spellcheck="false">



<div>
<span style="font-family:Arial;"><strong>Размер груза в m3</strong></span>
</div>

<input type="number" id="Size_in_m3" style="display:block;width: 100%;z-index:116;" name="Size_in_m3" value="<?php if (!empty($row['Size_in_m3'])){echo $row['Size_in_m3'];} else {echo 0;} ?>" step="0.01" spellcheck="false">



<div>
<span style="font-family:Arial;"><strong>Нужно таможенное оформление?</strong></span>
</div>

<select name="Need_customs" size="1" id="Need_customs" style="display:block;width: 100%;z-index:88;">
<option selected="selected"><?php echo $row['Need_customs']; ?></option>
<option value=""></option>
<option value="Да">Да</option>
<option value="Нет">Нет</option>
</select>



<div>
<span style="font-family:Arial;"><strong>Цена предложения в USD</strong></span>
</div>

<input type="number" id="Price_USD" style="display:block;width: 100%;z-index:116;" name="Price_USD" value="<?php echo $row['Price_USD']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Условия оплаты</strong></span>
</div>

<?php if($row['Application_type'] == "Груз"){echo '

<select name="Terms_Payment" size="1" id="Terms_Payment" style="display:block;width: 100%;z-index:88;">
<option selected="selected">'.$row['Terms_Payment'].'</option>
<option value="Готов внести полную предоплату">Готов внести полную предоплату</option>
<option value="Могу внести только частичную предоплату">Могу внести только частичную предоплату</option>
<option value="Оплата только после доставки груза">Оплата только после доставки груза</option>
</select>
';}

else {echo '

<select name="Terms_Payment" size="1" id="Terms_Payment" style="display:block;width: 100%;z-index:88;">
<option selected="selected">'.$row['Terms_Payment'].'</option>
<option value="Только предоплата">Только предоплата</option>
<option value="Можно частично">Можно частично</option>
</select>
';}

?>

</div><!-- col-2 -->
<div class="col">

<div>
<span style="font-family:Arial;"><strong>Дополнительная информация</strong></span>
</div>

<textarea name="Comment" id="Comment" rows="10" cols="31" style="width: 100%;" spellcheck="false"><?php echo $row['Comment']; ?></textarea>


<div>
<span style="font-family:Arial;"><strong>ФИО</strong></span>
</div>

<input type="text" id="Who_added" style="display:block;width: 100%;z-index:116;" name="Who_added" value="<?php echo $row['Who_added']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Телефон</strong></span>
</div>

<input type="text" id="Phone" style="display:block;width: 100%;z-index:116;" name="Phone" value="<?php echo $row['Phone']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>E-mail</strong></span>
</div>

<input type="text" id="Email" style="display:block;width: 100%;z-index:116;" name="Email" value="<?php echo $row['Email']; ?>" spellcheck="false">



<div id="buttons-div" style="text-align: center;">
   <input type="submit" id="Button2" value="Сохранить" style="display:inline-block;z-index:119;">
   <button id="Close"><a href="admin.php">Вернуться</a></button>
</div>

</div><!-- col-3 -->


</form>
</body>
</html>