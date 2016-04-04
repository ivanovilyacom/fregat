<?php

/**
 * Запись лога пользователя
 *
 * @author azazello
 */
class FrLogUser extends FrLogAbstract {

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

}

?>
