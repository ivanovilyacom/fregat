<?php

/**
 * Класс держит соединение с базой данных
 *
 * @author azazello
 */
class FRDBConnection {

    /**
     * Текущее соединение к БД
     * 
     * @var ADOConnection 
     */
    private $CONNECTION = null;

    /** @return ADOConnection */
    public static final function conn() {
        return self::inst()->CONNECTION;
    }

    /*
     * 
     * СИНГЛТОН
     * 
     */

    private static $inst;

    /** @return FRDBConnection */
    private static function inst() {
        return self::$inst ? self::$inst : self::$inst = new FRDBConnection();
    }

    /**
     * КОНСТРУКТОР
     */
    private function __construct() {
        //Подключаем adodb
        FrLibs::AdoDb();

        //Подключаемся (FW_DB_URL определяется в Defines.php)
        $this->CONNECTION = ADONewConnection(FW_DB_URL);

        FrUtil::assert(is_object($this->CONNECTION), 'Unable to establish database connection');

        //Зададим некоторые настройки
        $this->CONNECTION->debug = ADODB_DEBUG;
        $this->CONNECTION->SetFetchMode(ADODB_FETCH_ASSOC);
        $this->CONNECTION->query("SET NAMES 'utf8'");
        $this->CONNECTION->query("SET CHARACTER SET 'utf8'");
    }

    /**
     * Очистим все выданные ранее соединения
     */
    public final function __destruct() {
        if ($this->CONNECTION) {
            $this->CONNECTION->Close();
            $this->CONNECTION = null;
        }
    }

}

?>