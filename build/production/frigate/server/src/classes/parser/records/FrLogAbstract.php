<?php

/**
 * Базовый класс для всех записей логов
 *
 * @author azazello
 */
abstract class FrLogAbstract {

    /**
     * ip адрес посетителя
     */
    private $ip;

    /**
     * Оригинальная строка из файла лога
     */
    private $line;

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        $this->ip = $ip;
    }

    public function getLine() {
        return $this->line;
    }

    public function setLine($line) {
        $this->line = $line;
    }

}

?>
