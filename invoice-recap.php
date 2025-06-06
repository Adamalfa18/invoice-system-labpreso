<?php


include('header.php');
include('functions.php');

?>

<h1>Invoice Recap</h1>
<hr>

<div class="row">
	<div class="col-xs-12">
		<div id="response" class="alert alert-success" style="display:none;">
			<a href="#" class="close" data-dismiss="alert">&times;</a>
			<div class="message"></div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4>Customer's Invoice Recap</h4>
			</div>
			<div class="panel-body form-group form-group-sm">
				<form method="GET" class="form-inline" style="margin-bottom: 15px; overflow: hidden;">
					<div style="float: right;">
						<div class="form-group" style="margin-right: 5px;">
							Pilih Bulan: <input type="month" name="filter_month" id="filter-month" class="form-control"
								value="<?= isset($_GET['filter_month']) ? htmlspecialchars($_GET['filter_month']) : '' ?>">
						</div>
						<button type="submit" class="btn btn-primary">Filter</button>
						<a href="invoice-recap.php" class="btn btn-default">Reset</a>
					</div>
				</form>
				<div id="invoice-recap-content">
					<?php
					$filterName = isset($_GET['filter_name']) ? $_GET['filter_name'] : null;
					$filterMonth = isset($_GET['filter_month']) ? $_GET['filter_month'] : null;
					getInvoiceRecap($filterMonth, $filterName);
					?>
				</div>
			</div>
		</div>
	</div>
	<div>

		<div id="delete_invoice" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
								aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Delete Invoice</h4>
					</div>
					<div class="modal-body">
						<p>Are you sure you want to delete this invoice?</p>
					</div>
					<div class="modal-footer">
						<button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Delete</button>
						<button type="button" data-dismiss="modal" class="btn">Cancel</button>
					</div>
				</div><!-- /.modal-content -->
			</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->

		<?php
		include('footer.php');
		?>