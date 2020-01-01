<?php

require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;

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
//		  if ($entity->getVehicle()->getTrip()->getRouteId() == "130-1426")
		  if ($entity->getVehicle()->getTrip()->getRouteId() == "GCN-876")
		  {
//			echo "route: " . $entity->getVehicle()->getTrip()->getRouteId();

			if ($entity->getVehicle()->hasPosition())
			{
				$tmparr = ['lat' => $entity->getVehicle()->getPosition()->getLatitude(), 'lng' => $entity->getVehicle()->getPosition()->getLongitude()];
				array_push($newarr, $tmparr);

		//		echo " lat: " . $entity->getVehicle()->getPosition()->getLatitude();
			//	echo " lng: " . $entity->getVehicle()->getPosition()->getLongitude();
			}
		  }
	  }
	  
	  
	  /*
	  if ($entity->getVehicle()->hasVehicle())
	  {
		echo "trip: " . $entity->getVehicle()->getVehicle()->getId();
	  }
	  */
  }
}

echo json_encode($newarr);

?>