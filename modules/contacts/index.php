<?php
$AppUI->savePlace();

/*$tabBox = new CTabBox ("?m=contacts", dPgetConfig('root_dir') . "/modules/contacts/");
$tabBox->add('vw_contacts', 'Staff');
$tabBox->show();*/
require_once ($baseDir."/modules/contacts/vw_contacts_new.php");

