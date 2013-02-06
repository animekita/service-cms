<?php
$uriSegments = explode('/', $_SERVER['REQUEST_URI']);

// Remove the first (empty) part
array_shift($uriSegments);

// Reset the array pointer
$currentMain = reset($uriSegments);

// Use full urls in order to work on different subdomains
$topLinks = array(
 'home' => array(
	'label' => 'home',
	'url' => 'http://www.anime-kita.dk',
	'attrbs' => array()
 ),
 'forum' => array(
	'label' => 'forum',
	'url' => 'http://www.anime-kita.dk/forum/',
	'attrbs' => array()
 ),
 'galleri' => array(
	'label' => 'galleri',
	'url' => 'http://www.anime-kita.dk/galleri/',
	'attrbs' => array()
 ),
 'intranet' => array(
	'label' => 'intranet',
	'url' => 'http://www.anime-kita.dk/intranet/',
	'attrbs' => array()
 )
);

// A downright blatant assumption..
switch($currentMain) {
	case 'intranet':
		$current = 'intranet';
		break;
	default:
		$current = 'home';
		break;
}

?>

<ul id="topNav">

	<?php foreach($topLinks as $index => $link): ?>

		<?php

		if($index === $current) {
			$link['attrbs']['class'] = 'current';
		}

		// Assign the keys in the array to variables
		extract($link);

		// Put attributes together
		$attributes = '';
		foreach($attrbs as $key => $value) {
			$attributes .= $key . '="'.$value.'" ';
		}

		?>
	<li><a href="<?php echo $url;?>" <?php echo $attributes; ?>><?php echo $label ?></a></li>
	<?php endforeach; ?>

</ul>
