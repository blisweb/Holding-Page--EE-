<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
  'pi_name' => 'Holding Page',
  'pi_version' => '1.0',
  'pi_author' => 'Sam Lomax',
  'pi_author_url' => 'http://www.blis.net.au/',
  'pi_description' => 'allows your site to be online but restricted to set IP addresses',
  'pi_usage' => Holding_Page::usage()
  );

/**
 *  Field Finder Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Sam Lomax
 * @copyright		Copyright (c) 2011, Sam Lomax
 * @link			http://www.blis.net.au
 */

class Holding_Page
{

var $return_data = "";

	// --------------------------------------------------------------------

	/**
	 * is iPhone
	 *
	 * Returns data if iphone is the user agent
	 *
	 * @access	public
	 * @return	string or php header redirect
	 */

  function Holding_Page()
  {
	
			$this->EE =& get_instance();
		
		// Fetch our parameters from the plugin tag
		$location = str_replace("&#47;", "/", $this->EE->TMPL->fetch_param('location'));
		$method = $this->EE->TMPL->fetch_param('method');
		
		// Check for the logged_in param.
		$logged_in = $this->EE->TMPL->fetch_param('logged_in');
		
		// Check for the group param.
		$member_group = $this->EE->TMPL->fetch_param('group_id');
		
		$ips = $this->EE->TMPL->fetch_param('ips');
		
		// Check to see if location should be set to http_referer
		$referrer = strtolower($this->EE->TMPL->fetch_param('referrer'));
		
		if ($logged_in === "yes")
		{
			$logged_in = ($this->EE->session->userdata('member_id') == 0) ? FALSE : TRUE; 
		}
		elseif ($logged_in === "no")
		{
			$logged_in = ($this->EE->session->userdata('member_id') != 0) ? FALSE : TRUE; 
		}
		
		
		$ip = false;
		$ipaddress = $_SERVER['REMOTE_ADDR'];
		if ($ips){
			$iparray = explode(",",$ips);
			foreach ($iparray as $i){
			    if ($i == $ipaddress){
				    $ip = true;
			    }					
			}
			
		} else {
			
			$ip = true;
		}
		
		if ($ip == false){
		    $this->EE->functions->redirect($location);
	    }
	
		// Is this a full URL?
		if (strpos($location, 'http') !== 0 AND !(strpos($location, 'http') > 0)) 
   		{
     		// If not, let's make it one.
 			$location = $this->EE->functions->create_url($location);
		}
		
		// Use HTTP_REFERER if referrer param equals 'true'.
		$location = (($referrer === 'true' && $this->EE->input->server('HTTP_REFERER')) ? $this->EE->input->server('HTTP_REFERER') : $location);
		
		if ($member_group)
		{
			// Is the visitor part of the member group entered into the group_id param?
			$member_group = ($member_group === $this->EE->session->userdata('group_id')) ? TRUE : FALSE;
		}
		else
		{
			$member_group = TRUE;
		}
		
		if ($member_group === TRUE AND $logged_in === TRUE)
		{
			// Perform a check to see if method parameter supplied
			if ($method === FALSE)
			{
			   		// If we do not find method parameter then perform PHP redirect
			    $this->EE->functions->redirect($location);
			 }
				else
			 {
			  	// If we find method parameter then create redirection javascript and output it
			  	$output = '<script type="text/javascript">location.href="'.$location.'"</script>';
			  	$this->return_data = $output;
			}
		}
			
		
  }

	// --------------------------------------------------------------------

	/**
	 * Usage
	 *
	 * This function describes how the plugin is used.
	 *
	 * @access	public
	 * @return	string
	 */


  function usage()
  {
  ob_start(); 
  ?>
  
This plugin will detect if the user agent is that of an i
phone or ipod touch, and preform some action based on those results.

There are two options with this plugin

1) Use it to output something if the user agent is iPhone:
==========================================================
{exp:is_iphone}

Include your iphone/ipod touch only text, Javascript, or style-sheets here. 
 
{/exp:is_iphone}
==========================================================


2) Use it to redirect iPhone users to another page or site.

If you do chose to use this option the following code must 
be at the top of your templates. It must be the first thing 
output. Got it?
==========================================================
{exp:is_iphone redirect="/index.php/relative/path/"}
==========================================================

OR

==========================================================
{exp:is_iphone redirect="http://www.devot-ee.com"}
==========================================================

  <?php
  $buffer = ob_get_contents();
	
  ob_end_clean(); 

  return $buffer;
  }
  // END

}
/* End of file pi.is_iphone.php */ 
/* Location: ./system/expressionengine/third_party/memberlist/pi.is_iphone.php */