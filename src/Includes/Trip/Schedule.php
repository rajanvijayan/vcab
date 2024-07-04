<?php 
namespace EcabVendasta\Includes\Trip;

use EcabVendasta\Includes\Trip\GoogleMap;

class Schedule {

    public function __construct() {
        add_action('init', [__CLASS__, 'schedule_trips']);
    }

    public static function schedule_trips() {
        // Get the vcab_shifts from options
        $vcab_shifts = get_option('vcab_shifts', []);
        if (!is_array($vcab_shifts)) {
            return;
        }

        foreach( $vcab_shifts as $key => $vcab_shift ) {

            $time = $vcab_shift['time'];
            $type = $vcab_shift['type'];
            $tomorrow = strtotime( 'tomorrow' );

            $routes[$key]['meta']['date'] = date( 'd M Y', $tomorrow );
            $routes[$key]['meta']['time'] = date( 'h:i A', strtotime( $time ) );
            $routes[$key]['meta']['type'] = $type;            

            $arg = [
                'role' => 'staff',
                'meta_key' => 'cab_'.$vcab_shift['type'].'_time',
                'meta_value' => $vcab_shift['time'],
            ];

            $users = get_users( $arg );

            foreach ($users as $user) {
                
                $cab_bookings = get_user_meta($user->ID, 'cab_bookings', true);

                $now = strtotime('now');
                $tomorrow = strtotime('+1 day', $now);

                if( isset($cab_bookings) && is_array($cab_bookings) ) {                    
                    foreach ($cab_bookings as $timestamp => $booking) {
                        if ($timestamp >= $now && $timestamp < $tomorrow) {

                            // echo '<pre>';
                            // print_r($booking);
                            // echo '</pre>';

                            $staff = [];

                            if( ($type == "pickup" && $booking['pick_up'] == 1) || ($type == "drop" && $booking['drop_off'] == 1) ){
                                $staff = [
                                    'name' => $user->display_name,
                                    'email' => $user->user_email,
                                    'phone' => get_user_meta($user->ID, 'phone', true),
                                    'location' => get_user_meta($user->ID, 'cab_location', true),
                                ];
                            }


                            $routes[$key]['staffs'][] = $staff;

                        }
                    }
                }

            }
        }

        foreach ($routes as $key => $route) {
            
            $staff_query = [];
            foreach( $route['staffs'] as $staff ) {
                if( !empty($staff) && !empty($staff['location']) ) {
                    $staff_query[$staff['name']] = ["location" => $staff['location']['location_name']];                    
                }
            }

            $googleMap = new GoogleMap();
            $groupedStaff = $googleMap->groupStaffByLocation($staff_query);            

            foreach( $groupedStaff as $key => $trip ){

                $trip_index = $key + 1;
                $trip_title = "Route $trip_index - " . $route['meta']['date'] . " | " . $route['meta']['time'] . " | " . $route['meta']['type'];

                $trip_data = [
                    'post_title' => $trip_title,
                    'post_status' => 'publish',
                    'post_type' => 'trip',
                ];

                // echo "<pre>";
                // print_r($trip_data);
                // echo "</pre>";

                $trip_id = wp_insert_post($trip_data);

                $passenger_list = [];
                foreach ($trip['staff'] as $staff) {
                    $passenger_list[] = [
                        'name' => $staff['name'],
                        'shift_time' => $route['meta']['time'],
                        'location' => $staff['location'],
                    ];
                }

                if (!is_wp_error($trip_id)) {
                    update_post_meta($trip_id, 'trip_schedule', $route['meta']['type']);

                    if (isset($passenger_list) && is_array($passenger_list)) {
                        $passenger_list = array_map(function($passenger) {
                            return array_map('sanitize_text_field', $passenger);
                        }, $passenger_list);
                        update_post_meta($trip_id, 'passenger_list', $passenger_list);
                    }
                }
                
            }

            

        }


        die;

        
    }
}