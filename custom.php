<?php

/**
 * Plugin Name: Custom Plugin
 * Author: Devsyed
 * Description: Extending WooFood Functionality
 * Text-Domain: custom-plugin
 */

 final class Custom{
	 public function __construct(){
		add_action('plugins_loaded',array($this,'init'));
	 }

	 public function init(){
		 if(class_exists('WooCommerce')){
			 require_once 'functions.php'; 
		 }
	 }
 }

new Custom();