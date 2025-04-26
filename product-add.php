<?php
include('header.php');
?>

<h2>Add Product</h2>
<hr>
<div id="response" class="alert alert-success" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<div class="message"></div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Product Information</h4>
			</div>
			<div class="style-add form-group form-group-sm">
				<form method="post" id="add_product">
					<input type="hidden" name="action" value="add_product">

					<div class="row">
						<div class="col-md-3 col-sm-6 col-xs-12 mb-2">
							<label for="id_pegawai">Nama Product</label>
							<input type="text" class="form-control required" name="product_name"
								placeholder="Enter Product Name">
						</div>
						<div class="col-md-3 col-sm-6 col-xs-12 mb-2">
							<label for="id_pegawai">Deskripsi</label>
							<input type="text" class="form-control required" name="product_desc"
								placeholder="Enter Product Description">
						</div>
						<div class="col-md-3 col-sm-6 col-xs-12 mb-2">
							<label for="id_pegawai">Harga</label>
							<div class="input-group">
								<span class="input-group-addon"><?php echo CURRENCY ?></span>
								<input type="number" name="product_price" class="form-control required"
									placeholder="0.00" aria-describedby="sizing-addon1">
							</div>
						</div>
						<div class="col-md-3 col-sm-6 col-xs-12 mb-2">
							<label for="id_pegawai">Quantity</label>
							<input type="text" class="form-control required" name="qty" placeholder="Quantity">
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 margin-top btn-group">
							<input type="submit" id="action_add_product" class="btn btn-success float-right"
								value="Add Product" data-loading-text="Adding...">
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div>
		<?php
		include('footer.php');
		?>