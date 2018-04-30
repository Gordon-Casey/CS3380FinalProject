<?php
	require ('web_utils.php');

	$message = '';
	
	$target = $_GET['target'];
	$action = $_POST['action'];
	
	switch($action) {
		case 'addDevice':
			$message = addDevice();
			break;
		case 'addUser':
			$message = addUser();
			break;
		case 'deleteDevice':
			$message = deleteDevice();
			break;
		case 'updateDevice':
			$message = updateDevice();
			break;
		case 'updateDeviceCode':
			$message = updateDeviceCode();
			break;
		case 'searchDevices':
			$message = searchDevices();
			break;

	}
	
	switch($target) {
		case 'addDevice':
			presentAddDevice();
			break;
		case 'updateDevice':
			updateDevice();
			break;
		case 'searchDevices':
			searchDevices();
			break;
		case 'blah':
			break;
		default:
			getInventory($message);
	}

	function getInventory($message = ""){

		$stylesheet = 'inventory.css';

		$devices = array();

		// Create Connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		if($mysqli->connect_error){

			$message = $mysqli->connect_error;

		} else {

			// This string is the SQL statement to be executed

			$sql = "SELECT Devices.Brand, Devices.Model, Devices.Type, (SELECT Users.FirstName FROM Users WHERE Devices.Owner = Users.Id) as UserFirstName, (SELECT Users.LastName FROM Users WHERE Devices.Owner = Users.Id) as UserLastName, (SELECT Users.Pawprint FROM Users WHERE Devices.Owner = Users.Id) as UserPawprint, (SELECT Users.OfficeNumber FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeNumber, (SELECT Users.OfficeLetter FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeLetter, Devices.SerialNumber, Devices.DepartmentOwner, Devices.MoCodePurchasedBy, Devices.ID  FROM Devices";

			// Preforms the SQL query and checks to see if there was an error.
			if ($result = $mysqli->query($sql)) {
				if ($result->num_rows > 0) {
					// If no error, then turns the dada into an associative array
					while($row = $result->fetch_assoc()) {
						array_push($devices, $row);
					}
				}
				$result->close();
			} else {
				// If there was an error from the SQL statement
				$message = $mysqli->error;
			}

			$mysqli->close();
		}

		print generatePageHTML("Devices", generateInventoryHTML($devices, $message), $stylesheet);

	}

	function generateInventoryHTML($devices, $message){
		$html = "<h1>Devices</h1>\n";

		// This appends any sort of notification messages onto the screen
		if($message){
			$html .= "<p class='message'>$message</p>\n";
		}
		//$html .= "<form>Search:<input type='text' name='searchString'></form>";


		$html .= "<form action='index.php' method='post'><input type='hidden' name='action' value='searchDevices'/><input type='text' name='searchString' value=''placeholder='Search here..' ><input type='submit' value='searchDevices'></form>";
		
		$html .= "<p><a class='deviceButton' href='index.php?target=addDevice'>+ Add Device</a></p>\n"; 

		// <a class='userButton' href=index.php?target=addUser'>+ Add User</a>

		//"<form action='index.php' method='post'><input type='hidden' name='action' value='updateDevice'/><input type = 'hidden' name='ID' value='$id' /><input type='submit' value='Update'></form>"

	
		if (count($devices) < 1) {
			$html .= "<p>No devices to display!</p>\n";
			return $html;
		}
	
		$html .= "<table>\n";
		$html .= "<tr><th>Brand</th><th>Type</th><th>Model</th><th>User</th><th>Pawprint</th><th>Location</th><th>Serial Number</th><th>ID</th><th>Department Owner</th><th>MoCode</th><th>Update Device</th><th>Delete Device</th></tr>\n";
	
		foreach ($devices as $device) {
			$brand = $device['Brand'];
			$model = $device['Model'];
			$type = $device['Type'];
			$name = $device['UserFirstName'] . " " . $device['UserLastName'];
			$pawprint = $device['UserPawprint'];
			$location = $device['UserOfficeNumber'] . $device['UserOfficeLetter'];
			$serialNumber = $device['SerialNumber'];
			$id = $device['ID'];
			$department = $device['DepartmentOwner'];
			$mocode = $device['MoCodePurchasedBy'];
			
			$html .= "<tr><td>$brand</td><td>$type</td><td>$model</td><td>$name</td><td>$pawprint</td><td>$location</td><td>$serialNumber</td><td>$id</td><td>$department</td><td>$mocode</td><td><form action='index.php' method='post'><input type='hidden' name='action' value='updateDevice'/><input type = 'hidden' name='ID' value='$id' /><input type='submit' value='Update'></form></td><td><form action='index.php' method='post'><input type='hidden' name='action' value='deleteDevice'/><input type = 'hidden' name='ID' value='$id' /><input type='submit' value='Delete'></form></td></tr>\n";
		}
		
		$html .= "</table>\n";
	
		return $html;
	}

	function searchDevices(){
		print("heyo");
		$searchString = $_GET['searchString'];
		print($searchString);

		return $html;
	}

	function deleteDevice(){
		$id = $_POST['ID'];

		$message = "";

		if(!$id){
			$message = "No device was found to delete";
		} else {
			require('db_credentials.php');
			$mysqli = new mysqli($servername, $username, $password, $dbname);
			if ($mysqli->connect_error) {
				$message = $mysqli->connect_error;
			} else {
				$id = $mysqli->real_escape_string($id);
				$sql = "DELETE FROM Devices WHERE ID = $id";
				if ( $result = $mysqli->query($sql) ) {
					$message = "Device was deleted.";
				} else {
					$message = $mysqli->error;
				}
				$mysqli->close();
			}	
		}

		return $message;

	}

	function presentAddDevice(){
		$html = <<<EOT
			<!DOCTYPE html>
			<html>
			<head>
			<title>Gordon's Inventory</title>
			<link rel="stylesheet" type="text/css" href="inventory.css">
			</head>
			<body>
			<h1>Add a Device</h1>
			<form action="index.php" method="post">

			<form action="index.php" method="post"><input type="hidden" name="action" value="addDevice" />

			  <p>User<br />
			  <input type="text" name="Owner" value="" placeholder="User's Pawprint" maxlength="255" size="30"></p>

			  <p>Serial Number<br />
			  <input type="text" name="SerialNumber" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Brand<br />
			  <input type="text" name="Brand" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Type<br />
			  <input type="text" name="Type" value="" placeholder="Desktop, Monitor, Printer, etc." maxlength="255" size="30"></p>

			  <p>Model<br />
			  <input type="text" name="Model" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Department<br />
			  <input type="text" name="DepartmentOwner" value="" placeholder="Department owner of this device" maxlength="255" size="30"></p>

			  <p>MoCode<br />
			  <input type="text" name="MoCodePurchasedBy" value="XXXX" placeholder="MoCode used to purchase this device" maxlength="255" size="35"></p>

			  <input type="submit" value="Submit">
			</form>
			</body>
			</html>

EOT;

		print $html;

	}

	function addDevice(){
		$message = "";

		$serialNumber = $_POST['SerialNumber'];
		$brand = $_POST['Brand'];
		$model = $_POST['Model'];
		$owner = $_POST['Owner'];
		$departmentOwner = $_POST['DepartmentOwner'];
		$moCode = $_POST['MoCodePurchasedBy'];
		$type = $_POST['Type'];

		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($mysqli->connect_error) {
			$message = $mysqli->connect_error;
		} else {
			$serialNumber = $mysqli->real_escape_string($serialNumber);
			$brand = $mysqli->real_escape_string($brand);
			$model = $mysqli->real_escape_string($model);
			$owner = $mysqli->real_escape_string($owner);
			$departmentOwner = $mysqli->real_escape_string($departmentOwner);
			$moCode = $mysqli->real_escape_string($moCode);
			$type = $mysqli->real_escape_string($type);

			$sql = "SELECT ID FROM Users WHERE Pawprint = '$owner'";

			if ($result = $mysqli->query($sql)) {
				$thing = $result->fetch_assoc();
				$holder = $thing['ID'];

				$sql = "INSERT INTO Devices (SerialNumber, Brand, Model, Owner, DepartmentOwner, MoCodePurchasedBy, Type) VALUES ('$serialNumber', '$brand', '$model', '$holder', '$departmentOwner', '$moCode', '$type')";
				if ($result = $mysqli->query($sql)) {
					$message = "Device was added";
					$mysqli->close();
				} else {
					$message = $mysqli->error;
					$mysqli->close();
				}

			} else {
				$sql = "INSERT INTO Devices (SerialNumber, Brand, Model, Owner, DepartmentOwner, MoCodePurchasedBy, Type) VALUES ('$serialNumber', '$brand', '$model', '', '$departmentOwner', '$moCode', '$type')";
				if ($result = $mysqli->query($sql)) {
					$message = "Device was added";
					$mysqli->close();
				} else {
					$message = $mysqli->error;
					$mysqli->close();
				}
			
			}		
		return $message;
		}
	}



	function updateDevice(){	
		$message = "";

		$id = $_POST['ID'];

		if (!$id) {
			$message = "No device was specified to update.";
		} else {
			// Create connection
			require('db_credentials.php');
			$mysqli = new mysqli($servername, $username, $password, $dbname);
			// Check connection
			if ($mysqli->connect_error) {
				$message = $mysqli->connect_error;
			} else {
				$id = $mysqli->real_escape_string($id);

				$sql = "SELECT Devices.Brand, Devices.Model, Devices.Type, (SELECT Users.FirstName FROM Users WHERE Devices.Owner = Users.Id) as UserFirstName, (SELECT Users.LastName FROM Users WHERE Devices.Owner = Users.Id) as UserLastName, (SELECT Users.Pawprint FROM Users WHERE Devices.Owner = Users.Id) as UserPawprint, (SELECT Users.OfficeNumber FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeNumber, (SELECT Users.OfficeLetter FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeLetter, Devices.SerialNumber, Devices.DepartmentOwner, Devices.MoCodePurchasedBy, Devices.ID  FROM Devices WHERE Devices.ID = $id";

				if ( $result = $mysqli->query($sql) ) {
					$device = $result->fetch_assoc();

					$brand = $device['Brand'];
					$model = $device['Model'];
					$type = $device['Type'];
					$name = $device['UserFirstName'] . " " . $device['UserLastName'];
					$pawprint = $device['UserPawprint'];
					$location = $device['UserOfficeNumber'] . $device['UserOfficeLetter'];
					$serialNumber = $device['SerialNumber'];
					$id = $device['ID'];
					$department = $device['DepartmentOwner'];
					$mocode = $device['MoCodePurchasedBy'];

$html = <<<EOT
			<!DOCTYPE html>
			<html>
			<head>
			<title>Gordon's Inventory</title>
			<link rel="stylesheet" type="text/css" href="inventory.css">
			</head>
			<body>
			<h1>Edit Device</h1>
			

			<form action='index.php' method='post'><input type='hidden' name='action' value='updateDeviceCode' />

			<form action='index.php' method='post'><input type = 'hidden' name='ID' value='$id' />

			  <p>User<br />
			  <input type="text" name="User" value="$pawprint" placeholder="$pawprint" maxlength="255" size="30"></p>

			  <p>Serial Number<br />
			  <input type="text" name="SerialNumber" value="$serialNumber" placeholder="$serialNumber" maxlength="255" size="30"></p>

			  <p>Brand<br />
			  <input type="text" name="Brand" value="$brand" placeholder="$Brand" maxlength="255" size="30"></p>

			  <p>Type<br />
			  <input type="text" name="Type" value="$type" placeholder="$type" maxlength="255" size="30"></p>

			  <p>Model<br />
			  <input type="text" name="Model" value="$model" placeholder="$model" maxlength="255" size="30"></p>

			  <p>Department<br />
			  <input type="text" name="DepartmentOwner" value="$department" placeholder="$department" maxlength="255" size="30"></p>

			  <p>MoCode<br />
			  <input type="text" name="MoCodePurchasedBy" value="$mocode" placeholder="$mocode" maxlength="255" size="35"></p>

			  <input type="submit" value="Submit">
			</form>
			</body>
			</html>

EOT;
			$mysqli->close();
			print $html;

				} else {
					//$message = $mysqli->error;
					$mysqli->close();
				}
			}
		}

		$target = null;
	}

	function updateDeviceCode(){
		$message = "";

		$id = $_POST['ID'];
		$serialNumber = $_POST['SerialNumber'];
		$brand = $_POST['Brand'];
		$model = $_POST['Model'];
		$owner = $_POST['User'];
		$departmentOwner = $_POST['DepartmentOwner'];
		$moCode = $_POST['MoCodePurchasedBy'];
		$type = $_POST['Type'];

		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		// Check connection
		if ($mysqli->connect_error) {
			$message = $mysqli->connect_error;
		} else {
			$serialNumber = $mysqli->real_escape_string($serialNumber);
			$brand = $mysqli->real_escape_string($brand);
			$model = $mysqli->real_escape_string($model);
			$owner = $mysqli->real_escape_string($owner);
			$departmentOwner = $mysqli->real_escape_string($departmentOwner);
			$moCode = $mysqli->real_escape_string($moCode);
			$type = $mysqli->real_escape_string($type);

			$sql = "SELECT ID FROM Users WHERE Pawprint = '$owner'";

			if ($result = $mysqli->query($sql)) {
				$thing = $result->fetch_assoc();
				$holder = $thing['ID'];

				$sql = "UPDATE Devices SET SerialNumber = '$serialNumber', Brand = '$brand', Model = '$model', Owner = '$holder', DepartmentOwner = '$departmentOwner', MoCodePurchasedBy = '$moCode', Type = '$type' WHERE Devices.ID = '$id'";

				if ($result = $mysqli->query($sql)) {
					$message = "Device was updated";
				} else {
					$message = $mysqli->error;
				}

			} else {
				$sql = "UPDATE Devices SET SerialNumber = '$serialNumber', Brand = '$brand', Model = '$model', Owner = '', DepartmentOwner = '$department', MoCodePurchasedBy = '$mocode', Type = '$type' WHERE Devices.ID = '$id'";
				if ($result = $mysqli->query($sql)) {
					$message = "Device was updated";
					$mysqli->close();
				} else {
					$message = $mysqli->error;
					$mysqli->close();
				}
			
			}
		}
	}

?>