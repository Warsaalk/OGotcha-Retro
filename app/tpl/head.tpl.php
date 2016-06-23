<?php
	use Plinth\Response\Response;
	use Plinth\Common\Language;
	/* @var $self Response */;
	$route = $self->Main()->getRouter()->hasRoute() ? $self->Main()->getRouter()->getRoute() : false;
?><base href="<?= __BASE_URL ?>" />
	<meta charset="UTF-8">

	<meta name="description" content="An OGame combat report converter, OGotcha allows you to convert your combat reports so you can post them on the boards">
	<meta name="keywords" content="ogame, gameforge, universeview, warsaalk, ogotcha, combat report, converter, kokx, origin, cr converter, ogame origin, ogotcha converter">
	<meta name="theme-color" content="#1E2B39">

	<link rel="canonical" href="<?= __APP_URL . $self->Main()->getRouter()->getRoute('page_home')->getPath(); ?>">
	<link href='https://fonts.googleapis.com/css?family=Rajdhani:300,400&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Yantramanav:400,300,100&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link rel="shortcut icon" type="image/png" href="<?= $self->getAsset('img/icon.png'); ?>" />

	<link rel="alternate" hreflang="x-default" href="<?= __BASE_URL ?>" />
	<?php foreach (Language::getLanguages() as $code) {
		if ($route !== false && $route->getPathData('lang') === true) {
			$data = $route->getData();
			$data['lang'] = $code;
			?>
			<link rel="alternate" hreflang="<?= $code; ?>" href="<?= __BASE_URL . $route->getPath($data); ?>" />
		<?php } else { ?>
			<link rel="alternate" hreflang="<?= $code; ?>" href="<?= __BASE_URL . $code ?>" />
		<?php } } ?>

	<title><?= $__("page.title") . (isset($pageTitle) ? $pageTitle : ''); ?></title>
	<?= $self->getCssTag('css/app.css'); ?>