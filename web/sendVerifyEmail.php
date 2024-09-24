<?php
require "../helperFile/helper.php";


if (isset($_POST['email'])) {
    // Get the input value sent via AJAX
    $email = req('email') ?? '';
    if (checkGmail($email) != null) {
        //if email correct then only send
        $newOtp = rand(100000, 999999);

        //store the otp in the session
        $_SESSION['otp'] = $newOtp;
        $_SESSION['validate'] = false;

        $clickLink = "http://localhost:8000/web/sendVerifyEmail.php?otp=" . $newOtp;
        // Send email
        $m = get_mail();
        $m->addAddress("$email");
        $m->Subject = "Email Verification";
        $m->Body = "Please click the link to verify your email: " . $clickLink;
        $m->send();

        temp('info', 'A verification email is send. Please check your email.');
        redirect("member_registration.php");
    } else {
        temp('info', "error: " . checkGmail($email));
        redirect("member_registration.php");
    }
} else {
    $email = req('input') ?? '';
    temp('info', 'Unsuccess request' . $email);
    redirect("member_registration.php");
}
