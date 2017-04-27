<?php
if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array (
	'key' => 'group_5900722b367e2',
	'title' => 'Elokuvat',
	'fields' => array (
		array (
			'key' => 'field_590055700ebe1',
			'label' => 'Ikäraja',
			'name' => 'agelimit',
			'type' => 'text',
			'instructions' => 'Elokuvan ikäraja. Esim K-7',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_590055890ebe2',
			'label' => 'Kesto',
			'name' => 'runtime',
			'type' => 'text',
			'instructions' => 'Elokuvan kesto. Esim. 1h 32min',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array (
			'key' => 'field_590055a50ebe3',
			'label' => 'Luokitus',
			'name' => 'classification',
			'type' => 'taxonomy',
			'instructions' => 'Elokuvan luokitukset',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'mantyharju-elokuva-categories',
			'field_type' => 'checkbox',
			'allow_null' => 0,
			'add_term' => 1,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'multiple' => 0,
		),
		array (
			'key' => 'field_590056eb15bdc',
			'label' => 'Lipun hinta',
			'name' => 'ticketprice',
			'type' => 'number',
			'instructions' => 'Lipun hinta ilman € -merkkiä.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '€',
			'min' => '',
			'max' => '',
			'step' => '0.01',
		),
		array (
			'key' => 'field_59006c876afd6',
			'label' => 'Trailerin osoite',
			'name' => 'trailerurl',
			'type' => 'url',
			'instructions' => 'Tähän kenttään laitetaan linkki traileriin (e.g. YouTube -osoite)',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
		),
		array (
			'key' => 'field_5900727b894e7',
			'label' => 'Näytösajat',
			'name' => 'showtimes',
			'type' => 'repeater',
			'instructions' => 'Lisää näytösajat',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array (
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'collapsed' => '',
			'min' => 0,
			'max' => 0,
			'layout' => 'table',
			'button_label' => 'Lisää esitysaika',
			'sub_fields' => array (
				array (
					'key' => 'field_590072a7894e8',
					'label' => 'Esitysaika',
					'name' => 'datetime',
					'type' => 'date_time_picker',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'display_format' => 'F j, Y H:i',
					'return_format' => 'Y-m-d H:i',
					'first_day' => 1,
				),
			),
		),
	),
	'location' => array (
		array (
			array (
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'mantyharju-elokuva',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => 'Näytösajat yksittäisinä riveinä',
));

endif;
?>