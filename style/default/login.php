<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $dPconfig['page_title'];?></title>
    <meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
    <title><?php echo $dPconfig['company_name'];?> :: dotProject Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Le styles -->
    <link href="./style/<?php echo $uistyle;?>/login_bootstrap.min.css" rel="stylesheet">
    <link href="./style/<?php echo $uistyle;?>/login_bootstrap-responsive.min.css" rel="stylesheet">

    <link href="./style/<?php echo $uistyle;?>/login_new.css" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le favicon -->
    <link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />

</head>

<body>

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
<!--            <a id="brand" class="brand" href="./">--><?php //echo $dPconfig['company_name'];?><!--</a>-->
        </div>
    </div>
</div>

<div class="container">

    <div id="login-wraper">
        <form class="form login-form" method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
            <input type="hidden" name="login" value="<?php echo time();?>" />
            <input type="hidden" name="lostpass" value="0" />
            <input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
            <legend><span class="blue"><?php echo $dPconfig['company_name'];?></span></legend>

            <div class="body">
                <label class="Thelabel"><?php echo $AppUI->_('Username');?></label>
                <input type="text" class="form-control" name="username" placeholder="Username" required="">


                <label class="Thelabel"><?php echo $AppUI->_('Password');?></label>
                <input type="password" class="form-control" name="password" placeholder="Password" required="">

            </div>

            <div class="footer">
<!--                <label class="checkbox inline">-->
<!--                    <input type="checkbox" id="inlineCheckbox1" value="option1"> Remember me-->
<!--                </label>-->

                <button type="submit" class="btn btn-primary"><?php echo $AppUI->_('login');?></button>
            </div>

        </form>
    </div>

</div>

<footer class="white navbar-fixed-bottom">
<img style="margin-top: -40px;" src="<?php echo DP_BASE_URL;?>/style/default/btm-logos_2.png" />
</footer>


<!-- Le javascript
================================================== -->
<!--<!-- Placed at the end of the document so the pages load faster -->
<!--<script src="js/jquery.js"></script>-->
<!--<script src="js/bootstrap.js"></script>-->
<!--<script src="js/backstretch.min.js"></script>-->
<!--<script src="js/typica-login.js"></script>-->

</body>
</html>
