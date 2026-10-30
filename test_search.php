<?php
$content = file_get_contents('d:/Kos Mas Robi/resources/views/dashboard.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'Yakin Ingin Keluar') !== false || stripos($line, 'kmr-logout') !== false || stripos($line, 'btnToggleTheme') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}

