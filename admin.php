<?php

include "api.php";

session_start();
if (!in_array(trim($_SESSION['Status']), ["Менеджер","Администратор"])){header('Location: /yuk247.php');}


if(isset($_GET['del'])){

    $id = $_GET['del'];

    $conn = connect_to_db();

    $sql = "DELETE FROM zayavki WHERE ID='$id'";
    $result = mysqli_query($conn, $sql);

    header('Location: admin.php');

}



//---------------------- Подсчёты по фильтрам ---------------------- \\

$conn = connect_to_db();

list($count_all, $count_gruz, $count_allgruz, $count_transport, $count_alltransport, $count_open, $count_sovpad, $count_from_tkm, $count_to_tkm, $count_today, $date_today, $count_overdue) = filters_count_app($conn);

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Панель Администратора</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="logo2.png" rel="icon" type="image/png">
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/admin.css" rel="stylesheet">

</head>
<body>
<div id="wb_LayoutGrid1">
<div id="LayoutGrid1">
<div class="row">
<div class="col-1">
<div id="wb_Heading1" style="display:inline-block;width:100%;z-index:0;">
<h1 id="Heading1">Администрирование заявок</h1>
</div>
</div>
<div class="col-2">
<a id="Button2" href="./yuk247.php" style="display:inline-block;width:100px;height:48px;z-index:1;">Главная</a>
<a id="Button1" href="./clients.php" style="display:inline-block;width:100px;height:48px;z-index:2;">Клиенты</a>
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
<a href="?showoverdue" class="nav-link"><i class="fa fa-calendar-times-o"></i>Истекшие заявки <?php echo $count_overdue;?></a>
</li>
</ul>
</div>
</div>
</div>
</nav>
</div>
</div>
</div>
<div id="wb_Table_Applications">
<div id="Table_Applications">
<div class="row">
<div class="col-1">
<!-- Таблица заявок -->
<div id="Html2" style="display:inline-block;width:100%;height:604px;overflow:scroll;z-index:6">
<?php

$sql = check_filter($conn);

$result = mysqli_query($conn, $sql);

echo  '<table class="sql_table">
  <thead>
  <tr>';
  
  echo '<th colspan="2">Действие</th><th>№</th><th>Дата заявки</th><th>Статус</th><th>Тип заявки</th><th>Наименование груза</th><th>Категория <br>груза / транспорта</th><th>Страна отправки</th><th>Город и адрес отправки</th><th>Отправка не позже даты</th><th>Страна назначения</th><th>Город и адрес назначения</th><th>Прибытие позже даты</th><th>Вес груза в тоннах</th><th>Размер груза в m3</th><th>Нужно таможенное оформление?</th><th>Цена предложения USD</th><th>Условия оплаты</th><th>Дополнительная информация</th><th>ФИО</th><th>Телефон</th><th>E-mail</th>
  </tr></thead><tbody>';
  


  // Вывод данных из бд
  while($row = mysqli_fetch_assoc($result)) {
  
     if($row['Start_no_later']<=$date_today){$overdue_start = ' style="color: red;"';}else{$overdue_start = '';}
     if($row['Finish_no_later']<=$date_today){$overdue_finish = ' style="color: red;"';}else{$overdue_finish = '';}
     if($row['Finish_no_later'] == '0000-00-00'){$finish_no_later = '';}else{$finish_no_later = DateTime::createFromFormat('Y-m-d', $row["Finish_no_later"])->format('d.m.Y');}
     
     if ($row["Application_Status"]  == 'Открыта' and $row["Application_type"] == 'Транспорт'){$app_class = "open_trans";} 
     elseif ($row["Application_Status"]  == 'Закрыта' and $row["Application_type"] == 'Транспорт'){$app_class = "close_trans";} 
     elseif ($row["Application_Status"]  == 'Открыта' and $row["Application_type"] == 'Груз') {$app_class = "open_gruz";}  
     elseif ($row["Application_Status"]  == 'Закрыта' and $row["Application_type"] == 'Груз') {$app_class = "close_gruz";}

     if ($row["Application_type"] == 'Груз') {$type = $row["Cargo_category"];} else {$type = $row["Type_transport"];}
        
    echo '<tr class="'.$app_class.'"> <td class="del_td"><a href="?del='.$row["ID"].'">Удалить</a></td> <td class="edit_td"><a href="/editapp.php?edit='.$row["ID"].'">Редактировать</a></td> <td>'.$row["ID"].'</td> <td>'.DateTime::createFromFormat('Y-m-d', $row["Datestamp"])->format('d.m.Y').'</td> <td class="open_td">'.$row["Application_Status"].'</td> <td>'.$row["Application_type"].'</td> <td>'.$row["Cargo_name"].'</td> <td>'.$type.'</td> <td>'.$row["From_where"].'</td> <td>'.$row["Adress_1"].'</td> <td'.$overdue_start.'>'.DateTime::createFromFormat('Y-m-d', $row["Start_no_later"])->format('d.m.Y').'</td> <td>'.$row["Where_to"].'</td> <td>'.$row["Adress_2"].'</td> <td'.$overdue_finish.'>'.$finish_no_later.'</td> <td>'.$row["Weight_in_ton"].'</td> <td>'.$row["Size_in_m3"].'</td> <td>'.$row["Need_customs"].'</td> <td>'.$row["Price_USD"].'</td> <td>'.$row["Terms_Payment"].'</td> <td>'.$row["Comment"].'</td> <td>'.$row["Who_added"].'</td> <td>'.$row["Phone"].'</td> <td>'.$row["Email"].'</td></tr>';

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
<script src="admin.js"></script>
</body>
</html><?php mysqli_close($conn); ?>