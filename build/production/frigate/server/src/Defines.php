<?php

/*
 * Переменная позволяет включить дебаг библиотеки AdoDB
 */
define('ADODB_DEBUG', false);

/*
 * Настройки коннекта к базе (плагин://логин:пароль@ipбазы/схема)
 */
define('FW_DB_URL', 'mysql://frigate:frigate@localhost/frigate');

/*
 * Формат даты MySQL.
 */
define('DF_MYSQL', 'Y-m-d H:i:s');

/**
 * Метод позволяет получить название файла - без расширения и пути
 */
function get_file_name($path) {
    return pathinfo($path, PATHINFO_FILENAME);
}

/**
 * Аналог assert в java
 * 
 * @param mixed $condition - условие для проверки
 * @param string $message - сообщение в случае ошибки
 * @return mixed - возвращается переданное условие
 * @throws Exception
 */
function check_condition($condition, $message) {
    if (isEmpty($condition)) {
        throw new Exception($message);
    }
    return $condition;
}

/**
 * Метод проверяет, начинается ли строка с переданной подстроки.
 * 
 * @param string $haystack - строка
 * @param string $needle - подстрока
 * @return boolean
 */
function starts_with($haystack, $needle) {
    return $needle === '' || strpos($haystack, $needle) === 0;
}

/**
 * Метод проверяет, заканчивается ли строка с переданной подстрокой.
 * 
 * @param string $haystack - строка
 * @param string $needle - подстрока
 * @return boolean
 */
function ends_with($haystack, $needle) {
    return substr($haystack, - strlen($needle)) === $needle;
}

/**
 * Метод удаляет окончание строки, если строка заканчивается на указанную подстроку
 * 
 * @param type $string
 * @param type $suffix
 * @return type
 */
function cut_string_end($string, $suffix) {
    if (ends_with($string, $suffix)) {
        return substr($string, 0, strlen($string) - strlen($suffix));
    }
    return $string;
}

/**
 * Метод выполняет простую проверку, является ли строка валидным названием файла
 * 
 * @param type $name
 * @return type
 */
function is_valid_file_name($name) {
    return !isEmpty($name) && $name[0] != '.';
}

/**
 * Проверка значения на пустоту
 */
function isEmpty($var) {
    return !isset($var) || empty($var) || (is_string($var) && !trim($var)) || is_null($var) || !$var || count($var) == 0;
}

/**
 * Аналогично #isEmpty, с тем исключением, что 0 и строка '0' не считаются пустыми значениями
 */
function isTotallyEmpty($var) {
    return isEmpty($var) && $var !== 0 && $var !== '0';
}

/**
 * Преобразование переменной к массиву
 */
function to_array($data) {
    return is_array($data) ? $data : (isTotallyEmpty($data) ? array() : array($data));
}

/**
 * Безопасное получение элемента из массива
 * 
 * @param type $key - ключ
 * @param array $searcharray - массив
 * @param type $default - значение, которое будет возвращено, если в массиве не будет найден искомый ключ
 * @return type
 */
function array_get_value($key, array $searcharray, $default = null) {
    return array_key_exists($key, $searcharray) ? $searcharray[$key] : $default;
}

?>