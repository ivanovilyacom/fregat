<?php

require_once 'include.php';

FrLogs::clearUploadedLogs();
if (!array_key_exists('clearOnly', $_REQUEST)) {
    FrLogs::uploadAllLogs();
}
?>