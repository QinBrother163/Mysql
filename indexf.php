<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9 0009
 * Time: 上午 9:37
 */
header("Content-Type: text/html;charset=utf-8");
$mysql_server_name = '172.16.147.104';
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
$mysql_dataname = 'fsdp_mgpayrecord_91';//读取的数据库

$mysql_server_name2 = '120.25.107.206';
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
$mysql_dataname2 = 'mg_gd';//写入的数据库

$mysql_server_name3 = '120.25.107.206';
$mysql_username3 = 'toodo';
$mysql_password3 = 'toodo1815';
$mysql_dataname3 = 'test';//记录写入状态的数据库

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn,"set names 'UTF-8'");
mysqli_select_db($conn,$mysql_dataname);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$table_time = "list_" . date("Ym");

$startTime = date("Y-m-d H:i:s",strtotime('-1 hour'));//开始日期选择
$stopTime = date("Y-m-d H:i:s");//结束日期选择
//$end = date('Y-m-d', strtotime('+1 day'))." 23:59:59";
//echo $end;

$sql = "select * from $table_time WHERE createDate between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn,$sql);

$txtFileName = "D:\/www\/test\/txt\/fsdp_mgpayrecord.txt";
$TxtRes = fopen($txtFileName, "w");

while ($row = mysqli_fetch_array($result)) {
    if($row[5] == "68"){
        $row[5] = "30";
        fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "\n");
    }else {
        fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "\n");
    }
}
fclose($TxtRes);

$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$table_time = "fsdp_mgpayrecord_list_".date("Ym");

while (!feof($data)) {
    $line = fgets($data);
    $lines = join("','", explode('#', $line));
    $conn = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
    mysqli_query($conn,"set names 'UTF-8'");
    mysqli_select_db($conn,$mysql_dataname2);

    $createtable = "create table $table_time(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate datetime,devNO varchar(20),CARegionCode varchar(20),needCnfm int(4),price int(10),CPID int(10))";
    mysqli_query($conn,$createtable);

    $lists = "insert into $table_time(id,createDate,devNO,CARegionCode,needCnfm,price,CPID) values ('$lines')";
    mysqli_query($conn,$lists);
    mysqli_close($conn);
}
fclose($data);

$conn = mysqli_connect($mysql_server_name3, $mysql_username3, $mysql_password3) or die("error Database link failure!!!");
mysqli_query($conn,"set names 'UTF-8'");
mysqli_select_db($conn,$mysql_dataname3);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$createTableDate = 'phpLoopRecords_m_' . $createShowtime;

$createTable = "create table $createTableDate(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate datetime,completionStatus int(11))";
mysqli_query($conn,$createTable);

$insertData = "insert into $createTableDate(createDate,completionStatus) values ('$insertShowtime','1')";
mysqli_query($conn,$insertData);
mysqli_close($conn);
echo 'GOOD';
