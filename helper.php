<?php
//No access
defined( '_JEXEC' ) or die;
/**
 * 
 * 
 * 
 */
class modHbTrainingHelper
{
    /**
     * Retrieves the hello message
     *
     * @param array $params An object containing the module parameters
     * @access public
     */   
    
    public static function getTeam($teamkey)
    {
        // getting further Information of the team
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('hb_mannschaft'));
        $query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
        $db->setQuery($query);
        $team = $db->loadObject();
		
        //display and convert to HTML when SQL error
        if (is_null($posts=$db->loadRowList())) 
        {
            $jAp->enqueueMessage(nl2br($db->getErrorMsg()),'error');
            return;
        }
		if (empty($team)){
			$team = new stdClass();
			$team->mannschaft = 'Mannschaft';
			$team->liga = 'Liga';
			$team->kuerzel = '';
			$team->nameKurz = '';
		}
        return $team;
    }
	
	public static function getHeadline ($option, $team)
    {
        switch ($option)
		{
			case 'title':
				$headline = JText::_('MOD_HBTRAINING_TITLE');
				break;
			case 'not':
				$headline = '';
				break;
			case 'titleandteam':
			default:
				$headline = JText::_('MOD_HBTRAINING_TITLE').' - '.$team->team;
				break;
		}
        return $headline;
    }
    
	public static function getTrainings ($teamkey)
	{
		// getting training information
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*, DATE_FORMAT(beginn, \'%H:%i\') as beginn,'.
			'DATE_FORMAT(ende, \'%H:%i\') as ende');
		$query->from($db->qn('hb_mannschaft_training'));
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->leftJoin('hb_training USING ('.$db->qn('trainingID').')');
		$query->leftJoin('hb_halle USING (hallenNr)');
		$db->setQuery($query);
		$trainings = $db->loadObjectList ();
		//echo "Trainings<pre>"; print_r($trainings); echo "</pre>";
		return $trainings;
	}
    
	public static function getGlobalContactSettings()
	{
		// getting global contact settings
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->qn('#__extensions'));
		$query->where('name = '.$db->q('com_contact'));
		$db->setQuery($query);
		$contactSettings = $db->loadObject();
		$par = $contactSettings->params;
		$globalParams = new JRegistry;
		$globalParams->loadString($par);
		//echo __FUNCTION__.':<pre>';print_r($globalParams);echo'</pre>';
		$items = array('email','mobile','telephone');
		$global_show = null;
		foreach ($items as $value){
			$global_show[$value] = $globalParams->get('show_'.$value);
		}
		//echo __FUNCTION__.':<pre>';print_r($global_show);echo'</pre>';
		return $global_show;
	}
	
	public static function getTrainer($teamkey)
	{
		// getting trainer information
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('hb_mannschaft_trainer'));
		$query->where('kuerzel = '.$db->q($teamkey));
		$query->leftJoin('hb_trainer USING (trainerID)');
		$query->leftJoin('#__contact_details USING (alias)');
		$query->order('IF(ISNULL(`rangfolge`),1,0),`rangfolge` DESC');
		$db->setQuery($query);
		$trainer = $db->loadObjectList ();
		//echo __FUNCTION__.':<pre>';print_r($trainer);echo'</pre>';
		$trainer = self::addContact($trainer);
		//echo __FUNCTION__.':<pre>';print_r($trainer);echo'</pre>';
		return $trainer;
	}
	
	protected function addContact($trainer) 
	{
		$global_show = modHbTrainingHelper::getGlobalContactSettings();
		foreach ($trainer as $coach)
		{
			$coach = self::getShowSettings($coach, $global_show);
			//echo __FUNCTION__.':<pre>';print_r($coach);echo'</pre>';
			$coach->contact = self::getContact($coach);
		}
		//echo __FUNCTION__.':<pre>';print_r($trainer);echo'</pre>';
		return $trainer;
	}
	
	protected function getShowSettings($trainer, $global) 
	{
		$par=$trainer->params;
		$params = new JRegistry;
		$params->loadString($par);
		//echo __FUNCTION__.':<pre>';print_r($params);echo'</pre>';
		$items = array('email','mobile','telephone');
		foreach ($items as $value)
		{
			$show[$value] = $params->get('show_'.$value);
			//echo "show[".$value."]: ".$show[$value]."<br>";
			if ($show[$value] === null) {
				$show[$value] = $global[$value];
			}
			if ($show[$value] === 0) {
				$trainer->{$value} = null;
			}
		}
		//echo __FUNCTION__.':<pre>';print_r($trainer);echo'</pre>';
		return $trainer;
	}
	
	protected function getContact($trainer) 
	{
		//echo __FUNCTION__.':<pre>';print_r($trainer);echo'</pre>';
		$trainerContact = array();
		if($trainer->email_to != null) {
			$trainerContact[] = JHtml::_('email.cloak', $trainer->email_to);
		}
		if($trainer->mobile != null) {
			$trainer->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', 
					'$1 $2 / $3', $trainer->mobile);
			$trainerContact[] = $trainer->mobile;
		}
		if($trainer->telephone != null) {
			$trainer->telephone = preg_replace('/(\+49)(\d{4})(\d{3,9})/', 
					'$1 $2 / $3', $trainer->telephone);
			$trainerContact[] = $trainer->telephone;
		}
		if(count($trainerContact) > 0) {
			return implode(', ', $trainerContact);
		}
		return null;
	}
}














