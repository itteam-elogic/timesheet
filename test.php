<?php

 $to_email = "laxmikanth@elogictech.com";
 $subject = "Simple Email Testing via PHP";
 $body = "Hello,nn It is a testing email sent by PHP Script";
 $headers = "From: sender\'s email";

 if (mail($to_email, $subject, $body, $headers)) {
   echo "Email successfully sent to $to_email...";
 } else {
   echo "Email sending failed...";
 }

?>