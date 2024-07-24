<?php
	// config
	include "config.php";

	if (isset($_REQUEST) && $_REQUEST['action'] === 'toggleHomeTypeColumn') {
		$data = mysqli_real_escape_string($conn, $_REQUEST['data']);
		$updateValue = mysqli_real_escape_string($conn, $_REQUEST['updateValue']);
		if ($updateValue === 'true') {
			$updateValue = 1;
		} else {
			$updateValue = 0;
		}
		$explode = explode("@", $data);
		$id = $explode['0'];
		$column_name = $explode['1'];

		// check column is present in the table or not
		$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'productsnew' AND COLUMN_NAME = '$column_name'";
		$query = mysqli_query($conn, $sql);

		if ($query) {
			$isColumnRows = mysqli_num_rows($query);

			if ($isColumnRows > 0) {
				// update the column status
				$updateSQL = "UPDATE `productsnew` SET `$column_name` = '$updateValue' WHERE `id`='$id'";
				$updateQuery = mysqli_query($conn, $updateSQL);

				$rowsAffected = mysqli_affected_rows($conn);
			}
		}

		echo $rowsAffected;
	}
?>