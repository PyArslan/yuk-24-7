<?php

include "api.php";

session_start();
if ($_SESSION['Status'] != "Администратор"){header('Location: /yuk247.php');}

date_default_timezone_set('Asia/Ashgabat');

$conn = connect_to_db();

list($date_today, $count_all, $count_admins, $count_managers, $count_guests, $count_users, $count_overdue) = filters_count_cli($conn);


//---------------------------------

if(isset($_GET['del'])){

   $id = $_GET['del'];

   $sql = "DELETE FROM klienty WHERE ID='$id'";
   $result = mysqli_query($conn, $sql);


   header('Location: clients.php');

}

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Администрирование клиентов</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/clients.css" rel="stylesheet">

</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
<div id="wb_Heading2" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Heading2">Администрирование клиентов</h1>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./yuk247.php" style="display:inline-block;width:100px;height:48px;z-index:1;">Главная</a>
<a id="Button1" href="./admin.php" style="display:inline-block;width:100px;height:48px;z-index:2;">Заявки</a>
<a id="Button3" href="./logs.php" style="display:inline-block;width:100px;height:48px;z-index:3;">Логи</a>
<a id="Button4" href="./email_newsletter.php" style="display:inline-block;width:100px;height:48px;z-index:4;">Рассылка</a>
</div>
</div>
</div>
</div>
<div id="wb_Free_layout">
<div id="Free_layout">
<div class="col-1">
<nav id="wb_ThemeableMenu1" style="display:inline-block;width:100%;z-index:5;">
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
<a href="?showall" class="nav-link">Все <?php echo $count_all;?></a>
</li>
<li class="nav-item">
<a href="?showadmins" class="nav-link">Администраторы <?php echo $count_admins;?></a>
</li>
<li class="nav-item">
<a href="?showmanagers" class="nav-link">Менеджеры <?php echo $count_managers;?></a>
</li>
<li class="nav-item">
<a href="?showusers" class="nav-link">Пользователи <?php echo $count_users;?></a>
</li>
<li class="nav-item">
<a href="?showguests" class="nav-link">Гости <?php echo $count_guests;?></a>
</li>
<li class="nav-item">
<a href="?showoverdue" class="nav-link">Просроченные <?php echo $count_overdue;?></a>
</li>
</ul>
</div>
</div>
</div>
</nav>
</div>
</div>
</div>
<div id="wb_Table_Clients">
<div id="Table_Clients">
<div class="row">
<div class="col-1">
<!-- Таблица клиентов -->
<div id="Html1" style="display:inline-block;width:100%;height:781px;overflow:auto;z-index:6">
<?php

$sql = check_filter_cli($conn);

$result = mysqli_query($conn, $sql);

echo  '<table class="sql_table">
  <thead>
  <tr>';
  
  echo '<th colspan="2">Действие</th><th>№</th><th>Дата регистрации</th><th>ФИО</th><th>Логин</th><th>Телефон</th><th>E-mail</th><th>Статус</th><th>Конец срока</th><th>Пароль</th><th>Токен</th></tr></thead><tbody>';
  


  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
  
     if($row['End_date']<$date_today && $row['Status'] == "Пользователь"){$overdue = ' style="color: red;"';}else{$overdue = '';}
  
     if(!empty($row['Datestamp'])){$Datestamp = str_replace('-','.',DateTime::createFromFormat('Y-m-d', $row["Datestamp"])->format('d-m-Y'));}else{$Datestamp = '';}
     if(!empty($row['End_date'])){$End_date = str_replace('-','.',DateTime::createFromFormat('Y-m-d', $row["End_date"])->format('d-m-Y'));}else{$End_date = '';}
     
     echo '<tr><td class="del_td"><a href="?del='.$row["ID"].'">Удалить</a></td> <td class="edit_td"><a href="/editclient.php?edit='.$row["ID"].'">Редактировать</a></td> <td>'.$row["ID"].'</td> <td>'.$Datestamp.'</td> <td>'.$row["Fio"].'</td> <td>'.$row["Login"].'</td> <td>'.$row["Phone"].'</td> <td>'.$row["Email"].'</td> <td>'.$row["Status"].'</td>  <td'.$overdue.'>'.$End_date.'</td> <td>'.$row["Password"].'</td> <td>'.$row["Token"].'</td></tr>';

  }//while

                  


echo '</tbody></table>'; 
?>
</div>
</div>
</div>
</div>
</div>
<script src="jquery-3.6.0.min.js"></script>
<script src="popper.min.js"></script>
<script src="util.min.js"></script>
<script src="collapse.min.js"></script>
<script src="dropdown.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script src="clients.js"></script>
</body>
</html><?php mysqli_close($conn); ?>