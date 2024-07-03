<?php
namespace EcabVendasta\Includes\Trip;

class GoogleMap {

    private $api_key;

    public function __construct($api_key) {
        $this->api_key = $api_key;
    }

    public function calculateDistance($origin, $destination) {
        $origin = urlencode($origin);
        $destination = urlencode($destination);
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins={$origin}&destinations={$destination}&key={$this->api_key}&mode=driving";

        $response = file_get_contents($url);
        $json = json_decode($response, true);

        if ($json['status'] == 'OK') {
            return $json['rows'][0]['elements'][0]['distance']['value'];
        } else {
            return PHP_INT_MAX; // Return a very large value if distance cannot be calculated
        }
    }

    public function groupStaffByLocation($staff) {
        $groupedStaff = array();
        $carIndex = 0;

        // Set the starting point for all cars
        $startingPoint = "Ambathur, Chennai, India";

        foreach ($staff as $name => $details) {
            // Initialize variables for tracking the minimum distance and car index
            $minDistance = 20000; // 20 km
            $minCarIndex = null;

            // Iterate through existing cars to find the one with the shortest distance
            foreach ($groupedStaff as $index => $car) {
                // Calculate distance from the last staff member in the current car to the new staff member
                $lastPersonLocation = end($car["staff"])["location"];
                $distance = $this->calculateDistance($lastPersonLocation, $details["location"]);

                // Check if this car has the shortest distance so far
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $minCarIndex = $index;
                }
            }

            // If no cars exist yet or all existing cars are full, create a new car
            if ($minCarIndex === null || count($groupedStaff[$minCarIndex]["staff"]) >= 4) {
                $groupedStaff[] = array(
                    "starting_point" => $startingPoint,
                    "ending_point" => "Vendasta India, Chennai, India",
                    "time" => "", // Time can be estimated using a Map API later
                    "staff" => array(),
                );
                $minCarIndex = count($groupedStaff) - 1; // Index of the newly created car
            }

            // Add the current staff member to the car with the shortest distance
            $groupedStaff[$minCarIndex]["staff"][] = array("name" => $name, "location" => $details["location"]);
        }

        return $groupedStaff;
    }
}
