<?php 
require "../helperFile/helper.php";
 
 $newOtp = rand(100000, 999999);
 //store the email in the session
 $_SESSION['otp'] = $newOtp;
 $_SESSION['validate'] = false;
 
 // Send email
 $m = get_mail();
 $m->addAddress("jiazhehello@gmail.com");
 $m->Subject = "Email Verification";
 $m->Body = "Please click the link to ";
 $m->send();

 temp('info', 'A verification email is send. Please check your email.');

?>