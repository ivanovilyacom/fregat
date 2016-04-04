<?php

/**
 * Бин для работы с логами
 *
 * @author azazello
 */
class FrLogsBean extends BaseBean {

    /**
     * Метод сохраняет запись лога в БД
     * 
     * @param FrLogAbstract $rec
     */
    public function saveLogRecord(FrLogAbstract $rec) {
        if ($rec instanceof FrLogReal) {
            return $this->saveLogReal($rec); //---
        }
        if ($rec instanceof FrLogVisit) {
            return $this->saveLogVisit($rec); //---
        }
        if ($rec instanceof FrLogUser) {
            return $this->saveLogUser($rec); //---
        }

        FrUtil::raise('Unknown log record type: {}', FrUtil::getClassName($rec));
    }

    private function saveLogReal(FrLogReal $rec) {
        $params[] = $rec->getIp();
        $params[] = $rec->getUrlFrom();
        $params[] = $rec->getUrlTo();
        $params[] = $rec->getDtVizit();
        $params[] = $rec->getBrowser();
        $params[] = $rec->getOs();
        //Мы вызываем update, так как если вызвать insert будет выполнен доб запрос в БД
        return $this->insert('INSERT INTO fr_logs_real (v_ip, v_url_from, v_url_to, dt_visit, v_browser, v_os) VALUES (?, ?, ?, ?, ?, ?)', $params);
    }

    private function saveLogVisit(FrLogVisit $rec) {
        $params[] = $rec->getIp();
        $params[] = $rec->getUrlFrom();
        $params[] = $rec->getUrlTo();
        $params[] = $rec->getDtVizit();
        //Мы вызываем update, так как если вызвать insert будет выполнен доб запрос в БД
        return $this->insert('INSERT INTO fr_logs_visit (v_ip, v_url_from, v_url_to, dt_visit) VALUES (?, ?, ?, ?)', $params);
    }

    private function saveLogUser(FrLogUser $rec) {
        $params[] = $rec->getIp();
        $params[] = $rec->getBrowser();
        $params[] = $rec->getOs();
        //Мы вызываем update, так как если вызвать insert будет выполнен доб запрос в БД
        return $this->insert('INSERT INTO fr_logs_user (v_ip, v_browser, v_os) VALUES (?, ?, ?)', $params);
    }

    /**
     * Метод очищает все логи
     */
    public function clearAllLogs() {
        foreach (array('fr_logs_real', 'fr_logs_visit', 'fr_logs_user') as $table) {
            $this->update("delete from $table");
        }
    }

    /**
     * Метод загружает кол-во уникальных визитов с IP
     * 
     * @param string $query - возможная маска запроса
     * @return type
     */
    public function getUniqueVisitLogs($query) {
        $like = '';
        $params = array();

        //Поиск с маской
        if (FrUtil::isNotEmptyString($query)) {
            /*
             * SQL-иньекция невозможна, так как мы передадим маску через bind
             */
            $like = ' and v_ip like ?';
            $params[] = '%' . $query . '%';
        }

        return $this->getCnt('select count(distinct v_ip) as cnt from fr_logs_visit where 1=1' . $like, $params);
    }

    /**
     * Метод загружает логи визитов грида
     * 
     * @param int $page - номер страницы. Начинается от 1.
     * @param int $limit - ограничение кол-ва выводимых строк
     * @param int $query - обраничение на маску ip адреса
     */
    public static function loadVisitLogsPortion($page, $limit, $query) {
        /*
         * К сожалению в limit нельзя передать параметры через bind, но мы обезопасимся тем, 
         * что сами проверим их - оба параметра должны быть положительными числами
         */
        $page = FrUtil::positiveInt($page);
        $limit = FrUtil::positiveInt($limit);

        $skip = ($page - 1) * $limit;

        $like = '';
        $params = array();

        //Поиск с маской
        if (FrUtil::isNotEmptyString($query)) {
            /*
             * SQL-иньекция невозможна, так как мы передадим маску через bind
             */
            $like = ' and v_ip like ?';
            $params[] = '%' . $query . '%';
        }

        return FRDB::getArray("
SELECT L.v_ip as ip,
       (select v_url_from from fr_logs_visit where v_ip=L.v_ip and dt_visit=L.dt_first limit 1) as url_from,
       (select v_url_to from fr_logs_visit where v_ip=L.v_ip and dt_visit=L.dt_last limit 1) as url_to,
       L.count,
       GROUP_CONCAT(U.v_browser) as browser,
       GROUP_CONCAT(U.v_os) as os
  FROM (SELECT v_ip,
               min(dt_visit) AS dt_first,
               max(dt_visit) AS dt_last,
               count(DISTINCT v_url_to) AS count
          FROM fr_logs_visit
         WHERE 1=1 $like
        GROUP BY v_ip) L
        left join fr_logs_user U
        on L.v_ip = U.v_ip
  GROUP BY L.v_ip limit $skip, $limit", $params);
    }

    /** @return FrLogsBean */
    public static function inst() {
        return parent::inst();
    }

}

?>