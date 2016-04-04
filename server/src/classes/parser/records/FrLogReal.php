<?php

/**
 * Запись реального лога визита пользователя
 *
 * @author azazello
 */
class FrLogReal extends FrLogAbstract {

    private $urlFrom;
    private $urlTo;
    private $dtVizit;
    private $browser;
    private $os;

    public function getBrowser() {
        return $this->browser;
    }

    public function setBrowser($browser) {
        $this->browser = $browser;
    }

    public function getOs() {
        return $this->os;
    }

    public function setOs($os) {
        $this->os = $os;
    }

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