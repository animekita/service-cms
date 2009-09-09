<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * This is a partial copy of the C5 page_controls_menu_js.php file.
 * Generates a subset of the menu in HTML for use directly on the page.
 **/

global $c;
global $cp;

$valt = Loader::helper('validation/token');
$sh = Loader::helper('concrete/dashboard/sitemap');
$dh = Loader::helper('concrete/dashboard');
$supportHelper=Loader::helper('concrete/support');

if (isset($cp)) {

	$u = new User();
	$username = $u->getUserName();
	$vo = $c->getVersionObject();

	$statusMessage = '';
	if ($c->isCheckedOut()) {
		if (!$c->isCheckedOutByMe()) {
			$cantCheckOut = true;
			$statusMessage .= t("Another user is currently editing this page.");
		}
	}
	
	if ($c->getCollectionPointerID() > 0) {
		$statusMessage .= t("This page is an alias of one that actually appears elsewhere. ");
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent'>" . t('View/Edit Original') . "</a>";
		if ($cp->canApproveCollection()) {
			$statusMessage .= "&nbsp;|&nbsp;";
			$statusMessage .= "<a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionPointerOriginalID() . "&ctask=remove-alias" . $token . "'>" . t('Remove Alias') . "</a>";
		}
	} else {
	
		if (is_object($vo)) {
			if (!$vo->isApproved() && !$c->isEditMode()) {
				$statusMessage .= t("This page is pending approval.");
				if ($cp->canApproveCollection() && !$c->isCheckedOut()) {
					$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve-recent" . $token . "'>" . t('Approve Version') . "</a>";
				}
			}
		}
		
		$pendingAction = $c->getPendingAction();
		if ($pendingAction == 'MOVE') {
			$statusMessage .= $statusMessage ? "&nbsp;|&nbsp;" : "";
			$statusMessage .= t("This page is being moved.");
			if ($cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action'>" . t('Approve Move') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		} else if ($pendingAction == 'DELETE') {
			$statusMessage .= $statusMessage ? "<br/>" : "";
			$statusMessage .= t("This page is marked for removal.");
			$children = $c->getNumChildren();
			if ($children > 0) {
				$pages = $children + 1;
				$statusMessage .= " " . t('This will remove %s pages.', $pages);
				if ($cp->canAdminPage()) {
					$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
				} else {
					$statusMessage .= " " . t('Only administrators can approve a multi-page delete operation.');
				}
			} else if ($children == 0 && $cp->canApproveCollection() && (!$c->isCheckedOut() || ($c->isCheckedOut() && $c->isEditMode()))) {
				$statusMessage .= " <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=approve_pending_action" . $token . "'>" . t('Approve Delete') . "</a> | <a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "&ctask=clear_pending_action" . $token . "'>" . t('Cancel') . "</a>";
			}
		}
	
	}

if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage()) { ?>

<div id="topPageNav">
<!-- This is why whitespaces inside block elements shouldn't be parsed -->
<?php  if ($c->isArrangeMode()) { ?>
<a href="#" id="ccm-nav-save-arrange"><?php echo t('Save Positioning')?></a

<?php  } else if ($c->isEditMode()) { ?>
<a href="javascript:void(0)" id="ccm-nav-exit-edit"><?php echo t('Exit Edit Mode')?></a
><span class="vertSeperatorTiny">|</span
><a href="javascript:void(0)" id="ccm-nav-properties"><?php echo t('Properties')?></a

	<?php  if ($cp->canAdminPage()) { ?>
	><span class="vertSeperatorTiny">|</span
	><a href="javascript:void(0)" id="ccm-nav-design"><?php echo t('Design')?></a
	><span class="vertSeperatorTiny">|</span
	><a href="javascript:void(0)" id="ccm-nav-permissions"><?php echo t('Permissions')?></a
	<?php  } ?>

	<?php  if ($cp->canReadVersions()) { ?>
	><span class="vertSeperatorTiny">|</span
	><a href="javascript:void(0)" id="ccm-nav-versions"><?php echo t('Versions')?></a
	<?php  } ?>
	
	<?php  if ($sh->canRead() || $cp->canDeleteCollection()) { ?>
	><span class="vertSeperatorTiny">|</span
	><a href="javascript:void(0)" id="ccm-nav-mcd"><?php echo t('Move/Delete')?></a
	<?php  } ?>

<?php  } else { ?>

	<?php  if ($cantCheckOut) { ?>
	<span id="ccm-nav-edit"><?php echo t('Edit Page')?></span
	
	<?php  } else if ($cp->canWrite()) { ?>
	<a href="javascript:void(0)" id="ccm-nav-edit"><?php echo t('Edit Page')?></a
	
	<?php  } ?>

	<?php  if ($cp->canAddSubContent()) { ?>
	><span class="vertSeperatorTiny">|</span
	><a href="javascript:void(0)" id="ccm-nav-add"><?php echo t('Add Page')?></a
	<?php  } ?>
	
<?php  } ?>

>

</div>

<?php } } ?>

<script type="text/javascript">
$(function() {
        /**
         * Remove old navigation and re-run initialization
         * Fixes problems with Firefox not playing nice with
         * duplicated ID's.
         **/

        $('#ccm-nav-exit-edit').remove();
        $('#ccm-nav-properties').remove();
        $('#ccm-nav-permissions').remove();
        $('#ccm-nav-design').remove();
        $('#ccm-nav-versions').remove();
        $('#ccm-nav-mcd').remove();

		<?php  if ($c->isArrangeMode()) { ?>
			$(ccm_arrangeInit);	
		<?php  } else if ($c->isEditMode()) { ?>
			$(ccm_editInit);	
		<?php  } else { ?>
			$(ccm_init);
		<?php  } ?>
		
	});
</script>

