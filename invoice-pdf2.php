<?php


// include('header.php');
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
		$customer_name = $row['name']; // customer name
		$customer_email = $row['email']; // customer email
		$customer_address_1 = $row['address_1']; // customer address
		$customer_address_2 = $row['address_2']; // customer address
		$customer_town = $row['town']; // customer town
		$customer_county = $row['county']; // customer county
		$customer_postcode = $row['postcode']; // customer postcode
		$customer_phone = $row['phone']; // customer phone number
		
		//shipping
		$customer_name_ship = $row['name_ship']; // customer name (shipping)
		$customer_address_1_ship = $row['address_1_ship']; // customer address (shipping)
		$customer_address_2_ship = $row['address_2_ship']; // customer address (shipping)
		$customer_town_ship = $row['town_ship']; // customer town (shipping)
		$customer_county_ship = $row['county_ship']; // customer county (shipping)
		$customer_postcode_ship = $row['postcode_ship']; // customer postcode (shipping)

		// invoice details
		$invoice_number = $row['invoice']; // invoice number
		$custom_email = $row['custom_email']; // invoice custom email body
		$invoice_date = $row['invoice_date']; // invoice date
		$invoice_due_date = $row['invoice_due_date']; // invoice due date
		$invoice_subtotal = $row['subtotal']; // invoice sub-total
		$invoice_shipping = $row['shipping']; // invoice shipping amount
		$invoice_discount = $row['discount']; // invoice discount
		$invoice_vat = $row['vat']; // invoice vat
		$invoice_total = $row['total']; // invoice total
		$invoice_notes = $row['notes']; // Invoice notes
		$invoice_type = $row['invoice_type']; // Invoice type
		$invoice_status = $row['status']; // Invoice status
	}
}

/* close connection */
$mysqli->close();

// Load Dompdf library
use Dompdf\Options;
use Dompdf\Dompdf;
require 'vendor/autoload.php';


$options = new Options();

$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('chroot', realpath(''));

// Create an instance of Dompdf
$dompdf = new Dompdf($options);

// Start output buffering
ob_start();
?>

<head>
    <link rel="stylesheet" href="css/pdf.css" type="text/css" media="all" />
    <style>
        /* Tambahkan CSS di sini jika perlu */
        .custom-class {
            color: red;
        }
    </style>
</head>

<div class="bacgroun-gambar">
    <div class="py-4">
        <div class="px-14 py-6">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-lg1 align-top">
                            <div>
                                <h1 class="ukuran-judul">INVOICE</h1>
                                <!-- <img src="https://menkoff.com/assets/brand-sample.png" class="h-12" /> -->
                            </div>
                        </td>

                        <td class="w-lg2 align-top">
                            <div>
                                <div class="posisi-logo">
                                    <img src="images/marketlab.png" />
                                </div>
                                <div class="row">
                                    <div class="w-1/2 alamat-marketlab"></div>
                                    <div class="w-1/2 alamat-marketlab">
                                        <p>Jl. Summarecon Bandung,</p>
                                        <p>Magna Commercial / MC No 55, RT.1/RW.12,</p>
                                        <p>Rancabolang, Kec. Gedebage,</p>
                                        <p>Kota Bandung, Jawa Barat 40296, Indonesia</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-slate-100 px-14 py-6 text-sm t2">
            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-1/2 align-top">
                            <div class="text-sm text-neutral-600">
                                <p class="font-bold">To</p>
                                <p><?php echo $customer_name; ?></p>
                                <p><?php echo $customer_email; ?></p>
                                <p><?php echo $customer_town; ?></p>
                                <p><?php echo $customer_county; ?></p>
                                <p><?php echo $customer_postcode; ?></p>
                                <p> <?php echo $customer_phone; ?></p>
                            </div>
                        </td>
                        <td class="w-1/2 align-top text-right">
                            <table class="w-full">
                                <tbody>
                                    <tr>
                                        <td class="w-costume"></td>
                                        <td>
                                            <table class="w-full content">
                                                <tbody class="tb1">
                                                    <tr>
                                                        <td colspan="2" class="p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                Sales Invoice
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                Creation Date:
                                                            </div>
                                                        </td>
                                                        <td class="p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <?php echo $invoice_date; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-main p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                Due Date:
                                                            </div>
                                                        </td>
                                                        <td class="bg-main p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <?php echo $invoice_due_date; ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>

            <hr />

            <table class="w-full border-collapse border-spacing-0 tabel-barketlab">
                <thead>
                    <tr class="tamilan-tabel">
                        <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main text-center">
                            Item
                        </td>
                        <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main text-center">
                            Qty.
                        </td>
                        <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main text-center">
                            Price
                        </td>
                        <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main text-center">
                            Discount
                        </td>
                        <td class="border-b-2 border-main pb-3 pl-2 pr-3 text-right font-bold text-main text-center">
                            Sub Total
                        </td>
                    </tr>
                </thead>
                <tbody class="tabel-market">
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
                            $item_discount = $rows['discount'] . '%';
                            $item_subtotal = $rows['subtotal'];
                    ?>
                    <tr>
                        <td name="invoice_product[]" class="border-b py-3 pl-2">
                            <?php echo $item_product; ?>
                        </td>
                        <td class=" border-b py-3 pl-2 text-center form-control invoice_product_qty calculate"
                            name="invoice_product_qty[]">
                            <?php echo $item_qty; ?>
                        </td>
                        <td class="border-b py-3 pl-2 text-center form-control calculate invoice_product_price required"
                            name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00">
                            <?php echo $item_price; ?>
                        </td>
                        <td class="border-b py-3 pl-2 text-center form-control calculate">
                            <div class="form-group form-group-sm  no-margin-bottom" name="invoice_product_discount[]"
                                placeholder="Enter % or value (ex: 10% or 10.50)">
                                <?php echo $item_discount; ?>
                            </div>
                        </td>
                        <td class="border-b py-3 pl-2 text-center">
                            <div class="input-group input-group-sm form-control calculate-sub"
                                name="invoice_product_sub[]" id="invoice_product_sub" aria-describedby="sizing-addon1">
                                <?php echo $item_subtotal; ?>
                            </div>
                        </td>
                    </tr>
                    <?php } } ?>
                </tbody>
            </table>

            <hr />

            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-1/2 align-top">
                            <div class="text-sm text-neutral-600">
                                <p class="font-bold">Payment Details</p>
                                <p>Atas Nama : Muhamad Ali</p>
                                <p>Nama Bank : Mandiri</p>
                                <p>Cabang Bank : KCP Bandung Trade Center</p>
                                <p>NO Akun Bank : 5141000293</p>
                            </div>
                        </td>
                        <td class="w-1/2 align-top text-right">
                            <table class="w-full border-collapse border-spacing-0">
                                <tbody>
                                    <tr>
                                        <td class="w-costume"></td>
                                        <td>
                                            <table class="w-full border-collapse border-spacing-0">
                                                <tbody class="tb1">
                                                    <tr>
                                                        <td class="bg-main p-3 ">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                Sub Total:
                                                            </div>
                                                        </td>
                                                        <td class="bg-main p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="invoice-sub-total">
                                                                        <?php echo rupiah($invoice_subtotal); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-main p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <div class="col-xs-3 col-xs-offset-6">
                                                                    <strong>Discount:</strong>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="bg-main p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="invoice-sub-total">
                                                                        <?php echo rupiah($invoice_discount); ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="bg-main p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <div class="col-xs-3 col-xs-offset-6">
                                                                    <strong>TAX/VAT:</strong>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="bg-main p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <span class="invoice-sub-total">
                                                                    <?php echo rupiah($invoice_vat); ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="bg-main p-3">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <div class="col-xs-3 col-xs-offset-6">
                                                                    <strong>Total:</strong>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="bg-main p-3 text-right">
                                                            <div class="whitespace-nowrap font-bold text-white">
                                                                <span class="invoice-sub-total">
                                                                    <?php echo rupiah($invoice_total); ?>
                                                                </span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
        </div>
    </div>



</div>

<?php
// Get the HTML content
$html = ob_get_clean();

// Load HTML content to Dompdf
$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("invoice.pdf", array("Attachment" => false));
exit();
?>