<?php
function ICC_config_page()
{
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('ICC Configuration'), 'ICC Configuration', 'manage_options', 'ICC-key-config', 'ICC_conf');	
}

function ICC_conf() {
	if(isset($_POST['submit']))
	{
		if(isset($_POST['ICC_Star_H']))
		{
			update_option('ICC_Star_H', $_POST['ICC_Star_H']);
			if($_POST['ICC_Star_H']<350){update_option('ICC_Star_H', 350);}		
		}
		if(isset($_POST['ICC_Star_Add'])){	update_option('ICC_Star_Add', "true" );}
		else{	update_option('ICC_Star_Add', "false" );}
	}
	?>
	<div id="icc_config_div_body">
		<div id="icc_div_content">
			<div id="icc_cfg_head">ICC Configuration</div>
			<div id="icc_cfg_explain">
				<b>Introductions:</b><br>				
				1. First make sure you have activated this plugin.
				<br>
				2. You have complete control on where add "In-Context Comment" icons in each blog post using the following three commands:
					<div id="icc_cfg_epl2">
					2.1 &lt;in-context-comment:auto-off&frasl;on&gt;
					<br>&nbsp;&nbsp;&nbsp;&nbsp;Add &lt;in-context-comment:auto-off&frasl;on&gt; anywhere in a blog post (in HTML edit mode) to turn on the function to automatically add  an In-Context Comment icon at the end of each paragraph that is longer than a certain number of characters (see command below). The auto mode is on by default for all posts. You can use &lt;in-context-comment:auto-off&gt; to turn this function off in a post you don't want in context comments.
					<br>2.2 &lt;in-context-comment:block-size:N&gt;
					<br>&nbsp;&nbsp;&nbsp;&nbsp;&lt;in-context-comment:block-size:N&gt;where N is a positive integer, e.g., 350. When the auto mode is on, this command controls the minimum number of characters a paragraph must have for an In-Context Comment icon to be added at the end of the paragraph. This is to avoid adding icons to a very short paragraph.
					<br>2.3 &lt;in-context-comment:here:tag&gt;
					<br>&nbsp;&nbsp;&nbsp;&nbsp;&lt;in-context-comment:here:tag&gt;where "tag" is any word or words connected by hyphen, e.g., first-comment. You can add this command at any place inside a post where you want readers to leave comments. This command works regardless whether the auto mode is on or off.
					<br>					
					</div>
					3. When the auto mode is on, if you need to edit a post, in the HTML edit mode, you will see a command &lt;in-context-comment:autotag&gt; (where autotag is an auto generated tag for the icon) at the location of each  In-Context Comment icon. You can cut and paste the command &lt;in-context-comment:autotag&gt; to move the comment to any place in the post.
				<br>
				<br><font color='red'>(Note: This plugin will only affect new posts you write after the installation, and you need to edit a post in the HTML edit mode)</font>
			</div>
			<br>
			<div>You can turn on/off the auto mode and change the minimum block-size globally for all posts. The configuration here will apply to all posts unless you use the commands above to change it for an individual post</div>
			<form action="" method="post">
				<div id="icc_cfg_set01">
					1. auto mode:<input type="checkbox" id="ICC_Star_Add" name="ICC_Star_Add" <?php 
					$tmp_check = ' checked="checked" ';
					if(get_option('ICC_Star_Add') == 'true'){
						$tmp_check = ' checked="checked" ';
					}
					if(get_option('ICC_Star_Add') == 'false'){
						$tmp_check = ' ';
					}					
					echo $tmp_check;					
					?>/>
				</div>
				<br>
				<div id="icc_cfg_set02">
					2. minimum block-size:<input type="text" id="ICC_Star_H" name="ICC_Star_H" size="12" maxLength="12" value="<?php 
					$tmp_star_h = 350;
					if(get_option('ICC_Star_H'))
					{
						$tmp_star_h = get_option('ICC_Star_H');
					}
					echo $tmp_star_h;
					?>"/> (>=350 characters)
				</div>
				<br>
				<input name="submit" type="submit" value="Update" />
			</form>
		</div>
	</div>
	
	<script type="text/javascript">
	</script>
	<style rel="stylesheet" type="text/css">
		#icc_config_div_body{
			text-align: center;
			margin:0;
			padding:0;
		}
		#icc_div_content{
			width: 600px;
			text-align: left;
			margin:0 auto;
		}
		#icc_cfg_head{
			text-align: center;
			font-size: 24px;
		}
		#icc_cfg_epl2{
			margin: 0 0 0 20px;
		}
		#icc_cfg_set01{
			margin: 10px 0 0 0;
		}
	</style>
<?php
}
function ICC_plugin_action_links( $links, $file ) {
	if ( $file == plugin_basename( dirname(__FILE__).'/in-context_comments.php' ) ) {
		$links[] = '<a href="plugins.php?page=ICC-key-config">'.__('Settings').'</a>';
	}
	return $links;
}
add_filter( 'plugin_action_links', 'ICC_plugin_action_links', 10, 2 );
add_action('admin_menu','ICC_config_page');
?>