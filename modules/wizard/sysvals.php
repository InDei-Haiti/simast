<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 09.05.11
 * Time: 15:15
 */

function protectSort($a){
	$b=array();
	$ind=0;
	if(count($a) > 0){
		foreach($a as $key => $val){
			$b[$ind++] = array("key"=>" ".$key, "val" => $val);
		}
	}
	return $b;
}

switch ($_GET['mode']) {
	case 'insval':
		if(strlen($_POST['nsval']) > 0) {
			$nvals=json_decode(stripslashes($_POST['nsval']),true);
			$opts=array();
			$ind=1;
			$childOpts=array();
			$childCase = false;
			$inChild = false;
			$childCount=1;
			$maxChild=1;
			$ph=0;
			$lborder=0;
			foreach($nvals['options'] as $nid => $pv){
				$akeys = array_keys($pv);
				if(is_numeric($akeys[0])){
					$upv = $pv;
					$inChild = false;
				}else{
					$upv = $pv['parent'];
					$childCase = true;
					$inChild=true;
				}
				if(trim($upv[0]) === '' || !$upv[0]){
					if($nvals['start']==1){
						$vid = $nid+1;
					}else{
						$vid = $nid;
					}
					
				}else{
					$vid=$upv[0];
				}
				$opts[]=$vid.'|'.$upv[1];
        
				if($inChild === true){
					$prestr = $vid.'<#>';
					foreach ($pv['child'] as $pcid => $pcv){
						if(trim($pcv[0]) === '' || !$pcv[0]){
							//$vcid = $pcid+1;
							//$vcid = $maxChild;
							$vcid = '#'.$ph++.'#';
						}else{
							$vcid=$pcv[0];
							//$vcid = $childCount++;
							$lborder = $vcid;
						}
						$childOpts[]=$prestr.$vcid.'|'.$pcv[1];
						$prestr='';
						if(($vcid + 1) > $maxChild)$maxChild=$vcid+1;
					}
				}
			}
			//var_dump($opts);
			//exit;
			if($nvals['scope'] === 'local'){
				if($nvals['id'] == 0){
					/*$sql = "insert into sysvals (sysval_title,sysval_value,sysval_key_id)
					VALUES ('".mysql_real_escape_string($nvals['name'])."',
					'".mysql_real_escape_string(join("\n",$opts))."','1')";*/
					$sql='insert into svsets (title,vtype,level,touch,options) values (
			        "'.mysql_real_escape_string($nvals['name']).'",
					"'.$nvals['vtype'].'",
					"'.$nvals['level'].'",now(),
					"'.mysql_real_escape_string(join("\n",$opts)).'")';
				}else{
					/*$sql= 'update sysvals set sysval_title="'.mysql_real_escape_string($nvals['name']).'",
					sysval_value="'.mysql_real_escape_string(join("\n",$opts)).'" where sysval_id="'.(int)$nvals['id'].'"';*/
					$sql='update svsets set title="'.mysql_real_escape_string($nvals['name']).'",
						options="'.mysql_real_escape_string(join("\n",$opts)).'",

						vtype="'.$nvals['vtype'].'",
						level="'.$nvals['level'].'",
						touch=now()
						where id="'.(int)$nvals['id'].'"';
				}
			}else{
				//case for sysvals
				//for now no adding of sysvals
				/*if($nvals['id'] == 0){
					/*$sql = "insert into sysvals (sysval_title,sysval_value,sysval_key_id)
					VALUES ('".mysql_real_escape_string($nvals['name'])."',
					'".mysql_real_escape_string(join("\n",$opts))."','1')";*/
					//$sql='insert into svsets (title,vtype,level,touch,options) values (
			        //"'.mysql_real_escape_string($nvals['name']).'",
					//"'.$nvals['vtype'].'",
					//"'.$nvals['level'].'",now(),
					//"'.mysql_real_escape_string(join("\n",$opts)).'")';
				//}else*/
					if($nvals['id'] > 0){
						$sql0= 'update sysvals set sysval_title="%s",
									sysval_value="%s" where sysval_id="%d"';
						$sql = sprintf($sql0,mysql_real_escape_string($nvals['name']),
							mysql_real_escape_string(join("\n",$opts)),
							(int)$nvals['id']
						);
				}
			}
			$res=mysql_query($sql);
			if($res){
				$res='ok';
				if($nvals['id'] == 0){
					$res=mysql_insert_id();
					$npar = ((int)$nvals['parent'] > 0 ? (int)$nvals['parent'] : $res);
					$sql = 'update svsets set parent="'.$npar.'" where id="'.$res.'"';
					$nres=mysql_query($sql);

					if($nvals['sys_old'] != ''){
						$sql='update sysvals set sysval_tport="1" where sysval_title="'.mysql_real_escape_string($nvals['sys_old']).'"';
						$ures=mysql_query($sql);
					}
				}else{
					$npar = $nvals['id'];
				}

				if($childCase === true){
					if(isset($nvals['child'])){
						$tosql=join("\n",$childOpts);
						if((int)$nvals['child']['id'] > 0){
							if($ph > 0){
								for($ih = 0; $ih < $ph; $ih ++){
									$tosql=str_replace("#".$ih.'#',$maxChild++,$tosql);
								}
							}
							$sql='update svsets set title="'.mysql_real_escape_string($nvals['child']['name']).'",
						options="'.mysql_real_escape_string($tosql).'",
						parent="'.(int)$npar.'",
						vtype="'.$nvals['child']['type'].'",
						level="'.$nvals['level'].'",
						touch=now()
						where id="'.(int)$nvals['child']['id'].'"';
						}else{
							$lborder = ($lborder ? $lborder : 1);
							for($ih = 0; $ih < $ph; $ih ++){
								$tosql=str_replace("#".$ih.'#',$lborder++,$tosql);
							}
							$sql='insert into svsets (title,parent,vtype,level,touch,options) values (
				        "'.mysql_real_escape_string($nvals['child']['name']).'",
				        "'.(int)$npar.'",
						"'.$nvals['child']['type'].'",
						"'.$nvals['level'].'",now(),
						"'.mysql_real_escape_string($tosql).'")';
						}
						$childRes = mysql_query($sql);
					}
				}else{
					$childRes = true;
				}
				if($childRes === true){
					echo $res;
				}
			}else{
				echo 'fail';
			}
			return false;
		}
		break;

	case 'getSV':
		if($_GET['sval']!='') {
			$ztype=trim($_GET['stype']);
			$dsysval=mysql_real_escape_string($_GET['sval']);
			/*$wz = new Wizard();
			$t = $wz->getValues($ztype,$dsysval,false,true);*/
			$t= dPgetSysVal($dsysval);

			if(is_array($t) && count($t) > 0){
				echo json_encode(protectSort($t));
			}else{
				echo 'fail';
			}
			return;
		}
		break;
	case  'getNSet':
		if($_GET['sval'] != '') {
			$ztype=trim($_GET['stype']);
			if((int)$_GET['wid'] === 0){
				$dsysval=mysql_real_escape_string($_GET['sval']);
			}elseif((int)$_GET['wid'] === 1){
				$did  = (int)$_GET['sval'];
				$sql='select title from svsets where id="'.$did.'" limit 1';
				$res= mysql_query($sql);
				if($res && mysql_num_rows($res) === 1){
					$dar= mysql_fetch_assoc($res);
					$dsysval = $dar['title'];
				}
			}

			if(isset($_GET['parval'])){
				$pval=mysql_real_escape_string($_GET['parval']);
			}else{
				$pval=false;
			}
			$wz = new Wizard();
			$t = $wz->getValues($ztype,$dsysval,false,true,false,$pval);
			$sql='select s1.id as parent ,s2.id as child
			from svsets s1
			left join svsets s2 on s1.id <> s2.id
			where s1.title="'.$dsysval.'" and s2.parent = s1.id ';
			$res = mysql_query($sql);
			if($res && mysql_num_rows($res) == 1){
				$chq = mysql_fetch_assoc($res);
				$child_id = $chq["child"];
			}else{
				$child_id = false;
			}
			//$t= dPgetSysValSet($dsysval);
			if(is_array($t) && count($t) > 0){
				if($pval === false){
					echo json_encode(array("data"=>$t,"child"=>$child_id));
				}else{
					$newt =array();
					if(count($t['rels'][$pval]) > 0){
						foreach ($t['rels'][$pval] as $cpart){
							$newt[$cpart]=$t[$cpart];
						}
					}
					echo json_encode($newt);
				}
			}else{
				echo 'fail';
			}
			return;
		}
		break;

	case 'delSV':
		if((int)$_GET['svid'] > 0){
			$todel=(int)$_GET['svid'];
			$sql='delete from sysvals where sysval_id = "'.$todel.'"';
			$res = mysql_query($sql);
			if($res){
				echo "ok";
			}else{
				echo 'fail';
			}
			return;
		}
		break;
	case 'loadall':
		$q = new DBQuery();
		$q->addTable("svsets","sv1");
		$q->addOrder("title");
		$q->addQuery('id,title, vtype, level, parent, `status`, touch, options, "local" as gtype');
		//$q->addQuery('sv1.*, "local" as gtype, sv2.id as `child`');
		//$q->addJoin('svsets','sv2','sv2.id=sv1.id');
		//$q->addWhere('sv1.id = sv2.parent AND sv2.id <> sv1.id');

		$list= $q->loadList();

		$sqli = 'select id as total from svsets where parent="%d" and id <> "%d"';
		foreach($list as &$litem){
			$resi = mysql_query(sprintf($sqli, $litem['id'],$litem['id']));
			if($resi && mysql_num_rows($resi) > 0){
				$litem['childs'] = mysql_num_rows($resi);
			}
			mysql_free_result($resi);
		}

		$q2 = new DBQuery();
		$q2->addTable('sysvals');
		$q2->addQuery(  'sysval_title as title, sysval_value as options,
						"select" as `vtype`, sysval_id as id,
						sysval_id as parent,"1" as status, "global" as gtype');
		$q2->addOrder('title');

		$list2 = $q2->loadList();
		$allList = array_merge($list,$list2);
		echo json_encode($allList);
		return;
		break;

	case 'tstatus':
		$tvid=(int)$_GET['vid'];
		$ptxt=trim($_GET['vtxt']);
		if($tvid > 0 && $ptxt != '' && strstr($ptxt,"active")){
			if($ptxt === 'inactive'){
				$nst="1";
			}else{
				$nst="0";
			}
			$sql='update svsets set status="'.$nst.'" where id="'.$tvid.'"';
			$res=mysql_query($sql);
			if($res){
				$pout='ok';
			}else{
				$pout='fail';
			}
			echo $pout;
			return;
		}
		break;

	case 'exportAll':
		$q = new DBQuery();
		$q->addTable("svsets");
		$list= $q->loadList();

		if(count($list) > 0){
			printForSave(base64_encode(addslashes(gzcompress(serialize($list),9))),'application/octet-stream','vsets_'.$dPconfig['current_center'].'.vbn',true,false);
		}
		return ;
		break;

	case 'import_init':
		$wfile=&$_FILES['formfile'];
		$imported=0;
		$result = array();
		if (is_uploaded_file ( $wfile['tmp_name'] ) && $wfile ['error'] == 0) {
			$fpath = $wfile ['tmp_name'];
			$news = file_get_contents ( $fpath );
			$upset = @unserialize(@gzuncompress(@stripslashes(@base64_decode($news))));
			$result = importSets($upset);
		}
		echo json_encode($result);
		return ;

		break;
	case 'import_finish':

		$direct = json_decode(stripslashes($_POST['parts']),true);
		$result = importDelayed($direct['use'],$direct['leave']);
		echo $result;
		return ;
		break;
	default:
		break;
}