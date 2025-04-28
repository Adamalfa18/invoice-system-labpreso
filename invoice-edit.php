<?php


include('header.php');
include('functions.php');

$getID = $_GET['id'];

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
	die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
}

// the query
$query = "SELECT p.*, i.*, c.*
			FROM invoice_items p 
			JOIN invoices i ON i.invoice = p.invoice
			JOIN customers c ON c.invoice = i.invoice
			WHERE p.invoice = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if($result) {
	while ($row = mysqli_fetch_assoc($result)) {
		// Tambahkan pengecekan untuk menghindari undefined
		$customer_name = isset($row['name']) ? $row['name'] : ''; // customer name
		$customer_email = isset($row['email']) ? $row['email'] : ''; // customer email
		$customer_address_1 = isset($row['address_1']) ? $row['address_1'] : ''; // customer address
		$customer_address_2 = isset($row['address_2']) ? $row['address_2'] : ''; // customer address
		$customer_town = isset($row['town']) ? $row['town'] : ''; // customer town
		$customer_county = isset($row['county']) ? $row['county'] : ''; // customer county
		$customer_postcode = isset($row['postcode']) ? $row['postcode'] : ''; // customer postcode
		$customer_phone = isset($row['phone']) ? $row['phone'] : ''; // customer phone number
		
		//shipping
		$customer_name_ship = isset($row['name_ship']) ? $row['name_ship'] : ''; // customer name (shipping)
		$customer_address_1_ship = isset($row['address_1_ship']) ? $row['address_1_ship'] : ''; // customer address (shipping)
		$customer_address_2_ship = isset($row['address_2_ship']) ? $row['address_2_ship'] : ''; // customer address (shipping)
		$customer_town_ship = isset($row['town_ship']) ? $row['town_ship'] : ''; // customer town (shipping)
		$customer_county_ship = isset($row['county_ship']) ? $row['county_ship'] : ''; // customer county (shipping)
		$customer_postcode_ship = isset($row['postcode_ship']) ? $row['postcode_ship'] : ''; // customer postcode (shipping)

		// invoice details
		$invoice_number = isset($row['invoice']) ? $row['invoice'] : ''; // invoice number
		// Pastikan untuk memeriksa apakah data invoice_number ada
		if (empty($invoice_number)) {
			die('Error: Invoice number is missing.');
		}
		$invoice_date = isset($row['invoice_date']) ? $row['invoice_date'] : ''; // invoice date
		$invoice_due_date = isset($row['invoice_due_date']) ? $row['invoice_due_date'] : ''; // invoice due date
		$invoice_subtotal = isset($row['subtotal']) ? $row['subtotal'] : 0; // invoice sub-total
		$invoice_shipping = isset($row['shipping']) ? $row['shipping'] : 0; // invoice shipping amount
		$invoice_discount = isset($row['discount']) ? $row['discount'] : 0; // invoice discount
		$invoice_vat = isset($row['vat']) ? $row['vat'] : 0; // invoice vat
		$invoice_total = isset($row['total']) ? $row['total'] : 0; // invoice total
		$invoice_notes = isset($row['notes']) ? $row['notes'] : ''; // Invoice notes
		$invoice_type = isset($row['invoice_type']) ? $row['invoice_type'] : ''; // Invoice type
		$id_bayar = isset($row['id_bayar']) ? $row['id_bayar'] : ''; // Invoice statu
		$id_pegawai = isset($row['id_pegawai']) ? $row['id_pegawai'] : ''; // Invoice statu
		$invoice_status = isset($row['status']) ? $row['status'] : ''; // Invoice status
	}
}

/* close connection */
$mysqli->close();

?>

<h1>Edit Invoice (<?php echo $getID; ?>)</h1>
<hr>

<div id="response" class="alert alert-success" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<div class="message"></div>
</div>

<form method="post" id="update_invoice">
	<input type="hidden" name="action" value="update_invoice">
	<input type="hidden" name="update_id" value="<?php echo $getID; ?>">

	<!-- <div class="row">
		<div class="col-xs-12">
			<textarea name="custom_email" id="custom_email" class="custom_email_textarea"
				placeholder="Enter a custom email message here if you wish to override the default invoice type email message."><?php // echo $custom_email; ?></textarea>
		</div>
	</div> -->


	<div class="row sty-row">
		<!-- Kolom Pegawai -->
		<div class="col-md-4 col-sm-6 col-xs-12 mb-3 posisi-row">
			<div class="form-group">
				<label for="id_pegawai">Pilih Pegawai</label>
				<select name="id_pegawai" id="id_pegawai" class="form-control">
					<option value="<?php echo $id_pegawai; ?>">Pilih Pegawai</option>
					<?php
                $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
                $query = "SELECT * FROM pegawai";
                $result = mysqli_query($mysqli, $query);
                if (!$result) {
                    die("Query Error: " . mysqli_error($mysqli));
                }
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['id_pegawai']}'>{$row['nama']} - {$row['jabatan']}</option>";
                    }
                } else {
                    echo "<option value=''>Tidak ada data pegawai</option>";
                }
                ?>
				</select>
			</div>
		</div>

		<!-- Kolom Tanggal, Status, dan Nomor Invoice -->
		<div class="col-md-8 col-sm-6 col-xs-12">
			<div class="row">
				<!-- Tanggal Invoice -->
				<div class="col-md-4 col-sm-6 col-xs-12 mb-3">
					<div class="form-group">
						<label for="invoice_date">Tanggal Invoice</label>
						<div class="input-group date" id="invoice_date">
							<input type="text" class="form-control required" name="invoice_date"
								placeholder="Select invoice date" data-date-format="<?php echo DATE_FORMAT ?>"
								value="<?php echo $invoice_date; ?>" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>

				<!-- Status Invoice -->
				<div class="col-md-4 col-sm-6 col-xs-12 mb-3">
					<div class="form-group">
						<label for="invoice_status">Status</label>
						<select name="invoice_status" id="invoice_status" class="form-control">
							<option value="open" <?php if($invoice_status === 'Kasbon'){ echo 'selected'; } ?>>Kasbon
							</option>
							<option value="paid" <?php if($invoice_status === 'Transper'){ echo 'selected'; } ?>>
								Transper</option>
						</select>
					</div>
				</div>

				<!-- Nomor Invoice -->
				<div class="col-md-4 col-sm-6 col-xs-12 mb-3">
					<div class="form-group">
						<label for="invoice_id">No. Invoice</label>
						<div class="input-group">
							<span class="input-group-addon">#<?php echo INVOICE_PREFIX ?></span>
							<input type="text" name="invoice_id" id="invoice_id" class="form-control required"
								placeholder="Invoice Number" aria-describedby="sizing-addon1"
								value="<?php echo $getID; ?>" readonly>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>




	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Customer Information</h4>
					<div class="clear"></div>
				</div>
				<div class="panel-body form-group form-group-sm">
					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required"
									name="customer_name" id="customer_name" placeholder="Enter name" tabindex="1"
									value="<?php echo $customer_name; ?>">
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="input-group float-right margin-bottom">
								<span class="input-group-addon">@</span>
								<input type="email" class="form-control copy-input required" name="customer_email"
									id="customer_email" placeholder="E-mail address" aria-describedby="sizing-addon1"
									tabindex="2" value="<?php echo $customer_email; ?>">
							</div>
						</div>
						<div class="col-xs-12 col-sm-4">
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_phone"
									id="invoice_phone" placeholder="Phone number" tabindex="8"
									value="<?php echo $customer_phone; ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- / end client details section -->
	<table class="table table-bordered" id="invoice_table">
		<thead>
			<tr>
				<th width="500">
					<h4><a href="#" class="btn btn-success btn-xs add-row"><span class="glyphicon glyphicon-plus"
								aria-hidden="true"></span></a> Item</h4>
				</th>
				<th>
					<h4>Qty</h4>
				</th>
				<th>
					<h4>Price</h4>
				</th>
				<th width="100">
					<h4>Discount</h4>
				</th>
				<th>
					<h4>Sub Total</h4>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php 

						// Connect to the database
						$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

						// output any connection error
						if ($mysqli->connect_error) {
							die('Error : ('.$mysqli->connect_errno .') '. $mysqli->connect_error);
						}

						// the query
						$query2 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($getID) . "'";

						$result2 = mysqli_query($mysqli, $query2);

						//var_dump($result2);

						// mysqli select query
						if($result2) {
							while ($rows = mysqli_fetch_assoc($result2)) {

								//var_dump($rows);

							    $item_product = $rows['product'];
							    $item_qty = $rows['qty'];
							    $item_price = $rows['price'];
							    $item_discount = $rows['discount'];
							    $item_subtotal = $rows['subtotal'];
					?>
			<tr>
				<td>
					<div class="form-group form-group-sm  no-margin-bottom">
						<a href="#" class="btn btn-danger btn-xs delete-row"><span class="glyphicon glyphicon-remove"
								aria-hidden="true"></span></a>
						<input type="text" class="form-control form-group-sm item-input invoice_product"
							name="invoice_product[]" placeholder="Enter item title and / or description"
							value="<?php echo $item_product; ?>">
						<p class="item-select">or <a href="#">select an item</a></p>
					</div>
				</td>
				<td class="text-right">
					<div class="form-group form-group-sm no-margin-bottom">
						<input type="text" class="form-control invoice_product_qty calculate"
							name="invoice_product_qty[]" value="<?php echo $item_qty; ?>">
					</div>
				</td>
				<td class="text-right">
					<div class="input-group input-group-sm  no-margin-bottom">
						<span class="input-group-addon"><?php echo CURRENCY ?></span>
						<input type="text" class="form-control calculate invoice_product_price required"
							name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00"
							value="<?php echo $item_price; ?>">
					</div>
				</td>
				<td class="text-right">
					<div class="form-group form-group-sm  no-margin-bottom">
						<input type="text" class="form-control calculate" name="invoice_product_discount[]"
							placeholder="Enter % or value (ex: 10% or 10.50)" value="<?php echo $item_discount; ?>">
					</div>
				</td>
				<td class="text-right">
					<div class="input-group input-group-sm">
						<span class="input-group-addon"><?php echo CURRENCY ?></span>
						<input type="text" class="form-control calculate-sub" name="invoice_product_sub[]"
							id="invoice_product_sub" aria-describedby="sizing-addon1"
							value="<?php echo $item_subtotal; ?>" disabled>
					</div>
				</td>
			</tr>
			<?php } } ?>
		</tbody>
	</table>
	<div id="invoice_totals" class="row text-right">
		<div class="col-md-6 col-md-offset-6 col-sm-12">
			<div class="row mb-2">
				<div class="col-xs-6 text-right">
					<strong>Sub Total:</strong>
				</div>
				<div class="col-xs-6 text-left">
					<?php echo CURRENCY ?>
					<span class="invoice-sub-total"><?php echo $invoice_subtotal; ?></span>
					<input type="hidden" name="invoice_subtotal" id="invoice_subtotal"
						value="<?php echo $invoice_subtotal; ?>">
				</div>
			</div>

			<div class="row mb-2">
				<div class="col-xs-6 text-right">
					<strong>Discount:</strong>
				</div>
				<div class="col-xs-6 text-left">
					<?php echo CURRENCY ?>
					<span class="invoice-discount"><?php echo $invoice_discount; ?></span>
					<input type="hidden" name="invoice_discount" id="invoice_discount"
						value="<?php echo $invoice_discount; ?>">
				</div>
			</div>

			<div class="row">
				<div class="col-xs-6 text-right">
					<strong>Total:</strong>
				</div>
				<div class="col-xs-6 text-left">
					<?php echo CURRENCY ?>
					<span class="invoice-total"><?php echo $invoice_total; ?></span>
					<input type="hidden" name="invoice_total" id="invoice_total" value="<?php echo $invoice_total; ?>">
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 margin-top btn-group">
			<input type="submit" id="action_edit_invoice" class="btn btn-success float-right" value="Update Invoice"
				data-loading-text="Updating...">
		</div>
	</div>
</form>

<div id="insert" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Select an item</h4>
			</div>
			<div class="modal-body">
				<?php popProductsList(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn btn-primary" id="selected">Add</button>
				<button type="button" data-dismiss="modal" class="btn">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
	include('footer.php');
?>