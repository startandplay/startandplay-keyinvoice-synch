<div class="wrap">
	<h1>Keyinvoice Configurações</h1>
	<?php settings_errors(); ?>
	<form method="post" action="options.php">
		<?php
		settings_fields('keyinvoice_configs_options_group');
		do_settings_sections('keyinvoice');
		submit_button();
		?>
	</form>
</div>