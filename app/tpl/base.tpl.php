<?php
use Plinth\Response\Response;
use Plinth\Routing\Route;
/* @var $self Response */

$language = $self->Main()->getRouter()->getRoute()->get(Route::DATA_LANG);
?><!doctype html>
<html lang="<?= $self->Main()->getLang(); ?>" prefix="og: http://ogp.me/ns#">
	<head>
		<?= $self->getTemplate('head'); ?>
	</head>
	<body <?= $language === false ? 'class="default-language"' : ''; ?>>
		<div id="main-container">
			<div id="container">
				<div id="info">
					<?= $self->getTemplate('info'); ?>
				</div>
				<?= $self->content; ?>
			</div>
		</div>
		<footer>
			<?= $self->getTemplate('footer'); ?>
		</footer>
	</body>
</html>