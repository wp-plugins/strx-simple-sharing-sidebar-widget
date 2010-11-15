<?php 
/*
Plugin Name: Strx Simple Sharing Sidebar Widget
Plugin URI: http://www.strx.it
Description: Simple Sharing Sidebar Widget
Version: 1.0
Author: Strx
Author URI: http://www.strx.it
License: GPL2
*/

require_once('strx-widget.php');
class StrxSimpleSharingSidebar_Widget extends Strx_Widget {
    function w_id(){ return 'strx-simple-sharing-sidebar-widget'; }
    function w_name(){ return 'Strx Simple Sharing Sidebar'; }

    /** Widget Default Options, abstract */
    function w_defaults(){
        return array(
            'title' => 'Share This %type',
            'sites' => 'digg,twitter,buzz,facebook',
            'hmargin' => '4',
            'customcss' => ''
        );
    }

    /** Return the dashboard admin form */
    function w_form($instance){
        $rv='';
        $rv.=   '<p>'.$this->w_form_input($instance, 'title', 'Title, if empty will not be shown; if contains %type, it will be replaced with current page type (Site, Page or Post)').'</p>';
        $rv.=   '<p>'.$this->w_form_input($instance, 'sites', 'Sites buttons to display, comma separated; the order corresponds to the buttons order (left to right)<br/>Supported Sites: digg,twitter,facebook,stumble,buzz (buzz stands for Google Buzz)').'</p>';
        $rv.=   '<p>'.$this->w_form_input($instance, 'hmargin', 'Horizontal space between every button, in pixel (default 4)').'</p>';
        $rv.=   '<p>'.$this->w_form_textarea($instance, 'customcss', 
                    'Custom CSS: You can make custom css rules for your widget that come with predefined classes:<br>
                    <strong>strx-simple-sharing-sidebar-buttons</strong>, widget wrapper<br/>
                    <strong>strx-simple-sharing-sidebar-button</strong>, applied to every button<br/>
                    <strong>strx-simple-sharing-sidebar-TYPE-button</strong>, applied to every button with TYPE specified (ie: strx-simple-sharing-sidebar-twitter-button)<br/>
                    Usage example:<br/>
                    .strx-simple-sharing-sidebar-twitter-button { border: 1px solid #555; }
                    ').'</p>';
        return $rv;
    }

	/** Title %type substitution */
	function w_title($instance){
		$t=$instance['title'];

		if (!empty($t)){
			if (strpos($t, '%type')){
				$type=(is_home() || is_front_page()?'Site':(is_single()?'Post':'Page'));
				$t=str_replace('%type', $type, $t);
			}
		}
		return $t;
	}

    /** Return the real widget content */
    function w_content($instance){
        extract($instance);

        $rv=$this->buttonStyles($instance).'<div class="strx-simple-sharing-sidebar-buttons">';	
        $sites=explode(",", $sites);
        $nsites=count($sites);
        for ($i=0; $i<$nsites; $i++){
			$site=strtolower(trim($sites[$i]));
        //foreach(explode(",", $sites) as $site){
			//$site=strtolower(trim($site));
			if (method_exists($this, $site.'Button')){
                //Don't insert margin for last button
                $hm= $i<($nsites-1)?$hmargin:'0';
				$rv.='<div style="float:left; margin-right:'.$hm.'px;" class="strx-simple-sharing-sidebar-button strx-simple-sharing-sidebar-'.$site.'-button">'.
						$this->{$site.'Button'}().
					'</div>';
			}
		}
		$rv.='</div>';

        return $rv;
    }

	function buttonStyles($instance){
		$rv='';
		$rv.='<style type="text/css">';
		$rv.='.strx-simple-sharing-sidebar-buttons { height:72px; } ';
		//$rv.='.strx-simple-sharing-sidebar-buttons div { float:left; vertical-align:middle; } ';
		$rv.='.strx-simple-sharing-sidebar-button { float:left; vertical-align:middle; } ';
		$rv.='.strx-simple-sharing-sidebar-twitter-button { margin-top:2px; } ';
		$rv.='.strx-simple-sharing-sidebar-stumble-button { margin-top:3px; } ';
		$rv.='.strx-simple-sharing-sidebar-facebook-button { margin-top:2px; } ';
        $rv.=$instance['customcss'];
		$rv.='</style>';
		return $rv;
	}

	function diggButton(){
		//http://about.digg.com/downloads/button/smart
		return '<script type="text/javascript">(function() {var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0]; s.type = \'text/javascript\'; s.async = true; s.src = \'http://widgets.digg.com/buttons.js\'; s1.parentNode.insertBefore(s, s1); })();</script><!-- Medium Button --><a class="DiggThisButton DiggMedium"></a>';
	}
	function twitterButton(){
		//http://twitter.com/about/resources/tweetbutton
		return '<a href="http://twitter.com/share" class="twitter-share-button" data-count="vertical" data-via="fstraps">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
	}
	function facebookButton(){
		//http://developers.facebook.com/docs/reference/plugins/like
		return '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like layout="box_count" width="50"></fb:like>';
	}
	function buzzButton(){
		//http://www.google.com/buzz/api/admin/configPostWidget
		return '<a title="Post to Google Buzz" class="google-buzz-button" href="http://www.google.com/buzz/post" data-button-style="normal-count"></a>
<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>';
	}
	function stumbleButton(){
		//http://www.stumbleupon.com/badges/
		return '<script src="http://www.stumbleupon.com/hostedbadge.php?s=5"></script>';
	}

	function currentUrl(){
		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
		$host     = $_SERVER['HTTP_HOST'];
		$script   = $_SERVER['SCRIPT_NAME'];
		$params   = $_SERVER['QUERY_STRING'];
		$currentUrl = $protocol . '://' . $host . $script;// . '?' . $params;
      	return $currentUrl;
	}

}

StrxSimpleSharingSidebar_Widget::w_init('StrxSimpleSharingSidebar_Widget');

