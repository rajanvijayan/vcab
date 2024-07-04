<?php

namespace EcabVendasta\Includes\Trip;

class TripListings {

    public function __construct() {
        add_filter('manage_trip_posts_columns', [$this, 'add_columns']);
        add_action('manage_trip_posts_custom_column', [$this, 'custom_columns'], 10, 2);
    }

    public function add_columns($columns) {
        $columns['passenger_list'] = __('Passenger List', 'text_domain');
        return $columns;
    }

    public function custom_columns($column, $post_id) {
        switch ($column) {
            case 'passenger_list':
                $passenger_list = get_post_meta($post_id, 'passenger_list', true);
                if (!empty($passenger_list) && is_array($passenger_list)) {
                    foreach ($passenger_list as $passenger) {
                        echo '<p>' . esc_html($passenger['name']) . ' (' . esc_html($passenger['location']) . ')</p>';
                    }
                } else {
                    echo __('No passengers', 'text_domain');
                }
                break;
        }
    }
}