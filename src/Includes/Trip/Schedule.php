<?php 
namespace EcabVendasta\Includes\Trip;

use EcabVendasta\Includes\Trip\GoogleAI;

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


            $prompt = '';
            foreach( $routes[$key]['staffs'] as $staff ) {
                $prompt .= $staff['name'].' from '.$staff['location']['location_name']. ', ';
            }

            $prompt .= ' prepare tripsheet to Vendasta India, Chennai, All cabs starts from Ambathur, Chennai. And max capacity of the cab is 3. prepare tripsheet based on location. Output should be in JSON format.';

            // echo '<pre>';   
            // print_r($prompt);
            // echo '</pre>';

        }

        
        // echo '<pre>';   
        // print_r($routes);
        // echo '</pre>';

        // $ai = new GoogleAI();
        // $output = $ai->fetchData($prompt);

        // die;

        
    }
}