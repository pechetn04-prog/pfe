<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/../resources/views');
$iterator = new RecursiveIteratorIterator($dir);
$files = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        $foreachCount = substr_count($content, '@foreach');
        $endforeachCount = substr_count($content, '@endforeach');
        
        if ($foreachCount !== $endforeachCount) {
            echo "Mismatched foreach in: " . $file->getPathname() . "\n";
            echo "  @foreach count: $foreachCount\n";
            echo "  @endforeach count: $endforeachCount\n\n";
        }
    }
}
echo "Scan complete.\n";
