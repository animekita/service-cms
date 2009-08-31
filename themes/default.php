<?php
/** Template source: revision 392 **/
$staticFiles = 'http://www.anime-kita.dk/static/dev';

echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php Loader::element('header_required'); ?>
        <link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>css/reset.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>css/base.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo $staticFiles; ?>css/cms.css" />
    </head>
    <body>
		
        <div id="wrapper">

            <div id="topNavBg"></div>

            <div id="top">
                <img id="topLogo" src="/graphics/toplogo.png" alt="Anime Kita logo" />

                <div id="topNavBgFade"></div>

                <ul id="topNav">
                    <?php
    					$a = new Area('Header Nav');
    					$a->display($c);
					?>
                </ul>

                <div id="topUserNav">
                    <!-- This is why whitespaces inside block elements shouldn't be parsed -->
                    <a class="floatLeft" href="/">Login</a
                    ><span class="vertSeperatorTiny">|</span
                    ><a href="/">Opret Bruger</a>
                </div>

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
            <a href="/">Kontakt</a
            ><span class="vertSeperatorMedium">|</span
            ><a href="/">Copyright</a
            ><span class="vertSeperatorMedium">|</span
            ><a href="/">Databehandlingspolitik</a>
        </div>
        
    </body>
</html>
