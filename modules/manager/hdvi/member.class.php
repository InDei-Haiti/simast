<?php
class Member{
	public $member_fld_linkparent;
	public $member_fld_sex;
	public $member_fld_status_mat;
	public $member_fld_age;
	public $member_fld_write;
	public $member_fld_read;
	public $member_fld_level_edu;
	public $member_fld_act_edu;
	public $member_fld_lst_scho_12;
	public $member_fld_lsickness = array();
	public $member_fld_chron;
	public $member_fld_eco_active;
	public $member_prob_eye;
	public $member_prob_speak;
	public $member_prob_hear;
	public $member_prob_autooins;
	public $member_transf;
	public $member_supp;
	public $member_id;

    function getJsonData(){
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value,'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }
}

?>