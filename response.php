<?php


include_once('includes/config.php');

// show PHP errors
ini_set('display_errors', 1);

// output any connection error
if ($mysqli->connect_error) {
	die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$action = isset($_POST['action']) ? $_POST['action'] : "";

if ($action == 'email_invoice') {

	$fileId = $_POST['id'];
	$emailId = $_POST['email'];
	$invoice_type = $_POST['invoice_type'];
	$custom_email = $_POST['custom_email'];

	require_once('class.phpmailer.php');

	$mail = new PHPMailer(); // defaults to using php "mail()"

	$mail->AddReplyTo(EMAIL_FROM, EMAIL_NAME);
	$mail->SetFrom(EMAIL_FROM, EMAIL_NAME);
	$mail->AddAddress($emailId, "");

	$mail->Subject = EMAIL_SUBJECT;
	//$mail->AltBody = EMAIL_BODY; // optional, comment out and test
	if (empty($custom_email)) {
		if ($invoice_type == 'invoice') {
			$mail->MsgHTML(EMAIL_BODY_INVOICE);
		} else if ($invoice_type == 'quote') {
			$mail->MsgHTML(EMAIL_BODY_QUOTE);
		} else if ($invoice_type == 'receipt') {
			$mail->MsgHTML(EMAIL_BODY_RECEIPT);
		}
	} else {
		$mail->MsgHTML($custom_email);
	}

	$mail->AddAttachment("./invoices/" . $fileId . ".pdf"); // attachment

	if (!$mail->Send()) {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mail->ErrorInfo . '</pre>'
		));
	} else {
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Invoice has been successfully send to the customer'
		));
	}
}
// download invoice csv sheet
if ($action == 'download_csv') {

	header("Content-type: text/csv");

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$file_name = 'invoice-export-' . date('d-m-Y') . '.csv';   // file name
	$file_path = 'downloads/' . $file_name; // file path

	$file = fopen($file_path, "w"); // open a file in write mode
	chmod($file_path, 0777);    // set the file permission

	$query_table_columns_data = "SELECT * 
									FROM invoices i
									JOIN customers c
									ON c.invoice = i.invoice
									WHERE i.invoice = c.invoice
									ORDER BY i.invoice";

	if ($result_column_data = mysqli_query($mysqli, $query_table_columns_data)) {

		// fetch table fields data
		while ($column_data = $result_column_data->fetch_row()) {

			$table_column_data = array();
			foreach ($column_data as $data) {
				$table_column_data[] = $data;
			}

			// Format array as CSV and write to file pointer
			fputcsv($file, $table_column_data, ",", '"');
		}
	}

	//if saving success
	if ($result_column_data = mysqli_query($mysqli, $query_table_columns_data)) {
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'CSV has been generated and is available in the /downloads folder for future reference, you can download by <a href="downloads/' . $file_name . '">clicking here</a>.'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}


	// close file pointer
	fclose($file);

	$mysqli->close();
}

if ($action == 'create_customer') {

	header('Content-Type: application/json');

	$customer_name = mysqli_real_escape_string($mysqli, $_POST['customer_name']);
	$customer_email = mysqli_real_escape_string($mysqli, $_POST['customer_email']);
	$customer_phone = mysqli_real_escape_string($mysqli, $_POST['customer_phone']);

	$query = "INSERT INTO store_customers (name, email, phone) VALUES (?, ?, ?)";

	$stmt = $mysqli->prepare($query);

	if ($stmt === false) {
		echo json_encode([
			'status' => 'Error',
			'message' => 'Prepare failed: ' . $mysqli->error
		]);
		exit;
	}

	$stmt->bind_param('sss', $customer_name, $customer_email, $customer_phone);

	if ($stmt->execute()) {
		echo json_encode([
			'status' => 'Success',
			'message' => 'Customer has been created successfully!'
		]);
	} else {
		echo json_encode([
			'status' => 'Error',
			'message' => 'Execute failed: ' . $stmt->error
		]);
	}

	$mysqli->close();
}


// Create invoice
if ($action == 'create_invoice') {
	header('Content-Type: application/json');
	// Penagihan
	$customer_name = mysqli_real_escape_string($mysqli, $_POST['customer_name']);
	$customer_email = mysqli_real_escape_string($mysqli, $_POST['customer_email']);
	$customer_phone = mysqli_real_escape_string($mysqli, $_POST['customer_phone']);

	// Detail faktur
	$invoice_number = mysqli_real_escape_string($mysqli, $_POST['invoice_id']);
	// $custom_email = mysqli_real_escape_string($mysqli, $_POST['custom_email']);
	$invoice_date = mysqli_real_escape_string($mysqli, $_POST['invoice_date']);
	$invoice_subtotal = floatval($_POST['invoice_subtotal']);
	$invoice_discount = floatval($_POST['invoice_discount']);
	$invoice_total = floatval($_POST['invoice_total']);
	$id_pegawai = mysqli_real_escape_string($mysqli, $_POST['id_pegawai']);
	$invoice_status = mysqli_real_escape_string($mysqli, $_POST['invoice_status']);

	// Gunakan prepared statement untuk menghindari SQL injection
	$query = $mysqli->prepare("INSERT INTO invoices (invoice, customer_email, invoice_date, subtotal, discount, total, id_pegawai, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
	$query->bind_param("sssdddis", $invoice_number, $customer_email, $invoice_date, $invoice_subtotal, $invoice_discount, $invoice_total, $id_pegawai, $invoice_status);

	if ($query->execute()) {
		$customer_query = $mysqli->prepare("INSERT INTO customers (invoice, name, email, phone) VALUES (?, ?, ?, ?)");
		$customer_query->bind_param("ssss", $invoice_number, $customer_name, $customer_email, $customer_phone);
		$customer_query->execute();

		// Item produk faktur
		$item_query = $mysqli->prepare("INSERT INTO invoice_items (invoice, product, qty, price, discount, subtotal) VALUES (?, ?, ?, ?, ?, ?)");

		foreach ($_POST['invoice_product'] as $key => $value) {
			$item_product = mysqli_real_escape_string($mysqli, $value);
			$item_qty = intval($_POST['invoice_product_qty'][$key]);
			$item_price = floatval($_POST['invoice_product_price'][$key]);
			$item_discount = floatval($_POST['invoice_product_discount'][$key]);
			$item_subtotal = floatval($_POST['invoice_product_sub'][$key]);

			$item_query->bind_param("ssiddd", $invoice_number, $item_product, $item_qty, $item_price, $item_discount, $item_subtotal);
			$item_query->execute();

			$update_stock_query = $mysqli->prepare("UPDATE products SET qty = qty - ? WHERE product_name = ?");
			$update_stock_query->bind_param("is", $item_qty, $item_product);
			$update_stock_query->execute();
		}

		// Buat PDF faktur
		// ... (kode untuk membuat PDF tetap sama)
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Faktur telah berhasil dibuat!'
		));
	} else {
		header('Content-Type: application/json');
		echo json_encode(array(
			'status' => 'Error',
			'message' => 'Terjadi kesalahan saat membuat faktur. Silakan coba lagi.'
		));
	}
	$mysqli->close();
}

// Update product
if ($action == 'update_product') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	// invoice product information
	$getID = $_POST['id']; // id
	$product_name = $_POST['product_name']; // product name
	$product_desc = $_POST['product_desc']; // product desc
	$product_price = $_POST['product_price']; // product price
	$product_qty = $_POST['product_qty']; // product quantity

	// the query
	$query = "UPDATE products SET
				product_name = ?,
				product_desc = ?,
				product_price = ?,
				qty = ?
			WHERE product_id = ?
			";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'ssssi', // Type parameter updated to include 'i' for qty (integer)
		$product_name,
		$product_desc,
		$product_price,
		$product_qty,  // bind product quantity
		$getID
	);

	//execute the query
	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Product has been updated successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}
	//close database connection
	$mysqli->close();
}


// Adding new product
if ($action == 'delete_invoice') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM invoices WHERE invoice = " . $id . ";";
	$query .= "DELETE FROM customers WHERE invoice = " . $id . ";";
	$query .= "DELETE FROM invoice_items WHERE invoice = " . $id . ";";


	if ($mysqli->multi_query($query)) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Product has been deleted successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	// close connection 
	$mysqli->close();
}

// Adding new product
if ($action == 'update_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$getID = $_POST['id']; // id

	// invoice customer information
	// billing
	$customer_name = $_POST['customer_name']; // customer name
	$customer_email = $_POST['customer_email']; // customer email
	$customer_phone = $_POST['customer_phone']; // customer phone number

	// the query
	$query = "UPDATE store_customers SET
            name = ?,
            email = ?,
            phone = ?
        WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param(
		'ssss',
		$customer_name,
		$customer_email,
		$customer_phone,
		$getID
	);

	//execute the query
	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Customer has been updated successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}
	//close database connection
	$mysqli->close();
}

if ($action == 'update_invoice') {
	if ($mysqli->connect_error) {
		die(json_encode(['status' => 'Error', 'message' => 'Connection error: ' . $mysqli->connect_error]));
	}

	$mysqli->begin_transaction(); // MULAI TRANSAKSI

	try {
		$id = $_POST["update_id"];
		$customer_name = $_POST['customer_name'];
		$customer_email = $_POST['customer_email'];
		$customer_phone = $_POST['customer_phone'];

		$invoice_number = $_POST['invoice_id'];
		$invoice_date = $_POST['invoice_date'];
		$invoice_subtotal = $_POST['invoice_subtotal'];
		$invoice_discount = $_POST['invoice_discount'];
		$invoice_total = $_POST['invoice_total'];
		$id_pegawai = $_POST['id_pegawai'];
		$invoice_status = $_POST['invoice_status'];

		// UPDATE invoices
		$stmt = $mysqli->prepare("
            UPDATE invoices 
            SET invoice_date = ?, subtotal = ?, discount = ?, total = ?, id_pegawai = ?, status = ?
            WHERE invoice = ?
        ");
		if (!$stmt) throw new Exception('Prepare update invoices error: ' . $mysqli->error);
		$stmt->bind_param("sdddiss", $invoice_date, $invoice_subtotal, $invoice_discount, $invoice_total, $id_pegawai, $invoice_status, $id);
		if (!$stmt->execute()) throw new Exception('Update invoices error: ' . $stmt->error);

		// UPDATE customers
		$stmt = $mysqli->prepare("
            UPDATE customers 
            SET name = ?, email = ?, phone = ?
            WHERE invoice = ?
        ");
		if (!$stmt) throw new Exception('Prepare update customers error: ' . $mysqli->error);
		$stmt->bind_param("ssss", $customer_name, $customer_email, $customer_phone, $id);
		if (!$stmt->execute()) throw new Exception('Update customers error: ' . $stmt->error);

		// HAPUS semua item lama di invoice_items
		$stmt = $mysqli->prepare("DELETE FROM invoice_items WHERE invoice = ?");
		if (!$stmt) throw new Exception('Prepare delete invoice_items error: ' . $mysqli->error);
		$stmt->bind_param('s', $id);
		if (!$stmt->execute()) throw new Exception('Delete invoice_items error: ' . $stmt->error);

		// INSERT ulang invoice_items
		foreach ($_POST['invoice_product'] as $key => $item_product) {
			$item_qty = $_POST['invoice_product_qty'][$key];
			$item_price = $_POST['invoice_product_price'][$key];
			$item_discount = $_POST['invoice_product_discount'][$key];
			$item_subtotal = $_POST['invoice_product_sub'][$key];

			$stmt = $mysqli->prepare("
                INSERT INTO invoice_items (invoice, product, qty, price, discount, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
			if (!$stmt) throw new Exception('Prepare insert invoice_items error: ' . $mysqli->error);
			$stmt->bind_param("ssiddd", $invoice_number, $item_product, $item_qty, $item_price, $item_discount, $item_subtotal);
			if (!$stmt->execute()) throw new Exception('Insert invoice_items error: ' . $stmt->error);
		}

		$mysqli->commit(); // SEMUA BERHASIL
		header('Content-Type: application/json');
		echo json_encode(['status' => 'Success', 'message' => 'Invoice updated successfully.']);
	} catch (Exception $e) {
		$mysqli->rollback(); // ERROR, batalkan semua perubahan
		header('Content-Type: application/json');
		echo json_encode(['status' => 'Error', 'message' => $e->getMessage()]);
	}

	$mysqli->close();
}


// Adding new product
if ($action == 'delete_product') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM products WHERE product_id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s', $id);

	//execute the query
	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Product has been deleted successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	// close connection 
	$mysqli->close();
}

// Login to system
if ($action == 'login') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	session_start();

	extract($_POST);

	$username = mysqli_real_escape_string($mysqli, $_POST['username']);
	$pass_encrypt = md5(mysqli_real_escape_string($mysqli, $_POST['password']));

	$query = "SELECT * FROM `users` WHERE username='$username' AND `password` = '$pass_encrypt'";

	$results = mysqli_query($mysqli, $query) or die(mysqli_error());
	$count = mysqli_num_rows($results);

	if ($count != "") {
		$row = $results->fetch_assoc();

		$_SESSION['login_username'] = $row['username'];

		// processing remember me option and setting cookie with long expiry date
		if (isset($_POST['remember'])) {
			session_set_cookie_params('604800'); //one week (value in seconds)
			session_regenerate_id(true);
		}

		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Login was a success! Transfering you to the system now, hold tight!'
		));
	} else {
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'Login incorrect, does not exist or simply a problem! Try again!'
		));
	}
}

// Adding new product
if ($action == 'add_product') {
	$product_name = $_POST['product_name'];
	$product_desc = $_POST['product_desc'];
	$product_price = $_POST['product_price'];
	$qty = $_POST['qty'];
	//our insert query query
	$query  = "INSERT INTO products
				(
					product_name,
					product_desc,
					product_price, qty
				)
				VALUES (
					?, 
                	?,
                	?, ?
                );";
	header('Content-Type: application/json');
	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}
	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('ssdi', $product_name, $product_desc, $product_price, $qty);
	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Product has been added successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}
	//close database connection
	$mysqli->close();
}

// Adding new user
if ($action == 'add_user') {

	$user_name = $_POST['name'];
	$user_username = $_POST['username'];
	$user_email = $_POST['email'];
	$user_phone = $_POST['phone'];
	$user_password = $_POST['password'];

	//our insert query query
	$query  = "INSERT INTO users
				(
					name,
					username,
					email,
					phone,
					password
				)
				VALUES (
					?,
					?, 
                	?,
                	?,
                	?
                );
              ";

	header('Content-Type: application/json');

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	$user_password = md5($user_password);
	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('sssss', $user_name, $user_username, $user_email, $user_phone, $user_password);

	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'User has been added successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	//close database connection
	$mysqli->close();
}

// Update product
if ($action == 'update_user') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	// user information
	$getID = $_POST['id']; // id
	$name = $_POST['name']; // name
	$username = $_POST['username']; // username
	$email = $_POST['email']; // email
	$phone = $_POST['phone']; // phone
	$password = $_POST['password']; // password

	if ($password == '') {
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?
				 WHERE id = ?
				";
	} else {
		// the query
		$query = "UPDATE users SET
					name = ?,
					username = ?,
					email = ?,
					phone = ?,
					password =?
				 WHERE id = ?
				";
	}

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	if ($password == '') {
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'sssss',
			$name,
			$username,
			$email,
			$phone,
			$getID
		);
	} else {
		$password = md5($password);
		/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
		$stmt->bind_param(
			'ssssss',
			$name,
			$username,
			$email,
			$phone,
			$password,
			$getID
		);
	}

	//execute the query
	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'User has been updated successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	//close database connection
	$mysqli->close();
}

// Delete User
if ($action == 'delete_user') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM users WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s', $id);

	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'User has been deleted successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	// close connection 
	$mysqli->close();
}

// Delete User
if ($action == 'delete_customer') {

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	$id = $_POST["delete"];

	// the query
	$query = "DELETE FROM store_customers WHERE id = ?";

	/* Prepare statement */
	$stmt = $mysqli->prepare($query);
	if ($stmt === false) {
		trigger_error('Wrong SQL: ' . $query . ' Error: ' . $mysqli->error, E_USER_ERROR);
	}

	/* Bind parameters. TYpes: s = string, i = integer, d = double,  b = blob */
	$stmt->bind_param('s', $id);

	if ($stmt->execute()) {
		//if saving success
		echo json_encode(array(
			'status' => 'Success',
			'message' => 'Customer has been deleted successfully!'
		));
	} else {
		//if unable to create new record
		echo json_encode(array(
			'status' => 'Error',
			//'message'=> 'There has been an error, please try again.'
			'message' => 'There has been an error, please try again.<pre>' . $mysqli->error . '</pre><pre>' . $query . '</pre>'
		));
	}

	// close connection 
	$mysqli->close();
}
