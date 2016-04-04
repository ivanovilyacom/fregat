/*
Created: 14.08.2010
Modified: 04.04.2016
Model: MySQL 5.1
Database: MySQL 5.1
*/

-- Create tables section -------------------------------------------------

-- Table fr_logs_visit

CREATE TABLE  IF NOT EXISTS fr_logs_visit
(
  id_log_visit Int UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Первичный ключ записи посещения',
  v_ip Varchar(23) NOT NULL
  COMMENT 'ip-адрес посетителя',
  v_url_from Text
  COMMENT 'URL с которого зашел',
  v_url_to Text
  COMMENT 'URL куда зашел',
  dt_visit Datetime
  COMMENT 'Дата визита',
 PRIMARY KEY (id_log_visit)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Таблица содержит логи посещений сайта'
;

CREATE INDEX idx_log_visit ON fr_logs_visit (v_ip,dt_visit)
;

-- Table fr_logs_user

CREATE TABLE  IF NOT EXISTS fr_logs_user
(
  id_log_user Int UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Первичный ключ записи',
  v_ip Varchar(23) NOT NULL
  COMMENT 'ip-адрес посетителя',
  v_browser Varchar(255)
  COMMENT 'Наименование используемого браузера',
  v_os Varchar(255)
  COMMENT 'Наименование используемой ОС',
 PRIMARY KEY (id_log_user)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Таблица содержит логи посещений сайта'
;

CREATE INDEX idx_logs_user ON fr_logs_user (v_ip)
;

-- Table fr_logs_real

CREATE TABLE  IF NOT EXISTS fr_logs_real
(
  id_log Int UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Первичный ключ записи посещения',
  v_ip Varchar(23) NOT NULL
  COMMENT 'ip-адрес посетителя',
  v_url_from Text
  COMMENT 'URL с которого зашел',
  v_url_to Text
  COMMENT 'URL куда зашел',
  dt_visit Datetime
  COMMENT 'Дата визита',
  v_browser Varchar(255)
  COMMENT 'Браузер',
  v_os Varchar(255)
  COMMENT 'Операционная система',
 PRIMARY KEY (id_log)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_general_ci
  COMMENT = 'Таблица содержит логи посещений сайта'
;


