<?php

include "api.php";

$labels = [
    "Австрия",
    "Азербайджан",
    "Армения",
    "Афганистан",
    "Белоруссия",
    "Болгария",
    "Бельгия",
    "Германия",
    "Грузия",
    "Иран",
    "Казахстан",
    "Киргизия",
    "Китай",
    "Латвия",
    "Литва",
    "Молдова",
    "Монголия",
    "ОАЭ",
    "Польша",
    "РФ",
    "Румыния",
    "Сербия",
    "Таджикистан",
    "Туркменистан",
    "Турция",
    "Узбекистан",
    "Украина",
    "Франция",
    "Эстония",
    "Южная Корея",
    "Другое"
];

function findCor($country){
    $Coordinates = [
        1 => "Австрия",
        2 => "Азербайджан",
        3 => "Армения",
        4 => "Афганистан",
        5 => "Белоруссия",
        6 => "Бельгия",
        7 => "Болгария",
        8 => "Германия",
        9 => "Грузия",
        10 => "Иран",
        11 => "Казахстан",
        12 => "Киргизия",
        13 => "Китай",
        14 => "Латвия",
        15 => "Литва",
        16 => "Молдова",
        17 => "Монголия",
        18 => "ОАЭ",
        19 => "Польша",
        20 => "РФ",
        21 => "Румыния",
        22 => "Сербия",
        23 => "Таджикистан",
        24 => "Туркменистан",
        25 => "Турция",
        26 => "Узбекистан",
        27 => "Украина",
        28 => "Франция",
        29 => "Эстония",
        30 => "Южная Корея",
        31 => "Другое"
    ];

    return $Coordinates[$country];
}

function ColorValue($value){

   if (0 < $value && $value <= 10) {
      $color = 'rgba(0, 220, 0, 0.2)';
   } else if (10 < $value && $value <= 50){
      $color = 'rgba(0, 220, 0, 0.4)';
   } else if (50 < $value && $value <= 100){
      $color = 'rgba(0, 220, 0, 0.7)';
   } else {
      $color = 'rgba(0, 220, 0, 1)';
   }

   return $color;
}

function Chart($labels, $values){

    $count_x = 1;
    $count_y = 1;
    echo "<thead><tr><td>Откуда &darr;</td> <td colspan='".count($labels)."' class='labels-top count'>Заявки (".count($values).")</td></tr></thead>";
    
   echo "<tbody>";
    foreach ($labels as $y){
        echo "<tr>";
        echo "<td class='labels-top'>".$y."</td>";

        foreach ($labels as $x){
            $check = count(array_keys($values, ["From_where" => findCor($count_y), "Where_to" => findCor($count_x)]));
            if ($check == 0){
               echo "<td></td>";
            } else {
               echo "<td style='background-color:".ColorValue($check).";'>".$check."</td>";
            }
            $count_x += 1;
        }
        $count_x = 1;
        $count_y += 1;
        echo "</tr>";
    }
      
    echo "</tbody><tfoot><tr id='last-labels-row'> <td id='NoneCol'>Куда &rarr;</td>";
        foreach ($labels as $x){
            echo "<td class='labels-bottom'>".$x."</td>";
            $count_x += 1;
        }
    echo "</tr></tfoot></table>";
}

?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>График заявок</title>
<link href="css/tmcloud.css" rel="stylesheet">
<link href="css/charts.css" rel="stylesheet">
</head>
<body>
<div id="chart">
   <table>
           <?php
            $conn = connect_to_db();

            $sql = "SELECT * FROM zayavki";
            $result = mysqli_query($conn, $sql);
            $values = [];

	     while($row = mysqli_fetch_assoc($result)) {
	         array_push($values, ["From_where" => $row["From_where"], "Where_to" => $row["Where_to"]]);
	     }
      
            Chart($labels, $values);
           ?>
   </table>
</div>
</body>
</html>