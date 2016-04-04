<?php

/**
 * Основной класс для работы с БД.
 */
final class FRDB {

    /** @return ADORecordSet */
    private static function executeQuery($query, $params = false) {
        $params = to_array($params);

        $rs = FRDBConnection::conn()->execute($query, $params);
        if (is_object($rs)) {
            return $rs;
        }
        $error = FRDBConnection::conn()->ErrorMsg();

        throw new Exception($error);
    }

    /**
     * Возвращает индексированный массив ассоциативных массивов со строками БД.
     */
    public static function getArray($query, $inputarr = false) {
        return self::executeQuery($query, $inputarr)->GetArray();
    }

    /**
     * Метод предназначен для загрузки единичной записи. Если извлекается более одной записи -
     * выбразывается исключение.
     *
     * $ensureHasOne:
     * true - если не найдена единственная запись, выкидывает ошибку
     * false - если не найдена единственная запись, метод возвращает null
     */
    public static function getRec($query, $inputarr = false, $ensureHasOne = false) {
        $rs = self::executeQuery($query, $inputarr);
        $rs->Close();

        switch ($rs->RecordCount()) {
            case 0:
                if ($ensureHasOne) {
                    throw new Exception('No data found');
                }
                return null; //---

            case 1:
                return $rs->fields; //---

            default:
                throw new Exception('Too many rows');
        }

        FrUtil::raise('Unexpected recs count requrned: {}', $rs->RecordCount());
    }

    /**
     * Метод выполняет апдейт записи в базе
     */
    public static function update($query, $inputarr = false) {
        self::executeQuery($query, $inputarr);
        return FRDBConnection::conn()->Affected_Rows();
    }

    /**
     * Делает тоже самое, что и update, только возвращает последний айдишник.
     * Это приводит к выполнению 'SELECT LAST_INSERT_ID()', поэтому лишний раз
     * лучше не вызывать.
     */
    public static function insert($query, $inputarr = false) {
        self::executeQuery($query, $inputarr);
        return FRDBConnection::conn()->Insert_ID();
    }

}

?>