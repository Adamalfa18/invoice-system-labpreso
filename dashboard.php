<?php

/*******************************************************************************
 *  Invoice Management System                                                *
 *                                                                              *
 * Version: 1.0	                                                               *
 * Developer:  Abhishek Raj                                   				           *
 *******************************************************************************/

include('header.php');
include('functions.php');
include_once("includes/config.php");

?>

<section class="content">
  <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-purple">
        <div class="inner">
          <h3><?php

              $result = mysqli_query($mysqli, 'SELECT SUM(subtotal) AS value_sum FROM invoices');
              $row = mysqli_fetch_assoc($result);
              $sum = $row['value_sum'] ? $row['value_sum'] : 0; // Menangani nilai null
              echo rupiah($sum); // Menggunakan fungsi rupiah untuk format
              ?></h3>

          <p>Total penjualan</p>
        </div>
        <div class="icon">
          <i class="ion ion-printer"></i>
        </div>

      </div>
    </div>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-green">
        <div class="inner">
          <h3>
            <?php
            $result = mysqli_query($mysqli, 'SELECT SUM(subtotal) AS value_sum FROM invoices WHERE status = "transfer"');
            $row = mysqli_fetch_assoc($result);
            $sum = $row['value_sum'] ? $row['value_sum'] : 0; // Menangani nilai null
            echo rupiah($sum); // Menggunakan fungsi rupiah untuk format

            ?></h3>

          <p>Total Pendapatan</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-usd"></i>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-red">
        <div class="inner">
          <h3><?php

              $result = mysqli_query($mysqli, 'SELECT SUM(subtotal) AS value_sum FROM invoices WHERE status = "kasbon"');
              $row = mysqli_fetch_assoc($result);
              $sum = $row['value_sum'];
              echo rupiah($sum);
              ?></h3>

          <p>Total Kasbon</p>
        </div>
        <div class="icon">
          <i class="ion ion-alert-circled"></i>
        </div>

      </div>
    </div>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-primary">
        <div class="inner">
          <h3><?php

              $sql = "SELECT * FROM products";
              $query = $mysqli->query($sql);

              echo "$query->num_rows";
              ?></h3>
          <p>Total Produk</p>
        </div>
        <div class="icon">
          <i class="ion ion-social-dropbox"></i>
        </div>

      </div>
    </div>
    <!-- ./col -->

    <!-- ./col -->

    <!-- ./col -->

    <!-- ./col -->
  </div>
  <!-- /.row -->


  <!-- 2nd row -->
  <div class="row">


    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-maroon">
        <div class="inner">
          <h3><?php

              $sql = "SELECT * FROM store_customers";
              $query = $mysqli->query($sql);

              echo "$query->num_rows";
              ?></h3>

          <p>Total Customer</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-people"></i>
        </div>

      </div>
    </div>

    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-olive">
        <div class="inner">
          <h3><?php

              $sql = "SELECT * FROM invoices WHERE status = 'transfer'";
              $query = $mysqli->query($sql);

              echo "$query->num_rows";
              ?></h3>

          <p>Paid Bills</p>
        </div>
        <div class="icon">
          <i class="ion ion-ios-paper"></i>
        </div>

      </div>
    </div>
    <div class="col-lg-3 col-xs-6">
      <!-- small box -->
      <div class="small-box bg-yellow">
        <div class="inner">
          <h3><?php

              $sql = "SELECT * FROM invoices WHERE status = 'kasbon'";
              $query = $mysqli->query($sql);

              echo "$query->num_rows";
              ?></h3>

          <p>Pending Bills</p>
        </div>
        <div class="icon">
          <i class="ion ion-load-a"></i>
        </div>

      </div>
    </div>
  </div>



</section>
<!-- /.content -->



<?php
include('footer.php');
?>