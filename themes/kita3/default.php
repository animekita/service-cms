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

                <ul id="topNav"><li><a href="http://beta.anime-kita.dk">home</a></li><li><a href="http://www.anime-kita.dk/forum">forum</a></li><li><a href="http://galleri.anime-kita.dk">galleri</a></li><li><a href="http://anime-kita.dk/intranet/">intranet</a></li></ul>

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
            ><a href="http://www.anime-kita.dk/kontakt.html">Kontakt</a
            ><span class="vertSeperatorMedium">|</span
            ><a href="https://selvbetjening.anime-kita.dk/behandling-af-personoplysninger/">Databehandlingspolitik</a
            <?php  $dh = Loader::helper('concrete/dashboard'); if ($dh->canRead()) { ?>
            ><span class="vertSeperatorMedium">|</span
			><a href="<?php echo View::url('/dashboard')?>">Administrations interface</a
			<?php  } ?>
			>
        </div>

		<?php  Loader::element('footer_required'); ?>
    </body>
</html>
