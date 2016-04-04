<?php

/**
 * Класс региструется через spl_autoload_register для подключения всех классов.
 * При первом обращении (lazy load) мы произведём загрузку всех классов из директории /classes
 * и позднее они будут подключаться автоматически.
 * 
 * Аналог Zend_Loader_Autoloader, но написан для демонстрации навыков:)
 */
final class Autoload {

    /** callable объект класслоадера */
    private $AUTOLOAD;

    /**
     * Карта `Название класса`=>`Путь к классу` из src/auto. Пример:
     * [FrUtil] => /var/www/.../server/src/FrUtil.php
     */
    private $PATHES;

    /**
     * Регистрирует функцию {@link Autoload::load} как класслоадер
     */
    public function register() {
        if (is_array($this->AUTOLOAD)) {
            return; //---
        }

        $this->AUTOLOAD = array($this, 'load');
        spl_autoload_register($this->AUTOLOAD) or die('Could not register class autoload function');
    }

    /**
     * Снимает регистрирацию функции {@link Autoload::load} как класслоадера
     */
    public function unregister() {
        if (!is_array($this->AUTOLOAD)) {
            return; //---
        }

        spl_autoload_unregister($this->AUTOLOAD) or die('Could not unregister class autoload function');
        $this->AUTOLOAD = null;
    }

    /**
     * Метод вызывается автолоадером для поиска класса
     * 
     * @param string $className - название класса, который нужно попытаться подключить
     */
    protected function load($className) {
        $path = $this->getClassPath($className);
        if ($path) {
            /*
             * В кеше нет класса, тем не менее самостоятельно не выбрасываем ошибку, т.к.
             * могут быть другие загрузчики.
             */
            require_once($path);
        }
    }

    /**
     * Основной метод, возвращающий абсолютный путь к классу
     */
    public function getClassPath($className) {
        $className = cut_string_end($className, '.php');

        if (!is_array($this->PATHES)) {
            $this->PATHES = array();
            $this->loadClassPath(__DIR__ . '/classes', $this->PATHES);
        }

        return array_get_value($className, $this->PATHES);
    }

    /**
     * Метод рекурсивно собирает все классы в директории.
     * 
     * @param string $dirAbsPath - путь к директории
     * @param array $classes - карта [FrUtil] => /var/www/.../server/src/FrUtil.php
     */
    public function loadClassPath($dirAbsPath, array &$classes) {
        if (!is_dir($dirAbsPath)) {
            return; //---
        }

        $dir = openDir($dirAbsPath);

        while ($file = readdir($dir)) {
            if (!is_valid_file_name($file)) {
                continue;
            }

            $isphp = ends_with($file, '.php');
            $path = $dirAbsPath . DIR_SEPARATOR . $file;
            if ($isphp) {
                $classes[cut_string_end($file, '.php')] = $path;
            } else {
                $this->loadClassPath($path, $classes);
            }
        }

        closedir($dir);
    }

    /**
     * КОНСТРУКТОР
     */
    private function __construct() {
        
    }

    /*
     * 
     * СИНГЛТОН
     * 
     */

    private static $inst;

    /** @return Autoload */
    public static function inst() {
        return self::$inst ? self::$inst : self::$inst = new Autoload();
    }

}

?>