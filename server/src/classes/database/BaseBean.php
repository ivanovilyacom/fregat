<?php

/**
 * Базовый бин для работы с БД.
 *
 * @author azazello
 */
abstract class BaseBean {

    protected function getArray($query, $inputarr = false) {
        return FRDB::getArray($query, $inputarr);
    }

    protected function getRec($query, $inputarr = false) {
        return FRDB::getRec($query, $inputarr);
    }

    protected function getRecEnsure($query, $inputarr = false) {
        return FRDB::getRec($query, $inputarr, true);
    }

    protected function update($query, $inputarr = false) {
        return FRDB::update($query, $inputarr);
    }

    protected function insert($query, $inputarr = false) {
        return FRDB::insert($query, $inputarr);
    }

    protected function getCnt($query, $inputarr = false) {
        return (int) array_get_value('cnt', $this->getRecEnsure($query, $inputarr));
    }

    protected function getInt($query, $inputarr = false, $default = null) {
        $rec = $this->getRec($query, $inputarr);
        return $rec ? (int) reset($rec) : $default;
    }

    protected function getValue($query, $inputarr = false, $default = null) {
        $rec = $this->getRec($query, $inputarr);
        return $rec ? reset($rec) : $default;
    }

    protected final function __construct() {
        
    }

    private static $inst;

    /** @return BaseBean */
    protected static function inst() {
        return self::$inst ? self::$inst : self::$inst = new static();
    }

}

?>
