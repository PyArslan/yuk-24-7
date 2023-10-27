<?php

include "api.php";

session_start();
if ($_SESSION['Status'] != 'Администратор'){header('Location: /yuk247.php');}

date_default_timezone_set("Asia/Ashgabat");

$conn = connect_to_db();

$id = $_GET['edit'];

$sql = "SELECT ID, Fio, Login, Phone, Email, Status, End_date FROM klienty WHERE ID='$id'";
$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_array($result);  

//----------------------------------------------



if ($_SERVER["REQUEST_METHOD"] == "POST") {

   $id = $_POST['ID'];
   
   $Fio = test_input($_POST['Fio']);
   $Login = test_input($_POST['Login']);
   $Phone = test_input($_POST['Phone']);
   $Email = test_input($_POST['Email']);
   $Status = test_input($_POST['Status']);
   $End_date = test_input($_POST['End_date']);
   $Password = $_POST['Password'];   

    $Fio = mysqli_real_escape_string($conn, $Fio);
    $Login = mysqli_real_escape_string($conn, $Login);
    $Phone = mysqli_real_escape_string($conn, $Phone);
    $Email = mysqli_real_escape_string($conn, $Email);
    $Status = mysqli_real_escape_string($conn, $Status);
    $End_date = mysqli_real_escape_string($conn, $End_date);
     
    if(!empty($Password)){
    $Password = md5($Password);

   $sql = "UPDATE `klienty` SET Fio = '$Fio', Login = '$Login', Phone = '$Phone', Email = '$Email', Status = '$Status', Password = '$Password', End_date = '$End_date' WHERE ID='$id'";
   } else {$sql = "UPDATE `klienty` SET Fio = '$Fio', Login = '$Login', Phone = '$Phone', Email = '$Email', Status = '$Status', End_date = '$End_date' WHERE ID='$id'";}

   $result = mysqli_query($conn, $sql);
   header('Location: clients.php');
   }

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Редактирование клиента</title>
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/editclient.css" rel="stylesheet">
</head>
<body>

<form name="Update_form" method="post" accept-charset="UTF-8" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" enctype="multipart/form-data" id="Update_form">

<div style="width: 100%; text-align: center;"><h1>№<?php echo ' '.$id.' '.$row['Fio'];?></h1></div>

<input hidden id="ID" name="ID" type="text" value="<?php echo $id; ?>">

<div>
<span style="font-family:Arial;"><strong>ФИО</strong></span>
</div>

<input type="text" id="Fio" style="display: block; width: 100%; z-index: 116;" name="Fio" value="<?php echo $row['Fio']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Логин</strong></span>
</div>

<input type="text" id="Login" style="display:block;width: 100%;z-index:116;" name="Login" value="<?php echo $row['Login']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Телефон</strong></span>
</div>

<input type="text" id="Phone" style="display:block;width: 100%;z-index:116;" name="Phone" value="<?php echo $row['Phone']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>E-mail</strong></span>
</div>

<input type="text" id="Email" style="display:block;width: 100%;z-index:116;" name="Email" value="<?php echo $row['Email']; ?>" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Статус</strong></span>
</div>

<select name="Status" size="1" id="Status" style="display:block;width: 100%;z-index:88;">
<option selected="selected"><?php echo $row['Status']; ?></option>
<option>Гость</option>
<option>Пользователь</option>
<option>Менеджер</option>
<option>Администратор</option>
</select>


<div>
<span style="font-family:Arial;"><strong>Пароль</strong></span>
</div>

<input type="password" id="Password" style="display:block;width: 100%;z-index:116;" name="Password" minlength="4" spellcheck="false">


<div>
<span style="font-family:Arial;"><strong>Дата окончания</strong></span>
</div>

<input type="date" id="End_date" style="display:block;width: 100%;z-index:116;" name="End_date" value="<?php echo $row['End_date']; ?>" spellcheck="false">


<div id="buttons-div" style="text-align: center;">
   <input type="submit" id="Button2" value="Сохранить" style="display:inline-block;z-index:119;">
   <button id="Close"><a href="clients.php">Вернуться</a></button>
</div>

</form>
</body>
</html><?php mysqli_close($conn); ?>