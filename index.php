<?php
	require ('web_utils.php');

	$message = '';
	
	$action = $_POST['action'];
	
	switch($action) {
		case 'presentAddDevice':
			presentAddDevice();
			break;
		case 'addDevice':
			$message = addDevice();
			getInventory($message);
			break;
		case 'presentAddUser':
			presentAddUser();
			break;
		case 'addUser':
			$message = addUser();
			getInventory($message);
			break;
		case 'deleteDevice':
			$message = deleteDevice();
			getInventory($message);
			break;
		case 'beginUpdateDevice':
			$message = beginUpdateDevice();
			break;
		case 'updateDevice':
			$message = updateDevice();
			getInventory($message);
			break;
		case 'searchDevices':
			$message = searchDevices();
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
					// If no error, then turns the data into an associative array
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
		$html = "<div class='header'><img src='logo.png' alt='logo'/><h1>Technology Services Inventory System</h1></div>\n";

		// This appends any sort of notification messages onto the screen
		if($message){
			$html .= "<p class='message'>$message</p>\n";
		}

		$html .= "<style>
					input[type=text] {
    				width: 130px;
    				box-sizing: border-box;
    				border: 2px solid #ccc;
    				border-radius: 4px;
    				font-size: 16px;
    				background-color: white;
    				background-repeat: no-repeat;
    				-webkit-transition: width 0.4s ease-in-out;
    				transition: width 0.4s ease-in-out;
					}
					input[type=text]:focus {
    				width: 75%;
					}
					</style>
                    <form action='index.php' method='post'><input type='hidden' name='action' value='searchDevices'/><input type='text' name='searchString' value=''placeholder='Search here..' ><input type='submit' value='Search Devices'></form>";

		$html .= "<form action='index.php' method='post'><input type='hidden' name='action' value='presentAddDevice'/><input type='submit' value='Add Device'></form>";

		$html .= "<form action='index.php' method='post'><input type='hidden' name='action' value='presentAddUser'/><input type='submit' value='Add User'></form>";

		// $html .= "<a class='userButton' href=index.php?target=addUser'>+ Add User</a></p>";
	
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
			
			$html .= "<tr><td>$brand</td><td>$type</td><td>$model</td><td>$name</td><td>$pawprint</td><td>$location</td><td>$serialNumber</td><td>$id</td><td>$department</td><td>$mocode</td><td><form action='index.php' method='post'><input type='hidden' name='action' value='beginUpdateDevice'/><input type = 'hidden' name='ID' value='$id' /><input type='submit' value='Update'></form></td><td><form action='index.php' method='post'><input type='hidden' name='action' value='deleteDevice'/><input type = 'hidden' name='ID' value='$id' /><input type='submit' value='Delete'></form></td></tr>\n";
		}
		
		$html .= "</table>\n";
	
		return $html;
	}

	function searchDevices(){
		$searchString = $_POST['searchString'];

		if(!$searchString){
			getInventory("No devices specified to search for");
			return;
		}

		$stylesheet = 'inventory.css';

		$devices = array();

		// Create Connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		if($mysqli->connect_error){

			$message = $mysqli->connect_error;

		} else {

			// This string is the SQL statement to be executed
			$sql = "SELECT Devices.Brand, Devices.Model, Devices.Type, (SELECT Users.FirstName FROM Users WHERE Devices.Owner = Users.Id) as UserFirstName, (SELECT Users.LastName FROM Users WHERE Devices.Owner = Users.Id) as UserLastName, (SELECT Users.Pawprint FROM Users WHERE Devices.Owner = Users.Id) as UserPawprint, (SELECT Users.OfficeNumber FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeNumber, (SELECT Users.OfficeLetter FROM Users WHERE Devices.Owner = Users.Id) as UserOfficeLetter, Devices.SerialNumber, Devices.DepartmentOwner, Devices.MoCodePurchasedBy, Devices.ID  FROM Devices WHERE Devices.Brand LIKE '%$searchString%' || Devices.Model LIKE '%$searchString%' || Devices.Type LIKE '%$searchString%' || (SELECT Users.FirstName FROM Users WHERE Devices.Owner = Users.Id) LIKE '%$searchString%' || (SELECT Users.LastName FROM Users WHERE Devices.Owner = Users.Id) LIKE '%$searchString%' || (SELECT Users.Pawprint FROM Users WHERE Devices.Owner = Users.Id) LIKE '%$searchString%' || (SELECT Users.OfficeNumber FROM Users WHERE Devices.Owner = Users.Id) LIKE '%$searchString%' || (SELECT Users.OfficeLetter FROM Users WHERE Devices.Owner = Users.Id) LIKE '%$searchString%' || Devices.SerialNumber LIKE '%$searchString%' || Devices.DepartmentOwner LIKE '%$searchString%' || Devices.MoCodePurchasedBy LIKE '%$searchString%'";

			// Preforms the SQL query and checks to see if there was an error.
			if ($result = $mysqli->query($sql)) {
				if ($result->num_rows > 0) {
					// If no error, then turns the data into an associative array
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

	function presentAddUser(){
		$html = <<<EOT
			<!DOCTYPE html>
			<html>
			<head>
			<title>Gordon's Inventory</title>
			<link rel="stylesheet" type="text/css" href="inventory.css">
			</head>
			<body>
			<h1>Add a User</h1>
			<form action="index.php" method="post">

			<form action="index.php" method="post"><input type="hidden" name="action" value="addUser" />

			  <p>First Name<br />
			  <input type="text" name="FirstName" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Last Name<br />
			  <input type="text" name="LastName" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Pawprint<br />
			  <input type="text" name="Pawprint" value="" placeholder="" maxlength="255" size="30"></p>

			  <p>Office Number<br />
			  <input type="text" name="Number" value="" placeholder="Eg. 308" maxlength="255" size="30"></p>

			  <p>Office Letter<br />
			  <input type="text" name="Letter" value="" placeholder="Eg. D - Leave empty if no office letter" maxlength="255" size="30"></p>

			  <input type="submit" value="Submit">
			</form>
			</body>
			</html>
EOT;
		print($html);
	}

	function addUser(){
		$firstName = $_POST['FirstName'];
		$lastName = $_POST['LastName'];
		$pawprint = $_POST['Pawprint'];
		$number = $_POST['Number'];
		$letter = $_POST['Letter'];
		
		// Create connection
		require('db_credentials.php');
		$mysqli = new mysqli($servername, $username, $password, $dbname);
		if ($mysqli->connect_error) {
				$message = $mysqli->connect_error;
				print("here2");
		} else {
			$id = $mysqli->real_escape_string($id);
			$sql = "DELETE FROM Devices WHERE ID = $id";
			if ( $result = $mysqli->query($sql) ) {
				$message = "Device was deleted.";
			} else {
				$firstName = $mysqli->real_escape_string($firstName);
				$lastName = $mysqli->real_escape_string($lastName);
				$pawprint = $mysqli->real_escape_string($pawprint);
				$number = $mysqli->real_escape_string($number);
				$letter = $mysqli->real_escape_string($letter);
				$sql = "INSERT INTO Users (FirstName, LastName, Pawprint, OfficeNumber, OfficeLetter) VALUES ('$firstName', '$lastName', '$pawprint', '$number', '$letter')";
				if ( $result = $mysqli->query($sql) ) {
					$message = "User added";
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



	function beginUpdateDevice(){	
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
			

			<form action='index.php' method='post'><input type='hidden' name='action' value='updateDevice' />

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
	}

	function updateDevice(){
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
					$mysqli->close();
					return("Device was updated");
				} else {
					$mysqli->close();
					return($mysqli->error);
				}

			} else {
				$sql = "UPDATE Devices SET SerialNumber = '$serialNumber', Brand = '$brand', Model = '$model', Owner = '', DepartmentOwner = '$department', MoCodePurchasedBy = '$mocode', Type = '$type' WHERE Devices.ID = '$id'";
				if ($result = $mysqli->query($sql)) {
					$mysqli->close();
					return("Device was updated");
				} else {
					$mysqli->close();
					return($mysqli->error);
				}
			}
		}
	}

?>