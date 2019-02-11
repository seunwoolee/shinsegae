<?

class DrivingLog
{

	static function updateTotalDistance($db, $modifyDrivingLogSeq)
	{
		$db->que = "SELECT employeeNumber FROM drivingLog WHERE seq=". $modifyDrivingLogSeq;
		$db->query();
		if($db->affected_rows() > 0)
		{
			$employeeNumber = $db->getOne();
			$db->que = "SELECT * FROM drivingLog WHERE employeeNumber='". $employeeNumber. "' ORDER BY startDate DESC, stopDistance DESC, seq DESC LIMIT 1";
			$db->query();

			$row = $db->getRow();
			if($row["seq"] == $modifyDrivingLogSeq)
			{
				$DATA["totalDistance"] = $row["stopDistance"];
				$db->Update("user", $DATA, "WHERE employeeNumber='". $employeeNumber. "'", "user update error");
			}
		}
	}


	static function getPurposeName($purpose)
	{
		if($purpose == "a")
		{
			return "일반업무";
		}
		else if($purpose == "e")
		{
			return "출·퇴근";
		}
		else if($purpose == "g")
		{
			return "비업무용";
		}
		else
		{
			return "미입력";
		}
	}

	static function setDefaultPurpose($db, $companySeq)
	{
		$db->que = "SELECT * FROM purpose";
		$db->query();
		if($db->affected_rows() < 1)
		{
			$db->que = "insert into purpose (purposeName, purposeType, purposeCode, purposeDefault, purposeState, sort) ";
			$db->que .= " values ('일반업무', 'a', 'a', 'Y', 'Y', 1), ('출·퇴근', 'e', 'e', 'Y', 'Y', 2), ('비업무용', 'g', 'g', 'Y', 'Y', 3) ";
			$db->query();
		}
	}


	static function getOilTypeName($type)
	{

		if($type == "Gasoline_Up_1800" ){
			return "휘발유 1,800 이상";
		}
		else if($type == "Gasoline_Down_1800" ){
			return "휘발유 1,800 미만";
		}
		else if($type == "Diesel") 
		{
			return "경유";
		} 
		else if($type == "Gas") 
		{
			return "LPI(LPG)";
		} 
		else if($type == "Hybbrid_Gasoline") 
		{
			return "하이브리드_휘발유";
		}
		else if($type == "Hybbrid_LPI") 
		{
			return "하이브리드_LPI";
		}
		else
		{
			return "";
		}
	}

	function getDistanceBySpeed($locations)
	{
		$distanceArr["distance_00kmh"] = 0;
		$distanceArr["distance_10kmh"] = 0;
		$distanceArr["distance_20kmh"] = 0;
		$distanceArr["distance_30kmh"] = 0;
		$distanceArr["distance_40kmh"] = 0;
		$distanceArr["distance_50kmh"] = 0;
		$distanceArr["distance_60kmh"] = 0;
		$distanceArr["distance_70kmh"] = 0;
		$distanceArr["distance_80kmh"] = 0;
		$distanceArr["distance_90kmh"] = 0;
		$distanceArr["distance_100kmh"] = 0;

		$count = count($locations);
		if($count > 1)
		{
			for($i=1; $i<$count; $i++)
			{
				$lat1		= $locations[$i-1]["latitude"];
				$lng1		= $locations[$i-1]["longitude"];
				$time1	= $locations[$i-1]["time"];
				$lat2		= $locations[$i]["latitude"];
				$lng2		= $locations[$i]["longitude"];
				$time2	= $locations[$i]["time"];

				$distance = $this->getDistance($lat1, $lng1, $lat2, $lng2);
				$speed = $this->getSpeed($distance, $time1, $time2);

				if($speed < 10) {
					$distanceArr["distance_00kmh"] += $distance;
				} else if($speed < 20) {
					$distanceArr["distance_10kmh"] += $distance;
				} else if($speed < 30) {
					$distanceArr["distance_20kmh"] += $distance;
				} else if($speed < 40) {
					$distanceArr["distance_30kmh"] += $distance;
				} else if($speed < 50) {
					$distanceArr["distance_40kmh"] += $distance;
				} else if($speed < 60) {
					$distanceArr["distance_50kmh"] += $distance;
				} else if($speed < 70) {
					$distanceArr["distance_60kmh"] += $distance;
				} else if($speed < 80) {
					$distanceArr["distance_70kmh"] += $distance;
				} else if($speed < 90) {
					$distanceArr["distance_80kmh"] += $distance;
				} else if($speed < 100) {
					$distanceArr["distance_90kmh"] += $distance;
				} else {
					$distanceArr["distance_100kmh"] += $distance;
				} 
			}
		}

		return $distanceArr;
	}



	function getMaxSpeed($locations)
	{
		$count = count($locations);
		$maxSpeed = 0;
		if($count > 1)
		{
			for($i=1; $i<$count; $i++)
			{
				
				$lat1		= $locations[$i-1]["latitude"];
				$lng1		= $locations[$i-1]["longitude"];
				$time1	= $locations[$i-1]["time"];
				$lat		= $locations[$i]["latitude"];
				$lng		= $locations[$i]["longitude"];
				$time	= $locations[$i]["time"];

				$distance = $this->getDistance($lat1, $lng1, $lat, $lng);
				$speed = $this->getSpeed($distance, $time1, $time);


				if($i > 1)
				{
					//1개 구간만 측정시 속도가 들죽날죽함
					//2개 구간 속도를 측정해서 최고속도 구함
					$lat2		= $locations[$i-2]["latitude"];
					$lng2		= $locations[$i-2]["longitude"];
					$time2	= $locations[$i-2]["time"];
					$distance += $this->getDistance($lat2, $lng2, $lat1, $lng1);
					$speed = $this->getSpeed($distance, $time2, $time);
				}


				if($maxSpeed < $speed)
				{
					$maxSpeed = $speed;
				}
			}
		}
	
		return (int) $maxSpeed;
	}


	function getAverageSpeed($locations)
	{
		$count = count($locations);
		if($count > 1)
		{
			for($i=1; $i<$count; $i++)
			{
				$lat1		= $locations[$i-1]["latitude"];
				$lng1		= $locations[$i-1]["longitude"];
				$time1	= $locations[$i-1]["time"];
				$lat2		= $locations[$i]["latitude"];
				$lng2		= $locations[$i]["longitude"];
				$time2	= $locations[$i]["time"];

				$distance += $this->getDistance($lat1, $lng1, $lat2, $lng2);
			}
		}

		$drivingTime = $locations[$count-1]["time"] - $locations[0]["time"];
		$hour = ((int) ($drivingTime / 1000)) / 3600;
		if($distance > 0 && $hour > 0)
		{
			$speed = $distance / $hour;
			return (int) $speed;
		}
		else
		{
			return 0;
		}
	}


	function getDistance($lat1, $lng1, $lat2, $lng2)
	{
		$earth_radius = 6371;
		$dLat = deg2rad($lat2 - $lat1);
		$dLon = deg2rad($lng2 - $lng1);
		$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
		$c = 2 * asin(sqrt($a));
		$d = $earth_radius * $c;
		return $d;
	}

	function getSpeed($distance, $beginTime, $endTime)
	{
		$hour = ((int) (($endTime - $beginTime) / 1000)) / 3600;
		if($distance > 0 && $hour > 0)
		{
			$speed = $distance / $hour;
			return $speed;
		}
		else
		{
			return 0;
		}
	}
}
	