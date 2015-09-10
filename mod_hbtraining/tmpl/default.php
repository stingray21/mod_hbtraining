<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );


JHtml::stylesheet('mod_hbtraining/default.css', array(), true);

//echo "<p>".JText::_('DESC_MODULE')."</p>";
?>

<?php echo (!empty($headline)) ? '<h3>'.$headline.'</h3>' : ''; ?>

<div>
	<dl>
	<?php 
	if (!empty($trainer)) { 
		echo '<dt>'.JText::_('MOD_HBTRAINING_COACH').'</dt>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainer);echo'</pre>'; 
		foreach ($trainer as $curTrainer)
		{
			?>
				<dd><?php 
					echo (isset($curTrainer->name)) ? $curTrainer->name : '';
					echo (isset($curTrainer->contact)) ? " (&nbsp;".$curTrainer->contact."&nbsp;)" : '';
					?></dd>
			<?php
		}
	}	
	?>
				
	<?php
	if (!empty($trainings)) { 
		echo '<dt>'.JText::_('MOD_HBTRAINING_TRAINING').'</dt>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainings);echo'</pre>';
		foreach ($trainings as $training) 
		{	
			?>
				<dd><?php 
				echo $training->tag.' '.$training->beginn." - ".$training->ende." Uhr";
				echo ($training->bemerkung != "") ? " (".$training->bemerkung.")" : '';
				echo ($showHomeGym == '1' OR $training->hallenNr != $homeGym ) ? " (".$training->hallenName.")" : '';
				?></dd>
			<?php
		}
	}
?>
	</dl>
</div>

