<?php

require_once 'include.php';

header('Content-type: application/json');

//Проверим параметр page
$page = FrUtil::positiveInt(array_get_value('page', $_GET));
//Проверим параметр limit
$limit = FrUtil::positiveInt(array_get_value('limit', $_GET));
//$start = $_GET['start'];
$query = array_get_value('query', $_GET);

//Загрузим из бд кол-во уникальных визитов
$visits = FrLogs::getUniqueVisitLogs($query);

$sesponse = array(
    'success' => true,
    'total' => $visits,
    'logs' => array()
);

//Если нет визитов - сразу вернум данные
if (!$visits) {
    die(json_encode($sesponse));
}

$sesponse['logs'] = FrLogs::loadVisitLogsPortion($page, $limit, $query);

echo json_encode($sesponse);
?>