 <?php
$servername = "localhost";
$username = "id12111839_sean";
$password = "";
$dbname = "id12111839_bus";

//echo $_POST['serviceShortName'];
$services = json_decode($_POST["myServices"]);

/*
foreach($services as $service)
{
	echo $service->short_name;
	echo $service->period;
	echo $service->direction;
}
*/

$myobj = new stdClass();
$servicearr = array();



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";



foreach($services as $service)
{
	$serviceobj = new stdClass();


//	$sql = "SELECT routes.route_id FROM routes, main_trips WHERE routes.route_id = main_trips.route_id and routes.route_short_name = '" . $_POST['serviceShortName'] . "' and main_trips.direction = '" . $_POST['serviceDirection'] . "' and main_trips.trip_id like '%" . $_POST['servicePeriod'] . "%'";
	$sql = "SELECT routes.route_id FROM routes, main_trips WHERE routes.route_id = main_trips.route_id and routes.route_short_name = '" . $service->short_name . "' and main_trips.direction = '" . $service->direction . "' and main_trips.trip_id like '%" . $service->period . "%'";
	$result = $conn->query($sql);

	if (!$result) {
		trigger_error('Invalid query: ' . $conn->error);
	}



	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
	//        echo 'routeid' => $row["route_id"];
	//        echo trim($row["route_id"]);
			
	//		array_push($newarr, 'main_id' => $row["route_id"]);
			$serviceobj->main_id = $row["route_id"];
			

			$sql = "SELECT route_id FROM routes WHERE route_short_name = '" . $service->short_name . "'";
			$result = $conn->query($sql);

			if (!$result) {
				trigger_error('Invalid query: ' . $conn->error);
			}


			if ($result->num_rows > 0) {
				
				$newarr = array();
				
				// output data of each row
				while($row = $result->fetch_assoc()) {
			
	//				$tmparr = array();
		//			$tmparr = ['id' => $row["route_id"]];
			//		array_push($newarr, $tmparr);
					array_push($newarr, $row["route_id"]);
				}
				
				$serviceobj->all_id = $newarr;

			}
			
		}
	}
	
	array_push($servicearr, $serviceobj);

}

$conn->close();

$myobj->services = $servicearr;
//echo json_encode($newarr);
echo json_encode($myobj);


?> 