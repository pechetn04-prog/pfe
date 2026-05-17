<?php

function refactor($dir)
{
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($files as $file) {
        if ($file->isDir())
            continue;
        if ($file->getExtension() !== 'php' && $file->getExtension() !== 'blade')
            continue;

        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Variables communes
        $content = str_replace('$recentTickets', '$recentDossiers', $content);
        $content = str_replace('$totalTickets', '$totalDossiers', $content);
        $content = str_replace('$assignedTickets', '$assignedDossiers', $assignedDossiers); // Attention erreur ici dans mon esprit, je vais corriger

        // Correction de la ligne précédente
        $content = str_replace('$assignedTickets', '$assignedDossiers', $content);
        $content = str_replace('$ticketsEnDiagnostic', '$dossiersEnDiagnostic', $content);
        $content = str_replace('$ticketsEnReparation', '$dossiersEnReparation', $content);
        $content = str_replace('$ticketsClotures', '$dossiersClotures', $content);
        $content = str_replace('$ticketsEnAttentePieces', '$dossiersEnAttentePieces', $content);
        $content = str_replace('$ticketsEnAttenteDevis', '$dossiersEnAttenteDevis', $content);

        // Compact strings
        $content = str_replace("'recentTickets'", "'recentDossiers'", $content);
        $content = str_replace("'totalTickets'", "'totalDossiers'", $content);
        $content = str_replace("'assignedTickets'", "'assignedDossiers'", $content);

        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Variable refactored in: " . $file->getPathname() . "\n";
        }
    }
}

echo "Starting variable refactoring...\n";
refactor('app/Http/Controllers');
refactor('resources/views');
echo "Finished!\n";
