<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

header('Location: http://www.anime-kita.dk/selvbetjening/profil/login/?next=' . $_SERVER['REQUEST_URI'], true, 303);
exit();

