<?php

add_action( 'admin_menu', 'nc_timezone_add_admin_menu' );
add_action( 'admin_init', 'nc_timezone_settings_init' );


function nc_timezone_add_admin_menu(  ) { 
	add_options_page( 'NC Timezone Settings', 'NC Timezone Settings', 'manage_options', 'nc-timezone-settings', 'nc_timezone_options_page' );
}


function nc_timezone_settings_init(  ) { 

	register_setting( 'nc_timezone_settings_group', 'nc_timezone_settings' );

	add_settings_section(
		'nc_timezone_settings_section', 
		__( 'API Settings', 'nativecommerce' ), 
		'nc_timezone_settings_section_callback', 
		'nc_timezone'
	);

	add_settings_field( 
		'nc_timezone_api_key', 
		__( 'API Key', 'nativecommerce' ), 
		'nc_timezone_api_key_render', 
		'nc_timezone', 
		'nc_timezone_settings_section' 
	);

	add_settings_field( 
		'nc_timezone_api_gateway', 
		__( 'API Gateway', 'nativecommerce' ), 
		'nc_timezone_api_gateway_render', 
		'nc_timezone', 
		'nc_timezone_settings_section' 
	);


	add_settings_field( 
		'nc_timezone_google_timezone_api_key', 
		__( 'Google Timezone API Key', 'nativecommerce' ), 
		'nc_timezone_google_timezone_api_key_render', 
		'nc_timezone', 
		'nc_timezone_settings_section' 
	);


}


function nc_timezone_api_gateway_render(  ) { 

	$options = get_option( 'nc_timezone_settings' );
	?>
	<input type='text' name='nc_timezone_settings[nc_timezone_api_gateway]' value='<?php echo $options['nc_timezone_api_gateway']; ?>' class="regular-text"></td>
	
	<?php

}


function nc_timezone_api_key_render(  ) { 

	$options = get_option( 'nc_timezone_settings' );
	?>
	<input type='text' name='nc_timezone_settings[nc_timezone_api_key]' value='<?php echo $options['nc_timezone_api_key']; ?>' class="regular-text"></td>
	
	<?php
}


function nc_timezone_google_timezone_api_key_render(  ) { 

	$options = get_option( 'nc_timezone_settings' );
	?>
	<input type='text' name='nc_timezone_settings[nc_timezone_google_timezone_api_key]' value='<?php echo $options['nc_timezone_google_timezone_api_key']; ?>' class="regular-text"></td>
	
	<?php
}


function nc_timezone_settings_section_callback(){
	
}


function nc_timezone_options_page(  ) { 

	?>
	<div class="wrap">
		<h2>NC Timezone Settings</h2>
		<?php settings_errors(); ?>

		<form action='options.php' method='post'>
			<table class="form-table">
			<tbody>
				<?php
					settings_fields( 'nc_timezone_settings_group' );
					do_settings_sections( 'nc_timezone' );
				?>
			</tbody>
			</table>
			<?php submit_button();?>

		</form>
	</div>
	<?php

}
