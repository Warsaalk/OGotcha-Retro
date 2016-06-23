<?php
	use Plinth\Response\Response;
	/* @var $self Response */

	$preview = isset($renderedPreview);
?>
	<h1 id="ogotcha-title" class="title">
		<a href="<?= $self->Main()->getRouter()->getRoute('page_home')->getPath(); ?>">
			<?= $__("home.title"); ?>
		</a>
	</h1>
	<form method="post" action="">
		<div id="cr-container">
			<div id="cr-left" class="cr-textareas">
				<textarea name="report" id="cr-input" rows="24" cols="80"><?= $self->Main()->getValidator()->getVariable('report')?:''; ?></textarea>
			</div>
			<div id="cr-right" class="cr-textareas">
				<div id="cr-title"><span><?= $__("Title:"); ?></span><input placeholder="<?= $__("Title Placeholder") ?>" name="title" id="title" value="<?= isset($renderedTitle) ? $renderedTitle : ''; ?>" style="width: 350px;"></div>
				<div class="clear"></div>
				<textarea placeholder="<?= $__("Result Placeholder") ?>" name="output" id="cr-output" rows="24" cols="80"><?= isset($renderedReport) ? $renderedReport : ''; ?></textarea>
			</div>
			<div class="clear"></div>
		</div>
		<div id="submit-container" class="clearfix">
			<input type="submit" name="submit" id="submit" value="<?= $__("Convert") ?>">
			<?php if($preview){ ?>
				<div id="goto-preview"><a href="#preview-container"><?= $__("Preview") ?></a></div>
			<?php } ?>
		</div>
		<div id="options-container" class="outer-block">
			<div class="option inner-block">
				<h2><?= $__("Generic options") ?></h2>
				<table id="option-table" border="0">
					<tr>
						<td><label for="theme"><?= $__("Skin") ?>:</label></td>
						<td>
							<select name="theme" id="theme">
								<?php foreach ($self->Main()->config->get('converter:themes') as $key) {
									?><option value="<?= $key; ?>"><?= $__('theme.' . $key); ?></option><?php
								} ?>
							</select>
						</td>
						<td><label for="hidetime"><?= $__("Hide the time") ?>:</label></td>
						<td><input type="checkbox" name="hidetime" id="hidetime" value="1" checked="checked"></td>
					</tr>
					<tr>
						<td><label for="middletext"><?= $__("Text in the middle of the CR") ?>:</label></td>
						<td><input type="text" name="middletext" id="middletext" value="<?= $__("After the battle"); ?>"></td>
						<td><label for="merge"><?= $__("Merge fleets of the same player") ?>:</label></td>
						<td><input type="checkbox" name="merge" id="merge_fleets" value="1" checked="checked"></td>
					</tr>
					<tr>
						<td><label for="advanced"><s><?= $__("Show advanced summary") ?>: <?= $__("New") ?></s></label></td>
						<td><input type="checkbox" name="advanced" id="advanced" value="1"></td>
						<td><label for="spoiler"><?= $__("Use spoilers for harvest reports") ?>:</label> <?= $__("New") ?></td>
						<td><input type="checkbox" name="spoiler" id="quotes" value="1"></td>
					</tr>
				</table>
			</div>
			<div class="option inner-block">
				<h2><?= $__("Raids") ?></h2>
				<textarea name="raids" id="raids" rows="5" cols="100"><?= $self->Main()->getValidator()->getVariable('raids')?:''; ?></textarea>
			</div>
			<div class="players clearfix">
				<div class="player attacker">
					<h1><?= $__("Attackers") ?></h1>
					<div class="option inner-block">
						<h2><?= $__("Harvest Reports") ?></h2>
						<textarea name="attacker_harvest" id="attacker_harvest_reports" rows="5" cols="100"><?= $self->Main()->getValidator()->getVariable('attacker_harvest')?:''; ?></textarea>
					</div>
					<div class="option inner-block">
						<h2><?= $__("Deuterium Costs") ?></h2>
						<textarea name="attacker_deuterium" id="attacker_deuterium" rows="5" cols="100"><?= $self->Main()->getValidator()->getVariable('attacker_deuterium')?:''; ?></textarea>
					</div>
				</div>
				<div class="player defender">
					<h1><?= $__("Defenders") ?></h1>
					<div class="option inner-block">
						<h2><?= $__("Harvest Reports") ?></h2>
						<textarea name="defender_harvest" id="defender_harvest_reports" rows="5" cols="100"><?= $self->Main()->getValidator()->getVariable('defender_harvest')?:''; ?></textarea>
					</div>
					<div class="option inner-block">
						<h2><?= $__("Deuterium Costs") ?></h2>
						<textarea name="defender_deuterium" id="defender_deuterium" rows="5" cols="100"><?= $self->Main()->getValidator()->getVariable('defender_deuterium')?:''; ?></textarea>
					</div>
				</div>
			</div>
		</div>
		<?php if ($preview) { ?>
			<div id="preview-container" class="outer-block">
				<h1><?= $__("Preview") ?></h1>
				<?php if(true/* $vl->getVariable('spoiler','value') == '1' */) {?>
					<h3 style="margin-top:10px"><?= $__("Preview spoiler") ?></h3>
				<?php } ?>
				<div id="preview">
					<?= isset($renderedPreview) ? $renderedPreview : ''; ?>
				</div>
			</div>
		<?php } ?>
	</form>