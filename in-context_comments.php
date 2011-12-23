<?php
/**
 * @package InContext_Comments
 * @version 0.8.2
 */
/*
Plugin Name: In-Context Comments
Plugin URI: http://wizag.com/incontext.php
Description: Allow readers to add comments in the context of a post, rather than at the end of a post.
Author: Wizag LLC
Version: 0.8.2
Author URI: http://wizag.com/incontext.php
*/
include_once 'icc_config.php';

function zxy_add_js()
{		
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/js/prototype.js"></script>');   
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/js/effects.js"></script>');   
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/js/window.js"></script>');    
    print('<script type="text/javascript" src="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/js/self_window.js"></script>');
    print('<link rel="stylesheet" type="text/css" href="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/css/default.css"></link>');   
    print('<link rel="stylesheet" type="text/css" href="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/css/alphacube.css"></link>');
    print('<link rel="stylesheet" type="text/css" href="'
            . get_option('siteurl') 
            . '/wp-content/plugins/in-context-comments/css/self_window.css"></link>');
}

add_action('wp_head', 'zxy_add_js');
add_action('wp_ajax_nopriv_refreshNum', 'refreshNum');
add_action('wp_ajax_refreshNum', 'refreshNum');

function change_body_content($subject)
{
	global $wpdb;
    $table_flag = get_option('ICC_table_db');
    if(!isset($table_flag) || $table_flag!='true_and_1')
    {
	     $sql_cratetable = "Create table incontextblog(
                         id int(10) unsigned primary key auto_increment not null,
                         comment_num int(10) unsigned not null default 0,
                         keyword varchar(200) not null,
                         url varchar(200) not null,
                         star_property tinyint(3) not null default 0
                        )";
         $wpdb->query($sql_cratetable);
         update_option('ICC_table_db', 'true_and_1');
    }
		if(!is_single())
		{
			return $subject;
		}
    $wordpress_url = get_option('siteurl');
    $wordpress_article_title = get_the_title();
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
     

     $return_data = get_document_content($subject,$wordpress_url,$wordpress_page,$wordpress_article_user,$wordpress_login_user,$wordpress_auto,$wordpress_size,$wordpress_article_title);
	  if(preg_match("/<icc_update_wp_post>/i",$return_data))
		{		
			$icc_cut_data_pos = strpos($return_data,"<icc_update_wp_post>");
			$icc_post_update_data = substr($return_data,$icc_cut_data_pos,strlen($return_data));
			$return_data = substr($return_data,0,$icc_cut_data_pos);
			$icc_post_update_data = str_replace("<icc_update_wp_post>","",$icc_post_update_data);
			$sql_test = "UPDATE $wpdb->posts SET post_content = '".mysql_real_escape_string($icc_post_update_data)."' where guid='".$wordpress_url."/?p=".$wordpress_page."';";
			$wpdb->query($sql_test);
		}
		return $return_data;
}

function get_document_content($subject,$wordpress_url,$wordpress_page,$wordpress_article_user,$wordpress_login_user,$wordpress_auto,$wordpress_size,$wordpress_article_title)
{
   try
   {
       global $wpdb;
	   $wordpress_md5 = "";
	   $subject = preg_replace("|<icc_update_wp_post>|U","",$subject);
	   $subject_db = "";
	   $b_add_auto = "true";
	
	   if(preg_match("/<in-context-comment:auto-on>/i",$subject))
	   {
		$b_add_auto = "false";
	   }
	  if(preg_match("/<in-context-comment:auto-off>/i",$subject))
	  {
		$b_add_auto = "false";
	  }
	  else if($wordpress_auto=="false")
	  {
	  }
	  else if(preg_match("/<icc-first-publish>/i",$subject))
	  {
	  }
	 else
	 {
		$icc_add_star_num = 350;
		if($wordpress_size!="" && $wordpress_size>350)
		{
			$icc_add_star_num = $wordpress_size;
		}
		preg_match_all("|<in-context-comment:block-size:(.+)?>|U",$subject,$out);
		if($out[1][0] >= 350)
		{
			$icc_add_star_num = $out[1][0];
		}		
		//------end
		$array_subject = explode("\r\n",$subject);
		$subject_copy = "";
		for($tmp_i=0,$tmp_j=0;$tmp_i < count($array_subject);$tmp_i++)
		{
			$subject_copy .= $array_subject[$tmp_i];
			$array_temp = $array_subject[$tmp_i];
			$array_temp = preg_replace("/<(.+)?>/i", "", $array_temp);
			
			if(strlen($array_temp) > $icc_add_star_num){
				$subject_copy .= "<in-context-comment:auto:".$tmp_j.">";
				$tmp_j++;
			}
			if($tmp_i < count($array_subject)-1)
			{
				$subject_copy .= "\r\n";
			}
		}		
		$subject = $subject_copy;
		$subject_db = "<icc_update_wp_post>".$subject."<icc-first-publish>";
	}
	$message = "";
	preg_match_all("|<in-context-comment:here:(.+)?>|U",$subject,$out);
	if(count($out[1])>0)
	{
		$message = $message."out >0";
		$sql_select_count = "select keyword,comment_num from incontextblog where url='".$wordpress_url."?p=".$wordpress_page."' and star_property=1";		
		$sql_result_count = $wpdb->get_results($sql_select_count);
		if(isset($sql_result_count) && !empty($sql_result_count))
		{
				foreach ($sql_result_count as $a_comments_count)
				{
					if($a_comments_count->keyword <10)
					{
						$replace_here = "<span class='InContext_HaveComments' id='add_img_slf_".($a_comments_count->keyword)."' onclick=\"javascript:show_self('".$a_comments_count->keyword."',this.scrollTop,this.scrollLeft,1,event,'".$wordpress_article_title."')\">&nbsp;".$a_comments_count->comment_num."&nbsp;<span class='InContext_HaveComments_Up'></span></span>";
					}
					else
					{
						$replace_here = "<span class='InContext_HaveComments' id='add_img_slf_".($a_comments_count->keyword)."' onclick=\"javascript:show_self('".$a_comments_count->keyword."',this.scrollTop,this.scrollLeft,1,event,'".$wordpress_article_title."')\">".$a_comments_count->comment_num."<span class='InContext_HaveComments_Up'></span></span>";
					}
					$subject = preg_replace("|<in-context-comment:here:".($a_comments_count->keyword)."?>|U",$replace_here,$subject);
					$message = $message.$a_comments_count->keyword."===>".$a_comments_count->comment_num."<br>";
				}
		}
		$subject = preg_replace("|<in-context-comment:here:(.+)?>|U","<in-context-comment:here:\${1}><icc_end_icc>",$subject);
		$subject = preg_replace("|<in-context-comment:here:(.+)['\"](.+)?(<icc_end_icc>)|U","<font color='red'>ICC_Key_Words_Error!</font>",$subject);		
		$replace_here = "<span class='InContext_NoComments' id='add_img_slf_\${1}' onclick=\"javascript:show_self('\${1}',this.scrollTop,this.scrollLeft,1,event,'".$wordpress_article_title."')\">&nbsp;0&nbsp;<span class='InContext_NoComments_Up'></span></span>";
		$subject = preg_replace("|<in-context-comment:here:(.+)?(><icc_end_icc>)|U",$replace_here,$subject);
	}
	preg_match_all("|<in-context-comment:auto:(.+)?>|U",$subject,$out);
	if(count($out[1])>0)
	{	
		$sql_select_count = "select keyword,comment_num from incontextblog where url='".$wordpress_url."?p=".$wordpress_page."' and star_property=0";													
		$sql_result_count = $wpdb->get_results($sql_select_count);
		if(isset($sql_result_count) && !empty($sql_result_count))
		{
				foreach ($sql_result_count as $a_comments_count)
				{
					if($a_comments_count->keyword < 10)
					{
						$replace_here = "<span class='InContext_HaveComments' id='panelDiv".($a_comments_count->keyword)."' onclick=\"javascript:show_self('".$a_comments_count->keyword."',this.scrollTop,this.scrollLeft,0,event,'".$wordpress_article_title."')\">&nbsp;".$a_comments_count->comment_num."&nbsp;<span class='InContext_HaveComments_Up'></span></span>";
					}
					else
					{
						$replace_here = "<span class='InContext_HaveComments' id='panelDiv".($a_comments_count->keyword)."' onclick=\"javascript:show_self('".$a_comments_count->keyword."',this.scrollTop,this.scrollLeft,0,event,'".$wordpress_article_title."')\">".$a_comments_count->comment_num."<span class='InContext_HaveComments_Up'></span></span>";
					}
					$subject = preg_replace("|<in-context-comment:auto:".($a_comments_count->keyword)."?>|U",$replace_here,$subject);
				}
		}
		$subject = preg_replace("|<in-context-comment:auto:(.+)?>|U","<in-context-comment:auto:\${1}><icc_end_icc>",$subject);
		$subject = preg_replace("|<in-context-comment:auto:(.+)['\"](.+)?(<icc_end_icc>)|U","<font color='red'>ICC_Key_Words_Error!</font>",$subject);
		$replace_here = "<span class='InContext_NoComments' id='panelDiv\${1}' onclick=\"javascript:show_self('\${1}',this.scrollTop,this.scrollLeft,0,event,'".$wordpress_article_title."')\">&nbsp;0&nbsp;<span class='InContext_NoComments_Up'></span></span>";
		$subject = preg_replace("|<in-context-comment:auto:(.+)?(><icc_end_icc>)|U",$replace_here,$subject);
	}
	$script = "<script type=\"text/javascript\">";		
	$script .= "init_g_val(\"".$wordpress_url."\",\"".$wordpress_md5."\",\"".$wordpress_login_user."\",\"".$wordpress_page."\");";
	$script .= "</script>";
	$subject .= $script;
	if($b_add_auto=="true")
	{
		if($wordpress_auto=="false"){
			$subject_db .= "<in-context-comment:auto-off>";
		}
		else{
			$subject_db .= "<in-context-comment:auto-on>";
		}
	}
	$subject .= $subject_db;
	$return_result = stripslashes($subject);
	return  $return_result;
  }
   catch(Exception $ex)
   {
     return $subject;
   }
}

add_filter('the_content', 'change_body_content', 1);
function refreshNum()
{
	global $wpdb;
	$wordpress_url = $_POST['wordpress_url'];
	$wordpress_page = $_POST['wordpress_page'];
	$keyword = $_POST['keyword'];
	$star_property = $_POST['star_property'];
	$in_context_array =$wordpress_url.";".$wordpress_page.";".$keyword;
    $post = array   
   (   
      'in_context_array' => $in_context_array,
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
   $return_data = file_get_contents('http://incontext.wizag.com/blogcomment/wordpress/In-Context_Comments_ajax.php', false, stream_context_create($context));
   $str_star_num = '/<ICC-Star-Num0>(.+?)<ICC-Star-Num1>|i/';
   $str_star_key = '/<ICC-Star-Keys0>(.+?)<ICC-Star-Keys1>|i/';
   $str_star_num_inf = snatch_str($str_star_num,$return_data);   
   if($str_star_num_inf == "error_error")
   {
   	
   }else
   {
   	 $str_star_key_inf =  snatch_str($str_star_key,$return_data);
     $sql_select_count = "select keyword,comment_num from incontextblog where url='".$wordpress_url."?p=".$wordpress_page."' and keyword='".$keyword."' and star_property=".$star_property;													
	 $sql_result_count = $wpdb->get_results($sql_select_count);

	 if(isset($sql_result_count) && !empty($sql_result_count))
	 {
		$updatesql = "update incontextblog set comment_num=".$str_star_num_inf." where url='".$wordpress_url."?p=".$wordpress_page."' and keyword='".$keyword."' and star_property=".$star_property;
		$num = $wpdb->query($updatesql);
	 }
	 else 
	 {
		$insertsql = "insert into incontextblog 
		              set comment_num=".$str_star_num_inf.",
		              keyword = '".$keyword."',
		              url='".$wordpress_url."?p=".$wordpress_page."',
		              star_property=".$star_property;
		$num = $wpdb->query($insertsql);
	 }
   }
   
   die($return_data);
   exit;
}	
function snatch_str($str, $data)
{
	if ($data) {
		@preg_match($str,$data,$match);
		if(!$match)
		{
			return "-1";
		}
		else
		{
			return trim($match[1]);
		}
	} else {
		return "-1";
	}
}
?>
