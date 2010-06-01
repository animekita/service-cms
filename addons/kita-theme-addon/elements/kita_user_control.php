<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div id="topUserNav">
<!-- This is why whitespaces inside block elements shouldn't be parsed -->
<?php $u = new User(); if ($u->isRegistered()) { ?>
<a class="floatLeft" href="<?php echo SELV_URL; ?>/profil/"><?php echo $u->uName; ?></a
><span class="vertSeperatorTiny">|</span
><a href="<?php echo SELV_URL; ?>/profil/logud/">Log ud</a>
<?php } else { ?>
<a class="floatLeft" href="<?php echo SELV_URL; ?>/bliv-medlem/">Opret Bruger</a
><span class="vertSeperatorTiny">|</span
><a href="<?php echo SELV_URL; ?>/profil/login/?next=<?php  echo $_SERVER['REQUEST_URI']; ?>">Log ind</a>
<?php } ?>
</div>
