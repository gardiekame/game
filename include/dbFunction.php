<?php
function connectDb($host, $user, $pwd, $dbName) {
	$conn = new mysqli($host, $user, $pwd, $dbName);

	if($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	
	echo 'MySQL Server connected<br>';	
	$conn->query("SET NAMES 'utf8'");
	
	return $conn;
}

function addGameItem($title, $type, $platform, $date, $img, $dbCon, $table) {
	$sql = "INSERT INTO $table (title, type, platform, release_date, img_url)
		VALUES ('$title' , '$type', '$platform', '$date', '$img')";

	if ($dbCon->query($sql) === TRUE) {
		echo "New record created successfully";
	}else {
		echo "Error: " .$sql ."<br>" .$dbCon->error;
	}
}

function updateGameItem($id, $title, $type, $platform, $date, $img, $dbCon, $table) {
	$sql = "UPDATE $table SET title='$title', type='$type', platform='$platform',
		release_date='$date', img_url='$img' WHERE g_id=$id";

	if ($dbCon->query($sql) === TRUE) {
		echo "New record updated successfully";
	}else {
		echo "Error: " .$sql ."<br>" .$dbCon->error;
	}
}

function updateGameItem2($id, $title, $type, $platform, $date, $dbCon, $table) {
	$sql = "UPDATE $table SET title='$title', type='$type', platform='$platform',
		release_date='$date' WHERE g_id=$id";

	if ($dbCon->query($sql) === TRUE) {
		echo "New record updated successfully";
	}else {
		echo "Error: " .$sql ."<br>" .$dbCon->error;
	}
}

function queryDb($sql, $dbCon) {
	$result = $dbCon->query($sql);
	
	if($result->num_rows > 0) {
		$i = 0;
		
		while($rs[$i] = $result->fetch_assoc()) {
			$i++;
		}
		
		array_pop($rs);
		
		return $rs;
	}
	else {
		echo $sql .": 查無資料";
	}
}

function counts($sql, $dbCon) {
	$result = $dbCon->query($sql)->fetch_row();
	
	return $result[0];
}
//$conn->close();
?>