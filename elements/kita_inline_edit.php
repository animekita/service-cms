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
		$statusMessage .= "<br/><a href='" . DIR_REL . "/" . DISPATCHER_FILENAME . "?cID=" . $c->getCollectionID() . "'>" . t('View/Edit Original') . "</a>";
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

if ($cp->canWrite() || $cp->canAddSubContent() || $cp->canAdminPage() || $cp->canApproveCollection()) {

$html = '';

$html .= '<ul>';

---

if (!$c->isAlias()) {

	$hideOnEdit = '';
	if ($c->isEditMode()) {
		$hideOnEdit = ' style="display: none"';
	}

	$html .= '<li' . $hideOnEdit . '>';

	if ($cantCheckOut) {
		$html .= '<span id="ccm-nav-edit">' . t('Edit Page') . '</span>';
	} else if ($cp->canWrite() || $cp->canApproveCollection()) {
		$html .= '<a href="javascript:void(0)" id="ccm-nav-edit">' . t('Edit Page') . '</a>';
	}

	$html .= '</li>';


	if ($cp->canAddSubContent()) {
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-add">' . t('Add Page') . '</a></li>';
	}


	$html .= '<li' . $hideOnEdit . '><a href="#" id="ccm-nav-save-arrange">' . t('Save Positioning') . '</a></li>';

	$hideOnEditModeNew = ''
	if (!$c->isEditMode() || ($vo->isNew()))  { 
		$hideOnEditModeNew = ' style="display: none"';
	}

	$html .= '<li' . $hideOnEditModeNew . '><a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $c->getCollectionID() . '&ctask=check-in' . $token . '" id="ccm-nav-exit-edit-direct">' . t('Exit Edit Mode') . '</a></li>';

	$html .= '<li' . $hideOnEditModeNew . '><a href="javascript:void(0)" id="ccm-nav-exit-edit">' . t('Exit Edit Mode') . '</a></li>';

	if ($cp->canWrite()) {
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-properties">' . t('Properties') . '</a></li>';
	}

	if ($cp->canAdminPage()) {
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-design">' . t('Design') . '</a></li>';
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-permissions">' . t('Permissions') . '</a></li>';
	}

	if ($cp->canReadVersions()) {
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-versions">' . t('Versions') . '</a></li>';
	}

	if ($sh->canRead() || $cp->canDeleteCollection()) {
		$html .= '<li' . $hideOnEdit . '><a href="javascript:void(0)" id="ccm-nav-mcd">' . t('Move/Delete') . '</a></li>';
	}
}

$html .= '</ul>';

echo $html;

?>

<script type="text/javascript">
$(function() {
        /**
         * Remove old navigation and re-run initialization
         * Fixes problems with Firefox not playing nice with
         * duplicated ID's.
         **/
        $('#ccm-nav-edit').remove();
        $('#ccm-nav-add').remove();

        $('#ccm-nav-exit-edit').remove();
        $('#ccm-nav-properties').remove();
        $('#ccm-nav-permissions').remove();
        $('#ccm-nav-design').remove();
        $('#ccm-nav-versions').remove();
        $('#ccm-nav-mcd').remove();

        $('#ccm-nav-save-arrange').remove();

		<?php  if ($c->isArrangeMode()) { ?>
			$(ccm_arrangeInit);
		<?php  } else if ($c->isEditMode()) { ?>
			$(ccm_editInit);
		<?php  } else { ?>
			$(ccm_init);
		<?php  } ?>

	});
</script>

<?php } } ?>
