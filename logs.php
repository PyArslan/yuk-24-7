<?php

include "api.php";

session_start();
if ($_SESSION['Status'] != 'Администратор'){header('Location: /yuk247.php');}


date_default_timezone_set('Asia/Ashgabat');
$date_today = date('Y-m-d');
$date_yesterday = date('Y-m-d',strtotime("-1 days"));

//------------ Count ----------- \\


$conn = connect_to_db();


//all
$result = mysqli_query($conn, "SELECT count(*) FROM logs");
$count_all = mysqli_fetch_array($result)[0];



//Today
$result = mysqli_query($conn, "SELECT COUNT(*) FROM logs WHERE DATE(Datetime) = '$date_today'");
$count_today = mysqli_fetch_array($result)[0];



//Yesterday
$result = mysqli_query($conn, $sql = "SELECT COUNT(*) FROM logs WHERE DATE(Datetime) = '$date_yesterday'");
$count_yesterday = mysqli_fetch_array($result)[0];



//Guests
$result = mysqli_query($conn, $sql = "SELECT COUNT(*) FROM logs WHERE Status = 'Гость'");
$count_guests = mysqli_fetch_array($result)[0];



//Users
$result = mysqli_query($conn, $sql = "SELECT COUNT(*) FROM logs WHERE Status = 'Пользователь'");
$count_users = mysqli_fetch_array($result)[0];


?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Действия пользователей</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/logs.css" rel="stylesheet">
</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Heading1">Действия пользователей</h1>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./yuk247.php" style="display:inline-block;width:100px;height:48px;z-index:1;">Главная</a>
<a id="Button1" href="./clients.php" style="display:inline-block;width:100px;height:48px;z-index:2;">Клиенты</a>
<a id="Button3" href="./admin.php" style="display:inline-block;width:100px;height:48px;z-index:3;">Заявки</a>
</div>
</div>
</div>
</div>
<div id="wb_Free_layout">
<div id="Free_layout">
<div class="col-1">
<nav id="wb_ThemeableMenu1" style="display:inline-block;width:100%;z-index:4;">
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
<a href="?showall" class="nav-link">Все записи <?php echo $count_all;?></a>
</li>
<li class="nav-item">
<a href="?showtoday" class="nav-link">Сегодня <?php echo $count_today;?></a>
</li>
<li class="nav-item">
<a href="?showyesterday" class="nav-link">Вчера <?php echo $count_yesterday;?></a>
</li>
<li class="nav-item">
<a href="?showusers" class="nav-link">Пользователи <?php echo $count_users;?></a>
</li>
<li class="nav-item">
<a href="?showguests" class="nav-link">Гости <?php echo $count_guests;?></a>
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
<div id="Html1" style="display:inline-block;width:100%;height:781px;overflow:auto;z-index:5">
<?php

if (strpos($_SERVER['REQUEST_URI'], '?showtoday')){

   $sql = "SELECT * FROM logs WHERE DATE(Datetime) = '$date_today' GROUP BY `IP` ORDER BY `Datetime` DESC";
   

// --------------------------------------Yesterday----------------------------------------- \\

} else if (strpos($_SERVER['REQUEST_URI'], '?showyesterday')){

   $sql = "SELECT * FROM logs WHERE DATE(Datetime) = '$date_yesterday' GROUP BY `IP` ORDER BY `Datetime` DESC";

// -------------------------------------- Guests ------------------------------------------ \\

} else if (strpos($_SERVER['REQUEST_URI'], '?showguests')){

   $sql = "SELECT * FROM logs WHERE Status = 'Гость' GROUP BY `IP` ORDER BY `Datetime` DESC";

// -------------------------------------- Users ------------------------------------------ \\

} else if (strpos($_SERVER['REQUEST_URI'], '?showusers')){

   $sql = "SELECT * FROM logs WHERE Status = 'Пользователь' GROUP BY `IP` ORDER BY `Datetime` DESC";

// ------------------------------------------All----------------------------------------------- \\

} else {$sql = "SELECT * FROM logs ORDER BY Datetime DESC";}


$result = mysqli_query($conn, $sql);

echo  '<table class="sql_table">
  <thead>
  <tr>';
  
  echo '<th>Дата и время</th> <th>Статус</th> <th>Логин</th>  <th>IP</th> <th>Действие</th> </tr></thead><tbody>';
  


  // output data of each row
  while($row = mysqli_fetch_assoc($result)) {
     
     echo '<tr> <td>'.$row["Datetime"].'</td> <td>'.$row["Status"].'</td> <td>'.$row["Login"].'</td> <td>'.$row["IP"].'</td> <td>'.$row["Activity"].'</td> </tr>';

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
<script src="logs.js"></script>
</body>
</html><?php mysqli_close($conn); ?>