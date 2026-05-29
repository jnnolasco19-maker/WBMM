<?php

$hash = '$2y$10$PG.mjl3gdwbwKpqXKbMXs.TqwLEfbtFn4.8Me8X8vL.IxmYPFxvKm';
echo password_verify('Admin@1234', $hash) ? "OK\n" : "FAIL\n";
