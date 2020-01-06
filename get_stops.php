 <?php
$servername = "localhost";
$username = "id12111839_sean";
$password = "";
$dbname = "id12111839_bus";

$services = json_decode($_POST["myServices"]);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$servicesobj = new stdClass();
$servicesarr = array();


foreach($services as $service)
{
	$servicestopsobj = new stdClass();


	$sql = "SELECT stops.stop_id, main_stop_times.stop_sequence, stops.stop_lat, stops.stop_lon FROM routes, main_trips, main_stop_times, stops WHERE routes.route_id = main_trips.route_id and main_trips.trip_id = main_stop_times.trip_id and main_stop_times.stop_id = stops.stop_id and routes.route_short_name = '" . $service->short_name . "' and main_trips.trip_id like '%" . $service->period . "%' and main_trips.direction = '" . $service->direction . "' order by main_stop_times.stop_sequence";
	$result = $conn->query($sql);

	if (!$result) {
		trigger_error('Invalid query: ' . $conn->error);
	}

	$newarr = array();
	$prev_stop_lat = "";
	$prev_stop_lng = "";
	$distance = 0;

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			
			if ($prev_stop_lat !== "")
			
				$distance = (int)haversineGreatCircleDistance($row["stop_lat"], $row["stop_lon"], $prev_stop_lat, $prev_stop_lng);
			
			$tmparr = ['id' => $row["stop_id"], 'sequence' => $row["stop_sequence"], 'lat' => $row["stop_lat"], 'lng' => $row["stop_lon"], 'distance_to_previous' => $distance];
			array_push($newarr, $tmparr);
			
			$prev_stop_lat = $row["stop_lat"];
			$prev_stop_lng = $row["stop_lon"];
		}
	}
	
	$servicestopsobj->stops = $newarr;
	array_push($servicesarr, $servicestopsobj);

}
$conn->close();

$servicesobj->services = $servicesarr;
echo json_encode($servicesobj);


/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}


?> 