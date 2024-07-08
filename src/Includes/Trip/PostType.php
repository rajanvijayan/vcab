<?php 
namespace EcabVendasta\Includes\Trip;

class PostType {
    public function __construct() {
        add_action('init', [__CLASS__, 'register_trip_post_type']);
        add_action('add_meta_boxes', [__CLASS__, 'add_trip_meta_boxes']);
        add_action('save_post', [__CLASS__, 'save_trip_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_styles']);
    }

    public static function enqueue_styles() {
        wp_enqueue_style('trip-module-styles', plugin_dir_url(__FILE__) . '../../../assets/css/trip.css');
    }

    public static function register_trip_post_type() {
        $labels = array(
            'name'                  => _x('Trips', 'Post type general name', 'textdomain'),
            'singular_name'         => _x('Trip', 'Post type singular name', 'textdomain'),
            'menu_name'             => _x('Trips', 'Admin Menu text', 'textdomain'),
            'name_admin_bar'        => _x('Trip', 'Add New on Toolbar', 'textdomain'),
            'add_new'               => __('Add New', 'textdomain'),
            'add_new_item'          => __('Add New Trip', 'textdomain'),
            'edit_item'             => __('Edit Trip', 'textdomain'),
            'view_item'             => __('View Trip', 'textdomain'),
            'all_items'             => __('All Trips', 'textdomain'),
            'search_items'          => __('Search Trips', 'textdomain'),
        );
    
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'trip'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true, // Enable parent and child posts
            'menu_position'      => null,
            'supports'           => array('title', 'page-attributes'), // Supports title, editor, and page attributes for hierarchy
            'menu_icon'          => 'dashicons-car',
        );
    
        register_post_type('trip', $args);
    }    

    public static function add_trip_meta_boxes() {
        add_meta_box(
            'trip_details',
            __('Trip Details', 'textdomain'),
            [__CLASS__, 'render_trip_details_meta_box'],
            'trip',
            'normal',
            'high'
        );
    }

    public static function render_trip_details_meta_box($post) {
        
        wp_nonce_field('trip_details_meta_box', 'trip_details_meta_box_nonce');

        $trip_schedule = get_post_meta($post->ID, 'trip_schedule', true);
        $driver = get_post_meta($post->ID, 'driver', true);
        $driver_phone = get_post_meta($post->ID, 'driver_phone', true);
        $cab_reg_no = get_post_meta($post->ID, 'cab_reg_no', true);
        $passenger_list = get_post_meta($post->ID, 'passenger_list', true);
        $trip_status = get_post_meta($post->ID, 'trip_status', true);
        $start_time = get_post_meta($post->ID, 'start_time', true);
        $end_time = get_post_meta($post->ID, 'end_time', true);
        $driver_feedback = get_post_meta($post->ID, 'driver_feedback', true);
        $driver_notes = get_post_meta($post->ID, 'driver_notes', true);
        $admin_notes = get_post_meta($post->ID, 'admin_notes', true);

        $passenger_list = is_array($passenger_list) ? $passenger_list : [];

        ?>
        <table class="form-table">
            <tr>
                <th><label for="trip_schedule"><?php _e('Trip Schedule', 'textdomain'); ?></label></th>
                <td>
                    <select name="trip_schedule" id="trip_schedule" class="regular-text">
                        <option value="pickup" <?php selected($trip_schedule, 'pickup'); ?>><?php _e('Pickup', 'textdomain'); ?></option>
                        <option value="drop" <?php selected($trip_schedule, 'drop'); ?>><?php _e('Drop', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="driver"><?php _e('Driver Name', 'textdomain'); ?></label></th>
                <td><input type="text" name="driver" id="driver" value="<?php echo esc_attr($driver); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="driver_phone"><?php _e('Driver Phone', 'textdomain'); ?></label></th>
                <td><input type="text" name="driver_phone" id="driver_phone" value="<?php echo esc_attr($driver_phone); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="cab_reg_no"><?php _e('Cab Reg No', 'textdomain'); ?></label></th>
                <td><input type="text" name="cab_reg_no" id="cab_reg_no" value="<?php echo esc_attr($cab_reg_no); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="passenger_list"><?php _e('Passenger List', 'textdomain'); ?></label></th>
                <td>
                    <div id="passenger-list-container">
                        <?php foreach ($passenger_list as $index => $passenger) : ?>
                            <div class="passenger">
                                <label for="passenger_name_<?php echo $index; ?>"><?php _e('Name', 'textdomain'); ?></label>
                                <input type="text" id="passenger_name_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][name]" placeholder="<?php _e('Name', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['name']); ?>" class="regular-text">

                                <!-- <label for="shift_time_<?php echo $index; ?>"><?php _e('Shift Time', 'textdomain'); ?></label>
                                <input type="time" id="shift_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][shift_time]" placeholder="<?php _e('Shift Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['shift_time']); ?>" class="regular-text"> -->

                                <label for="location_<?php echo $index; ?>"><?php _e('Location', 'textdomain'); ?></label>
                                <input type="text" id="location_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][location]" placeholder="<?php _e('Location', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['location']); ?>" class="regular-text">

                                <!-- <label for="start_time_<?php echo $index; ?>"><?php _e('Start Time', 'textdomain'); ?></label>
                                <input type="time" id="start_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][start_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['start_time']); ?>" class="regular-text">

                                <label for="end_time_<?php echo $index; ?>"><?php _e('Start Time', 'textdomain'); ?></label>
                                <input type="time" id="end_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][end_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['end_time']); ?>" class="regular-text">

                                <label for="rating_<?php echo $index; ?>"><?php _e('Rating', 'textdomain'); ?></label>
                                <input type="number" id="rating_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][rating]" placeholder="<?php _e('Rating', 'textdomain'); ?>" min="1" max="5" class="regular-text"> -->
                            </div>

                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-passenger" class="button button-secontry button-large"><?php _e('Add Passenger', 'textdomain'); ?></button>
                </td>
            </tr>
            <tr>
                <th><label for="trip_status"><?php _e('Trip Status', 'textdomain'); ?></label></th>
                <td>
                    <select name="trip_status" id="trip_status" class="regular-text">
                        <option value="scheduled" <?php selected($trip_status, 'scheduled'); ?>><?php _e('Scheduled', 'textdomain'); ?></option>
                        <option value="in_progress" <?php selected($trip_status, 'in_progress'); ?>><?php _e('In Progress', 'textdomain'); ?></option>
                        <option value="completed" <?php selected($trip_status, 'completed'); ?>><?php _e('Completed', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="start_time"><?php _e('Start Time', 'textdomain'); ?></label></th>
                <td><input type="datetime-local" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="end_time"><?php _e('End Time', 'textdomain'); ?></label></th>
                <td><input type="datetime-local" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="driver_feedback"><?php _e('Driver Feedback', 'textdomain'); ?></label></th>
                <td><textarea name="driver_feedback" id="driver_feedback" rows="5" class="large-text"><?php echo esc_textarea($driver_feedback); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="driver_notes"><?php _e('Driver Notes', 'textdomain'); ?></label></th>
                <td><textarea name="driver_notes" id="driver_notes" rows="5" class="large-text"><?php echo esc_textarea($driver_notes); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="admin_notes"><?php _e('Admin Notes', 'textdomain'); ?></label></th>
                <td><textarea name="admin_notes" id="admin_notes" rows="5" class="large-text"><?php echo esc_textarea($admin_notes); ?></textarea></td>
            </tr>
        </table>

        <!-- Add a container for the map -->
        <div id="map" style="width: 100%; height: 400px;"></div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var addPassengerButton = document.getElementById('add-passenger');
                var passengerListContainer = document.getElementById('passenger-list-container');
                var passengerIndex = <?php echo count($passenger_list); ?>;
    
                addPassengerButton.addEventListener('click', function () {
                    var passengerDiv = document.createElement('div');
                    passengerDiv.classList.add('passenger');
    
                    var passengerNameLabel = document.createElement('label');
                    passengerNameLabel.setAttribute('for', 'passenger_name_' + passengerIndex);
                    passengerNameLabel.innerText = '<?php _e('Name', 'textdomain'); ?>';
                    passengerDiv.appendChild(passengerNameLabel);
    
                    var passengerNameInput = document.createElement('input');
                    passengerNameInput.type = 'text';
                    passengerNameInput.id = 'passenger_name_' + passengerIndex;
                    passengerNameInput.name = 'passenger_list[' + passengerIndex + '][name]';
                    passengerNameInput.placeholder = '<?php _e('Name', 'textdomain'); ?>';
                    passengerNameInput.classList.add('regular-text');
                    passengerDiv.appendChild(passengerNameInput);
    
                    var locationLabel = document.createElement('label');
                    locationLabel.setAttribute('for', 'location_' + passengerIndex);
                    locationLabel.innerText = '<?php _e('Location', 'textdomain'); ?>';
                    passengerDiv.appendChild(locationLabel);
    
                    var locationInput = document.createElement('input');
                    locationInput.type = 'text';
                    locationInput.id = 'location_' + passengerIndex;
                    locationInput.name = 'passenger_list[' + passengerIndex + '][location]';
                    locationInput.placeholder = '<?php _e('Location', 'textdomain'); ?>';
                    locationInput.classList.add('regular-text');
                    passengerDiv.appendChild(locationInput);
    
                    passengerListContainer.appendChild(passengerDiv);
    
                    passengerIndex++;
                });
            });
        </script>

        <?php $api_key = get_option('vcab_google_maps_api_key', '');?>

        <!-- Include Google Maps script -->
        <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key;?>&libraries=places"></script>
        <script>
            function initMap() {
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 10,
                    center: {lat: 40.7128, lng: -74.0060} // Set the center of the map (e.g., New York City)
                });

                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer({
                    map: map
                });

                var waypoints = [
                    <?php foreach ($passenger_list as $passenger) : ?>
                        {
                            location: '<?php echo esc_js($passenger['location']); ?>',
                            stopover: true
                        },
                    <?php endforeach; ?>
                ];

                var origin = waypoints.shift().location;
                var destination = waypoints.pop().location;

                directionsService.route({
                    origin: origin,
                    destination: destination,
                    waypoints: waypoints,
                    optimizeWaypoints: true,
                    travelMode: 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                });
            }

            google.maps.event.addDomListener(window, 'load', initMap);
        </script>

        <?php
    }

    public static function save_trip_meta($post_id, $post) {
        if (!isset($_POST['trip_details_meta_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['trip_details_meta_box_nonce'];

        if (!wp_verify_nonce($nonce, 'trip_details_meta_box')) {
            return $post_id;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        if ('trip' !== $post->post_type) {
            return $post_id;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        $trip_schedule = isset($_POST['trip_schedule']) ? sanitize_text_field($_POST['trip_schedule']) : '';
        $driver = isset($_POST['driver']) ? sanitize_text_field($_POST['driver']) : '';
        $driver_phone = isset($_POST['driver_phone']) ? sanitize_text_field($_POST['driver_phone']) : '';
        $cab_reg_no = isset($_POST['cab_reg_no']) ? sanitize_text_field($_POST['cab_reg_no']) : '';
        $passenger_list = isset($_POST['passenger_list']) ? array_map('sanitize_text_field', $_POST['passenger_list']) : [];
        $trip_status = isset($_POST['trip_status']) ? sanitize_text_field($_POST['trip_status']) : '';
        $start_time = isset($_POST['start_time']) ? sanitize_text_field($_POST['start_time']) : '';
        $end_time = isset($_POST['end_time']) ? sanitize_text_field($_POST['end_time']) : '';
        $driver_feedback = isset($_POST['driver_feedback']) ? sanitize_textarea_field($_POST['driver_feedback']) : '';
        $driver_notes = isset($_POST['driver_notes']) ? sanitize_textarea_field($_POST['driver_notes']) : '';
        $admin_notes = isset($_POST['admin_notes']) ? sanitize_textarea_field($_POST['admin_notes']) : '';

        update_post_meta($post_id, 'trip_schedule', $trip_schedule);
        update_post_meta($post_id, 'driver', $driver);
        update_post_meta($post_id, 'driver_phone', $driver_phone);
        update_post_meta($post_id, 'cab_reg_no', $cab_reg_no);
        update_post_meta($post_id, 'passenger_list', $passenger_list);
        update_post_meta($post_id, 'trip_status', $trip_status);
        update_post_meta($post_id, 'start_time', $start_time);
        update_post_meta($post_id, 'end_time', $end_time);
        update_post_meta($post_id, 'driver_feedback', $driver_feedback);
        update_post_meta($post_id, 'driver_notes', $driver_notes);
        update_post_meta($post_id, 'admin_notes', $admin_notes);
    }
}