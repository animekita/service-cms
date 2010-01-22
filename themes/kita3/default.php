<?php
/** Template source: revision 392 **/
$staticFiles = 'http://www.anime-kita.dk/static/dev';

echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<?php
		Loader::element('header_required');
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>/css/reset.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>/css/base.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>/css/cms.css" />
	</head>
	<body>

		<div id="wrapper">

			<div id="topNavBg"></div>

			<div id="top">
				<a href="http://www.anime-kita.dk"><img id="topLogo" src="<?php echo $staticFiles; ?>/graphics/toplogo.png" alt="Anime Kita logo" /></a>

				<div id="topNavBgFade"></div>

				<?php
				$uriSegments = explode('/', $_SERVER['REQUEST_URI']);

				// Remove the first (empty) part
				array_shift($uriSegments);

				// Reset the array pointer
				$currentMain = reset($uriSegments);

				$topLinks = array(
				 'home' => array(
					'label' => 'home',
					'url' => '/',
					'attrbs' => array()
				 ),
				 'forum' => array(
					'label' => 'forum',
					'url' => 'http://forum.anime-kita.dk',
					'attrbs' => array()
				 ),
				 'galleri' => array(
					'label' => 'galleri',
					'url' => 'http://galleri.anime-kita.dk',
					'attrbs' => array()
				 ),
				 'intranet' => array(
					'label' => 'intranet',
					'url' => '/intranet/',
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

				<?php Loader::element('kita_user_control'); ?>

			</div>

			<div id="pageControls">
				<?php Loader::element('kita_inline_edit'); ?>
			</div>

			<div id="mid">
				<div id="midLeft">
					<?php
					$a = new Area('Sidebar');
					$a->display($c);
					?>

					<div id="kitaKanji"></div>

				</div>

				<div id="midContent">
					<?php
					$a = new Area('Header');
					$a->display($c);
					?>

					<?php
					$a = new Area('Main');
					$a->display($c);
					?>
				</div>
			</div>
			<div id="footerFix"></div>
		</div>

		<div id="footer">
			<!-- This is why ... -->
			<span>Anime Kita, 2007 - <?php echo date("Y") ?> &copy; All rights reserved</span
			><span class="vertSeperatorMedium">|</span
			><a href="/om-os/kontakt/">Kontakt</a
			><span class="vertSeperatorMedium">|</span
			><a href="/databehandlingspolitik/">Databehandlingspolitik</a
			<?php  $dh = Loader::helper('concrete/dashboard'); if ($dh->canRead()) { ?>
			><span class="vertSeperatorMedium">|</span
			><a href="<?php echo View::url('/dashboard')?>">Administrations interface</a
				<?php  } ?>
			>
		</div>

		<?php  Loader::element('footer_required'); ?>
	</body>
</html>
