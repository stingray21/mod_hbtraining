<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );


JHtml::stylesheet('mod_hbtraining/default.css', array(), true);

//echo "<p>".JText::_('DESC_MODULE')."</p>";
?>

<?php echo (!empty($headline)) ? '<h3>'.$headline.'</h3>' : ''; ?>

<div class="hbtraining">
	<dl>
	
	<?php 
	if (!empty($emailAlias)) { 
		echo '<dt class="trainer">'.JText::_('MOD_HBTRAINING_EMAIL').'</dt>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainer);echo'</pre>'; 
				?><dd class="email"><?php echo $emailAlias; ?></dd>
			<?php
	}
	?>
		
	<?php 
	if (!empty($trainer)) { 
		echo '<dt class="trainer">'.JText::_('MOD_HBTRAINING_COACH').'</dt>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainer);echo'</pre>'; 
		foreach ($trainer as $curTrainer)
		{
			?>
				<dd class="trainer"><?php 
					echo (isset($curTrainer->name)) ? ' <span class="tr-name">'.$curTrainer->name.'</span>' : '';
					echo (isset($curTrainer->contact)) ? ' <span class="tr-contact">'.$curTrainer->contact.'</span>' : '';
					?></dd>
			<?php
		}
	}	
	?>
				
	<?php
	if (!empty($trainings)) { 
		echo '<dt class="training">'.JText::_('MOD_HBTRAINING_TRAINING').'</dt>';
		//echo __FILE__.'('.__LINE__.'):<pre>';print_r($trainings);echo'</pre>';
		foreach ($trainings as $training) 
		{	
			?>
				<dd class="training"><?php 
				echo '<span class="tr-date"><span class="tr-day">'.$training->tag.'</span> ';
				echo '<span class="tr-start">'.$training->beginn.'</span> ';
				echo ' - ';
				echo '<span class="tr-end">'.$training->ende.'</span> Uhr</span>';
				echo ($training->bemerkung != "") ? " (".$training->bemerkung.")" : '';
				if ($training->hallenNr != '') {
					if ( $showHomeGym == '1' OR $training->hallenNr != $homeGym ) {
						echo ' <span class="tr-gym">'.$training->hallenName.'</span>' ;
					}
				}
				?></dd>
			<?php
		}
	}
?>
	</dl>
</div>

