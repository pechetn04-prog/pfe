<?php

$logPath = __DIR__ . '/../storage/logs/laravel.log';
if (!file_exists($logPath)) {
    die("Log file not found.\n");
}

$contents = file_get_contents($logPath);
preg_match_all('/(ParseError|syntax error|unexpected token).*$/mi', $contents, $matches);

echo "Parse errors found:\n";
print_r(array_slice($matches[0], -10));
