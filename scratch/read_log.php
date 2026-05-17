<?php

$file = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($file)) {
    die("Log file not found.\n");
}

$lines = [];
$fp = fopen($file, 'r');
fseek($fp, 0, SEEK_END);
$pos = ftell($fp);

$lineCount = 0;
$chunk = '';

while ($pos > 0 && $lineCount < 100) {
    $pos = max(0, $pos - 1024);
    fseek($fp, $pos);
    $chunk = fread($fp, 1024) . $chunk;
    $lineCount = substr_count($chunk, "\n");
}

fclose($fp);

$lines = explode("\n", $chunk);
$lastLines = array_slice($lines, -100);

echo implode("\n", $lastLines);
