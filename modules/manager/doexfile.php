<?php

require_once $AppUI->getSystemClass('systemExporter');

class LTPExport extends systemExporter{
	
	function __construct($x,$y,$z){
		parent::__construct($x,$y,$z,true);
	}
	
	function store(&$list){
		$this->putstr(serialize($list));
		unset($list);
		$this->move();
	}	
}

function exportProceed($clinic,$list,$ltpath){	
	
	//$clinic=(int)$_GET['cid'];
	//now we receive clinic id from cycle
	$clins=centerList();
	$clinicName=$clins[$clinic];
	
	if ($clinic > 0) {
		
		$wfile= new LTPExport('plain',$clinicName.'-LTP' ,$ltpath);
		
		/*$q=new DBQuery();
		$q->addTable('ltp_transfers');
		$q->addWhere('clinic_id ="'.$clinic.'"');
		$q->addWhere('ondate  is null');
		$q->addQuery('client_id');
		$list=$q->loadArrayList();*/
		
		$blist = array ();
		
		if(count($list) == 0){
			unset($wfile);
			return FALSE;
		}
		
		$centers=centerList();
		
		$q = new DBQuery();
		$q->addTable('clinics');
		$q->addQuery('clinic_id, clinic_name');
		$clinics=$q->loadHashList();
		$clinics['target']=$clinic;
		
		$wfile->store($clinics);
		
		foreach ( $list as $iii => $uservals) {
			
			$user=$uservals['id'];
			//Take data from clients table
			$q = new DBQuery ();
			$q->addTable ( 'clients' );
			$q->addWhere ( 'client_id="' . $user . '"' );
			$q->setLimit ( 1 );
			$clitab = $q->loadList ();
			$clitab = $clitab [0];
			$cadm = $clitab ['client_adm_no'];
			$blist [$cadm] = array ('clients' => $clitab );
			
			$blist[$cadm]['profile']=array(
				'date'=>$uservals['date'],
				'name'=>$clitab['client_first_name'].' '.$clitab['client_other_name'].' '.$clitab['client_last_name'],
				'center'=>$centers[$clitab['client_center']],
				'adm_no'=>$cadm
				);
						
			//Admission caregivers
			$q = new DBQuery ();
			$q->addTable ( 'admission_caregivers' );
			$q->addWhere ( 'client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['admission_caregivers'] = $clitab;
			
			//Admission info table
			$q = new DBQuery ();
			$q->addTable ( 'admission_info' );
			$q->addWhere ( 'admission_client_id="' . $user . '"' );
			$q->setLimit ( 1 );
			$admtab = $q->loadList ();
			$admtab = $admtab [0];
			$blist [$cadm] ['admission_info'] = $admtab;
			
			//clinical visits
			$q = new DBQuery ();
			$q->addTable ( 'clinical_visits' );
			$q->addWhere ( 'clinical_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['clinical_visits'] = $clitab;
			
			//counselling visits
			$q = new DBQuery ();
			$q->addTable ( 'counselling_visit' );
			$q->addWhere ( 'counselling_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['counselling_visit'] = $clitab;
			
			//nutrition visits
			$q = new DBQuery ();
			$q->addTable ( 'nutrition_visit' );
			$q->addWhere ( 'nutrition_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['nutrition_visit'] = $clitab;
			
			//nutrition visits - sub NUTR SERVs
			$q = new DBQuery ();
			$q->addTable ( 'nutrition_service' );
			$q->addWhere ( 'nutrition_service_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['nutrition_service'] = $clitab;
			
			//medical assessment
			$q = new DBQuery ();
			$q->addTable ( 'medical_assessment' );
			$q->addWhere ( 'medical_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$clitab=$clitab[0];
			$blist [$cadm] ['medical_assessment'] = $clitab;
			
			//sub med assess - MED HISTORY
			$q = new DBQuery ();
			$q->addTable ( 'medical_history' );
			$q->addWhere ( 'medical_history_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['medical_history'] = $clitab;
			
			//sub med assess - MEDs HISTORY
			$q = new DBQuery ();
			$q->addTable ( 'medications_history' );
			$q->addWhere ( 'medications_history_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['medications_history'] = $clitab;
			
			//mortality info
			$q = new DBQuery ();
			$q->addTable ( 'mortality_info' );
			$q->addWhere ( 'mortality_client_id="' . $user . '"' );
			$q->setLimit ( 1 );
			$clitab = $q->loadList ();
			$blist [$cadm] ['mortality_info'] = $clitab;
			
			//social visits
			$q = new DBQuery ();
			$q->addTable ( 'social_visit' );
			$q->addWhere ( 'social_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['social_visit'] = $clitab;
			
			//social services is sub sto social visits
			$q = new DBQuery ();
			$q->addTable ( 'social_services' );
			$q->addWhere ( 'social_services_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['social_services'] = $clitab;
			
			//household_info
			$q = new DBQuery ();
			$q->addTable ( 'household_info' );
			$q->addWhere ( 'household_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['household_info'] = $clitab;
			
			//CHW INFO
			$q = new DBQuery ();
			$q->addTable ( 'chw_info' );
			$q->addWhere ( 'chw_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['chw_info'] = $clitab;
			
			//CBC INFO
			$q = new DBQuery ();
			$q->addTable ( 'cbc_info' );
			$q->addWhere ( 'cbc_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['cbc_info'] = $clitab;
			
			//Client status changes (history)
			$q = new DBQuery ();
			$q->addTable ( 'status_client' );
			$q->addWhere ( 'social_client_id="' . $user . '"' );
			$clitab = $q->loadList ();
			$blist [$cadm] ['status_client'] = $clitab;
			
			$wfile->store(&$blist[$cadm]);
			//Last action
			$sql = 'update ltp_transfers set status="1" where client_id="' . $user . '" and clinic_id="'.$clinic.'" limit 1';
			$res = mysql_query ( $sql );
		}
		$wfile->close(true,false);
	}
}