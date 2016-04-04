<?php

/**
 * Основной файл, который должен быть подключён первым.
 * 
 * В свою очередь он подключит все необходимые ресурсы.
 */
/*
 * Разделитель директорий
 */
define('DIR_SEPARATOR', '/');

/*
 * Корневая директория сервера (DocumentRoot/server) - /var/www/ivanovilya.com/server/
 */
define('PATH_SERVER_DIR', str_replace('\\', DIR_SEPARATOR, __DIR__) . DIR_SEPARATOR);

/*
 * Директория с подключаемыми библиотеками
 */
define('PATH_LIBS_DIR', PATH_SERVER_DIR . 'lib' . DIR_SEPARATOR);

/*
 * Директория с логами
 */
define('PATH_LOGS_DIR', PATH_SERVER_DIR . 'logs' . DIR_SEPARATOR);

/*
 * Подключим утиличные методы
 */
require_once __DIR__ . '/src/Defines.php';

/*
 * Подключим автолоадер, который выполнит всю работу по подключению остальных классов
 */
require_once __DIR__ . '/src/Autoload.php';
Autoload::inst()->register();
?>