<?php

/**
 * Description of FrPhpJsBridge
 *
 * @author azaz
 */
class FrPhpJsBridge {

    /**
     * Имя переменной, через которую будет производиться обращение к константам из js кода
     */
    const JS_VAR_NAME = 'FRCONST';

    /**
     * Кол-во элементов в хранилище грида
     */
    const STORE_PAGING_SIZE = 7;

    /**
     * Метод вызывается в index.php для отпеределения констант, общих в php и js коде
     */
    public static function getJsVars() {
        $consts = array(
            'STORE_PAGING_SIZE' => self::STORE_PAGING_SIZE,
            'USER_IP' => FrUtil::remoteAddr(),
            'USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
        );


        return 'var ' . self::JS_VAR_NAME . '=' . json_encode($consts) . ';';
    }

}
