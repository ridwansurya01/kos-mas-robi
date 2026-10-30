<?php
$lines = file('d:/Kos Mas Robi/public/css/portal.css');
echo "Total lines: " . count($lines) . "\n";
foreach ($lines as $idx => $line) {
    if (preg_match('/DARK\s*MODE/i', $line)) {
        echo ($idx + 1) . ": " . trim($line) . "\n";
    }
}

