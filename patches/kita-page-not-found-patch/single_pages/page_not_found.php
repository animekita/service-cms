<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div id="midLeft">
	<?php
		$bt = BlockType::getByHandle('autonav');
		$bt->controller->orderBy = 'display_asc';
		$bt->controller->displayPages = 'top';
		$bt->controller->displaySubPages = 'all';
		$bt->controller->displaySubPageLevels = 1;
		$bt->render('templates/kita_left_nav');
	?>

	<div id="kitaKanji"></div>

</div>

<div id="midContent">
	<h1>Siden blev ikke fundet</h1>

	<p>Den eftersøgte side blev desværre ikke fundet. Brug eventuelt sitemappet nedenfor for at finde det du søger.</p>

	<p>Du kan også <a href="http://beta.anime-kita.dk/om-os/kontakt/">kontakte os</a> så skal vi hjælpe dig videre hurtigs muligt.</p>

	<?php
		$bt = BlockType::getByHandle('autonav');
		$bt->controller->orderBy = 'display_asc';
		$bt->controller->displayPages = 'top';
		$bt->controller->displaySubPages = 'all';
		$bt->controller->displaySubPageLevels = 'all';
		$bt->render('view');
	?>
</div>