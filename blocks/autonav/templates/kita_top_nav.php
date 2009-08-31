<?php 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	$aBlocks = $controller->generateNav();
	global $c;
	echo("<ul id=\"topNav\">");

	$nh = Loader::helper('navigation');

	$isFirst = true;
	foreach($aBlocks as $ni) {
		$_c = $ni->getCollectionObject();
		if (!$_c->getCollectionAttributeValue('exclude_nav')) {
			
			$pageLink = false;

			if ($_c->getCollectionAttributeValue('replace_link_with_first_in_nav')) {
				$subPage = $_c->getFirstChild();
				if ($subPage instanceof Page) {
					$pageLink = $nh->getLinkToCollection($subPage);
				}
			}
			
			if (!$pageLink) {
				$pageLink = $ni->getURL();
			}
			
			echo '<li>';
			
			if ($c->getCollectionID() == $_c->getCollectionID()) { 
				echo('<a class="current" href="' . $pageLink . '">' . $ni->getName() . '</a>');
			} else {
				echo('<a href="' . $pageLink . '">' . $ni->getName() . '</a>');
			}	
			
			echo('</li>');
			$isFirst = false;			
		}
	}
	
	echo('</ul>');
?>
