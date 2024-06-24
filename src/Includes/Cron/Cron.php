<?php
namespace EcabVendasta\Includes\Cron;

Use EcabVendasta\Includes\Trip\DailyTrip;

class Cron {
    public function __construct() {
        // Schedule cron job if not already scheduled
        if (!wp_next_scheduled('daily_trip_event')) {
            wp_schedule_event(time(), 'daily', 'daily_trip_event');
        }

        // Hook our custom function into the cron event
        add_action('daily_trip_event', [__CLASS__, 'run_daily_trip']);
    }

    public static function run_daily_trip() {
        // Create an instance of the DailyTrip class and call the create_daily_trip method
        new DailyTrip();
    }

    public static function deactivate() {
        // Clear the scheduled event on plugin deactivation
        wp_clear_scheduled_hook('daily_trip_event');
    }
}
