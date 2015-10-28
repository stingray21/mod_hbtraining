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
		$query->from($db->qn('hb_training'));
		$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
		$query->leftJoin('hb_halle USING (hallenNr)');
		$query->order("FIELD(tag, 'MO', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So')");
		//echo "Trainings<pre>".$query."</pre>";
		$db->setQuery($query);
		$trainings = $db->loadObjectList ();
		//echo "Trainings<pre>"; print_r($trainings); echo "</pre>";
		return $trainings;
	}
    
	public static function getGlobalShowSettings()
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
		$query->leftJoin('#__contact_details USING (alias)');
		$query->order('IF(ISNULL(`rangfolge`),1,0),`rangfolge` DESC');
		$db->setQuery($query);
		$coaches = $db->loadObjectList ();
		//echo __FUNCTION__.':<pre>';print_r($coaches);echo'</pre>';
		$coaches = self::addContact($coaches);
		//echo __FUNCTION__.':<pre>';print_r($coaches);echo'</pre>';
		return $coaches;
	}
	
	protected static function addContact($coaches) 
	{
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($coaches);echo'</pre>';
		$global_show = self::getGlobalShowSettings();		
		foreach ($coaches as &$coach)
		{
			$show = self::getShowSettings($coach, $global_show);
			//echo __FUNCTION__.'<br>'.__FILE__.' ('.__LINE__.')<pre>';print_r($show);echo'</pre>';
			$coach->contact = self::getContact($coach, $show);
		}
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($coaches);echo'</pre>';
		return $coaches;
	}
	
	
	protected static function getShowSettings($coach, $global_show) 
	{
		$contact_show = self::getContactShowSettings($coach);
		$items = array('email','mobile','telephone');
		foreach ($items as $value)
		{
			if ($contact_show[$value] === null) {
				//echo "global[".$value."]:".$global[$value]."<br>";
				$show[$value] = $global_show[$value];
			} else {
				$show[$value] = $contact_show[$value];
			}
		}
		//echo __FUNCTION__.'<br>'.__FILE__.' ('.__LINE__.')<pre>';print_r($show);echo'</pre>';
		return $show;
	}
	
	protected static function getContactShowSettings($coach) 
	{
		$params = new JRegistry();
		if ($coach && isset($coach->params)) {
			//echo __FUNCTION__.':<pre>';print_r($trainer->params);echo'</pre>';
			$params->loadString($coach->params);
		}
		//echo __FUNCTION__.':<pre>';print_r($params);echo'</pre>';
		
		$contact_show['email'] = $params->get('show_email_to');
		$contact_show['mobile'] = $params->get('show_mobile');
		$contact_show['telephone'] = $params->get('show_telephone');		

		//echo __FUNCTION__.'<br>'.__FILE__.' ('.__LINE__.')<pre>';print_r($contact_show);echo'</pre>';
		return $contact_show;
	}
	
	protected static function getContact($coach, $show) 
	{
		//echo __FUNCTION__.':<pre>';print_r($coach);echo'</pre>';
		$contact = array();
		$emailSettings = self::getEmailSettings();
		if ($emailSettings === 'personal' && $coach->email_to != null && $show['email']) {
			$contact[] = JHtml::_('email.cloak', $coach->email_to);
		}
		if($coach->mobile != null && $show['mobile']) {
			//$coach->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '$1 $2 / $3', $coach->mobile);
			$coach->mobile = preg_replace('/(\+49)(1\d\d)(\d{6,9})/', '0$2 / $3', $coach->mobile);
			$contact[] = $coach->mobile;
		}
		if($coach->telephone != null && $show['telephone']) {
			//$coach->telephone = preg_replace('/(\+49)(\d{4})(\d{3,9})/', '$1 $2 / $3', $coach->telephone);
			$coach->telephone = preg_replace('/(\+49)(\d{4})(\d{3,9})/', '0$2 / $3', $coach->telephone);
			$contact[] = $coach->telephone;
		}
		if(count($contact) > 0) {
			return implode(', ', $contact);
		}
		return null;
	}
	
	protected static function getEmailSettings() {
		
		// SETTINGS FROM MENU ITEM (more important)
		$menuitemid = JRequest::getInt('Itemid');
		if ($menuitemid)
		{
			$menu = JFactory::getApplication()->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($menuparams); echo'</pre>';
			$emailSettings = $menuparams->get('email');
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($email_menu); echo'</pre>';
		}
		if ($emailSettings === 'component' || $emailSettings === null) {
			// SETTINGS FROM COMPONENT
			$params = JComponentHelper::getParams( 'com_hbteam' );
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($params); echo'</pre>';
			$emailSettings = $params->get( 'email' );
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($email_com); echo'</pre>';
		}
		return $emailSettings;
	}
	
	public static function getEmailAlias($teamkey) {
		$emailAlias = null;
		$emailSettings = self::getEmailSettings();
		if ($emailSettings === 'alias') {
			// getting email alias of the team
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select($db->qn('email'));
			$query->from($db->qn('hb_mannschaft'));
			$query->where($db->qn('kuerzel').' = '.$db->q($teamkey));
			$db->setQuery($query);
			$email = $db->loadResult();
			//echo __FILE__.' ('.__LINE__.')<pre>';print_r($email);echo'</pre>';
			$domain = self::getDomain();
			if (!empty($email) && !empty($domain)){
				$emailAlias = JHtml::_('email.cloak', $email.$domain);
			}					
		} 
		return $emailAlias;
	}
	
	protected static function getDomain () {
		// SETTINGS FROM COMPONENT
		$params = JComponentHelper::getParams( 'com_hbmanager' );
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($params); echo'</pre>';
		$emailDomain = $params->get( 'emaildomain' );
		//echo __FILE__.' ('.__LINE__.')<pre>';print_r($emailDomain); echo'</pre>';
		if (empty($emailDomain)) {
			echo 'Error: no email domain set';
		}
		
		return $emailDomain;
	}
}














