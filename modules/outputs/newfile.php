<?php
		//$q1->addTable ( "clients" );
		foreach ( $clients as $clid ) {
			$lrow = array ();
			foreach ( $lpost as $key => $var ) {
				$q1 = new DBQuery ( );
				$vname = findkey ( $key, $tkeys );
				if ($vname != '' && $key != 'clients') {
					for($z = 0; $z < count ( $var ); $z ++) {
						$decoder [] = array ('tar' => $key, 'var' => $var [$z] );
					}
					$marker = true;
					$header = array_merge ( $header, getTitles ( $var, $fielder [$vname] ['list']->getList () ) );
					$tstr = join ( ',', $var );
					/*if(strlen($tstr) > 0 ){
					$tstr=mysql_real_escape_string($titles[$vname]['uid'].'.'.str_replace(',',','.$titles[$vname]['uid'].'.',$tstr));
					$q1->addQuery($tstr);
				}				
				if($vname == 'activity'){
					//$q1->addQuery();
					$q1->addJoin('activity_clients','acli','acli.activity_clients_client_id=client_id');
					$q1->addTable('activity','tb9');
					//$q1->addWhere('acli.activity_clients_activity_id=tb9.');
					$q1->addWhere('tb9.activity_id=acli.activity_clients_activity_id');
				}else{
					$q1->addJoin($titles[$vname]['db'],$titles[$vname]['uid'],'client_id='.$titles[$vname]['uid'].'.'.$titles[$vname]['client']);				
				}*/
					$q1->addWhere ( $titles [$vname] ['client'] . '="' . $clid [0] . '"' );
					if($_POST['vis_sel'] == 'last'){
						$q1->setLimit(1);
						$q1->addOrder($titles [$vname]['date'].' desc');
					}
					if($starter > 0){
						$q1->addWhere($titles[$vname]['date'] . '>= '.$starter);
					}
					if($ender > 0){
						$q1->addWhere($titles[$vname]['date'] . '<= '.$ender);
					}
					if (strlen ( $tstr ) > 0) {
						//$tstr = mysql_real_escape_string ( $titles [$vname] ['uid'] . '.' . str_replace ( ',', ',' . $titles [$vname] ['uid'] . '.', $tstr ) );
						$q1->addQuery ( $tstr );
						$q1->addTable ( $titles [$vname] ['db'] );
						$bar = $q1->loadList ();
						if(count($bar) == 0){
							for($xi=0;$xi < count($var);$xi++){
								$bar[$var[$xi]]='-';
							}
						}elseif($_POST['vis_sel'] == 'last'){
							$bar=$bar[0];
						}
						$lrow = array_merge ( $lrow, $bar );
					}
				
				}
				unset ( $vname );
			}
			$bigtar [$clid[0]] = $lrow;
			unset($lrow);
			unset($q1);
		}