<?php
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
}
?>