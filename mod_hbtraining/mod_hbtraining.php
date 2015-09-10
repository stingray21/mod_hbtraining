<?php
//don't allow other scripts to grab and execute our file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

//This is the parameter we get from our xml file above
$headlineOption = $params->get('headline');
$showHomeGym = $params->get('showhomegym');

// get parameter from component menu item
$menuitemid = JRequest::getInt('Itemid');
if ($menuitemid)
{
	$menu = JFactory::getApplication()->getMenu();
	$menuparams = $menu->getParams( $menuitemid );
}
$teamkey = strtolower($menuparams->get('teamkey'));
$homeGym = trim(strtolower($menuparams->get('homegym')));
// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$team = modHbTrainingHelper::getTeam($teamkey);
$trainings = modHbTrainingHelper::getTrainings($teamkey);
$trainer = modHbTrainingHelper::getTrainer($teamkey);
$headline = modHbTrainingHelper::getHeadline($headlineOption, $team);

//Returns the path of the layout file
require JModuleHelper::getLayoutPath('mod_hbtraining', $params->get('layout', 'default'));

