<?php
$in_context_array = $_GET['in_context_array'];
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
echo $return_data;
?>