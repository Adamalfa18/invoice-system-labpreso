<?php


include_once("includes/config.php");

// Format Rupiah
function rupiah($angka)
{
	$sum = "Rp " . number_format($angka, 2, ',', '.');
	return $sum;
}

// get invoice list
function getInvoices()
{
	try {
		// Koneksi ke database menggunakan mysqli
		$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
		// Periksa koneksi
		if ($mysqli->connect_error) {
			throw new Exception('Kesalahan koneksi: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		// Query yang lebih efisien
		$query = "SELECT i.invoice, c.name, i.invoice_date, i.status, i.subtotal, c.email, i.customer_email, e.nama
		FROM invoices i
		JOIN customers c ON c.invoice = i.invoice
		LEFT JOIN pegawai e ON e.id_pegawai = i.id_pegawai
		ORDER BY i.invoice";
		// Gunakan prepared statement
		$stmt = $mysqli->prepare($query);

		if (!$stmt) {
			throw new Exception('Kesalahan dalam persiapan query: ' . $mysqli->error);
		}
		$stmt->execute();
		$results = $stmt->get_result();

		if ($results->num_rows > 0) {
			print '
			<input type="month" id="filter-month-year">
			<br><br>
			';
			print '<table class="table table-striped table-hover table-bordered" id="data-table" cellspacing="0"><thead>
			<tr>
				<th>Invoice</th>
				<th>Customer</th>
				<th>Date</th>
				<th>Total</th>
				<th>Status</th>
				<th>Actions</th>
			</tr></thead><tbody>';

			while ($row = $results->fetch_assoc()) {

				print '
				<tr>
					<td>' . htmlspecialchars($row["invoice"]) . '</td>
					<td>' . htmlspecialchars($row["name"]) . '</td>
				    <td>' . htmlspecialchars($row["invoice_date"]) . '</td>
					<td>Rp ' . number_format($row["subtotal"], 0, ',', '.') . '</td>
				';
				if ($row['status'] == "kasbon") {
					print '<td><span class="label label-primary">' . htmlspecialchars($row['status']) . '</span></td>';
				} elseif ($row['status'] == "transfer") {
					print '<td><span class="label label-success">' . htmlspecialchars($row['status']) . '</span></td>';
				}
				print '
				    <td>
				        <a href="invoice-edit.php?id=' . htmlspecialchars($row["invoice"]) . '" class="btn btn-primary btn-xs" title="Edit">
				            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
				        </a>
				        
				        <a href="#" 
							data-invoice-id="' . htmlspecialchars($row['invoice']) . '" 
							class="btn btn-danger btn-xs delete-invoice" 
							title="Hapus">
				            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
				        </a>
				    </td>
			    </tr>
			';
			}
			print '</tr></tbody></table>';
		} else {
			echo "<p>Tidak ada faktur untuk ditampilkan.</p>";
		}
		$stmt->close();
		$mysqli->close();
	} catch (Exception $e) {
		// Log error dan tampilkan pesan yang aman untuk pengguna
		error_log('Kesalahan dalam getInvoices: ' . $e->getMessage());
		echo "<p>Terjadi kesalahan saat memuat daftar faktur. Silakan coba lagi nanti.</p>";
	}
}

// Initial invoice number
function getInvoiceId()
{
	try {
		$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
		if ($mysqli->connect_error) {
			throw new Exception('Kesalahan koneksi: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$query = "SELECT MAX(CAST(invoice AS UNSIGNED)) as max_invoice FROM invoices";
		$stmt = $mysqli->prepare($query);
		if (!$stmt) {
			throw new Exception('Kesalahan dalam persiapan query: ' . $mysqli->error);
		}
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$maxInvoice = $row['max_invoice'] ?? 0;
		$nextInvoiceId = $maxInvoice + 1;
		$finalInvoiceId = max($nextInvoiceId, INVOICE_INITIAL_VALUE);
		error_log("Max Invoice: $maxInvoice, Next ID: $nextInvoiceId, Final ID: $finalInvoiceId");
		echo $finalInvoiceId;
		$stmt->close();
		$mysqli->close();
	} catch (Exception $e) {
		error_log('Kesalahan dalam getInvoiceId: ' . $e->getMessage());
		echo INVOICE_INITIAL_VALUE; // Fallback ke nilai awal jika terjadi kesalahan
	}
}


// populate product dropdown for invoice creation
function popProductsList()
{
	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	// the query
	$query = "SELECT * FROM products ORDER BY product_name ASC";
	// mysqli select query
	$results = $mysqli->query($query);
	if ($results) {
		echo '<select class="form-control item-select">';
		while ($row = $results->fetch_assoc()) {
			print '<option value="' . $row['product_price'] . '">' . $row["product_name"] . '</option>';
		}
		echo '</select>';
	} else {
		echo "<p>There are no products, please add a product.</p>";
	}
	// Frees the memory associated with a result
	$results->free();
	// close connection
	$mysqli->close();
}

// populate product dropdown for invoice creation
function popCustomersList()
{
	try {
		// Koneksi ke database menggunakan mysqli
		$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

		// Periksa koneksi
		if ($mysqli->connect_error) {
			throw new Exception('Kesalahan koneksi: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

		// Gunakan prepared statement untuk keamanan
		$query = "SELECT * FROM store_customers ORDER BY name ASC";
		$stmt = $mysqli->prepare($query);

		if (!$stmt) {
			throw new Exception('Kesalahan dalam persiapan query: ' . $mysqli->error);
		}

		$stmt->execute();
		$results = $stmt->get_result();

		if ($results->num_rows > 0) {
			echo '<table class="table table-striped table-hover table-bordered" id="data-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>';

			while ($row = $results->fetch_assoc()) {
				echo '<tr>
                    <td>' . htmlspecialchars($row["name"]) . '</td>
                    
                    <td><a href="#" class="btn btn-primary btn-xs customer-select" 
                        data-customer-name="' . htmlspecialchars($row['name']) . '"
                        data-customer-email="' . htmlspecialchars($row['email']) . '" 
                        data-customer-phone="' . htmlspecialchars($row['phone']) . '">Pilih</a></td>
                </tr>';
			}

			echo '</tbody></table>';
		} else {
			echo "<p>Tidak ada pelanggan untuk ditampilkan.</p>";
		}

		$stmt->close();
		$mysqli->close();
	} catch (Exception $e) {
		// Log error dan tampilkan pesan yang aman untuk pengguna
		error_log('Kesalahan dalam popCustomersList: ' . $e->getMessage());
		echo "<p>Terjadi kesalahan saat memuat daftar pelanggan. Silakan coba lagi nanti.</p>";
	}
}

// get products list
function getProducts()
{
	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	// the query
	$query = "SELECT * FROM products ORDER BY product_name ASC";
	// mysqli select query
	$results = $mysqli->query($query);

	if ($results) {

		print '<table class="table table-striped table-hover table-bordered" id="data-table">
	<thead>
		<tr>

			<th>Product</th>
			<th>Description</th>
			<th>Price</th>
			<th>Kuantity</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>';
		while ($row = $results->fetch_assoc()) {
			print '
		<tr>
			<td>' . $row["product_name"] . '</td>
			<td>' . $row["product_desc"] . '</td>
			<td>Rp. ' . $row["product_price"] . '</td>
			<td>' . $row["qty"] . '</td>
			<td><a href="product-edit.php?id=' . $row["product_id"] . '" class="btn btn-primary btn-xs"><span
						class="glyphicon glyphicon-edit" aria-hidden="true"></span></a> <a
					data-product-id="' . $row['product_id'] . '" class="btn btn-danger btn-xs delete-product"><span
						class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
		</tr>
		';
		}
		print '</tr>
	</tbody>
</table>';
	} else {
		echo "<p>There are no products to display.</p>";
	}
	// Frees the memory associated with a result
	$results->free();
	// close connection
	$mysqli->close();
}

// get user list
function getUsers()
{

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM users ORDER BY username ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if ($results) {

		print '<table class="table table-striped table-hover table-bordered" id="data-table">
	<thead>
		<tr>

			<th>Name</th>
			<th>Username</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Action</th>

		</tr>
	</thead>
	<tbody>';

		while ($row = $results->fetch_assoc()) {

			print '
		<tr>
			<td>' . $row['name'] . '</td>
			<td>' . $row["username"] . '</td>
			<td>' . $row["email"] . '</td>
			<td>' . $row["phone"] . '</td>
			<td><a href="user-edit.php?id=' . $row["id"] . '" class="btn btn-primary btn-xs"><span
						class="glyphicon glyphicon-edit" aria-hidden="true"></span></a> <a data-user-id="' . $row['id'] . '"
					class="btn btn-danger btn-xs delete-user"><span class="glyphicon glyphicon-trash"
						aria-hidden="true"></span></a></td>
		</tr>
		';
		}

		print '</tr>
	</tbody>
</table>';
	} else {

		echo "<p>There are no users to display.</p>";
	}

	// Frees the memory associated with a result
	$results->free();

	// close connection
	$mysqli->close();
}

// get user list
function getCustomers()
{

	// Connect to the database
	$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

	// output any connection error
	if ($mysqli->connect_error) {
		die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}

	// the query
	$query = "SELECT * FROM store_customers ORDER BY name ASC";

	// mysqli select query
	$results = $mysqli->query($query);

	if ($results) {

		print '<table class="table table-striped table-hover table-bordered" id="data-table">
	<thead>
		<tr>

			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th>Action</th>

		</tr>
	</thead>
	<tbody>';

		while ($row = $results->fetch_assoc()) {

			print '
		<tr>
			<td>' . $row["name"] . '</td>
			<td>' . $row["email"] . '</td>
			<td>' . $row["phone"] . '</td>
			<td><a href="customer-edit.php?id=' . $row["id"] . '" class="btn btn-primary btn-xs"><span
						class="glyphicon glyphicon-edit" aria-hidden="true"></span></a> <a
					data-customer-id="' . $row['id'] . '" class="btn btn-danger btn-xs delete-customer"><span
						class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>
		</tr>
		';
		}

		print '</tr>
	</tbody>
</table>';
	} else {

		echo "<p>There are no customers to display.</p>";
	}



	// Frees the memory associated with a result
	$results->free();

	// close connection
	$mysqli->close();
}

function getInvoiceRecap($monthFilter = null)
{
	try {
		$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
		if ($mysqli->connect_error) {
			throw new Exception('Connection error: ' . $mysqli->connect_error);
		}

		// Base query
		$query = "
			SELECT 
				c.name,
				summary.customer_email,
				summary.total_invoice,
				summary.bulan_format,
				summary.bulan_filter
			FROM (
				SELECT 
					i.customer_email,
					SUM(i.subtotal) AS total_invoice, 
					DATE_FORMAT(STR_TO_DATE(i.invoice_date, '%d/%m/%Y'), '%M %Y') AS bulan_format,
					DATE_FORMAT(STR_TO_DATE(i.invoice_date, '%d/%m/%Y'), '%Y-%m') AS bulan_filter
				FROM invoices i
		";

		$params = [];
		$types = "";

		if ($monthFilter) {
			$query .= " WHERE DATE_FORMAT(STR_TO_DATE(i.invoice_date, '%d/%m/%Y'), '%Y-%m') = ? ";
			$params[] = $monthFilter;
			$types .= "s";
		}

		$query .= "
				GROUP BY i.customer_email, bulan_filter
			) AS summary
			LEFT JOIN (
				SELECT email, MIN(name) AS name
				FROM customers
				GROUP BY email
			) AS c ON c.email = summary.customer_email
			ORDER BY c.name, summary.bulan_filter
		";

		$stmt = $mysqli->prepare($query);
		if (!$stmt) {
			throw new Exception('Query error: ' . $mysqli->error);
		}

		if (!empty($params)) {
			$stmt->bind_param($types, ...$params);
		}

		$stmt->execute();
		$results = $stmt->get_result();

		if ($results->num_rows > 0) {
			echo '<table class="table table-striped table-hover table-bordered" id="data-table" cellspacing="0"><thead>
					<tr>
						<th>Customer</th>
						<th>Bulan</th>
						<th>Total Invoice</th>
					</tr></thead><tbody>';

			while ($row = $results->fetch_assoc()) {
				echo '<tr>
						<td>' . htmlspecialchars($row["name"] ?? '-') . '</td>
						<td>' . htmlspecialchars($row["bulan_format"] ?? '-') . '</td>
						<td>Rp ' . number_format($row["total_invoice"], 0, ',', '.') . '</td>
					  </tr>';
			}
			echo '</tbody></table>';
		} else {
			echo "<p>Tidak ada data recap untuk filter yang dipilih.</p>";
		}

		$stmt->close();
		$mysqli->close();
	} catch (Exception $e) {
		error_log('getInvoiceRecap error: ' . $e->getMessage());
		echo "<p>Terjadi kesalahan saat memuat data recap.</p>";
	}
}
