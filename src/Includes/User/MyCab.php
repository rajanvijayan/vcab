<?php 
namespace EcabVendasta\Includes\User;

class MyCab {

    public function __construct() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_post_save_my_cab_profile', [__CLASS__, 'save_my_cab_profile']);
        add_action('admin_post_generate_bookings', [__CLASS__, 'generate_bookings']);
        add_action('wp_ajax_update_booking', [__CLASS__, 'update_booking']);
        add_action('wp_ajax_nopriv_update_booking', [__CLASS__, 'update_booking']);
    }

    public static function add_admin_menu() {
        add_menu_page(
            'My Cab',       // Page title
            'My Cab',       // Menu title
            'read',         // Capability
            'my_cab',       // Menu slug
            [__CLASS__, 'create_admin_page'], // Callback function
            'dashicons-location', // Icon URL
            6               // Position
        );
    }

    public static function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>My Cab</h1>
            <h2 class="nav-tab-wrapper">
                <a href="#bookings" class="nav-tab nav-tab-active">My Booking</a>
                <a href="#profile" class="nav-tab">My Profile</a>
            </h2>
            <div id="bookings" class="tab-content">

            <?php 
            $user_id = get_current_user_id();
            $cab_bookings = get_user_meta($user_id, 'cab_bookings', true);
            // echo '<pre>';
            // print_r($cab_bookings);
            // echo '</pre>';
            ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="generate_bookings">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="from_date">From Date</label></th>
                            <td>
                                <input type="date" id="from_date" name="from_date" required>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="to_date">To Date</label></th>
                            <td>
                                <input type="date" id="to_date" name="to_date" required>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Generate Booking'); ?>
                </form>

                    
                <div class="wrap">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th>Date 1</th>
                                <th>Pickup</th>
                                <th>Drop Off</th>
                                <!-- <th>Location</th> -->
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $user_id = get_current_user_id();
                            $cab_bookings = get_user_meta($user_id, 'cab_bookings', true);
                            foreach ($cab_bookings as $booking) : ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', $booking['date']); ?></td>
                                    <td>
                                        <input type="checkbox" class="pickup-toggle" <?php echo @$booking['pick_up'] == 1 ? 'checked' : ''; ?> >
                                    </td>
                                    <td>
                                        <input type="checkbox" class="drop-off-toggle" <?php echo @$booking['drop_off'] == 1 ? 'checked' : ''; ?> >
                                    </td>
                                    <!-- <td><?php echo $booking['location']['location_name']; ?></td> -->
                                    <td><?php echo ucfirst($booking['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div id="profile" class="tab-content" style="display:none;">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php
                    $user_id = get_current_user_id();
                    $pickup_time = get_user_meta($user_id, 'cab_pickup_time', true);
                    $drop_time = get_user_meta($user_id, 'cab_drop_time', true);
                    $location = get_user_meta($user_id, 'cab_location', true);
                    $location_name = is_array($location) ? $location['location_name'] : '';
                    $location_lat = is_array($location) ? $location['lat'] : '';
                    $location_lng = is_array($location) ? $location['lng'] : '';

                    $vcab_shifts = get_option('vcab_shifts', []);
                    if (!is_array($vcab_shifts)) {
                        $vcab_shifts = [];
                    }
                    
                    ?>
                    <input type="hidden" name="action" value="save_my_cab_profile">
                    <table class="form-table">
                        <tr>
                            <th scope="row"><label for="cab_pickup_time">Cab Pickup Time</label></th>
                            <td>
                                <select id="cab_pickup_time" name="cab_pickup_time">
                                    <option value="">--NA--</option>
                                    <?php foreach ($vcab_shifts as $shift) { 
                                        if ($shift['type'] === 'pickup') { ?>
                                            <option value="<?php echo esc_attr($shift['time']); ?>" <?php selected($pickup_time, $shift['time']); ?>><?php echo esc_html($shift['time']); ?></option>
                                    <?php } } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cab_drop_time">Cab Drop Time</label></th>
                            <td>
                                <select id="cab_drop_time" name="cab_drop_time">
                                    <option value="">--NA--</option>
                                    <?php foreach ($vcab_shifts as $shift) { 
                                        if ($shift['type'] === 'drop') { ?>
                                            <option value="<?php echo esc_attr($shift['time']); ?>" <?php selected($drop_time, $shift['time']); ?>><?php echo esc_html($shift['time']); ?></option>
                                    <?php } } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cab_location">Cab Location</label></th>
                            <td>
                                <input type="text" id="cab_location" name="cab_location" value="<?php echo esc_attr($location_name); ?>">
                                <p><?php echo $location_lat . ', '. $location_lng;?></p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button('Save Changes'); ?>
                </form>
            </div>
            
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabs = document.querySelectorAll('.nav-tab');
                const contents = document.querySelectorAll('.tab-content');

                tabs.forEach(tab => {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();

                        tabs.forEach(item => item.classList.remove('nav-tab-active'));
                        contents.forEach(content => content.style.display = 'none');

                        tab.classList.add('nav-tab-active');
                        document.querySelector(tab.getAttribute('href')).style.display = 'block';
                    });
                });

                jQuery(document).ready(function($) {
                    $('.pickup-toggle, .drop-off-toggle').on('change', function() {
                        var $this = $(this);
                        var date = $this.closest('tr').find('td:first').text();
                        var field = $this.hasClass('pickup-toggle') ? 'pick_up' : 'drop_off';
                        var value = $this.is(':checked');

                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'update_booking',
                                date: date,
                                field: field,
                                value: value
                            },
                            success: function(response) {
                                if (response.success) {
                                    alert('Booking updated successfully.');
                                } else {
                                    alert('Failed to update booking.');
                                }
                            }
                        });
                    });

                    // Handle the Generate Booking form submission
                    $('form[action$="generate_bookings"]').on('submit', function(e) {
                        e.preventDefault();

                        var fromDate = $('#from_date').val();
                        var toDate = $('#to_date').val();

                        $.ajax({
                            url: ajaxurl,
                            method: 'POST',
                            data: {
                                action: 'generate_bookings',
                                from_date: fromDate,
                                to_date: toDate
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Reload the page to see the updated bookings
                                    location.reload();
                                } else {
                                    alert('Failed to generate bookings.');
                                }
                            }
                        });
                    });
                });
            });
        </script>
        <?php
    }

    public static function save_my_cab_profile() {
        if (!current_user_can('read')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // check_admin_referer('my_cab_profile');

        $user_id = get_current_user_id();
        $pickup_time = sanitize_text_field($_POST['cab_pickup_time']);
        $drop_time = sanitize_text_field($_POST['cab_drop_time']);
        $location = sanitize_text_field($_POST['cab_location']);

        update_user_meta($user_id, 'cab_pickup_time', $pickup_time);
        update_user_meta($user_id, 'cab_drop_time', $drop_time);

        $geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($location) . "&key=YOUR_API_KEY";
        $geocodeData = file_get_contents($geocodeUrl);
        $locationData = json_decode($geocodeData, true);

        if ($locationData['status'] === 'OK') {
            $lat = $locationData['results'][0]['geometry']['location']['lat'];
            $lng = $locationData['results'][0]['geometry']['location']['lng'];
            $locationName = $locationData['results'][0]['formatted_address'];
            $locationArray = [
                'lat' => $lat,
                'lng' => $lng,
                'location_name' => $locationName
            ];
            update_user_meta($user_id, 'cab_location', $locationArray);
        } else {
            // Handle API error or invalid address
            wp_die(__('Invalid address. Please try again.'));
        }

        wp_redirect(admin_url('admin.php?page=my_cab&updated=true'));
        exit;
    }

    public static function generate_bookings() {
        if (!current_user_can('read')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $user_id = get_current_user_id();
        $from_date = sanitize_text_field($_POST['from_date']);
        $to_date = sanitize_text_field($_POST['to_date']);
        $pickup_time = get_user_meta($user_id, 'cab_pickup_time', true);
        $drop_time = get_user_meta($user_id, 'cab_drop_time', true);
        $location = get_user_meta($user_id, 'cab_location', true);

        if (!$pickup_time || !$drop_time || !$location) {
            wp_send_json_error(__('Please complete your profile settings first.'));
        }

        $from_date_ts = strtotime($from_date);
        $to_date_ts = strtotime($to_date);
        $bookings = [];

        $cab_bookings = get_user_meta($user_id, 'cab_bookings', true);

        $i = 0;
        for ($date = $from_date_ts; $date <= $to_date_ts; $date += DAY_IN_SECONDS) {

            $pickup = strtotime($date) ==  $cab_bookings[$i]['date'] ? $cab_bookings[$i]['pick_up'] : 0;
            $drop_off = strtotime($date) ==  $cab_bookings[$i]['date'] ? $cab_bookings[$i]['pick_up'] : 0; 

            $bookings[] = [
                'date' => $date,
                'pick_up' => $cab_bookings[$i]['pick_up'],
                'drop_off' => $cab_bookings[$i]['drop_off'],
                'location' => $location,
                'status' => 'future'
            ];
            $i++;
        }

        update_user_meta($user_id, 'cab_bookings', $bookings);

        wp_redirect(admin_url('admin.php?page=my_cab&updated=true'));
        exit;

        //wp_send_json_success();
    }

    public static function update_booking() {
        if (!current_user_can('read')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $user_id = get_current_user_id();
        $date = sanitize_text_field($_POST['date']);
        $field = sanitize_text_field($_POST['field']);
        $value = sanitize_text_field($_POST['value']);
        $cab_bookings = get_user_meta($user_id, 'cab_bookings', true);

        foreach ($cab_bookings as &$booking) {
            if ($booking['date'] == strtotime($date)) {
                $booking[$field] = $value == 'true' ? 1 : 0;
                break;
            }
        }

        update_user_meta($user_id, 'cab_bookings', $cab_bookings);

        wp_send_json_success();
    }
}
