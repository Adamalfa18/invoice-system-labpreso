<?php

include('header.php');

?>

<h1>Add Customer</h1>
<hr>

<div id="response" class="alert alert-success" style="display:none;">
	<a href="#" class="close" data-dismiss="alert">&times;</a>
	<div class="message"></div>
</div>

<form method="post" id="create_customer">
	<input type="hidden" name="action" value="create_customer">
	<div class="row">
		<div class="col-xs-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4>Customer Information</h4>
					<div class="clear"></div>
				</div>
				<div class="panel-body form-group form-group-sm">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<input type="text" class="form-control margin-bottom copy-input required"
									name="customer_name" id="customer_name" placeholder="Name" tabindex="1">
							</div>
						</div>
						<div class="col-xs-6">
							<div class="input-group float-right margin-bottom">
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
								<input type="email" class="form-control copy-input required" name="customer_email"
									id="customer_email" placeholder="E-mail Addres" aria-describedby="sizing-addon1"
									tabindex="2">
							</div>
							<div class="form-group no-margin-bottom">
								<input type="text" class="form-control required" name="customer_phone"
									id="invoice_phone" placeholder="Phone Number" tabindex="8">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 margin-top btn-group">
			<input type="submit" id="action_create_customer" class="btn btn-success float-right" value="Create Customer"
				data-loading-text="Creating...">
		</div>
	</div>
</form>

<?php
include('footer.php');
?>