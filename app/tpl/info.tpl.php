<?php if ($self->Main()->hasInfo()) { 
			foreach ($self->Main()->getInfo() as $i => $info) {
?>
				<div class="info">
					<div class="<?= $info->getType(); ?> info-content clearfix">
						<img src="<?= $info->getBase64Image(); ?>" alt="infoimg" />
						<span><?= $info->getMessage(); ?></span>
					</div>
					<div class="close-info" onclick="this.parentNode.style.height=0;this.parentNode.style.margin=0;">x</div>
				</div>
<?php } } ?>