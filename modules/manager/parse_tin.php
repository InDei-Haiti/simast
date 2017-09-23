<?php
require_once ('doimfile.php');

$mode = trim ( $_GET ['mode'] );

if ($mode === 'importfile') {
	echo putInList ();
} elseif ($mode === 'getlist') {
	echo buildListTins ();
} elseif ($mode === 'proceed') {
	importProceed ();
} elseif ($mode === 'undo') {
	$lirow = ( int ) $_GET ['row_id'];
	if ($lirow > 0) {
		$q = new DBQuery ();
		$q->addTable ( 'ltp_inbox', 'li' );
		$q->addQuery ( 'client_id' );
		$q->addJoin ( 'clients', 'c', 'c.client_adm_no=li.client_adm_no' );
		$q->addWhere ( 'li.id="' . $lirow . '"' );
		$clid = $q->loadResult ();
		
		if ($clid > 0) {
			
			require_once ('doimfile.php');
			
			global $firstPlan;
			foreach ( $firstPlan as $table => $tvars ) {
				$sql = 'delete from ' . $table . ' where ' . $tvars ['client'] . '="' . $clid . '"';
				$res = mysql_query ( $sql );
			}
			$sql = 'delete from clients where client_id="' . $clid . '"';
			mysql_query ( $sql );
			$sql = 'update ltp_inbox set ltp_status="pending" where id="' . $lirow . '"';
			mysql_query ( $sql );
			
			echo "ok";
		}
	}
	return;

}
?>