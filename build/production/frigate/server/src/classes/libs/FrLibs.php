<?php

/**
 * Класс для подключения внешних библиотек
 *
 * @author azazello
 */
final class FrLibs {

    /**
     * Список подключённых библиотек, а именно - списко вызванных методов
     * 
     * @var array 
     */
    private static $INCLUDED = array();

    /**
     * Библиотека для работы с базой
     * 
     * @link http://adodb.sourceforge.net
     */
    public static function AdoDb() {
        if (self::isAlreadyIncluded(__FUNCTION__)) {
            return; //---
        }

        require_once PATH_LIBS_DIR . 'Adodb/adodb5/adodb.inc.php';
        require_once PATH_LIBS_DIR . 'Adodb/adodb5/drivers/adodb-mysql.inc.php';

        /*
         * Установим некоторые параметры библиотеки AdoDB
         */
        GLOBAL $ADODB_FETCH_MODE, $ADODB_COUNTRECS;

        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $ADODB_COUNTRECS = false;
    }

    /**
     * Метод должен быть вызван перед подключением библиотеки для предотвращения повторного подключения
     * Пример использования:
     * 
     * if ($this->isAlreadyIncluded(__FUNCTION__)) {
     *     return;//---
     * }
     * 
     * @param string $libName - название подключаемой библиотеки
     * @return boolean - признак, нужно ли подключать данную библиотеку
     */
    private static final function isAlreadyIncluded($libName) {
        if (in_array($libName, self::$INCLUDED)) {
            return true;
        }

        self::$INCLUDED[] = $libName;

        return false;
    }

}

?>