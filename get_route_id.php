 <?php
$servername = "localhost";
$username = "id12111839_sean";
$password = "";
$dbname = "id12111839_bus";

//echo $_POST['serviceShortName'];



// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$sql = "SELECT routes.route_id FROM routes, main_trips WHERE routes.route_id = main_trips.route_id and routes.route_short_name = '" . $_POST['serviceShortName'] . "' and main_trips.direction = '" . $_POST['serviceDirection'] . "' and main_trips.trip_id like '%" . $_POST['servicePeriod'] . "%'";
$result = $conn->query($sql);

if (!$result) {
    trigger_error('Invalid query: ' . $conn->error);
}

$newarr = array();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
//        echo 'routeid' => $row["route_id"];
        echo trim($row["route_id"]);
    }
} else {
    echo "0 results";
}
$conn->close();

?> 