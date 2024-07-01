<?php
namespace EcabVendasta\Includes\API;

class GoogleAI {
    public function __construct() {
        add_action('init', [__CLASS__, 'schedule_trip_preparation']);
        add_action('prepare_trip_sheet_event', [__CLASS__, 'prepare_trip_sheet']);
    }

    public static function schedule_trip_preparation() {
        if (!wp_next_scheduled('prepare_trip_sheet_event')) {
            wp_schedule_event(time(), 'hourly', 'prepare_trip_sheet_event');
        }
    }

    public static function prepare_trip_sheet() {
        $checkin_times = get_option('checkin_times', []);
        $checkout_times = get_option('checkout_times', []);

        $current_time = current_time('H:i');

        // Check if the current time matches any shift time
        if (in_array($current_time, $checkin_times) || in_array($current_time, $checkout_times)) {
            $users = self::get_users_by_shift_time($current_time);
            if (!empty($users)) {
                self::create_trip_sheet($users);
            }
        }
    }

    public static function get_users_by_shift_time($shift_time) {
        $checkin_users = get_users([
            'meta_key' => 'checkin_time',
            'meta_value' => $shift_time,
        ]);

        $checkout_users = get_users([
            'meta_key' => 'checkout_time',
            'meta_value' => $shift_time,
        ]);

        return array_merge($checkin_users, $checkout_users);
    }

    public static function create_trip_sheet($users) {
        $trip_date = gmdate('d M Y');
        $daily_trip_post = self::get_or_create_daily_trip_post($trip_date);

        $car_capacity = 4;
        $min_capacity = 2;
        $trips = [];

        // Group users into trips
        while (count($users) > 0) {
            $trip_users = array_splice($users, 0, min($car_capacity, count($users)));
            if (count($trip_users) >= $min_capacity) {
                $trip_id = self::create_trip_post($daily_trip_post->ID, $trip_users);
                $trips[] = $trip_id;
            }
        }

        return $trips;
    }

    public static function get_or_create_daily_trip_post($trip_date) {
        $existing_post = get_posts([
            'post_type' => 'trip',
            'meta_key' => 'trip_date',
            'meta_value' => $trip_date,
            'posts_per_page' => 1,
        ]);

        if (!empty($existing_post)) {
            return $existing_post[0];
        }

        $post_id = wp_insert_post([
            'post_title' => $trip_date,
            'post_type' => 'trip',
            'post_status' => 'publish',
            'meta_input' => [
                'trip_date' => $trip_date,
            ],
        ]);

        return get_post($post_id);
    }

    public static function create_trip_post($parent_id, $users) {
        $trip_post_id = wp_insert_post([
            'post_title' => 'Trip - ' . implode(', ', array_map(function($user) { return $user->display_name; }, $users)),
            'post_type' => 'trip',
            'post_status' => 'publish',
            'post_parent' => $parent_id,
        ]);

        // Prepare AI prompt
        $prompt = self::prepare_ai_prompt($users);
        $trip_sheet = self::get_trip_sheet_from_ai($prompt);

        // Save trip details
        update_post_meta($trip_post_id, 'trip_sheet', $trip_sheet);

        return $trip_post_id;
    }

    public static function prepare_ai_prompt($users) {
        $user_details = array_map(function($user) {
            return [
                'name' => $user->display_name,
                'checkin_time' => get_user_meta($user->ID, 'checkin_time', true),
                'checkout_time' => get_user_meta($user->ID, 'checkout_time', true),
            ];
        }, $users);

        $prompt = "Prepare a trip sheet for the following users:\n";
        foreach ($user_details as $user) {
            $prompt .= "Name: {$user['name']}, Check-in Time: {$user['checkin_time']}, Check-out Time: {$user['checkout_time']}\n";
        }
        $prompt .= "\nEach car's capacity is 4, minimum 2 and maximum 4 users per trip.";

        return $prompt;
    }

    public static function get_trip_sheet_from_ai($prompt) {
        // Assuming we have a function to communicate with Google Studio AI
        // This function would send the prompt to Google Studio AI and receive the trip sheet
        // For the purpose of this example, let's assume it returns a dummy trip sheet
        return "Trip Sheet: \n" . $prompt;
    }

    public static function deactivate() {
        wp_clear_scheduled_hook('prepare_trip_sheet_event');
    }
}
