CREATE USER 'PMA'@'%' IDENTIFIED BY 'pma-pw';
GRANT ALL ON *.* TO 'PMA'@'%';

CREATE USER 'dokkie'@'%' IDENTIFIED BY 'dokkie-pw';
CREATE DATABASE IF NOT EXISTS `dokkie` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON `dokkie`.* TO 'dokkie'@'%';
