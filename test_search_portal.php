<?php
$content = file_get_contents('d:/Kos Mas Robi/resources/views/tenant/portal.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $num => $line) {
    if (stripos($line, 'btnToggleTheme') !== false || stripos($line, 'btnThemePortal') !== false || stripos($line, 'kmr-theme-switch') !== false) {
        echo ($num + 1) . ": " . trim($line) . "\n";
    }
}

