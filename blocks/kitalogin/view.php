<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div id="topUserNav">
<!-- This is why whitespaces inside block elements shouldn't be parsed -->
<?php $u = new User(); if ($u->isRegistered()) { ?>
<a class="floatLeft" href="https://selvbetjening.anime-kita.dk/profil/"><?php echo $u->uName; ?></a
><span class="vertSeperatorTiny">|</span
><a href="https://selvbetjening.anime-kita.dk/profil/logud/">Logud</a>
<?php } else { ?>
<a class="floatLeft" href="https://selvbetjening.anime-kita.dk/bliv-medlem/">Opret Bruger</a
><span class="vertSeperatorTiny">|</span
><a href="https://selvbetjening.anime-kita.dk/profil/login/">Login</a>
<?php } ?>
</div>