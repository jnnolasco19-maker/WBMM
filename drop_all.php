<?php
$db = new mysqli('localhost', 'root', '', 'wbmm_db');
if ($db->connect_error) {
    die('Connect failed: ' . $db->connect_error . PHP_EOL);
}
$tables = ['role_permissions','permissions','records','stalls','password_resets','roles','payments','vendors','audit_logs','users','migrations'];
$db->query('SET FOREIGN_KEY_CHECKS=0');
foreach ($tables as $t) {
    $r = $db->query('DROP TABLE IF EXISTS `' . $t . '`');
    echo 'Dropped ' . $t . ': ' . ($r ? 'OK' : $db->error) . PHP_EOL;
}
$db->query('SET FOREIGN_KEY_CHECKS=1');
echo 'All done.' . PHP_EOL;
