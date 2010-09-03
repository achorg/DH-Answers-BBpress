<?php
/*
Plugin Name: BB-Syntax
Plugin URI: http://www.finalwebsites.com/bbpress/geshi-syntax-highlighting.php
Description: This plugin will highlight your code snippets using the PHP class GeSHi. Wrap code blocks with <code>&lt;pre lang="LANGUAGE" line="1"&gt;</code> and <code>&lt;/pre&gt;</code> where <code>LANGUAGE</code> is a GeSHi supported language syntax.  The <code>line</code> attribute is optional. This plugin is a fork from the Wordpress plugin <a href="http://wordpress.org/extend/plugins/wp-syntax/">WP_Syntax</a> by Ryan McGeary.
Author: Olaf Lederer
Version: 0.1.1
Author URI: http://www.finalwebsites.com/
*/



function allow_syntax_tag($tags) {
	$tags['pre'] = array('lang' => array(), 'line' => array(), 'escaped' => array(), 'style' => array(), 'width' => array()); 
	return $tags;
}
add_filter('bb_allowed_tags', 'allow_syntax_tag');

include_once("geshi/geshi.php");


if (!defined("BB_PLUGIN_URL"))  define("BB_PLUGIN_URL",  bb_get_uri() . "/my-plugins");

function bb_syntax_head()
{
  $css_url = BB_PLUGIN_URL . "bb-syntax/bb-syntax.css";
  echo "\n".'<link rel="stylesheet" href="' . $css_url . '" type="text/css" media="screen" />'."\n";
}

function bb_syntax_code_trim($code)
{
    // special ltrim b/c leading whitespace matters on 1st line of content
    $code = preg_replace("/^\s*\n/siU", "", $code);
    $code = rtrim($code);
    return $code;
}

function bb_syntax_substitute(&$match)
{
    global $bb_syntax_token, $bb_syntax_matches;

    $i = count($bb_syntax_matches);
    $bb_syntax_matches[$i] = $match;

    return "\n\n<p>" . $bb_syntax_token . sprintf("%03d", $i) . "</p>\n\n";
}

function bb_syntax_line_numbers($code, $start)
{
    $line_count = count(explode("\n", $code));
    $output = "<pre>";
    for ($i = 0; $i < $line_count; $i++)
    {
        $output .= ($start + $i) . "\n";
    }
    $output .= "</pre>";
    return $output;
}

function bb_syntax_highlight($match)
{
    global $bb_syntax_matches;

    $i = intval($match[1]);
    $match = $bb_syntax_matches[$i];

    $language = strtolower(trim($match[1]));
    $line = trim($match[2]);
    $escaped = trim($match[3]);
    $code = bb_syntax_code_trim($match[4]);
    //if ($escaped == "true") $code = htmlspecialchars_decode($code);
    $code = htmlspecialchars_decode($code);
	//$code = str_replace("&lt;", "<", $code);
	//$code = str_replace("&gt;", ">", $code);
    $geshi = new GeSHi($code, $language);
    $geshi->enable_keyword_links(false);
    do_action_ref_array('bb_syntax_init_geshi', array(&$geshi));

    $output = "\n<div class=\"bb_syntax\">";

    if ($line)
    {
        $output .= "<table><tr><td class=\"line_numbers\">";
        $output .= bb_syntax_line_numbers($code, $line);
        $output .= "</td><td class=\"code\">";
        $output .= $geshi->parse_code();
        $output .= "</td></tr></table>";
    }
    else
    {
        $output .= "<div class=\"code\">";
        $output .= $geshi->parse_code();
        $output .= "</div>";
    }

    $output .= "</div>\n";
    return $output;
}

function bb_syntax_before_filter($content)
{
    $content = preg_replace_callback(
        "/\s*<pre(?:lang=[\"']([\w-]+)[\"']|line=[\"'](\d*)[\"']|escaped=[\"'](true|false)?[\"']|\s)+>(.*)<\/pre>\s*/siU",
        "bb_syntax_substitute",
        $content
    );
    return $content;
}

function bb_syntax_after_filter($content)
{
    global $bb_syntax_token;

     $content = preg_replace_callback(
         "/<p>\s*".$bb_syntax_token."(\d{3})\s*<\/p>/si",
         "bb_syntax_highlight",
         $content
     );
    return $content;
}

$bb_syntax_token = md5(uniqid(rand()));

// information on how to use it
function add_content_post_form(){
	echo '<p>To highlight syntax and number lines, use the following format: <code>'.htmlentities('<pre lang="ruby" line="1">puts "hello world"</pre>').'</code></p>';
}

add_action('post_form', 'add_content_post_form', 1);
add_action('edit_form','add_content_post_form', 1);

// Add styling
add_action('bb_head', 'bb_syntax_head');

// We want to run before other filters; hence, a priority of 0 was chosen.
// The lower the number, the higher the priority.  10 is the default and
// several formatting filters run at or around 6.
add_filter('post_text', 'bb_syntax_before_filter', 10);


// We want to run after other filters; hence, a priority of 99.
add_filter('post_text', 'bb_syntax_after_filter', 10);


?>
