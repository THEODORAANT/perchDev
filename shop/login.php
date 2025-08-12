<?php include('../perch/runtime.php');?>




<body>




<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-lg-12">
            <h1 class="account text-uppercase text-center"> Login </h1>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 mb-2">
            <img src="images/line.png" width="60" height="8" >
        </div>
        <div class="col-lg-12 text-center">
            <h4 class="h4-login">New customer? Please create account at the <a href="/register.php" class="login">create account.</a></h4>
        </div>
        <div class="col-lg-8">


        <?php perch_members_login_form();
            perch_member_form('register.html');
         ?>

        </div>
    </div>
</div>

<div class="container-fluid background-img mt-10">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h1 class="sub-h1 text-uppercase text-center">Don't Miss Out</h1>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 line2 mb-4 mt-2">
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <h6 class="sub-h6 text-center">Sign up for our detailed newsletter</h6>
            </div>





        </div>
    </div>
</div>



