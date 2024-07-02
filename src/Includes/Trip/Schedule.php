<?php 
namespace EcabVendasta\Includes\Trip;

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

            $shift_peoples[$key] = [];

            $tomorrow = strtotime( 'tomorrow' );

            $arg = [
                'role' => 'staff',
                'meta_key' => 'cab_'.$vcab_shift['type'].'_time',
                'meta_value' => $vcab_shift['time'],
            ];

            // echo '<pre>';
            // print_r($arg);
            // echo '</pre>';

            $users = get_users( $arg );

            foreach ($users as $user) {
                
                $cab_bookings = get_user_meta($user->ID, 'cab_bookings', true);

                $now = strtotime('now');
                $tomorrow = strtotime('+1 day', $now);

                if( isset($cab_bookings) && is_array($cab_bookings) ) {                    
                    foreach ($cab_bookings as $timestamp => $booking) {
                        if ($timestamp >= $now && $timestamp < $tomorrow) {
                            $shift_peoples[$key][] = $user->ID;

                            // echo '<pre>';   
                            // print_r($cab_bookings);
                            // echo '</pre>';

                        }
                    }
                }

            }
     
        }

        // echo '<pre>';   
        // print_r($shift_peoples);
        // echo '</pre>';

        

        // die;

        
    }
}