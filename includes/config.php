<?php
// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DATABASE INFORMATION
define('DATABASE_HOST', 'localhost');
define('DATABASE_NAME', 'labpreso');
define('DATABASE_USER', 'root');
define('DATABASE_PASS', '');

// COMPANY INFORMATION
define('COMPANY_LOGO', 'images/Invoice System.png');
define('COMPANY_LOGO_WIDTH', 300);
define('COMPANY_LOGO_HEIGHT', 90);
define('COMPANY_NAME', 'Invoice Mg System');
define('COMPANY_ADDRESS_1', '123 Something Street');
define('COMPANY_ADDRESS_2', 'Collierville, 3590 Lords Way');
define('COMPANY_ADDRESS_3', 'Paekinta');
define('COMPANY_COUNTY', 'US');
define('COMPANY_POSTCODE', '10100');

define('COMPANY_NUMBER', 'Company No: 699400000');
define('COMPANY_VAT', 'Company VAT: 690000007');

// EMAIL DETAILS
define('EMAIL_FROM', 'sales@inms.ccc');
define('EMAIL_NAME', 'Invoice Mg System');
define('EMAIL_SUBJECT', 'Invoice default email subject');
define('EMAIL_BODY_INVOICE', 'Invoice default body');
define('EMAIL_BODY_QUOTE', 'Quote default body');
define('EMAIL_BODY_RECEIPT', 'Receipt default body');

// OTHER SETTINGS
define('INVOICE_PREFIX', 'MD');
define('INVOICE_INITIAL_VALUE', 1);
define('INVOICE_THEME', '#222222');
define('TIMEZONE', 'America/Los_Angeles');
define('DATE_FORMAT', 'DD/MM/YYYY');
define('CURRENCY', 'Rp');
define('ENABLE_VAT', true);
define('VAT_INCLUDED', false);
define('VAT_RATE', 2);

define('PAYMENT_DETAILS', 'Invoice Mg System.<br>Sort Code: 00-00-00<br>Account Number: 12345678');
define('FOOTER_NOTE', 'Invoice Management System');

// CONNECT TO THE DATABASE
try {
    $mysqli = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
    if ($mysqli->connect_error) {
        throw new Exception("Koneksi database gagal: " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}