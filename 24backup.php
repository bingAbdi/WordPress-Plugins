<?php
/*
Plugin Name: 24Backup
Description: Performs a site backup every 24 hours.
Version: 1.0
Author: Your Name
*/

function schedule_site_backup() {
    if (!wp_next_scheduled('site_backup_event')) {
        wp_schedule_event(time(), 'daily', 'site_backup_event');
    }
}

add_action('wp', 'schedule_site_backup');

function perform_site_backup() {
    // Code to perform the site backup

// Define the source directory and backup filename
$sourceDir = '/path/to/source/directory';
$backupFilename = 'site_backup_' . date('Y-m-d') . '.zip';

// Create a new ZIP archive
$zip = new ZipArchive();
if ($zip->open($backupFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
    // Create a recursive directory iterator to traverse all files and directories
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they are automatically created in the archive)
        if (!$file->isDir()) {
            // Get the relative path of the file (remove the source directory)
            $relativePath = substr($name, strlen($sourceDir) + 1);

            // Add the file to the archive with its relative path
            $zip->addFile($name, $relativePath);
        }
    }

    // Close the ZIP archive
    $zip->close();

    echo 'Site backup created successfully.';
} else {
    echo 'Failed to create site backup.';
}




    // For simplicity, we'll just log a message in this example
    error_log('Site backup performed on ' . date('Y-m-d H:i:s'));
}

add_action('site_backup_event', 'perform_site_backup');
?>
