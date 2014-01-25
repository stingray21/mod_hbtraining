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
$query->select('tag, beginn, ende, bemerkung, sichtbar, hallenNummer, kurzname, name, strasse, plz, stadt');
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


// getting trainer information
$query = $db->getQuery(true);
$query->select('alias, trainerID, rangfolge, name, emailSichtbar, email_to, telefonSichtbar, telephone, handySichtbar, mobile, address, postcode, suburb');
$query->from($db->nameQuote('hb_mannschaft_trainer'));
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


