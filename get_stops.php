 <?php
$servername = "localhost";
$username = "id12111839_sean";
$password = "x";
$dbname = "id12111839_bus";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$sql = "SELECT stops.stop_id, stops.stop_lat, stops.stop_lon FROM routes, main_trips, main_stop_times, stops WHERE routes.route_id = main_trips.route_id and main_trips.trip_id = main_stop_times.trip_id and main_stop_times.stop_id = stops.stop_id and routes.route_short_name = '" . $_POST['serviceShortName'] . "' and main_trips.trip_id like '%" . $_POST['servicePeriod'] . "%' and main_trips.direction = '" . $_POST['serviceDirection'] . "'";
//$sql = "SELECT stops.stop_id, stops.stop_lat, stops.stop_lon FROM routes, main_trips, main_stop_times, stops WHERE routes.route_id = main_trips.route_id and main_trips.trip_id = main_stop_times.trip_id and main_stop_times.stop_id = stops.stop_id and routes.route_short_name = '130' and main_trips.trip_id like '%Weekday%' and main_trips.direction = 0";
//$sql = "SELECT stop_id FROM stops";
$result = $conn->query($sql);

if (!$result) {
    trigger_error('Invalid query: ' . $conn->error);
}

$newarr = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
     //   echo "id: " . $row["stop_id"]. " - Name: " . $row["stop_lat"]. " " . $row["stop_lon"]. "<br>";
		
		$tmparr = ['id' => $row["stop_id"], 'lat' => $row["stop_lat"], 'lng' => $row["stop_lon"]];
		array_push($newarr, $tmparr);
    }
} else {
    echo "0 results";
}
$conn->close();

echo json_encode($newarr);

?> 