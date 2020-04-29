<?php

/*
Plugin Name: Andràtuttobene
Plugin URI: https://github.com/ArcaneDiver/BHUB-andratuttobene-buongiorni
Description: Integrate Buongiorno post-type
Version: 1.0
Author: Michele Della Mea
Author URI: https://github.com/ArcaneDiver
*/


class Buongiorni
{

	public $post_type_name = 'buongiorni';

	function __construct()
	{

		add_filter('caldera_forms_get_form_processors', function ($processors) {

			$processors["buongiorni-form-text"] = array(
				'name'              =>  'Buongiorni creation text',
				'description'       =>  'Processor to create buongiorni with text',
				'pre_processor'     =>  array("Buongiorni", "caldera_processor_text")
			);
			$processors["buongiorni-form-audio"] = array(
				'name'              =>  'Buongiorni creation audio',
				'description'       =>  'Processor to create buongiorni with audio',
				'pre_processor'     =>  array("Buongiorni", "caldera_processor_audio")
			);

			$processors["buongiorni-form-video"] = array(
				'name'              =>  'Buongiorni creation video',
				'description'       =>  'Processor to create buongiorni with video',
				'pre_processor'     =>  array("Buongiorni", "caldera_processor_video")
			);

			$processors["buongiorni-form-link"] = array(
				'name'              =>  'Buongiorni creation link',
				'description'       =>  'Processor to create buongiorni with link',
				'pre_processor'     =>  array("Buongiorni", "caldera_processor_link")
			);


			return $processors;
		});

		$this->admin_page_only_for_admin();
		$this->register_post_type();
		$this->permalinks();
	}

	function permalinks()
	{

		register_deactivation_hook(plugin_dir_path(__FILE__) . 'buongiorni.php', 'flush_rewrite_rules');
	}


	function register_post_type()
	{

		add_action("init", function () {

			register_post_type($this->post_type_name, array(
				"labels" => array(
					"name" => __("Buongiorni"),
					"singular_name" => __("Buongiorni")
				),
				//"supports" => array("title", "custom-fields"),
				"public" => true,
				"has_archive" => true,
				"rewrite" => array(
					"slug" => "buongiorni"
				)
			));

			flush_rewrite_rules();
		});
	}

	function admin_page_only_for_admin()
	{
		add_action("admin_init", function () {

			$user = wp_get_current_user();

			if (in_array('subscriber', (array) $user->roles)) {
				wp_redirect("/");
			}
		});

		add_action('after_setup_theme', function () {
			if (!current_user_can('administrator') && !is_admin()) {
				show_admin_bar(false);
			}
		});
	}

	function create_post_text($title, $text)
	{
		$id = wp_insert_post(array(
			'post_title' => $title,
			'post_type' => $this->post_type_name,
			'post_status' => 'draft'

		));


		update_field('text', $text, $id);
	}

	function create_post_audio($title, $audio, $text) {
		$id = wp_insert_post(array(
			'post_title' => $title,
			'post_type' => $this->post_type_name,
			'post_status' => 'draft'

		));


		update_field('text', $text, $id);
		update_field('audio', "<audio controls> <source src=".$audio."> </audio>", $id);
		
	}

	function create_post_video($title, $video, $text) {
		$id = wp_insert_post(array(
			'post_title' => $title,
			'post_type' => $this->post_type_name,
			'post_status' => 'draft'

		));


		update_field('text', $text, $id);
		update_field('video', "<video controls> <source src=".$video."></video>", $id);
	}

	function create_post_link($title, $link, $text) {
		$id = wp_insert_post(array(
			'post_title' => $title,
			'post_type' => $this->post_type_name,
			'post_status' => 'draft'

		));


		update_field('text', $text, $id);
		update_field('link', $link, $id);
	}

	

	public static function caldera_processor_text($config, $form, $processor_id)
	{
		global $buongiorni;


		if(!is_user_logged_in()) {
			return array(
				"note" => "Please log-in",
				"type" => "error"
			);
		}

		$data = Caldera_Forms::get_submission_data($form);


		$buongiorni->create_post_text($data["fld_8340036"], $data["fld_6647393"]);

		return array(
			'note' => json_encode($data),
			'type' => 'error'
		);
	}

	public static function caldera_processor_audio($config, $form, $processor_id)
	{
		global $buongiorni;

		if(!is_user_logged_in()) {
			return array(
				"note" => "Please log-in",
				"type" => "error"
			);
		}


		$data = Caldera_Forms::get_submission_data($form);

		$buongiorni->create_post_audio($data["fld_6794564"], $data["fld_1227424"][0], $data["fld_4543576"]);

		return array(
			'note' => json_encode($data),
			'type' => 'error'
		);
	}


	public static function caldera_processor_video($config, $form, $processor_id)
	{
		global $buongiorni;

		if(!is_user_logged_in()) {
			return array(
				"note" => "Please log-in",
				"type" => "error"
			);
		}



		$data = Caldera_Forms::get_submission_data($form);
		$buongiorni->create_post_video($data["fld_6061100"], $data["fld_5741964"][0], $data["fld_2966742"]);

		return array(
			'note' => json_encode($data),
			'type' => 'error'
		);
	}

	public static function caldera_processor_link($config, $form, $processor_id)
	{
		global $buongiorni;

		if(!is_user_logged_in()) {
			return array(
				"note" => "Please log-in",
				"type" => "error"
			);
		}


		$data = Caldera_Forms::get_submission_data($form);
		$buongiorni->create_post_link($data["fld_5810910"], $data["fld_6221578"], $data["fld_3911754"]);

		return array(
			'note' => json_encode($data),
			'type' => 'error'
		);
	}


}



$buongiorni = new Buongiorni();

