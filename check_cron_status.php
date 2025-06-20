<?php
/**
 * Check and force MAILPN cron execution
 * 
 * This script will check the cron status and force execution if needed
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

echo "<h1>MAILPN Cron Status Check</h1>";
echo "<p>Generated on: " . date('Y-m-d H:i:s') . "</p>";

// Check if cron is disabled
if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
    echo "<div style='background: #ffe6e6; padding: 10px; border: 1px solid #dc3232;'>";
    echo "<strong>⚠️ Warning:</strong> WordPress cron is disabled (DISABLE_WP_CRON is set to true)<br>";
    echo "This means emails will not be sent automatically. You need to set up a server cron job or enable WordPress cron.";
    echo "</div>";
}

// Check cron schedules
echo "<h2>1. Cron Schedules</h2>";
$schedules = wp_get_schedules();
$mailpn_schedules = [];

foreach ($schedules as $schedule_name => $schedule_data) {
    if (strpos($schedule_name, 'mailpn') !== false) {
        $mailpn_schedules[$schedule_name] = $schedule_data;
    }
}

if (!empty($mailpn_schedules)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Schedule</th><th>Interval (seconds)</th><th>Display Name</th></tr>";
    foreach ($mailpn_schedules as $schedule_name => $schedule_data) {
        echo "<tr>";
        echo "<td>" . $schedule_name . "</td>";
        echo "<td>" . $schedule_data['interval'] . "</td>";
        echo "<td>" . $schedule_data['display'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No MAILPN cron schedules found<br>";
}

// Check scheduled events
echo "<h2>2. Scheduled Events</h2>";
$cron_events = _get_cron_array();
$mailpn_events = [];

foreach ($cron_events as $timestamp => $events) {
    foreach ($events as $hook => $event_data) {
        if (strpos($hook, 'mailpn') !== false) {
            $mailpn_events[] = [
                'timestamp' => $timestamp,
                'hook' => $hook,
                'next_run' => date('Y-m-d H:i:s', $timestamp)
            ];
        }
    }
}

if (!empty($mailpn_events)) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Hook</th><th>Next Run</th><th>Actions</th></tr>";
    foreach ($mailpn_events as $event) {
        echo "<tr>";
        echo "<td>" . $event['hook'] . "</td>";
        echo "<td>" . $event['next_run'] . "</td>";
        echo "<td>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='run_hook' value='" . $event['hook'] . "'>";
        echo "<input type='submit' value='Run Now' style='margin: 2px; padding: 5px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No MAILPN cron events scheduled<br>";
}

// Force run specific hooks
if (isset($_POST['run_hook'])) {
    $hook_to_run = sanitize_text_field($_POST['run_hook']);
    echo "<h2>3. Running Hook: " . $hook_to_run . "</h2>";
    
    switch ($hook_to_run) {
        case 'mailpn_cron_ten_minutes':
            $cron = new MAILPN_Cron();
            $cron->cron_ten_minutes();
            echo "✅ 10-minute cron executed successfully<br>";
            break;
            
        case 'mailpn_cron_daily':
            $cron = new MAILPN_Cron();
            $cron->cron_daily();
            echo "✅ Daily cron executed successfully<br>";
            break;
            
        default:
            echo "❌ Unknown hook: " . $hook_to_run . "<br>";
            break;
    }
}

// Check if cron jobs are properly scheduled
echo "<h2>4. Cron Job Status</h2>";
$next_daily = wp_next_scheduled('mailpn_cron_daily');
$next_ten_minutes = wp_next_scheduled('mailpn_cron_ten_minutes');

if (!$next_daily) {
    echo "❌ Daily cron not scheduled<br>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='schedule_daily' value='1'>";
    echo "<input type='submit' value='Schedule Daily Cron' style='margin: 5px; padding: 10px; background: #46b450; color: white; border: none; cursor: pointer;'>";
    echo "</form>";
} else {
    echo "✅ Daily cron scheduled for: " . date('Y-m-d H:i:s', $next_daily) . "<br>";
}

if (!$next_ten_minutes) {
    echo "❌ 10-minute cron not scheduled<br>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='schedule_ten_minutes' value='1'>";
    echo "<input type='submit' value='Schedule 10-Minute Cron' style='margin: 5px; padding: 10px; background: #46b450; color: white; border: none; cursor: pointer;'>";
    echo "</form>";
} else {
    echo "✅ 10-minute cron scheduled for: " . date('Y-m-d H:i:s', $next_ten_minutes) . "<br>";
}

// Schedule cron jobs if requested
if (isset($_POST['schedule_daily'])) {
    if (!wp_next_scheduled('mailpn_cron_daily')) {
        wp_schedule_event(time(), 'daily', 'mailpn_cron_daily');
        echo "✅ Daily cron scheduled successfully<br>";
    }
}

if (isset($_POST['schedule_ten_minutes'])) {
    if (!wp_next_scheduled('mailpn_cron_ten_minutes')) {
        wp_schedule_event(time(), 'mailpn_ten_minutes', 'mailpn_cron_ten_minutes');
        echo "✅ 10-minute cron scheduled successfully<br>";
    }
}

// Clear all cron jobs if requested
if (isset($_POST['clear_all_cron'])) {
    wp_clear_scheduled_hook('mailpn_cron_daily');
    wp_clear_scheduled_hook('mailpn_cron_ten_minutes');
    echo "✅ All MAILPN cron jobs cleared<br>";
}

// Show action buttons
echo "<h2>5. Actions</h2>";
echo "<form method='post' style='margin: 10px 0;'>";
echo "<input type='submit' name='run_ten_minutes' value='Run 10-Minute Cron' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
echo "<input type='submit' name='run_daily' value='Run Daily Cron' style='margin: 5px; padding: 10px; background: #0073aa; color: white; border: none; cursor: pointer;'>";
echo "<input type='submit' name='clear_all_cron' value='Clear All Cron Jobs' style='margin: 5px; padding: 10px; background: #dc3232; color: white; border: none; cursor: pointer;' onclick='return confirm(\"Are you sure you want to clear all cron jobs?\")'>";
echo "</form>";

// Run cron jobs if requested
if (isset($_POST['run_ten_minutes'])) {
    echo "<h2>6. Running 10-Minute Cron</h2>";
    $cron = new MAILPN_Cron();
    $cron->cron_ten_minutes();
    echo "✅ 10-minute cron executed successfully<br>";
}

if (isset($_POST['run_daily'])) {
    echo "<h2>6. Running Daily Cron</h2>";
    $cron = new MAILPN_Cron();
    $cron->cron_daily();
    echo "✅ Daily cron executed successfully<br>";
}

// Show server cron information
echo "<h2>7. Server Cron Information</h2>";
echo "If WordPress cron is disabled, you need to set up a server cron job.<br>";
echo "Add this to your server's crontab (run every 5 minutes):<br>";
echo "<code>*/5 * * * * wget -q -O /dev/null " . home_url('wp-cron.php') . "?doing_wp_cron</code><br><br>";

echo "Or if you have WP-CLI installed:<br>";
echo "<code>*/5 * * * * cd " . ABSPATH . " && wp cron event run --due-now</code><br>";

echo "<hr>";
echo "<p><strong>Note:</strong> If emails are not being sent automatically, check if WordPress cron is enabled and properly configured.</p>";
?> 