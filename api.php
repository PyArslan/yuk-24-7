<?php

function test_input($data) {
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data;

   }


function connect_to_db(){

   $servername = "127.0.0.1:3306";
   $username = "root";
   $password = "";
   $dbname = "haytek_gruz247";


   $conn = mysqli_connect($servername, $username, $password, $dbname);
   mysqli_set_charset($conn, 'utf8');

   if(mysqli_connect_errno()){die("Ошибка подключения к базе, мы скоро это исправим! ");}
   
   return $conn;
}

function check_token($conn){
   if (!empty($_SESSION['Token'])){
      $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT Token FROM klienty WHERE Login = '".$_SESSION['Username']."'"));
      if($check['Token'] != $_SESSION['Token']) {session_unset(); session_destroy(); $_SESSION['Status'] = 'Гость'; header("Location: login.php");}
   }
}

function save_logs($conn, $page){
   // Запись логов

   // 1. Настройка переменных
   $session_status = $_SESSION['Status'];
   $ip_adress = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_VALIDATE_IP)
       ?: filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_VALIDATE_IP)
       ?? $_SERVER['REMOTE_ADDR'];

   // 2. Запись в бд
   if(!empty($_SESSION['Username'])){$session_username = $_SESSION['Username'];}else{$session_username = 'Незарегистрированный'; $session_status = 'Гость';}
   $save_logs = mysqli_query($conn, "INSERT INTO logs (`IP`, `Login`, `Activity`, `Status`) VALUES ('$ip_adress', '$session_username', '$page',    '$session_status')");
}


function filters_count_app($conn){
   // Взятие количества заявок по фильтрам

   //all
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki");
   $count_all = mysqli_fetch_array($result)[0];


   //gruz
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Application_type='Груз' AND Application_Status='Открыта'");
   $count_gruz = mysqli_fetch_array($result)[0];

   //all gruz
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Application_type='Груз'");
   $count_allgruz = mysqli_fetch_array($result)[0];


   //transport
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Application_type='Транспорт' AND Application_Status='Открыта'");
   $count_transport = mysqli_fetch_array($result)[0];

   //all transport
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Application_type='Транспорт'");
   $count_alltransport = mysqli_fetch_array($result)[0];


   //open
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Application_Status = 'Открыта'");
   $count_open = mysqli_fetch_array($result)[0];


   //sovpadeniya
   $result = mysqli_query($conn, "WITH cte AS (
       SELECT *, COUNT(*) OVER (PARTITION BY From_where, Where_to) cnt
       FROM Zayavki
       WHERE Application_Status='Открыта'
       )
    SELECT COUNT(*)
    FROM cte 
    WHERE (cnt > 1 AND Application_Status='Открыта')
    ORDER BY From_where,Where_to,Start_no_later,Finish_no_later");
    
   $count_sovpad = mysqli_fetch_array($result)[0];

   //From tkm
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE From_where='Туркменистан'");
   $count_from_tkm = mysqli_fetch_array($result)[0];
   
   //To tkm
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Where_to='Туркменистан'");
   $count_to_tkm = mysqli_fetch_array($result)[0];


   //Today
   $date_today = date('Y-m-d');
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Datestamp = '$date_today'");
   $count_today = mysqli_fetch_array($result)[0];

   //Overdue
   $result = mysqli_query($conn, "SELECT count(*) FROM Zayavki WHERE Start_no_later <= '$date_today' AND Application_Status = 'Открыта'");
   $count_overdue = mysqli_fetch_array($result)[0];

   return [$count_all, $count_gruz, $count_allgruz, $count_transport, $count_alltransport, $count_open, $count_sovpad, $count_from_tkm, $count_to_tkm, $count_today, $date_today, $count_overdue];
}



function filters_count_cli($conn){
   $date_today = date('Y-m-d');

   //all
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty");
   $count_all = mysqli_fetch_array($result)[0];

   //Admins
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Администратор'");
   $count_admins = mysqli_fetch_array($result)[0];

   //Managers
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Менеджер'");
   $count_managers = mysqli_fetch_array($result)[0];

   //Guests
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Гость'");
   $count_guests = mysqli_fetch_array($result)[0];

   //Users
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status='Пользователь'");
   $count_users = mysqli_fetch_array($result)[0];

   //Overdue
   $result = mysqli_query($conn, "SELECT count(*) FROM klienty WHERE Status!='Гость' AND End_date <= '$date_today'");
   $count_overdue = mysqli_fetch_array($result)[0];

   return [$date_today, $count_all, $count_admins, $count_managers, $count_guests, $count_users, $count_overdue];
}


function is_mobile(){
   return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.

                    '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.

                    '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
}


function check_filter($conn){
    $date_today = date("Y-m-d");

    if (strpos($_SERVER['REQUEST_URI'], '?showgruz')){

    $sql = "SELECT * FROM Zayavki WHERE Application_type='Груз' AND Application_Status='Открыта' ORDER BY ID DESC";

    } else if (strpos($_SERVER['REQUEST_URI'], '?showtransport')){

    $sql = "SELECT * FROM Zayavki WHERE Application_type='Транспорт' AND Application_Status='Открыта' ORDER BY ID DESC";

    } else if (strpos($_SERVER['REQUEST_URI'], '?showopen')){

    $sql = "SELECT * FROM Zayavki WHERE Application_Status='Открыта' ORDER BY ID DESC";

    } else if (strpos($_SERVER['REQUEST_URI'], '?showsovpad')){

    $sql = "WITH cte AS (
        SELECT *, COUNT(*) OVER (PARTITION BY From_where, Where_to) cnt
        FROM Zayavki
        WHERE Application_Status='Открыта'
        )
        SELECT *
        FROM cte 
        WHERE (cnt > 1 AND Application_Status='Открыта')
        ORDER BY From_where,Where_to,Start_no_later,Finish_no_later";

    } else if (strpos($_SERVER['REQUEST_URI'], '?showtoday')){

    $sql = "SELECT * FROM Zayavki WHERE Datestamp = '$date_today' ORDER BY ID DESC";

    } else if (strpos($_SERVER['REQUEST_URI'], '?showoverdue')){

    $sql = "SELECT * FROM Zayavki WHERE Start_no_later <= '$date_today' AND Application_Status = 'Открыта'";
        
    } else {$sql = "SELECT * FROM Zayavki ORDER BY ID DESC";}

    return $sql;

}


function check_filter_cli($conn){
    $date_today = date("Y-m-d");
    
   if (strpos($_SERVER['REQUEST_URI'], '?showadmins')){

    $sql = "SELECT * FROM klienty WHERE Status='Администратор' ORDER BY ID DESC";

    } elseif (strpos($_SERVER['REQUEST_URI'], '?showmanagers')){

        $sql = "SELECT * FROM klienty WHERE Status='Менеджер' ORDER BY ID DESC";
    
    } elseif (strpos($_SERVER['REQUEST_URI'], '?showguests')){

        $sql = "SELECT * FROM klienty WHERE Status='Гость' ORDER BY ID DESC";
    
    } elseif (strpos($_SERVER['REQUEST_URI'], '?showusers')){

        $sql = "SELECT * FROM klienty WHERE Status='Пользователь' ORDER BY ID DESC";
    
    } elseif (strpos($_SERVER['REQUEST_URI'], '?showoverdue')){

        $sql = "SELECT * FROM klienty WHERE Status!='Гость' AND End_date<='$date_today' ORDER BY ID DESC";

    } else {$sql = "SELECT * FROM klienty ORDER BY ID DESC";}

   return $sql;
}

?>