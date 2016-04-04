<?php

/**
 * Запись лога визита
 *
 * @author azazello
 */
class FrLogVisit extends FrLogAbstract {

    private $urlFrom;
    private $urlTo;
    private $dtVizit;

    public function getUrlFrom() {
        return $this->urlFrom;
    }

    public function setUrlFrom($urlFrom) {
        $this->urlFrom = $urlFrom;
    }

    public function getUrlTo() {
        return $this->urlTo;
    }

    public function setUrlTo($urlTo) {
        $this->urlTo = $urlTo;
    }

    public function getDtVizit() {
        return $this->dtVizit;
    }

    public function setDtVizit($dtVizit) {
        $this->dtVizit = $dtVizit;
    }

}

?>