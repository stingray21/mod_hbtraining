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

echo "<dl class=\"training\">";

// trainer
echo "<dt>Trainer</dt>";

//echo "Trainer<pre>";print_r($trainer);echo "</pre>";
foreach ($trainer as $curTrainer)
{
	echo "<dd>";
	if(isset($curTrainer->name)) echo '<span class="trainerName">'.$curTrainer->name.'</span>';
	if(isset($curTrainer->contact)) echo " <br />( ".$curTrainer->contact." )";
	echo "</dd>";
}

// dates
echo '<dt class="times">Trainingszeiten</dt>';
foreach ($trainings as $training) 
{
	echo '<dd><span class="weekday">'.$training->tag.'</span> ';
	echo $training->beginn." - ".$training->ende." Uhr";
	if ($training->bemerkung != "") echo " (".$training->bemerkung.")";
	
	if ($showHomeGym == '1' OR $training->hallenNummer != 7014 )
	{
		echo " (".$training->name.")";
	}
	echo "</dd>";
}

echo "</dl>";
echo "</div>";