<?php
/**
 * Plugin Name: Watcher Social Net
 * Plugin URI: www.cws-tech.com
 * Description: Get Feeds Social Twitter / Instagram
 * Version: 1.0
 * Author: Comworks Technologies
 * Author URI: www.cws-tech.com
 * License: MIT
 */
 
	include ('lib/functions.php'); 
    
    function getFeeds($attributes){
    	
    	$attributes['instagram_access_token'] = get_option('instagram_access_token');
    	$attributes['twitter_access_token'] = get_option('twitter_access_token');
    	$attributes['twitter_access_token_secret'] = get_option('twitter_access_token_secret');
    	$attributes['twitter_consumer_key']= get_option('twitter_consumer_key');
    	$attributes['twitter_consumer_secret'] = get_option('twitter_consumer_secret');
    	
    	$functions = new functions($attributes);
    	
    	$feeds = $functions->getFeeds();
    	
    	if (isset($feeds)){
    		echo '<div class="container">';
    		echo '<div class="social-container">';
    		foreach ($feeds as $feed) {
    			
    			echo '<div class="social-element" dt-create="' . $feed['created_time'] . '" social-id = "'. $feed['id'] .'">';
    			echo '<div class="content">';
    			echo '<a class="pull-left" href="'.$feed['url'].'" target="_blank">';
    			echo '<img class="media-object" src="'.$feed['user_picture'].'">';
    			echo '</a>';
    			echo '<div class="media-body">';
    			echo '<p>';
    			echo '<i class="fa fa-'.$feed['type'].'"></i>';
    			echo '<span class="author-title">'.$feed['user'].'</span>';
    			echo '<span class="muted pull-right">'.$feed['date_show'].'</span>';
    			echo '</p>';
    			echo '<div class="text-wrapper">';
    			echo '<p class="social-text">'.$feed['text'].'</p>';
    			echo '<p class="social-text"><a href="'.$feed['url'].'" target="_blank" class="read-button"> Leer más</a></p>';
    			echo '</div>';
    			echo '</div>';
    			echo '</div>';
    			if (isset($feed['img']))
    				echo '<img class="attachment" src="'.$feed['img'].'" />';
    			echo '</div>';
    		}
    		echo '</div>';
    		echo '</div>';
    	}
    	
    	unset($feeds);
    }
    
    add_shortcode('getfeeds', 'getFeeds');
    
    add_action( 'wp_enqueue_scripts', 'prefix_add_cws_stylesheet' );
    
    /**
     * Añadimos estilo
     */
    function prefix_add_cws_stylesheet() {
        wp_register_style( 'prefix-style', plugins_url('/css/cws-social.css', __FILE__) );
        wp_enqueue_style( 'prefix-style' );
        
        wp_register_style( 'style-awesome', plugins_url('/css/font-awesome.css', __FILE__) );
        wp_enqueue_style( 'style-awesome' );
        
       
    }

    // Configuraciones
    function watcherSocialNetSettings(){
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        
        echo '<div class="wrap"> ';
        echo '<h2>Watcher Social Net Configuration</h2>';
        echo '<form action="options.php" method="post">';
        
        settings_fields('watchersocialnet-settings');
        do_settings_sections('watchersocialnet-settings');
        
        echo '<h3>Social Network - Instagram</h2>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th scope ="row">';
        echo '<label>Access Token</label>';
        echo '</th>';
        echo '<td>';
        echo '<input class="regular-text" id="instagram_access_token" name="instagram_access_token" type="text"  value="'. get_option('instagram_access_token').'"/>';
        echo '</td>';
        echo '</tr>';
        
        echo '</table>';
        
        echo '<h3>Social Network - Twitter</h2>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th scope ="row">';
        echo '<label>Auth Access Token</label>';
        echo '</th>';
        echo '<td>';
        echo '<input class="regular-text" id="twitter_access_token" name="twitter_access_token" type="text"  value="'. get_option('twitter_access_token').'"/>';
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        
        echo '<tr>';
        echo '<th scope ="row">';
        echo '<label>Auth Access Token Secret</label>';
        echo '</th>';
        echo '<td>';
        echo '<input class="regular-text" id="twitter_access_token_secret" name="twitter_access_token_secret" type="text"  value="'. get_option('twitter_access_token_secret').'"/>';
        echo '</td>';
        echo '</tr>';
        echo '<tr>';
        
        echo '<tr>';
        echo '<th scope ="row">';
        echo '<label>Consumer Key</label>';
        echo '</th>';
        echo '<td>';
        echo '<input class="regular-text" id="twitter_consumer_key" name="twitter_consumer_key" type="text"  value="'. get_option('twitter_consumer_key').'"/>';
        echo '</td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<tr>';
        echo '<th scope ="row">';
        echo '<label>Consumer Secret</label>';
        echo '</th>';
        echo '<td>';
        echo '<input class="regular-text" id="twitter_consumer_secret" name="twitter_consumer_secret" type="text"  value="'. get_option('twitter_consumer_secret').'"/>';
        echo '</td>';
        echo '</tr>';
        
        echo '</table>';
        
        submit_button();
        
        echo '</form>';
        echo '</div>';
    }
    
    
    function watcherSocialNetPluginMenu() {
        add_options_page( 'Watcher Social Net Settings', 'Watcher Social Net', 'manage_options', 'watchersocialnet_settings', 'watcherSocialNetSettings' );
    }
    
    add_action( 'admin_menu', 'watcherSocialNetPluginMenu' );

    function watcherSocialNetRegisterSettings()
    {
        register_setting( 'watchersocialnet-settings', 'instagram_access_token' );
        register_setting( 'watchersocialnet-settings', 'twitter_access_token' );
        register_setting( 'watchersocialnet-settings', 'twitter_access_token_secret' );
        register_setting( 'watchersocialnet-settings', 'twitter_consumer_key' );
        register_setting( 'watchersocialnet-settings', 'twitter_consumer_secret' );
    }
    
    add_action( 'admin_init', 'watcherSocialNetRegisterSettings');