<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
class KitaloginBlockController extends BlockController {
	
	var $pobj;
	
	protected $btTable = 'btKitalogin';
	protected $btInterfaceWidth = "550";
	protected $btInterfaceHeight = "400";
	
	public function getBlockTypeDescription() {
		return t("Displays currently logged-in Kita user and user navigation.");
	}
	
	public function getBlockTypeName() {
		return t("Kita login");
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);
	}
	
	function delete(){
	}
	
	function save($data) {	
		parent::save($args);
	}
	
}

?>
