<?php
/* STYLE/DEFAULT $Id: header.php,v 1.38.4.2 2006/04/18 15:11:10 pedroix Exp $ */
$dialog = dPgetParam ( $_GET, 'dialog', 0 );
if ($dialog)
	$page_title = '';
else
	$page_title = ($dPconfig ['page_title'] == 'dotProject') ? $dPconfig ['page_title'] . '&nbsp;' . $AppUI->getVersion () : $dPconfig ['page_title'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:display="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Cache-Control" content="no-store" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="Description" content="dotProject Default Style" />
<meta name="Version" content="<?php echo @$AppUI->getVersion();?>" />
<meta http-equiv="Content-Type"
	content="text/html;charset=<?php echo /*isset( $locale_char_set ) ? $locale_char_set :*/ 'ISO-8859-1';?>" />
<title><?php echo @dPgetConfig( 'page_title' );?></title>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic" rel="stylesheet">
<link rel="stylesheet" type="text/css"
	href="./style/<?php echo $uistyle;?>/main.css" media="all" />
<style type="text/css" media="all">
@import "./style/<?php echo $uistyle;?>/main.css";
</style>
<link href="./style/<?php echo $uistyle;?>/bootstrap.css" rel="stylesheet">
<link href="./style/<?php echo $uistyle;?>/font-awesome.css" rel="stylesheet">
<link href="./style/<?php echo $uistyle;?>/toolkit-light.css" rel="stylesheet">
<link href="./style/<?php echo $uistyle;?>/application.css" rel="stylesheet">
<!-- added by rpalexis -->
<link href="./style/<?php echo $uistyle;?>/rpaTabsStyles.css" rel="stylesheet">
<link href="./style/<?php echo $uistyle;?>/forPrint.css" rel="stylesheet" media="print" />
<link href="./style/<?php echo $uistyle;?>/font-awesome.min.css" rel="stylesheet">
<!-- added by rpalexis -->
<!-- <script type="text/javascript" src="/style/default/jquery-1.7.1.min.js"></script> -->

<!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script> -->


<link rel="shortcut icon"
	href="./style/<?php echo $uistyle;?>/images/favicon.ico"
	type="image/ico" />
<link rel="stylesheet" href="./style/jquery-ui.css" type="text/css"media="all" />
<link rel="stylesheet" href="./style/jquery.multiselect.css" type="text/css"media="all" />
<link rel="stylesheet" href="./style/<?php echo $uistyle;?>/jquery.contextMenu.css" type="text/css"media="all" />
<!-- <script type="text/javascript" src="./js/1jquery.best.js"></script>
	<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./js/jquery.multiselect.min.js"></script>
	<script type="text/javascript" src="./js/jquery.form.js"></script>
	<script type="text/javascript" src="./js/jquery.formparams.js"></script>
	<script type="text/javascript" src="./js/jquery.checkboxtree.min.js"></script>
	<script type="text/javascript" src="./js/base.min.js"></script>	 -->
	<?php @$AppUI->loadCSS(); ?>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> -->
<link href="./style/<?php echo $uistyle;?>/sweetalert.css" rel="stylesheet">
<script type="text/javascript"
	src="/style/<?php echo $uistyle;?>/html2canvas.js"></script>
    <!--<script type="text/javascript"
            src="/style/<?php echo $uistyle;?>/jquery-1.7.1.min.js"></script>-->
<!-- <script type="text/javascript" src="/modules/outputs/gchart.js"></script> -->

<?php
if($m=='outputs'){
	?>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		var myMapsApiKey = 'AIzaSyCuH-7lvQF0_76dCUFIOqAQ8wSXLJ4uiwA';
		google.charts.load('current', {mapsApiKey: myMapsApiKey, packages: ['corechart','bar','geomap']});
	</script>

	<style type="text/css">
		.showMain {
			cursor:pointer;
		}
	</style>

	<?php
}
?>
<style>

body {
	/*font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	font-size: 12px;
	line-height: 1.428571429;*/
    /*font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 0.9rem;
    font-weight: 300;
    line-height: 1.5;
    color: #cfd2da;*/

    font-family: "Roboto", "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 1.3rem;
    font-weight: 300;
    line-height: 1.5;
    color: #000;

    font-weight: 300;
    letter-spacing: 0;
    background: rgba(0,0,0,.1);;
}

select#soflow, select#soflow-color {
	-webkit-appearance: button;
	-webkit-border-radius: 2px;
	-webkit-box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
	-webkit-padding-end: 20px;
	-webkit-padding-start: 2px;
	-webkit-user-select: none;
	background-image: url(images/down.png),
		-webkit-linear-gradient(#A6D2F5, #A6D2F5 40%, #A6D2F5);
	background-position: 97% center;
	background-repeat: no-repeat;
	border: 1px solid #AAA;
	color: #555;
	font-size: inherit;
	overflow: hidden;
	padding: 5px 10px;
	text-overflow: ellipsis;
	white-space: nowrap;
	width: 300px;
}

select#soflow-color {
	color: #fff;
	background-image: url(images/down.png),
		-webkit-linear-gradient(#A6D2F5, #A6D2F5 40%, #A6D2F5);
	background-color: #A6D2F5;
	/*-webkit-border-radius: 20px;
   -moz-border-radius: 20px;*/
	/*border-radius: 20px;*/
	padding-left: 15px;
}

.newbanner {
	height: 30px;
	background: white;
	box-shadow: 0 0 10px rgba(0, 0, 0, .3);
	-moz-box-shadow: 0 0 10px rgba(0, 0, 0, .3);
	-webkit-box-shadow: 0 0 10px rgba(0, 0, 0, .3);
	border: 5px solid white;
}


	/*added by rpalexis*/
.nav > li > a.menu:hover, .nav > li > a.menu:focus {
	text-decoration: none;
	background-color: rgba(238, 238, 238, 0)
}
</style>


<style type="text/css">
.form-style-2 {
	max-width: 500px;
	padding: 20px 12px 10px 20px;
	font: 13px Arial, Helvetica, sans-serif;
}

.form-style-2-heading {
	font-weight: bold;
	font-style: italic;
	border-bottom: 2px solid #ddd;
	margin-bottom: 20px;
	font-size: 15px;
	padding-bottom: 3px;
}

.form-style-2 label {
	display: block;
	margin: 0px 0px 15px 0px;
}

.form-style-2 label>span {
	width: 100px;
	font-weight: bold;
	float: left;
	padding-top: 8px;
	padding-right: 5px;
}

.form-style-2 span.required {
	color: red;
}

.form-style-2 .tel-number-field {
	width: 40px;
	text-align: center;
}

.form-style-2 input.input-field {
	width: 48%;
}

.form-style-2 input.input-field, .form-style-2 .tel-number-field,
	.form-style-2 .textarea-field, .form-style-2 .select-field {
	box-sizing: border-box;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	border: 1px solid #C2C2C2;
	box-shadow: 1px 1px 4px #EBEBEB;
	-moz-box-shadow: 1px 1px 4px #EBEBEB;
	-webkit-box-shadow: 1px 1px 4px #EBEBEB;
	border-radius: 3px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	padding: 7px;
	outline: none;
}

.form-style-2 .input-field:focus, .form-style-2 .tel-number-field:focus,
	.form-style-2 .textarea-field:focus, .form-style-2 .select-field:focus {
	border: 1px solid #0C0;
}

.form-style-2 .textarea-field {
	height: 100px;
	width: 55%;
}

.form-style-2 input[type=submit], .form-style-2 input[type=button] {
	border: none;
	padding: 8px 15px 8px 15px;
	background: #FF8500;
	color: #fff;
	box-shadow: 1px 1px 4px #DADADA;
	-moz-box-shadow: 1px 1px 4px #DADADA;
	-webkit-box-shadow: 1px 1px 4px #DADADA;
	border-radius: 3px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
}

.form-style-2 input[type=submit]:hover, .form-style-2 input[type=button]:hover {
	background: #EA7B00;
	color: #fff;
}
.modal-header {
    padding: 2px 16px;
    background-color: #354c8c;
    color: white;
}

.modal-footer {
    padding: 2px 16px;
    background-color: #354c8c;
    color: white;
}

</style>

	<?php if($m=='outputs'){?>

<style type="text/css">
	.modal {
		display: none; /* Hidden by default */
		position: fixed; /* Stay in place */
		z-index: 100; /* Sit on top */
		padding-top: 100px; /* Location of the box */
		left: 0;
		top: 0;
		width: 100%; /* Full width */
		height: 100%; /* Full height */
		overflow: auto; /* Enable scroll if needed */
		background-color: rgb(0,0,0); /* Fallback color */
		background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
	}

	/* Modal Content */
	.modal-content {
		position: relative;
		background-color: #fefefe;
		margin: auto;
		padding: 0;
		border: 1px solid #888;
		width: 80%;
		box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
		-webkit-animation-name: animatetop;
		-webkit-animation-duration: 0.4s;
		animation-name: animatetop;
		animation-duration: 0.4s
	}

	/* The Close Button */
	.close {
		color: white;
		float: right;
		font-size: 28px;
		font-weight: bold;
	}

	.close:hover,
	.close:focus {
		color: #000;
		text-decoration: none;
		cursor: pointer;
	}

	.modal-header {
		padding: 2px 16px;
		background-color: #354c8c;
		color: white;
	}

	.modal-body {padding: 2px 16px;}

	.modal-footer {
		padding: 2px 16px;
		background-color: #354c8c;
		color: white;
	}

</style>

	<?php }?>



</head>

<body onload="this.focus();" class="ayy>
	<div style="display: none;">
		<!--<img id="calImg" src="images/calendar.png" alt="Popup" class="trigger">-->

	</div>
    <?php
    if (! $dialog) {
        $nav = $AppUI->getMenuModules ();
        $perms = & $AppUI->acl ();
        ?>
    <nav class="ci rd aem ri vr app-navbar">
        <button
                class="qx ra axz"
                type="button"
                data-toggle="collapse"
                data-target="#navbarResponsive"
                aria-controls="navbarResponsive"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="qy"></span>
        </button>
        <!--<span class="bv bik ayz"></span>-->
        <?php /*echo "<a style='color: white;font-size: 2rem;font-weight:500' href='{$dPconfig['base_url']}' class='qv'>$page_title</a>";*/?>


        <div class="collapse rc aij" id="navbarResponsive">
            <ul class="nav navbar-nav" style="font-size: 1.5rem;">
                <li class="qp">
                    <?php echo "<a class='menu'  style='margin-left: -5px;margin-right: 2rem;color: white;font-size: 2rem;font-weight:500' href='{$dPconfig['base_url']}' class='qv'>$page_title</a>";?>
                </li>
                <li class="qp<?php if ($m == 'dashboard') echo ' active';?>">
                    <?php echo '<a class="qn menu" href="?m=dashboard#tab-1_1"><b>' . $AppUI->_ ( 'Dashboard' ) . '</b></a>';?>
                </li>
                <li class="qp<?php if ($m == 'ownpage') echo ' active';?>">
                    <?php echo '<a class="qn menu" href="?m=ownpage"><b>' . $AppUI->_ ( 'My Page' ) . '</b></a>';?>
                </li>
                <li class="qp<?php if ($m == 'projects' || $m == 'tasks') echo ' active';?>">
                    <?php echo '<a class="qn menu" href="?m=projects"><b>' . $AppUI->_ ( 'Projects' ) . '</b></a>';?>
                </li>

<!--				Adding temporary link for analyser develop-->
<!--				<li class="qp--><?php //if ($m == 'analyser') echo ' active';?><!--">-->
<!--					--><?php //echo '<a class="qn menu" href="?m=analyser"><b>Analyser_2</b></a>';?>
<!--				</li>-->
<!--				Adding temporary link for analyser develop-->
                <?php
                    foreach ( $nav as $module ) {
                        if ($perms->checkModule ( $module ['mod_directory'], 'access' )) {

                            if ($module ['mod_directory'] != 'clients'){
                                if ($m == $module ['mod_directory']) $active = ' active';
                                echo '<li class="qp'.$active.'"><a class="qn menu" href="?m=' . $module ['mod_directory'] . '"><b>' . $AppUI->_ ( $module ['mod_ui_name'] ) . '</b></a></li>';
                                $active = '';
                            }
                        }
                    }
                ?>
            </ul>
            <div class="ox axy ail nav-left-cus">
            <?php
            if ($perms->checkModule ( 'calendar', 'access' )) {
                $now = new CDate ();
                ?>
                <a href="./index.php?m=calendar"><?php echo $AppUI->_('Calendar');?></a>&nbsp;<b><a href="./index.php?m=tasks&a=todo"><?php echo $AppUI->_('Todo');?></a></b>
            <?php } ?>
            <?php echo dPcontextHelp( 'Help' );?>
            <a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a>
            </div>

            <!--<form class="ox axy ail" method=GET action="./index.php">
                <input type="hidden" name="m" value="projects" />
                <input type="hidden" name="a" value="addedit" />
                <input class="form-control" type="submit" data-action="grow" value="<?php /*echo $AppUI->__("New Project")*/?>" placeholder="Search" style="font-weight: 500">
            </form>-->
        </div>
    </nav>
    <!--<div class="">

    </div>-->

    <?php }?>

<!--<table width="100%" cellpadding="0" cellspacing="0" border="0">-->
<!-- <tr>
	<td nowrap="nowrap"><table width='100%' cellpadding=3 cellspacing=0 border=0><tr>
	<th background="style/<?php //echo $uistyle;?>/images/titlegrad.jpg" class="banner" align="left"><strong>
	<?php
	// echo "<a style='color: white' href='{$dPconfig['base_url']}'>$page_title</a>";
	?></strong></th>
	</tr></table></td>
</tr>-->


	<!--<table width="100%" cellspacing="0" cellpadding="4" border="0">
		<tr>
			<td valign="top" align="left" width="98%">-->
<div class="dh azs">
    <!--<div class="brv">
        <div class="qb brz">
            <div class="ayt brg text-right" style="width: 250px">
                <?php
                if ($perms->checkModule ( 'calendar', 'access' )) {
                    $now = new CDate ();
                    ?>
                    <a href="./index.php?m=calendar"><?php echo $AppUI->_('Calendar');?></a>
                    | <b><a href="./index.php?m=tasks&a=todo"><?php echo $AppUI->_('Todo');?></a></b> |

                <?php } ?>
                <?php echo dPcontextHelp( 'Help' );?> |
                <a href="./index.php?logout=-1"><?php echo $AppUI->_('Logout');?></a>
            </div>
        </div>
    </div>-->
    <div id="msg_note_box_container" style="display: none"></div>
<?php
echo $AppUI->getMsg ();
?>
