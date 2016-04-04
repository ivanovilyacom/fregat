<?php

/**
 * Класс содержит различные методы для работы с логами
 * 
 * Для того, чтобы "научить" класс парсить новые файлы логов, достаточно в него добавить константу вида:
 * 
 * ПАРСЕР = 'название_файла_логов.log'
 * 
 * И реализовать в данном классе метод с названием ПАРСЕР. Этот метод автоматически получит на вход строку 
 * из файла логов и сможет её распарсить.
 * 
 * Удобство в том, что извне данный класс можно вызывать, используя готовые константы:
 * FrLogs::parseLogs(FrLogs::USER).
 * 
 * А если понадобится подправить парсер, то мы точно знаем, какой метод нужно искать в данном классе:)
 * 
 * TODO - остаётся вопрос, что делать в случае обнаружения невалидных логов, но в тестовом режиме мы этим вопросом не 
 * будем озабачиваться, а просто пропустим, позволив парсерам возвращать null.
 * 
 * @author azazello
 */
class FrLogs {

    /**
     * Названия файлов логов, которые мы умеем парсить
     */
    const USER = 'users.log';
    const VISIT = 'visits.log';

    /**
     * Метод удаляет все логи из БД
     * Здесь мы используем шаблон fasade, чтобы все методы для парсинга и работы с БД 
     * вызывались из данного класса, а не из нескольких мест.
     */
    public static function clearUploadedLogs() {
        FrLogsBean::inst()->clearAllLogs();
    }

    /**
     * Метод загружает кол-во уникальных визитов (уникальных по ip)
     * @param string $query - возможная маска запроса
     */
    public static function getUniqueVisitLogs($query) {
        return FrLogsBean::inst()->getUniqueVisitLogs($query);
    }

    /**
     * Метод загружает логи визитов грида
     * 
     * @param int $page - номер страницы. Начинается от 1.
     * @param int $limit - ограничение кол-ва выводимых строк
     * @param int $query - обраничение на маску ip адреса
     */
    public static function loadVisitLogsPortion($page, $limit, $query) {
        return FrLogsBean::inst()->loadVisitLogsPortion($page, $limit, $query);
    }

    /**
     * Метод загружает все файлы логов в БД
     */
    public static function uploadAllLogs() {
        /*
         * Просто получим константы класса, которые представляют собой ссылки 
         * на названия файлов логов, и для каждой из них вызовем метод #uploadLogs
         */
        foreach (FrUtil::getClassConsts(__CLASS__) as $logFileName) {
            self::uploadLogs($logFileName);
        }
    }

    /**
     * Метод загружает логи в БД.
     * 
     * Задача метода очень проста - вызываем парсинг файла логов, после пробегаемся по записям и сохраняем каждую из них в БД.
     * Пример использования: FrLogs::uploadLogs(FrLogs::USER)
     * 
     * 
     * @param string $logFileName - название файла логов
     */
    public static function uploadLogs($logFileName) {
        /** @var FrLogAbstract */
        foreach (self::parseLogs($logFileName) as $rec) {
            FrLogsBean::inst()->saveLogRecord($rec);
        }
    }

    /**
     * Метод парсит файл логов, возвращая массив объектов - наследников FrLogAbstract
     * 
     * @see FrLogAbstract
     * @param string $logFileName - название файла логов
     */
    public static function parseLogs($logFileName) {
        /*
         * Получи карту всех поддерживаемых файлов - это массив вида:
         * array('USER' => 'users.log', ...)
         */
        $supported = FrUtil::getClassConsts(__CLASS__);

        /*
         * Загрузим название метода парсера - это просто имя константы со значением, соответствующим названию файла
         */
        $parserMethod = array_search($logFileName, $supported);

        /*
         * Убедимся, что передан известный нам файл
         */
        FrUtil::assert($parserMethod, 'Unsupported logs file: {}', $logFileName);

        /*
         * Убедимся, что метод парсера существует (определён в данном классе)
         */
        FrUtil::assert(method_exists(__CLASS__, $parserMethod), 'Unknown parser method: {}::{}', __CLASS__, $parserMethod);

        /*
         * Распарсим файл логов, пропустив пустые строки
         */
        $lines = to_array(@file(PATH_LOGS_DIR . $logFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        /*
         * Строки логов
         */
        $logs = array();

        /*
         * Пробегаемся по строкам и вызываем соответствующий парсер
         */
        foreach ($lines as $line) {
            $rec = self::$parserMethod($line);

            /*
             * Если не смогли распарсить строку - просто пропускаем
             */
            if (!$rec) {
                continue; //
            }

            FrUtil::assert($rec instanceof FrLogAbstract, 'Invalid result returned by parser {}', $parserMethod);

            $logs[] = $rec;
        }

        /*
         * Всё, файл разобран, возвращаем
         */
        return $logs;
    }

    /*
     * ПАРСЕРЫ ЛОГОВ
     */

    /**
     * @param string $line - строка из файла логов посещения:
     * 2016-04-04|12:34:56|128.163.33.157|from.com|to.com
     */
    private static function VISIT($line) {
        $tokens = explode('|', $line);
        /*
         * Не 5 составляющих? Непонятная строка....
         */
        if (count($tokens) != 5) {
            return null; //---
        }

        /*
         * Разберёмся со временем
         * MySQL хранит дату в формате 'Y-m-d H:i:s' - константа DF_MYSQL в Defines.php
         */
        $date = $tokens[0];
        $time = $tokens[1];
        $dtVizit = $date . ' ' . $time;

        $dateParsed = DateTime::createFromFormat(DF_MYSQL, $dtVizit);
        /*
         * Не смогли распознать дату... Это некорректная запись!
         */
        if (!$dateParsed) {
            return null; //---
        }

        /*
         * Проверим самое простое - ip адрес. Мы требуем его обязательно.
         */
        $ip = $tokens[2];
        if (!FrUtil::isIp($ip)) {
            return null; //---
        }

        /*
         * Создаём и наполняем запись
         */
        $rec = new FrLogVisit();
        $rec->setIp($ip);
        $rec->setDtVizit($dtVizit);
        $rec->setUrlFrom($tokens[3]);
        $rec->setUrlTo($tokens[4]);
        $rec->setLine($line);

        return $rec; //---
    }

    /**
     * @param string $line - строка из файла логов пользователя
     * 128.163.33.157|Chrome|Windows
     */
    private static function USER($line) {
        $tokens = explode('|', $line);
        /*
         * Не 5 составляющих? Непонятная строка....
         */
        if (count($tokens) != 3) {
            return null; //---
        }

        /*
         * Проверим самое простое - ip адрес. Мы требуем его обязательно.
         */
        $ip = $tokens[0];
        if (!FrUtil::isIp($ip)) {
            return null; //---
        }

        /*
         * Создаём и наполняем запись
         */
        $rec = new FrLogUser();
        $rec->setIp($ip);
        $rec->setBrowser($tokens[1]);
        $rec->setOs($tokens[2]);
        $rec->setLine($line);

        return $rec; //---
    }

}

?>
