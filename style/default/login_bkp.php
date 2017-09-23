<?php /* STYLE/DEFAULT $Id: login.php,v 1.32 2005/03/31 20:11:15 gregorerhardt Exp $ */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $dPconfig['page_title'];?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=<?php echo isset( $locale_char_set ) ? $locale_char_set : 'UTF-8';?>" />
       	<title><?php echo $dPconfig['company_name'];?> :: dotProject Login</title>
	<meta http-equiv="Pragma" content="no-cache" />
	<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
    <link href="./style/<?php echo $uistyle;?>/bootstrap.css" rel="stylesheet">
    <link href="./style/<?php echo $uistyle;?>/font-awesome.css" rel="stylesheet">
    <link href="./style/<?php echo $uistyle;?>/toolkit-light.css" rel="stylesheet">
<!--    <link href="./style/--><?php //echo $uistyle;?><!--/application.css" rel="stylesheet">-->
    <link href="./style/<?php echo $uistyle;?>/login_new.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="./style/<?php echo $uistyle;?>/main.css" media="all" />
	<style type="text/css" media="all">@import "./style/<?php echo $uistyle;?>/main.css";</style>
	<link rel="shortcut icon" href="./style/<?php echo $uistyle;?>/images/favicon.ico" type="image/ico" />
</head>

<body bgcolor="#ffffff" onload="document.loginform.username.focus();">
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="index.html"><img src="logo.png"></a>
        </div>
    </div>
</div>
<link href="./style/<?php echo $uistyle;?>/application.css" rel="stylesheet">
<?php //please leave action argument empty ?>
<!--form action="./index.php" method="post" name="loginform"-->

<div class="container">
    <div id="login-wraper">
<form method="post" action="<?php echo $loginFromPage; ?>" name="loginform">
<!--<table align="center" border="0" width="500" cellpadding="6" cellspacing="0" class="std" style="background: #fff">-->
<input type="hidden" name="login" value="<?php echo time();?>" />
<input type="hidden" name="lostpass" value="0" />
<input type="hidden" name="redirect" value="<?php echo $redirect;?>" />
    <legend>Sign in to <span class="blue"><?php echo $dPconfig['company_name'];?></span></legend>


    <div class="body">
        <label><?php echo $AppUI->_('Username');?></label>
        <input type="text" class="form-control" name="username" placeholder="Username" required="">

        <label><?php echo $AppUI->_('Password');?></label>
        <input type="password" class="form-control" name="password" placeholder="Password" required="">
    </div>

    <div class="footer">
<!--        <label class="checkbox inline">-->
<!--            <input type="checkbox" id="inlineCheckbox1" value="option1"> Remember me-->
<!--        </label>-->

       <button type="submit" class="btn btn-success"><?php echo $AppUI->_('login');?></button>
<!--        <input type="submit" name="login" value="--><?php //echo $AppUI->_('login');?><!--" class="button" />-->
    </div>

    <!--
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h1><?php echo $dPconfig['company_name'];?></h1>
        </div>
    </div><br/><br/><br/>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <label><?php echo $AppUI->_('Username');?></label>
            <div class="form-group">
                <div class="form-line">
                    <input type="text" class="form-control" name="username" placeholder="Username" required="">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <label><?php echo $AppUI->_('Password');?></label>
            <div class="form-group">
                <div class="form-line">
                    <input type="password" class="form-control" name="password" placeholder="Password" required="">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="text-right">
                <div class="form-group">
                    <div class="form-line">
                        <!--<input type="password" class="form-control" name="password" placeholder="Password" required="">
                        <input type="submit" name="login" class="ce pi ahr" value="<?php echo $AppUI->_('login');?>" class="button" />
                    </div>
                </div>
            </div>
        </div>
    </div> -->
<!--<tr>
	<th colspan="2"><em><?php /*echo $dPconfig['company_name'];*/?></em></th>
</tr>
<tr>
	<td align="right" nowrap><?php /*echo $AppUI->_('Username');*/?>:</td>
	<td align="left" nowrap><input type="text" size="25" maxlength="20" name="username" class="text" /></td>
</tr>
<tr>
	<td align="right" nowrap><?php /*echo $AppUI->_('Password');*/?>:</td>
	<td align="left" nowrap><input type="password" size="25" maxlength="32" name="password" class="text" /></td>
</tr>
<tr>
	<td colspan ="2" align="center" valign="bottom" nowrap><input type="submit" name="login" class="ce pi ahr" value="<?php /*echo $AppUI->_('login');*/?>" class="button" /></td>
</tr>
<tr>
	<td colspan="2" align="center"><a href="#" onclick="f=document.loginform;f.lostpass.value=1;f.submit();"><?php /*echo $AppUI->_('forgotPassword');*/?></a></td>
</tr>
<tr>
    <td colspan="2" align="center"><a href="#">
            <?php
/*            echo '<span class="error">'.$AppUI->getMsg().'</span>';

            /*$msg = '';
            $msg .=  phpversion() < '4.1' ? '<br /><span class="warning">WARNING: contacts manager is NOT SUPPORT for this PHP Version ('.phpversion().')</span>' : '';
            $msg .= function_exists( 'mysql_pconnect' ) ? '': '<br /><span class="warning">WARNING: PHP may not be compiled with MySQL support.  This will prevent proper operation of dotProject.  Please check you system setup.</span>';
            */
            //echo $msg;
            ?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center"><a href="#">
            <center><?php /*echo "* ".$AppUI->_("You must have cookies enabled in your browser"); */?></center>
    </td>
</tr>
</table>-->
</form>
</div>
</div>
<div align="center">

</div>
<footer class="white navbar-fixed-bottom">
    Don't have an account yet? <a href="register.html" class="btn btn-black">Register</a>
</footer>
</body>
</html>