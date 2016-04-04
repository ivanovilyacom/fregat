<?php

/**
 * Различные утилитные методы
 */
final class FrUtil {

    /**
     * Метод выбрасывает ошибку, заменяя {} последовательно на параметры
     */
    public static function raise($msg, $param1 = null, $param2 = null) {
        $params = func_get_args();
        if (count($params) > 1) {
            unset($params[0]);
            $msg = self::replaceWithParams('{}', $msg, $params);
        }
        throw new Exception($msg);
    }

    /**
     * Метод проверяет передонное условние на непустоту (не просто на true)
     * 
     * @param mixed $condition - условие
     * @param string $msg - сообщение
     * @param mixed $param1, $param2 - параметры
     */
    public static function assert($condition, $msg, $param1 = null, $param2 = null) {
        if (isEmpty($condition)) {
            $params = func_get_args();
            unset($params[0]);
            return call_user_func_array(array(__CLASS__, 'raise'), $params); //---
        }
        return $condition; //---
    }

    /**
     * Метод заменяет подстроку $delimiter в строке $text на элементы из массива 
     * подстановок $params.
     * 
     * @param string $delimiter - элемент для поиска
     * @param string $text - текст, в котором производится поиск
     * @param array $params - элементы для замены
     * @param bool $checkCount - признак, проверять ли совпадение кол-ва разделителей в строке и элементов для замены
     * @return string
     */
    public static function replaceWithParams($delimiter, $text, array $params, $checkCount = false) {
        $paramsCount = count($params);
        if (!$paramsCount && !$checkCount) {
            //Выходим, если параметры не переданы и нам не нужно проверять совпадение кол-ва параметров с кол-вом разделителей
            return $text;
        }
        //Разделим текст на кол-во элеметнов, плюс два
        $tokens = explode($delimiter, $text, $paramsCount + 2);
        $tokensCount = count($tokens);
        if ($checkCount) {
            check_condition($paramsCount == ($tokensCount - 1), "Не совпадает кол-во элементов для замены. Разделитель: `$delimiter`. Строка: `$text`. Передано подстановок: $paramsCount.");
        }

        if ($tokensCount == 0 || $tokensCount == 1) {
            //Была передана пустая строка? Вернём её.
            return $text;
        }

        $idx = 0;
        $result[] = $tokens[$idx];
        foreach ($params as $param) {
            if (++$idx >= $tokensCount) {
                break;
            }
            $result[] = $param;
            $result[] = $tokens[$idx];
        }
        while (++$idx < $tokensCount) {
            $result[] = $delimiter;
            $result[] = $tokens[$idx];
        }

        return implode('', $result);
    }

    /**
     * Метод проверяет, является ли строка валидным ip адресом
     */
    public static function isIp($ip) {
        return !!filter_var($ip, FILTER_VALIDATE_IP);
    }

    /**
     * Метод проверяет, является ли строка валидным email адресом
     */
    public static function ipCheck($ip) {
        if (self::isIp($ip)) {
            return $ip;
        }
        self::raise('Ожидается валидный ip адрес, получено: {}', $ip);
    }

    /**
     * Проверка целочисленного значения
     */
    public static function isInt($var) {
        return is_numeric($var) && is_integer(1 * "$var");
    }

    /**
     * Проверка целочисленного значения
     */
    public static function int($var) {
        if (self::isInt($var)) {
            return (int) $var;
        }
        self::raise('Ожидается целочисленное значение, получено: {}', $var);
    }

    /**
     * Проверка целочисленного значения
     */
    public static function positiveInt($var) {
        if (self::int($var) > 0) {
            return (int) $var;
        }
        self::raise('Ожидается положительное целочисленное значение, получено: {}', $var);
    }

    /**
     * Проверка строки на пустоту
     */
    public static function isNotEmptyString($var) {
        return is_string($var) && !isEmpty($var);
    }

    /**
     * Метод возвращает название класса
     */
    public static function getClassName($class) {
        return check_condition(is_object($class) ? get_class($class) : get_file_name($class), 'Illegal class name');
    }

    /**
     * Получает константы класса. Есть возможность получить только те, что ограничены префиксами.
     */
    private static $CLASS_CONSTS = array();

    public static function getClassConsts($class, $prefix = null) {
        $prefix = trim($prefix);
        $key = self::getClassName($class) . ($prefix ? ':P:' . $prefix : '');

        if (!array_key_exists($key, self::$CLASS_CONSTS)) {
            self::$CLASS_CONSTS[$key] = array();
            $rc = new ReflectionClass($class);
            foreach ($rc->getConstants() as $name => $val) {
                if (!$prefix || starts_with($name, $prefix)) {
                    self::$CLASS_CONSTS[$key][$name] = $val;
                }
            }
        }

        return self::$CLASS_CONSTS[$key];
    }

    /**
     * Метод определяет удалённый IP адрес в $_SERVER
     */
    public static function remoteAddr() {
        /*
         * Мы используем nginx, поэтому вначале нужно посмотреть проставленный им заголовок
         */
        foreach (array('HTTP_X_REAL_IP', 'REMOTE_ADDR') as $name) {
            $ip = array_get_value($name, $_SERVER);
            if (self::isIp($ip)) {
                return $ip; //---
            }
        }
        return null;
    }

}

?>