<?php
$files = ['public/css/style.css', 'css/style.css', 'public/css/portal.css', 'css/portal.css', 'public/css/darkmode-portal.css'];
foreach ($files as $file) {
    if (!file_exists($file)) { echo "$file not found\n"; continue; }
    $content = file_get_contents($file);
    $open = 0; $line = 1;
    $len = strlen($content);
    for ($i = 0; $i < $len; $i++) {
        if ($content[$i] == "\n") $line++;
        if ($content[$i] == '{') $open++;
        elseif ($content[$i] == '}') {
            $open--;
            if ($open < 0) {
                echo "$file: EXTRA } at line $line\n";
                $open = 0;
            }
        }
    }
    echo "$file: Unclosed { count = $open, Total lines = $line\n";
}

