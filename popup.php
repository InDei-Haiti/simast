<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Save Filter</title>
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.style2 {
	font-size: 14px;
	font-weight: bold;
}
-->
</style>
<script language="javascript">
	function set_opener_value()
	{
		//parent.window.window.opener.document.frmFilter.query_name.value = getElementById('query_name').value;
		window.opener.document.frmFilter.query_name.value=document.getElementById('query_name').value;
		window.opener.document.getElementById('query_details').value=document.getElementById('query_details').value;
		window.opener.document.frmFilter.submit();
		window.close();
	}
	
</script>
<link rel="stylesheet" type="text/css" href="./style/default/main.css" media="all" />

</head>

<body style="margin:10px;">
<table width="100%" border="0"  cellpadding="2" cellspacing="0" style="background:#d6ebff;">
				<tr>
				  <td colspan="2" class="style1"><div align="center"><span class="style2">Save Query</span></div></td>
  </tr>
				<tr>
				  <td class="style1">&nbsp;</td>
				  <td>&nbsp;</td>
  </tr>
				<tr>
					<td width="5%" class="style1">Name</td>
				  <td width="95%"><input type="text" value="" style="width: 300px;" name="query_name" id="query_name"></td>
				</tr>
				
				<tr>
					<td valign="top" class="style1">Details</td>
					<td><textarea id="query_details" style="width:300px; height: 100px;" name="query_details"></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="button" name="save_btn"  class="button" onclick="set_opener_value();" value="save" />
						<input type="button" name="close_btn"  class="button" onclick="window.close();" value="close" /></td>
				</tr>
				</table>
</body>
</html>
