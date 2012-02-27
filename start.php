<?php

	/**
	 * Log In Required plugin.
	 * 
	 * @package LogInRequired
	 * @license http://www.opensource.org/licenses/gpl-license.php
	 * @author Khaled Afiouni
	 * @copyright skinju.com 2010
	 * @link http://skinju.com/
	 */

 
register_elgg_event_handler('pagesetup','system','loginrequired_restrict_access');
function loginrequired_restrict_access()
{
  // No need to do all the checking below if the user is already logged in... performance is key :)
  if (isloggedin()) return;

  // Get the current page URL without any ? & parameters... this is required for the registration page to work properly
  $current_url = current_page_url();
  $parameters_start = strrpos($current_url, '?');
  if ($parameters_start)
    $current_url = substr ($current_url, 0, $parameters_start);

  global $CONFIG;
  $allow = array();

  // Allow access to home page... otherwise a redirect loop error will prevent the page from being displayed
  $allow[] = $CONFIG->url;

  // Allow should have pages
  $allow[] = $CONFIG->url . 'pg/register';
  $allow[] = $CONFIG->url . 'pg/register/';
  $allow[] = $CONFIG->url . 'account/register.php';
  $allow[] = $CONFIG->url . 'account/forgotten_password.php';

  // Allow other plugin developers to edit the array values
  $add_allow = trigger_plugin_hook('login_required','login_required');

  // If more URL's are added... merge both with original list
  if (is_array ($add_allow))
    $allow = array_merge ($allow, $add_allow);
 
  // Check if current page is in the allowed list... Otherwise redirect to login
  if (!in_array(strtolower(trim($current_url)), array_map('strtolower', $allow)))
    gatekeeper();
}

// Add more allowed URL's... make that way here for demonstration purposes!
register_plugin_hook('login_required','login_required', 'login_required_default_allowed_list');
function login_required_default_allowed_list($hook, $type, $returnvalue, $params)
{
  global $CONFIG;
  
  // If externalpages plugin is active allow access to its pages
  $add = array();
  $add[] = $CONFIG->url . 'pg/expages/read/Terms/';
  $add[] = $CONFIG->url . 'pg/expages/read/Terms';
  $add[] = $CONFIG->url . 'pg/expages/read/Privacy/';
  $add[] = $CONFIG->url . 'pg/expages/read/Privacy';
  $add[] = $CONFIG->url . 'pg/expages/read/About/';
  $add[] = $CONFIG->url . 'pg/expages/read/About';

  return $add;
}
?>