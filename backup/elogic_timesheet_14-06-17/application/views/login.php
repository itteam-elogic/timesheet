<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<!--<link rel="shortcut icon" href="../images/favicon.png" type="image/png">-->
<title>eLogic Timesheet</title>
<link rel="stylesheet" href="<?php echo HTTP_LIB_PATH;?>fontawesome/css/font-awesome.css">
<link rel="stylesheet" href="<?php echo HTTP_CSS_PATH;?>quirk.css">
<script src="<?php echo HTTP_LIB_PATH;?>modernizr/modernizr.js"></script>
<?php echo base_url(); ?>
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
  <script src="../lib/html5shiv/html5shiv.js"></script>
  <script src="../lib/respond/respond.src.js"></script>
  <![endif]-->
</head>
<body class="signwrapper">
<div class="sign-overlay"></div>
<div class="signpanel"></div>
<div class="panel signin">
  <div class="panel-heading">
    <h1>eLogic Timesheet</h1>
  </div>
  <div class="panel-body">
    <form action="<?php echo base_url(); ?>welcome" method="post" enctype="multipart/form-data">
      <div class="form-group mb10">
        <div class="input-group"> <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
          <input type="text" class="form-control" placeholder="Enter Username">
        </div>
      </div>
      <div class="form-group nomargin">
        <div class="input-group"> <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
          <input type="text" class="form-control" placeholder="Enter Password">
        </div>
      </div>
      <div>&nbsp;
        <!--<a href="" class="forgot">Forgot password?</a>-->
      </div>
      <div>&nbsp;
        <!--<a href="" class="forgot">Forgot password?</a>-->
      </div>
      <div class="form-group">
        <button class="btn btn-success btn-quirk btn-block">Log In</button>
      </div>
    </form>
  </div>
</div>
<!-- panel -->
</body>
</html>
