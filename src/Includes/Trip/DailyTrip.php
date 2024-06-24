<?php
namespace EcabVendasta\Includes\Trip;

class DailyTrip {
    public function __construct() {
        $this->create_daily_trip();
    }

    private function create_daily_trip() {
        // Check if a trip for today already exists
        $today = wp_date('d M Y');  
        $args = array(
            'post_type'  => 'trip',
            'post_status' => 'publish',
            'title'      => $today,
        );
        $query = new \WP_Query($args);
        
        if ($query->have_posts()) {
            return; // Trip for today already exists
        }

        // Create a new trip post
        $new_trip = array(
            'post_title'    => $today,
            'post_status'   => 'publish',
            'post_type'     => 'trip',
        );

        // Insert the post into the database
        $post_id = wp_insert_post($new_trip);

        if (is_wp_error($post_id)) {
            // Handle error
            error_log($post_id->get_error_message());
        } else {
            // Successfully created the trip
            // You can add additional meta data here if needed
            update_post_meta($post_id, 'trip_schedule', 'up'); // example meta field
        }
    }
}
?>
