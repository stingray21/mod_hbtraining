<?php
//No access
defined( '_JEXEC' ) or die;

//Add database instance
$db = JFactory::getDBO();
$jAp = JFactory::getApplication();


// getting further Information of the team
if ($headline == 'titleandteam')
{
	$query = $db->getQuery(true);
	$query->select('*');
	$query->from($db->qn('hb_mannschaft'));
	$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
	$db->setQuery($query);
	$mannschaft = $db->loadObject ();
	
	//echo "Mannschaft<pre>"; print_r($mannschaft); echo "</pre>";
	
	//display and convert to HTML when SQL error
	if (is_null($posts=$db->loadRowList())) 
	{
		$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
		return;
	}
}

// getting training information
$query = $db->getQuery(true);
//$query->select('*');
$query->select('tag, DATE_FORMAT(beginn, \'%H:%i\') as beginn'.
	', DATE_FORMAT(ende, \'%H:%i\') as ende, bemerkung, sichtbar,'.
	' hallenNummer, kurzname, name, strasse, plz, stadt');
$query->from($db->qn('hb_mannschaft_training'));
$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
$query->leftJoin('hb_training USING ('.$db->qn('trainingID').')');
$query->leftJoin('hb_halle USING (hallenNummer)');
$db->setQuery($query);
$trainings = $db->loadObjectList ();
//echo "Trainings<pre>"; print_r($trainings); echo "</pre>";


//display and convert to HTML when SQL error
if (is_null($posts=$db->loadRowList()))
{
	$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
	return;
}


// getting global contact settings
$query = $db->getQuery(true);
$query->select('params');
$query->from($db->qn('#__extensions'));
$query->where('name = '.$db->q('com_contact'));
$db->setQuery($query);
$contactSettings = $db->loadObject();
//display and convert to HTML when SQL error
if (is_null($posts=$db->loadRowList()))
{
	$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
	return;
}
$par=$contactSettings->params;
$globalParams = new JRegistry;
$globalParams->loadString($par);
//echo "global params<pre>";print_r($globalParams);echo "</pre>";

$items = array('email','mobile','telephone');
$global_show = null;
foreach ($items as $value){
	$global_show[$value] = $globalParams->get('show_'.$value);
}
//echo "global show<pre>";print_r($global_show);echo "</pre>";

// getting trainer information
$query = $db->getQuery(true);
$query->select('alias, trainerID, rangfolge, name, email_to as email, telephone, mobile, address, postcode, suburb, params');
$query->from($db->qn('hb_mannschaft_trainer'));
$query->where('kuerzel = '.$db->Quote($teamkey));
$query->leftJoin('hb_trainer USING (trainerID)');
$query->leftJoin('#__contact_details USING (alias)');
$query->order('IF(ISNULL(`rangfolge`),1,0),`rangfolge`');
$db->setQuery($query);
$trainer = $db->loadObjectList ();
//display and convert to HTML when SQL error
if (is_null($posts=$db->loadRowList()))
{
	$jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
	return;
}
//echo "Trainer<pre>";print_r($trainer);echo "</pre>";

foreach ($trainer as $curTrainer)
{
	$par=$curTrainer->params;
	$params = new JRegistry;
	$params->loadString($par);
	//echo "Parameter<pre>";print_r($params);echo "</pre>";

	$show = null;
	foreach ($items as $value){
		
		$show[$value] = $params->get('show_'.$value);
		//echo "show[".$value."]: ".$show[$value]."<br>";
		if ($show[$value] === null) 
		{
			$show[$value] = $global_show[$value];
		}
		if ($show[$value] === 0) {
		$curTrainer->{$value} = null;
		}
	}
	//echo "show<pre>";print_r($show);echo "</pre>";
	
	$trainerContact = array();
		if($curTrainer->email != null) $trainerContact[] = JHtml::_('email.cloak', $curTrainer->email);
		
		if($curTrainer->mobile != null) {
			$curTrainer->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '$1 $2 / $3', $curTrainer->mobile);
			$trainerContact[] = $curTrainer->mobile;
		}
		if($curTrainer->telephone != null) {
			$curTrainer->telephone = preg_replace('/(\+49)(\d{4})(\d{3,9})/', '$1 $2 / $3', $curTrainer->telephone);
			$trainerContact[] = $curTrainer->telephone;
		}
	if(count($trainerContact) > 0) {
		$curTrainer->contact = implode(', ', $trainerContact);
	}
}
//echo "Trainer<pre>";print_r($trainer);echo "</pre>";
//echo "show_email".$trainer[0]->params->get('show_email')."<br>";

