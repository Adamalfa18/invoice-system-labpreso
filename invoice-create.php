<?php
include('header.php');
include('functions.php');


$conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME); // Ganti dengan detail yang sesuai

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>

<h2>Tambah data Cekout Labpreso</h2>
<!-- <hr> -->

<div id="response" class="alert alert-success" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<div class="message"></div>
</div>

<form method="post" id="create_invoice" class="style-invoice">
	<input type="hidden" name="action" value="create_invoice">

	<div class="row">
		<!-- Kolom Kiri -->
		<div class="col-md-4 col-xs-12 mb-3">
			<!-- Tempatkan konten tambahan di sini -->
		</div>

		<!-- Kolom Kanan -->
		<div class="col-xs-12">
			<div class="row">

				<!-- Penanggung Jawab -->
				<div class="col-sm-3 col-xs-12 mb-3">
					<label for="id_pegawai">Penanggung Jawab</label>
					<div class="form-group">
						<select name="id_pegawai" id="id_pegawai" class="form-control">
							<option selected>Penanggung Jawab</option>
							<?php
                    $query = "SELECT * FROM pegawai";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        die("Query Error: " . mysqli_error($conn));
                    }
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['id_pegawai']}'>{$row['nama']} - {$row['jabatan']}</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada data pembayaran</option>";
                    }
                    ?>
						</select>
					</div>
				</div>

				<!-- Tanggal Invoice -->
				<div class="col-sm-3 col-xs-12 mb-3">
					<label for="invoice_date">Tanggal Invoice</label>
					<div class="form-group">
						<div class="input-group date" id="invoice_date">
							<input type="text" class="form-control required" name="invoice_date"
								placeholder="Invoice Date" data-date-format="<?php echo DATE_FORMAT ?>" />
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>

				<!-- Status Invoice -->
				<div class="col-sm-3 col-xs-12 mb-3">
					<label for="invoice_status">Status</label>
					<div class="form-group">
						<select name="invoice_status" id="invoice_status" class="form-control">
							<option value="open" selected>Kasbon</option>
							<option value="paid">Transfer</option>
						</select>
					</div>
				</div>

				<!-- Nomor Invoice -->
				<div class="col-sm-3 col-xs-12 mb-3">
					<label for="invoice_id">Nomor Invoice</label>
					<div class="form-group">
						<div class="input-group">
							<span class="input-group-addon">#<?php echo INVOICE_PREFIX ?></span>
							<input type="text" name="invoice_id" id="invoice_id" class="form-control required"
								placeholder="Invoice Number" aria-describedby="sizing-addon1"
								value="<?php getInvoiceId(); ?>" readonly>
						</div>
					</div>
				</div>

			</div>
		</div>

	</div>


	<div class="row">
		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="float-left">Customer Information</h4>
					<a href="#" class="float-right select-customer"><b>OR</b> Select Existing Customer</a>
					<div class="clearfix"></div>
				</div>
				<div class="panel-body form-group form-group-sm">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required"
									name="customer_name" id="customer_name" placeholder="Brand Name" tabindex="1">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required"
									name="customer_address_1" id="customer_address_1" placeholder="PIC Name"
									tabindex="3">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required"
									name="customer_town" id="customer_town" placeholder="Town" tabindex="5">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control copy-input required" name="customer_postcode"
									id="customer_postcode" placeholder="Postcode" tabindex="7">
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="input-group float-right margin-bottom">
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
								<input type="email" class="form-control copy-input required" name="customer_email"
									id="customer_email" placeholder="E-mail Address" aria-describedby="sizing-addon1"
									tabindex="2">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input"
									name="customer_address_2" id="customer_address_2" placeholder="Address"
									tabindex="4">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom required" name="customer_county"
									id="customer_county" placeholder="Country" tabindex="6">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_phone"
									id="customer_phone" placeholder="Phone Number" tabindex="8">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Shipping Information</h4>
				</div>
				<div class="panel-body form-group form-group-sm">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom required" name="customer_name_ship"
									id="customer_name_ship" placeholder="Brand Name" tabindex="9">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom" name="customer_address_2_ship"
									id="customer_address_2_ship" placeholder="Address" tabindex="11">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_county_ship"
									id="customer_county_ship" placeholder="Country" tabindex="13">
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom required"
									name="customer_address_1_ship" id="customer_address_1_ship" placeholder="PIC Name"
									tabindex="10">
							</div>
							<div class="form-group">
								<input type="text" class="form-control margin-bottom required" name="customer_town_ship"
									id="customer_town_ship" placeholder="Town" tabindex="12">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_postcode_ship"
									id="customer_postcode_ship" placeholder="Postcode" tabindex="14">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- / end client details section -->
	<div class="table-responsive-scroll">
		<table class="table table-bordered table-hover table-striped" id="invoice_table">
			<thead>
				<tr>
					<th width="300">
						<h4><a href="#" class="btn btn-success btn-xs add-row"><span class="glyphicon glyphicon-plus"
									aria-hidden="true"></span></a> Product</h4>
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
				<tr>
					<td>
						<div class="form-group form-group-sm  no-margin-bottom">
							<a href="#" class="btn btn-danger btn-xs delete-row"><span
									class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>
							<input type="text" class="form-control form-group-sm item-input invoice_product"
								name="invoice_product[]" placeholder="Enter Product Name OR Description">
							<p class="item-select">or <a href="#">select a product</a></p>
						</div>
					</td>
					<td class="text-right">
						<div class="form-group form-group-sm no-margin-bottom">
							<input type="number" class="form-control invoice_product_qty calculate"
								name="invoice_product_qty[]" value="1">
						</div>
					</td>
					<td class="text-right">
						<div class="input-group input-group-sm  no-margin-bottom">
							<span class="input-group-addon"><?php echo CURRENCY ?></span>
							<input type="number" class="form-control calculate invoice_product_price required"
								name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00">
						</div>
					</td>
					<td class="text-right">
						<div class="form-group form-group-sm  no-margin-bottom">
							<input type="text" class="form-control calculate" name="invoice_product_discount[]"
								placeholder="Enter % OR value (ex: 10% or 10.50)">
						</div>
					</td>
					<td class="text-right">
						<div class="input-group input-group-sm">
							<span class="input-group-addon"><?php echo CURRENCY ?></span>
							<input type="text" class="form-control calculate-sub" name="invoice_product_sub[]"
								id="invoice_product_sub" value="0.00" aria-describedby="sizing-addon1" disabled>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="invoice_totals" class="row text-right">

		<!-- Subtotal -->
		<div class="col-xs-12 style-total">
			<div class="row">
				<div class="col-xs-6 col-sm-5 col-sm-offset-5 text-left text-sm-right">
					<strong>Sub Total:</strong>
				</div>
				<div class="col-xs-6 col-sm-2 text-left text-sm-right">
					<?php echo CURRENCY ?><span class="invoice-sub-total">0.00</span>
					<input type="hidden" name="invoice_subtotal" id="invoice_subtotal">
				</div>
			</div>

			<!-- Discount -->
			<div class="row">
				<div class="col-xs-6 col-sm-5 col-sm-offset-5 text-left text-sm-right">
					<strong>Discount:</strong>
				</div>
				<div class="col-xs-6 col-sm-2 text-left text-sm-right">
					<?php echo CURRENCY ?><span class="invoice-discount">0.00</span>
					<input type="hidden" name="invoice_discount" id="invoice_discount">
				</div>
			</div>

			<!-- Total -->
			<div class="row">
				<div class="col-xs-6 col-sm-5 col-sm-offset-5 text-left text-sm-right">
					<strong>Total:</strong>
				</div>
				<div class="col-xs-6 col-sm-2 text-left text-sm-right">
					<?php echo CURRENCY ?><span class="invoice-total">0.00</span>
					<input type="hidden" name="invoice_total" id="invoice_total">
				</div>
			</div>
		</div>

		<!-- Penanggung Jawab -->


		<!-- Tombol Submit -->

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
				<h4 class="modal-title">Select Product</h4>
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

<div id="insert_customer" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
						aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Select An Existing Customer</h4>
			</div>
			<div class="modal-body">
				<?php popCustomersList(); ?>
			</div>
			<div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">Cancel</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
	include('footer.php');
?>