<?php
$lines = file('d:/Kos Mas Robi/public/css/portal.css');
$clean = array_slice($lines, 0, 3241);
file_put_contents('d:/Kos Mas Robi/public/css/portal.css', implode('', $clean));
file_put_contents('d:/Kos Mas Robi/css/portal.css', implode('', $clean));
echo "portal.css cleaned! New line count: " . count($clean) . "\n";

