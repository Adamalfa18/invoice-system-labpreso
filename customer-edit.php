<?php


include('header.php');
include('functions.php');

$getID = $_GET['id'];

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
	die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// the query
$query = "SELECT * FROM store_customers WHERE id = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if ($result) {
	while ($row = mysqli_fetch_assoc($result)) {

		$customer_name = $row['name']; // customer name
		$customer_email = $row['email']; // customer email
		$customer_phone = $row['phone']; // customer phone number
	}
}

/* close connection */
$mysqli->close();

?>

<h1>Edit Customer</h1>
<hr>

<div id="response" class="alert alert-success" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<div class="message"></div>
</div>

<form method="post" id="update_customer">
	<input type="hidden" name="action" value="update_customer">
	<input type="hidden" name="id" value="<?php echo $getID; ?>">
	<div class="row">
		<div class="col-xs-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Editing Customer (<?php echo $getID; ?>)</h4>
					<div class="clear"></div>
				</div>
				<div class="panel-body form-group form-group-sm">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required" name="customer_name" id="customer_name" placeholder="Enter name" tabindex="1" value="<?php echo $customer_name; ?>">
							</div>
						</div>
						<div class="col-xs-6">
							<div class="input-group float-right margin-bottom">
								<span class="input-group-addon">@</span>
								<input type="email" class="form-control copy-input required" name="customer_email" id="customer_email" placeholder="E-mail address" aria-describedby="sizing-addon1" tabindex="2" value="<?php echo $customer_email; ?>">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_phone" id="invoice_phone" placeholder="Phone number" tabindex="8" value="<?php echo $customer_phone; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 margin-top btn-group">
			<input type="submit" id="action_update_customer" class="btn btn-success float-right" value="Update Customer" data-loading-text="Updating...">
		</div>
	</div>
</form>

<?php
include('footer.php');
?>