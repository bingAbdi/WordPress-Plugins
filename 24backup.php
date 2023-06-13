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
    // Replace this with your backup logic
    // Example: You can use a plugin like UpdraftPlus or create your custom backup routine
    // For simplicity, we'll just log a message in this example
    error_log('Site backup performed on ' . date('Y-m-d H:i:s'));
}

add_action('site_backup_event', 'perform_site_backup');
