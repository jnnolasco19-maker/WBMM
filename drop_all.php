<?php
$db = new mysqli('localhost', 'root', '', 'wbmm_db');
if ($db->connect_error) {
    die('Connect failed: ' . $db->connect_error . PHP_EOL);
}

$db->query('SET FOREIGN_KEY_CHECKS=0');
$result = $db->query('SHOW TABLES');
while ($row = $result->fetch_array()) {
    $table = $row[0];
    $r = $db->query('DROP TABLE IF EXISTS `' . $table . '`');
    echo 'Dropped ' . $table . ': ' . ($r ? 'OK' : $db->error) . PHP_EOL;
}
$db->query('SET FOREIGN_KEY_CHECKS=1');
echo 'All done.' . PHP_EOL;
