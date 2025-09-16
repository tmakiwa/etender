<?php
session_start();

// Check if the user is logged in by verifying the email session variable
if (!isset($_SESSION['email'])) {
    header("location:../index");
    exit;
}

include 'db.php'; // Include the PDO connection

// Retrieve the email from the session
$email = $_SESSION['email'];

// Initialize variables
$company_name = $activate = null;

try {
    // Prepare and execute the SQL statement securely using PDO
    $sql = "SELECT company_name, email, id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);

    // Bind the email parameter
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // Execute the statement
    $stmt->execute();

    // Fetch the user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Assign the fetched values to variables
        $company_name = $user['company_name'];
        $email = $user['email'];
        $user_id = $user['id'];
        

         // Get the count of tenders this supplier has been invited to
         $invite_sql = "SELECT COUNT(*) AS invitation_count FROM invited_tenders WHERE user_id = :user_id";
         $invite_stmt = $pdo->prepare($invite_sql);
         $invite_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
         $invite_stmt->execute();
         
         // Fetch the count
         $invitation_count = $invite_stmt->fetchColumn();
     
    } else {
        echo "User not found.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>






<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="Tinashe">
  <title>e-tender | Supplier </title>
	
  <!-- Favicons-->
  <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
  <link rel="apple-touch-icon" type="image/x-icon" href="img/favicon.png">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/favicon.png">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/favicon.png">
  <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/favicon.png">
	
  <!-- GOOGLE WEB FONT -->
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
	
  <!-- Bootstrap core CSS-->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Icon fonts-->
  <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Plugin styles -->
  <link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">

  <link href="vendor/dropzone.css" rel="stylesheet">
  <!-- Main styles -->
  <link href="css/admin.css" rel="stylesheet">
  <!-- Your custom styles -->
  <link href="css/admin.css" rel="stylesheet">

   <!-- WYSIWYG Editor -->
  <link rel="stylesheet" href="js/editor/summernote-bs4.css">
	
	
</head>

<body class="fixed-nav sticky-footer" id="page-top">
  <!-- Navigation-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-default fixed-top" id="mainNav">
    <a class="navbar-brand" href="index.php"><img src="img/etender.png" data-retina="true" alt="" width="163" height="40"></a>
    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
          <a class="nav-link" href="index.php">
            <i class="fa fa-fw fa-dashboard"></i>
            <span class="nav-link-text">Home</span>
          </a>
        </li>

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Bookings">
          <a class="nav-link" href="index.php?page=companyprofile.php">
            <i class="fa fa-fw fa-user"></i>
            <span class="nav-link-text">Profile</span>
          </a>
        </li>

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Bookings">
          <a class="nav-link" href="index.php?page=currenttenders.php">
            <i class="fa fa-fw fa-files-o"></i>
            <span class="nav-link-text">Current Tenders  <span class="badge badge-pill badge-primary"> <?php echo $invitation_count; ?></span></span>
          </a>
        </li>


        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Bookings">
          <a class="nav-link" href="index.php?page=mytenders.php">
            <i class="fa fa-fw fa-files-o"></i>
            <span class="nav-link-text">My Tenders</span>
          </a>
        </li>

        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tenders">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseProfile" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-files-o"></i>
            <span class="nav-link-text">Tenders</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseProfile">
            <li>
              <a href="index.php?page=addtender.php">New Tenders</a>
            </li>
			<li>
              <a href="index.php?page=tendermanagement.php">My Tenders</a>
            </li>

          

            <li>
              <a href="tenderreports.php">Payment Reports</a>
            </li>
          </ul>
        </li>



        <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Suppliers">
          <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseSuppliers" data-parent="#exampleAccordion">
            <i class="fa fa-fw fa-users"></i>
            <span class="nav-link-text">Suppliers</span>
          </a>
          <ul class="sidenav-second-level collapse" id="collapseSuppliers">
            <li>
              <a href="index.php?page=viewsuppliers.php">View Suppliers</a>
            </li>
			<li>
              <a href="#">Suppliers Management</a>
            </li>

            <li>
              <a href="#">Suppliers Reports</a>
            </li>
          </ul>
        </li>
		<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Messages">
          <a class="nav-link" href="#">
            <i class="fa fa-fw fa-envelope-open"></i>
            <span class="nav-link-text">Other Menu</span>
          </a>
        </li>
		<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Bookings">
          <a class="nav-link" href="#">
            <i class="fa fa-fw fa-calendar-check-o"></i>
            <span class="nav-link-text">Other 2 <span class="badge badge-pill badge-primary">6 New</span></span>
          </a>
        </li>
</ul>
   
      <ul class="navbar-nav ml-auto">
  
      
        <li class="nav-item">
          <a class="nav-link" data-toggle="modal" data-target="#exampleModal">
            <i class="fa fa-fw fa-sign-out"></i>Logout</a>
        </li>
      </ul>
    </div>
  </nav>
  <!-- /Navigation-->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Breadcrumbs-->
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="#">Supplier Logged as</a>
        </li>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($company_name); ?></li>
      </ol>
	  <!-- Icon Cards-->
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-3">
          <div class="card dashboard text-white bg-primary o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-files-o"></i>
              </div>
              <div class="mr-5"><h5>Current Tenders</h5></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="index.php?page=currenttenders.php">
              <span class="float-left">View Details</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
          <div class="card dashboard text-white bg-warning o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-folder"></i>
              </div>
				<div class="mr-5"><h5>My Tenders</h5></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="index.php?page=mytenders.php">
              <span class="float-left">View Details</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
          <div class="card dashboard text-white bg-success o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-file"></i>
              </div>
              <div class="mr-5"><h5>My Bids</h5></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="index.php?page=mybids.php">
              <span class="float-left">View Details</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3">
          <div class="card dashboard text-white bg-danger o-hidden h-100">
            <div class="card-body">
              <div class="card-body-icon">
                <i class="fa fa-fw fa-money"></i>
              </div>
              <div class="mr-5"><h5>Payments </h5></div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="index.php?page=payments.php">
              <span class="float-left">View Details</span>
              <span class="float-right">
                <i class="fa fa-angle-right"></i>
              </span>
            </a>
          </div>
        </div>
		</div>
		<!-- /cards -->
		<h2></h2>
		<div class="box_general padding_bottom">
		
	  <!-- /Page Content-->


    <?php
    $pg = @$_REQUEST['page'];
    if($pg != "" && file_exists(dirname(__FILE__)."/".$pg)){
    require(dirname(__FILE__)."/".$pg);
    }elseif(!file_exists(dirname(__FILE__)."/".$pg))
    include_once(dirname(__FILE__)."/404.php");
    else{
    include_once("home.php");
    }
    ?> 




		</div>
	  </div>
	  <!-- /.container-fluid-->
   	</div>
    <!-- /.container-wrapper-->
    <footer class="sticky-footer">
      <div class="container">
        <div class="text-center">
          <small>Product of Blackscript Solutions | Copyright © e-Tender 2025</small>
        </div>
      </div>
    </footer>
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
      <i class="fa fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <a class="btn btn-primary" href="../logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Page level plugin JavaScript-->
    <script src="vendor/chart.js/Chart.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
	<script src="vendor/jquery.selectbox-0.2.js"></script>
	<script src="vendor/retina-replace.min.js"></script>
	<script src="vendor/jquery.magnific-popup.min.js"></script>

  	<!-- Custom scripts for this page-->
    <script src="js/admin-datatables.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/admin.js"></script>
	<!-- Custom scripts for this page-->
    <script src="js/admin-charts.js"></script>

    	<!-- Custom scripts for this page-->
	<script src="vendor/dropzone.min.js"></script>
	<!-- WYSIWYG Editor -->
	<script src="js/editor/summernote-bs4.min.js"></script>
	<script>
      $('.editor').summernote({
		fontSizes: ['10', '14'],
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic', 'underline', 'clear']],
			['font', ['strikethrough']],
			['fontsize', ['fontsize']],
			['para', ['ul', 'ol', 'paragraph']]
		  ],
        placeholder: 'Write here your description....',
        tabsize: 2,
        height: 200
      });
    </script>
	
</body>
</html>
