<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- CSS-->
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_CSS_PATH; ?>main.css">
<script src="<?php echo HTTP_JS_PATH; ?>jquery-2.1.4.min.js"></script>
<script src="<?php echo HTTP_JS_PATH; ?>jquery.validate.min.js"></script>
<title>eLogic Timesheet</title>
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries-->
<!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->
</head>
<body>
<section class="material-half-bg">
  <div class="cover"></div>
</section>
<section class="login-content">
  <div class="logo">
    <h1><img src="<?php echo HTTP_IMAGES_PATH; ?>main_logo.png" alt="logo"></h1>
  </div>
  <div class="login-box">
    <form class="login-form" method="post" name="timesheet_login" id="timesheet_login" action="<?php echo base_url('home/login');?>">
      <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>Log In</h3>
      <?php if (!empty($error_message)) { echo '<label class="error">'.$error_message.'</label>'; } ?>
	  
	  <div class="form-group">
        <label class="control-label">USERNAME</label>
        <input class="form-control" type="text" name="username" id="username" style="text-transform: lowercase;" placeholder="Please enter username" autofocus onkeypress="this.value = this.value.toLowerCase();">
      </div>
      <div class="form-group">
        <label class="control-label">PASSWORD</label>
        <input class="form-control" type="password" name="password" id="password"  style="text-transform: lowercase;" placeholder="Please enter password" onkeypress="this.value = this.value.toLowerCase();">
      </div>
      <div class="form-group">
        <div class="utility">
          <div class="animated-checkbox">
            <label class="semibold-text">
            <input type="checkbox">
            <span class="label-text">Stay Signed in</span> </label>
          </div>
        </div>
      </div>
      <div class="form-group btn-container">
        <button class="btn btn-primary btn-block">LOGIN <i class="fa fa-sign-in fa-lg"></i></button>
      </div>
    </form>
  </div>
</section>
<!-- Login form validation -->
<script type="text/javascript" language="javascript">
		// Wait for the DOM to be ready
$(function() {
  $("form[name='timesheet_login']").validate({
    rules: {
      username: {
        required: true,
      },
      password: {
        required: true,
        //minlength: 5
      }
    },
    messages: {
      password: {
        required: "Please enter password",
        //minlength: "Your password must be at least 5 characters long"
      },
      username: "Please enter username"
    },
     submitHandler: function(form) {
      form.submit();
    }
  });
});

$('input').keyup(function(){
    this.value = this.value.toLowerCase();
});


</script>
<!-- Login form validation -->
</body>
<script src="<?php echo HTTP_JS_PATH; ?>essential-plugins.js"></script>
<script src="<?php echo HTTP_JS_PATH; ?>bootstrap.min.js"></script>
<script src="<?php echo HTTP_JS_PATH; ?>plugins/pace.min.js"></script>
<script src="<?php echo HTTP_JS_PATH; ?>main.js"></script>
</html>
