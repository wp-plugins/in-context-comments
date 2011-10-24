<?php
/**
 * @package InContext_Comments
 * @version 0.8.1
 */
/*
Plugin Name: In-Context Comments
Plugin URI: http://wizag.com/incontext.php
Description: Allow readers to add comments in the context of a post, rather than at the end of a post.
Author: Wizag LLC
Version: 0.8.1
Author URI: http://wizag.com/incontext.php
*/
include_once 'ICC_Config.php';

function zxy_add_js()
{		
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/In-Context_Comments/js/prototype.js"></script>');   
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/In-Context_Comments/js/effects.js"></script>');   
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/In-Context_Comments/js/window.js"></script>');    
    print('<link rel="stylesheet" type="text/css" href="'
            . get_option('siteurl') 
            . '/wp-content/plugins/In-Context_Comments/css/default.css"></link>');   
    print('<link rel="stylesheet" type="text/css" href="'
            . get_option('siteurl') 
            . '/wp-content/plugins/In-Context_Comments/css/alphacube.css"></link>');
}

add_action('wp_head', 'zxy_add_js');

function change_body_content($subject)
{
		if(!is_single())
		{
			return $subject;
		}
    $wordpress_url = get_option('siteurl');
    $wordpress_page = get_the_ID();
    $wordpress_article_user = "";
    $wordpress_login_user = "";
    $wordpress_auto = get_option('ICC_Star_Add');
    $wordpress_size = get_option('ICC_Star_H');
    $return_data="";    			    
    if(is_user_logged_in())
    {
	    	if(current_user_can('level_10'))
	    	{
					$current_user = wp_get_current_user();
					$wordpress_article_user = $current_user->user_login;
					$wordpress_login_user = $wordpress_article_user;
				}	
				else
				{			
		  		$current_user = wp_get_current_user();
					$wordpress_article_user = get_the_author();
					$wordpress_login_user = $current_user->user_login;
				}
		}
		$post = array   
		(   
		    'post_data' => $subject,
				'wordpress_url' => $wordpress_url,
				'wordpress_page' => $wordpress_page,
				'wordpress_article_user' => $wordpress_article_user,
				'wordpress_login_user' => $wordpress_login_user,
				'wordpress_auto' => $wordpress_auto,
				'wordpress_size' => $wordpress_size,
		);  
    $context = array();      
    if (is_array($post))   
    {   
        ksort($post);   
   
        $context['http'] = array   
        (   
            'method' => 'POST',
            'content' => http_build_query($post, '', '&'),
        );   
    }    
	  $return_data = file_get_contents('http://incontext.wizag.com/blogcomment/wordpress/wordpress_ajax.php', false, stream_context_create($context));
		if(preg_match("/<icc_update_wp_post>/i",$return_data))
		{		
			$icc_cut_data_pos = strpos($return_data,"<icc_update_wp_post>");
			$icc_post_update_data = substr($return_data,$icc_cut_data_pos,strlen($return_data));
			$return_data = substr($return_data,0,$icc_cut_data_pos);
			$icc_post_update_data = str_replace("<icc_update_wp_post>","",$icc_post_update_data);
			global $wpdb;
			$sql_test = "UPDATE $wpdb->posts SET post_content = '".mysql_real_escape_string($icc_post_update_data)."' where guid='".$wordpress_url."/?p=".$wordpress_page."';";
			$wpdb->query($sql_test);
		}		
		return $return_data;
}

add_filter('the_content', 'change_body_content', 1);
?>
