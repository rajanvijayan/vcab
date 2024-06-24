<?php
namespace EcabVendasta\Includes\Trip;

class Trip {
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
        if ($post->post_parent > 0) {
        wp_nonce_field('trip_details_meta_box', 'trip_details_meta_box_nonce');

        $trip_schedule = get_post_meta($post->ID, 'trip_schedule', true);
        $driver = get_post_meta($post->ID, 'driver', true);
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
                        <option value="up" <?php selected($trip_schedule, 'up'); ?>><?php _e('Up', 'textdomain'); ?></option>
                        <option value="down" <?php selected($trip_schedule, 'down'); ?>><?php _e('Down', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="driver"><?php _e('Driver', 'textdomain'); ?></label></th>
                <td>
                    <select name="driver" id="driver" class="regular-text">
                        <?php
                        $users = get_users(['role' => 'driver']);
                        foreach ($users as $user) {
                            echo '<option value="' . esc_attr($user->ID) . '" ' . selected($driver, $user->ID, false) . '>' . esc_html($user->display_name) . '</option>';
                        }
                        ?>
                    </select>
                </td>
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

                                <label for="shift_time_<?php echo $index; ?>"><?php _e('Shift Time', 'textdomain'); ?></label>
                                <input type="time" id="shift_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][shift_time]" placeholder="<?php _e('Shift Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['shift_time']); ?>" class="regular-text">

                                <label for="location_<?php echo $index; ?>"><?php _e('Location', 'textdomain'); ?></label>
                                <input type="text" id="location_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][location]" placeholder="<?php _e('Location', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['location']); ?>" class="regular-text">

                                <label for="start_time_<?php echo $index; ?>"><?php _e('Start Time', 'textdomain'); ?></label>
                                <input type="time" id="start_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][start_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['start_time']); ?>" class="regular-text">

                                <label for="end_time_<?php echo $index; ?>"><?php _e('Start Time', 'textdomain'); ?></label>
                                <input type="time" id="end_time_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][end_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['end_time']); ?>" class="regular-text">

                                <label for="rating_<?php echo $index; ?>"><?php _e('Rating', 'textdomain'); ?></label>
                                <input type="number" id="rating_<?php echo $index; ?>" name="passenger_list[<?php echo $index; ?>][rating]" placeholder="<?php _e('Rating', 'textdomain'); ?>" value="<?php echo esc_attr($passenger['rating']); ?>" min="1" max="5" class="regular-text">
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
                        <option value="not_list_prepared" <?php selected($trip_status, 'not_list_prepared'); ?>><?php _e('Not List Prepared', 'textdomain'); ?></option>
                        <option value="list_prepared" <?php selected($trip_status, 'list_prepared'); ?>><?php _e('List Prepared', 'textdomain'); ?></option>
                        <option value="driver_assigned" <?php selected($trip_status, 'driver_assigned'); ?>><?php _e('Driver Assigned', 'textdomain'); ?></option>
                        <option value="started" <?php selected($trip_status, 'started'); ?>><?php _e('Started', 'textdomain'); ?></option>
                        <option value="ended" <?php selected($trip_status, 'ended'); ?>><?php _e('Ended', 'textdomain'); ?></option>
                        <option value="canceled" <?php selected($trip_status, 'canceled'); ?>><?php _e('Canceled', 'textdomain'); ?></option>
                        <option value="completed" <?php selected($trip_status, 'completed'); ?>><?php _e('Completed', 'textdomain'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="start_time"><?php _e('Start Time', 'textdomain'); ?></label></th>
                <td><input type="time" name="start_time" id="start_time" value="<?php echo esc_attr($start_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="end_time"><?php _e('End Time', 'textdomain'); ?></label></th>
                <td><input type="time" name="end_time" id="end_time" value="<?php echo esc_attr($end_time); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="driver_feedback"><?php _e('Driver Feedback', 'textdomain'); ?></label></th>
                <td><input type="number" name="driver_feedback" id="driver_feedback" value="<?php echo esc_attr($driver_feedback); ?>" min="1" max="5" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="driver_notes"><?php _e('Driver Notes', 'textdomain'); ?></label></th>
                <td><textarea name="driver_notes" id="driver_notes" class="regular-text"><?php echo esc_textarea($driver_notes); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="admin_notes"><?php _e('Admin Notes', 'textdomain'); ?></label></th>
                <td><textarea name="admin_notes" id="admin_notes" class="regular-text"><?php echo esc_textarea($admin_notes); ?></textarea></td>
            </tr>
        </table>

        <script>
            document.getElementById('add-passenger').addEventListener('click', function () {
                var container = document.getElementById('passenger-list-container');
                var index = container.children.length;
                var passenger = document.createElement('div');
                passenger.className = 'passenger';
                passenger.innerHTML = `
                    <label for="passenger_name_${index}"><?php _e('Name', 'textdomain'); ?></label>
                    <input type="text" id="passenger_name_${index}" name="passenger_list[${index}][name]" placeholder="<?php _e('Name', 'textdomain'); ?>" class="regular-text">

                    <label for="shift_time_${index}"><?php _e('Shift Time', 'textdomain'); ?></label>
                    <input type="time" id="shift_time_${index}" name="passenger_list[${index}][shift_time]" placeholder="<?php _e('Shift Time', 'textdomain'); ?>" class="regular-text">
                    
                    <label for="location_${index}"><?php _e('Location', 'textdomain'); ?></label>
                    <input type="text" id="location_${index}" name="passenger_list[${index}][location]" placeholder="<?php _e('Location', 'textdomain'); ?>" class="regular-text">

                    <label for="start_time_${index}"><?php _e('Start Time', 'textdomain'); ?></label>
                    <input type="time" id="start_time_${index}" name="passenger_list[${index}][start_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" class="regular-text">

                    <label for="end_time_${index}"><?php _e('Start Time', 'textdomain'); ?></label>
                    <input type="time" id="end_time_${index}" name="passenger_list[${index}][end_time]" placeholder="<?php _e('Start Time', 'textdomain'); ?>" class="regular-text">

                    <label for="rating_${index}"><?php _e('Rating', 'textdomain'); ?></label>
                    <input type="number" id="rating_${index}" name="passenger_list[${index}][rating]" placeholder="<?php _e('Rating', 'textdomain'); ?>" min="1" max="5" class="regular-text">
                `;
                container.appendChild(passenger);
            });
        </script>

        <?php
        } else {
            ?>
            <p><?php _e('Daily trip reports will be shown here - Coming Soon', 'textdomain'); ?></p>
            <?php
        }
    }

    public static function save_trip_meta($post_id, $post) {
        if (!isset($_POST['trip_details_meta_box_nonce']) || !wp_verify_nonce($_POST['trip_details_meta_box_nonce'], 'trip_details_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if ($post->post_type != 'trip') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $meta_fields = [
            'trip_schedule',
            'driver',
            'cab_reg_no',
            'trip_status',
            'start_time',
            'end_time',
            'driver_feedback',
            'driver_notes',
            'admin_notes'
        ];

        foreach ($meta_fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            } else {
                delete_post_meta($post_id, $field);
            }
        }

        if (isset($_POST['passenger_list']) && is_array($_POST['passenger_list'])) {
            $passenger_list = array_map(function($passenger) {
                return array_map('sanitize_text_field', $passenger);
            }, $_POST['passenger_list']);
            update_post_meta($post_id, 'passenger_list', $passenger_list);
        } else {
            delete_post_meta($post_id, 'passenger_list');
        }
    }
}
?>