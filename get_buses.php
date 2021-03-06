<?php

require_once 'vendor/autoload.php';
use transit_realtime\FeedMessage;


$servername = "localhost";
$username = "id12111839_sean";
$password = "";
$dbname = "id12111839_bus";

//echo $_POST['serviceShortName'];
$routes = json_decode($_POST["myRoutes"]);



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$data = file_get_contents("https://gtfsrt.api.translink.com.au/Feed/SEQ");
$feed = new FeedMessage();
$feed->parse($data);

$newarr = array();

foreach ($feed->getEntityList() as $entity)
{
	if ($entity->hasVehicle())
	{
		if ($entity->getVehicle()->hasTrip())
		{
			foreach($routes as $route)
			{
				$bus_found_in_my_routes = 0;
				  
				foreach($route->all_id as $route_id)
				{
					if ($entity->getVehicle()->getTrip()->getRouteId() === $route_id)
					{
						$bus_found_in_my_routes = 1;
					}
				}

				if ($bus_found_in_my_routes === 1)
				{
					if ($entity->getVehicle()->hasPosition())
					{
						$sql = "SELECT trip_ids, trip_direction_ids FROM trips_merged WHERE trip_ids like '%" . substr($entity->getVehicle()->getTrip()->getTripId(), 0, 8) . "%'";
						$result = $conn->query($sql);

						if (!$result) {
							trigger_error('Invalid query: ' . $conn->error);
						}

						$tmparr = array();

						if ($result->num_rows > 0) {
							// output data of each row
							while($row = $result->fetch_assoc()) {
								//echo trim($row["trip_ids"]);
			
								$trip_ids = explode("~", $row["trip_ids"]);
								$index = array_search(substr($entity->getVehicle()->getTrip()->getTripId(), 0, 8), $trip_ids);
								//echo "~" . $index;
								
								$direction_ids = explode("~", $row["trip_direction_ids"]);
								$direction_id = $direction_ids[$index];
								
								$bus_id = $entity->getVehicle()->getVehicle()->getId();
								$bus_id_tmp = explode("_", $bus_id);
								$bus_id = end($bus_id_tmp);
								
								$tmparr = ['id' => $bus_id, 'route_id' => $entity->getVehicle()->getTrip()->getRouteId(), 'stop_id' => $entity->getVehicle()->getStopId(), 'stop_sequence' => '', 'direction' => $direction_id, 'lat' => $entity->getVehicle()->getPosition()->getLatitude(), 'lng' => $entity->getVehicle()->getPosition()->getLongitude()];
								array_push($newarr, $tmparr);
							}
						}
					}
				}
			}
		}
	}
}

//echo "Connected successfully";
$conn->close();


echo json_encode($newarr);


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

function secToHR($seconds) {
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  return $hours > 0 ? "$hours hrs, $minutes mins" : ($minutes > 0 ? "$minutes mins, $seconds secs" : "$seconds secs");
}

?>