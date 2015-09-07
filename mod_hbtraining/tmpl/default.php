<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );


$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'modules/mod_hbtraining/css/default.css');

//echo "<p>".JText::_('DESC_MODULE')."</p>";


// Headline
if (!empty($headline))
{
	echo '<h3>'.$headline.'</h3>';
}


echo "<div class=\"trainingBox\">";

echo "<dl class=\"training\">";

// trainer
echo '<dt>'.JText::_('MOD_HBTRAINING_COACH').'</dt>';
//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainer);echo'</pre>';
foreach ($trainer as $curTrainer)
{
	echo "<dd>";
	if(isset($curTrainer->name)) {
		echo '<span class="trainerName">'.$curTrainer->name.'</span>';
	}
	if(isset($curTrainer->contact)) {
		echo " <br />(&nbsp;".$curTrainer->contact."&nbsp;)";
	}
	echo "</dd>";
}

// dates
echo '<dt>'.JText::_('MOD_HBTRAINING_TRAINING').'</dt>';
//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainings);echo'</pre>';
foreach ($trainings as $training) 
{
	echo '<dd><span class="weekday">'.$training->tag.'</span> ';
	echo $training->beginn." - ".$training->ende." Uhr";
	if ($training->bemerkung != "") echo " (".$training->bemerkung.")";
	
	if ($showHomeGym == '1' OR $training->hallenNr != 7014 )
	{
		echo " (".$training->name.")";
	}
	echo "</dd>";
}

echo "</dl>";
echo "</div>";