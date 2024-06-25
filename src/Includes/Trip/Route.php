<?php
namespace EcabVendasta\Includes\Trip;

class Route {
    public function __construct() {
        // Schedule the cron job if not already scheduled
        if (!wp_next_scheduled('route_cron_event')) {
            // Retrieve check-in and check-out times from settings
            $check_in_times = get_option('checkin_times', []);
            $check_out_times = get_option('checkout_times', []);

            // Schedule cron jobs for each check-in and check-out time
            foreach ($check_in_times as $check_in_time) {
                $this->schedule_cron_job($check_in_time, 'checkin');
            }

            foreach ($check_out_times as $check_out_time) {
                $this->schedule_cron_job($check_out_time, 'checkout');
            }
        }

        // Hook our custom functions into the cron events
        add_action('route_checkin_event', [__CLASS__, 'run_checkin_route']);
        add_action('route_checkout_event', [__CLASS__, 'run_checkout_route']);
    }

    private function schedule_cron_job($time, $type) {
        $timestamp = strtotime($time);

        if ($type === 'checkin') {
            wp_schedule_event($timestamp, 'daily', 'route_checkin_event');
        } elseif ($type === 'checkout') {
            wp_schedule_event($timestamp, 'daily', 'route_checkout_event');
        }
    }

    public static function run_checkin_route() {
        // Logic to handle check-in route
    }

    public static function run_checkout_route() {
        // Logic to handle check-out route
    }

    public static function deactivate() {
        // Clear the scheduled events on plugin deactivation
        wp_clear_scheduled_hook('route_checkin_event');
        wp_clear_scheduled_hook('route_checkout_event');
    }
}
