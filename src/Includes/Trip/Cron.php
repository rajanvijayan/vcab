<?php 
namespace EcabVendasta\Includes\Trip;

class Cron {

    public function __construct() {
        add_action('init', [__CLASS__, 'schedule_daily_cron']);
        add_action('ecabvendasta_daily_trip_schedule', [__CLASS__, 'run_trip_schedule']);
    }

    public static function schedule_daily_cron() {
        if (!wp_next_scheduled('ecabvendasta_daily_trip_schedule')) {
            $cron_time = get_option('vcab_cron_time', '00:00');
            list($hour, $minute) = explode(':', $cron_time);

            // Schedule the event
            $timestamp = strtotime("today $hour:$minute");
            if ($timestamp < time()) {
                $timestamp = strtotime("tomorrow $hour:$minute");
            }

            wp_schedule_event($timestamp, 'daily', 'ecabvendasta_daily_trip_schedule');
        }
    }

    public static function run_trip_schedule() {
        require_once plugin_dir_path(__FILE__) . 'Schedule.php';
        $schedule = new Schedule();
        $schedule->schedule_trips();
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('ecabvendasta_daily_trip_schedule');
    }
}

new Cron();
