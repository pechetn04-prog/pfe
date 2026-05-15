<?php

function refactor($dir) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir()) continue;
        if ($file->getExtension() !== 'php' && $file->getExtension() !== 'blade') continue;

        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Remplacement des IDs de colonnes
        $content = str_replace('ticket_id', 'dossier_id', $content);
        $content = str_replace('num_ticket', 'num_dossier', $content); // On harmonise aussi le numéro

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "ID Refactored in: " . $file->getPathname() . "\n";
        }
    }
}

echo "Starting ID refactoring...\n";
refactor('app');
refactor('resources/views');
echo "Finished!\n";
