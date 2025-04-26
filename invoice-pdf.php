<?php


// include('header.php');
include('functions.php');

$getID = $_GET['id'];

// Connect to the database
$mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);

// output any connection error
if ($mysqli->connect_error) {
    die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// the query
$query = "SELECT p.*, i.*, c.*
			FROM invoice_items p 
			JOIN invoices i ON i.invoice = p.invoice
			JOIN customers c ON c.invoice = i.invoice
			WHERE p.invoice = '" . $mysqli->real_escape_string($getID) . "'";

$result = mysqli_query($mysqli, $query);

// mysqli select query
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customer_name = $row['name']; // customer name
        $customer_email = $row['email']; // customer email
        $customer_phone = $row['phone']; // customer phone number

        // invoice details
        $invoice_number = $row['invoice']; // invoice number
        $custom_email = $row['customer_email']; // invoice custom email body
        $invoice_date = $row['invoice_date']; // invoice date
        $invoice_subtotal = $row['subtotal']; // invoice sub-total
        $invoice_discount = $row['discount']; // invoice discount
        $invoice_total = $row['total']; // invoice total
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

        footer {

            /* Ganti dengan warna latar belakang yang diinginkan */
            padding: 10px 20px;
            text-align: center;
            position: fixed;
            bottom: 10;
            width: 100%;
            text-align: center;
        }



        */ .footer-link {
            color: white;
            /* Ganti dengan warna teks yang diinginkan */
            text-decoration: none;
            font-size: 14px;
            /* Ukuran font */
        }

        .footer-icon {
            width: 20px;
            /* Ukuran ikon */
            height: 20px;
            /* Ukuran ikon */
            margin-right: 5px;
            /* Jarak antara ikon dan teks */
        }
    </style>
</head>

<div class="bacgroun-gambar">
    <div class="py-4">
        <div class="px-14 py-6 ">
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
                                <!-- <div class="row">
                                    <div class="w-1/2 alamat-marketlab"></div>
                                    <div class="w-1/2 alamat-marketlab">
                                        <p>Jl. Summarecon Bandung,</p>
                                        <p>Magna Commercial / MC No 55, RT.1/RW.12,</p>
                                        <p>Rancabolang, Kec. Gedebage,</p>
                                        <p>Kota Bandung, Jawa Barat 40296, Indonesia</p>
                                    </div>
                                </div> -->
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-slate-100 px-14 py-6 text-sm t2">
            <div class="jarak-margin">
                <table class="w-full border-collapse border-spacing-0 ">
                    <tbody>
                        <tr>
                            <td class="w-1/2 align-top">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold">To
                                        <span class="font-normal">
                                            <?php echo $customer_name; ?>
                                        </span>
                                    </p>
                                    <p> <?php echo $customer_phone; ?></p>
                                    <p><?php echo $customer_email; ?></p>
                                </div>
                            </td>
                            <td class="w-1/2 vertical-align text-right">
                                <div class="text-sm text-neutral-600">
                                    <p class="font-bold">
                                        Creation Date:
                                    </p>
                                    <p class="font-bold">
                                        Creation Date:
                                        <span class="font-bold">
                                            <?php echo $invoice_date; ?>
                                        </span>
                                    </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- <hr /> -->

            <table class="w-full border-collapse border-spacing-0 tabel-barketlab">
                <thead>
                    <tr class="tamilan-tabel">
                        <td style="width: 200px;"
                            class="border-b-2 border-main pb-3 pl-2 font-bold text-main text-center">
                            Item
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
                        die('Error : (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
                    }

                    // the query
                    $query2 = "SELECT * FROM invoice_items WHERE invoice = '" . $mysqli->real_escape_string($getID) . "'";

                    $result2 = mysqli_query($mysqli, $query2);

                    //var_dump($result2);

                    // mysqli select query
                    if ($result2) {
                        while ($rows = mysqli_fetch_assoc($result2)) {

                            //var_dump($rows);

                            $item_product = $rows['product'];
                            $item_price = $rows['price'];
                            // $item_discount = $rows['discount'];
                            $item_discount = $rows['discount'] . '%';
                            $item_subtotal = $rows['subtotal'];
                    ?>
                            <tr>
                                <td name="invoice_product[]" class="border-b py-3 pl-2">
                                    <?php echo $item_product; ?>
                                </td>
                                <td class="border-b py-3 pl-2 text-center form-control calculate invoice_product_price required"
                                    name="invoice_product_price[]" aria-describedby="sizing-addon1" placeholder="0.00">
                                    <?php echo rupiah($item_price); ?>
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
                                        <?php echo rupiah($item_subtotal); ?>
                                    </div>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>

            <hr />

            <table class="w-full border-collapse border-spacing-0">
                <tbody>
                    <tr>
                        <td class="w-lg1 align-top">
                            <div class="text-sm text-neutral-600">
                                <p class="font-bold">Payment Details</p>
                                <?php
                                // Query untuk mendapatkan data bayar
                                $query_bayar = "SELECT * FROM bayar WHERE id_bayar = '" . $mysqli->real_escape_string($id_bayar) . "'"; // Ubah 'invoice' menjadi 'id_invoice'
                                $result_bayar = mysqli_query($mysqli, $query_bayar);

                                if ($result_bayar && mysqli_num_rows($result_bayar) > 0) {
                                    $bayar = mysqli_fetch_assoc($result_bayar);
                                    echo "<p>Bank Name : " . $bayar['bank'] . "</p>";
                                    echo "<p>Acount Number : " . $bayar['rekening'] . "</p>";
                                    echo "<p>Acount Name : " . $bayar['nama'] . "</p>";
                                } else {
                                    echo "<p>Tidak ada data pembayaran.</p>";
                                }
                                ?>
                            </div>
                        </td>

                        <td class="w-lg2 text-left">
                            <div class="play-flex text-sm text-neutral-600">
                                <p class="play">
                                    <span class="height font-bold" style="width: 100px; display: inline-block;">
                                        Sub Total :
                                    </span>
                                    <span class="font-normal" style="display: inline-block;">
                                        <?php echo rupiah($invoice_subtotal); ?>
                                    </span>
                                </p>
                                <p class="play">
                                    <span class="height font-bold" style="width: 100px; display: inline-block;">
                                        Discount :
                                    </span>
                                    <span class="font-normal" style="display: inline-block;">
                                        <?php echo rupiah($invoice_discount); ?>
                                    </span>
                                </p>
                                <p class="play">
                                    <span class="height font-bold" style="width: 100px; display: inline-block;">
                                        Pph 23 2% :
                                    </span>
                                    <span class="font-normal" style="display: inline-block;">
                                        <?php echo rupiah($invoice_vat); ?>
                                    </span>
                                </p>
                                <hr class="right">
                                <p class="play">
                                    <span class="height font-bold" style="width: 100px; display: inline-block;">
                                        Total :
                                    </span>
                                    <span class="font-bold" style="display: inline-block;">
                                        <?php echo rupiah($invoice_total); ?>
                                    </span>
                                </p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div>
        </div>
    </div>



    <footer>
        <div class="footer-container">
            <a href="https://www.instagram.com/marketlab_id/" target="_blank" class="footer-link">
                <img src="images/instagram.png" alt="Instagram" class="footer-icon" style="display: inline-block;">
                <span style="display: inline-block;">
                    marketlab_id
                </span>
            </a>
            <a href="https://marketlab.id" target="_blank" class="footer-link">
                <img src="images/internet.png" alt="Website" class="footer-icon" style="display: inline-block;">
                <span style="display: inline-block;">
                    https://marketlab.id
                </span>
            </a>
            <a href="https://www.google.com/maps/place/Marketlab+Digital+Marketing+Agency/@-6.9583701,107.6867563,17z/data=!3m1!4b1!4m6!3m5!1s0x2e68e916c4d8d1f5:0x9dd50ccd935dbd42!8m2!3d-6.9583701!4d107.6893366!16s%2Fg%2F11mr23gbk9?entry=ttu&g_ep=EgoyMDI0MTAxNS4wIKXMDSoASAFQAw%3D%3D"
                target="_blank" class="footer-link">
                <img src="images/location.png" alt="Location" class="footer-icon" style="display: inline-block;">
                <span style="display: inline-block;">
                    Location
                </span>
            </a>
        </div>
    </footer>
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