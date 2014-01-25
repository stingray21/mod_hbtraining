<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );


$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'modules/mod_hbtraining/css/default.css');

//echo "<p>".JText::_('DESC_MODULE')."</p>";


// Headline
echo '<h3>';
switch ($headline)
{
	case 'title':
		echo 'Training';
		break;
	case 'not':
		break;
	case 'titleandteam':
	default:
		echo 'Training - '.$mannschaft->mannschaft;
		break;
}
echo '</h3>';


echo "<div class=\"trainingBox\">";

echo "<table class=\"training\">";

// trainer
echo "<tr><th colspan=\"2\">Trainer:</th></tr>";
echo "<tr><td colspan=\"2\">";
foreach ($trainer as $curTrainer)
{
	unset($trainerContact);
	echo "<b>{$curTrainer->name}</b>";
	
	$trainerContact = array();
		if($curTrainer->emailSichtbar AND $curTrainer->email_to != '') $trainerContact[] = JHtml::_('email.cloak', $curTrainer->email_to);
		if($curTrainer->handySichtbar AND $curTrainer->mobile != '') $trainerContact[] = $curTrainer->mobile;
		if($curTrainer->telefonSichtbar AND $curTrainer->telephone != '') $trainerContact[] = $curTrainer->telephone;
	if(count($trainerContact) > 0) echo "<br />( ".implode(', ', $trainerContact)." )";
	echo "<br />";
}
echo "</td></tr>";

// dates
echo "<tr><th colspan=\"2\">Trainingszeiten:</th></tr>";
foreach ($trainings as $training) 
{
	echo "<tr><td>{$training->tag}</td><td> ".substr($training->beginn,0,5)." - ".substr($training->ende,0,5)." Uhr";
	if ($training->bemerkung != "") echo " (".$training->bemerkung.")";
	
	if ($showHomeGym == '1' OR $training->hallenNummer != 7014 )
	{
		echo " (".$training->name.")";
	}
	echo "</td></tr>";
}

echo "</table>";
echo "</div>";