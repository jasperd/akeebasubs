<?php
/**
 * @package		akeebasubs
 * @copyright	Copyright (c)2010-2012 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

/**
 * A sample plugin which creates two extra fields, age group and gender.
 * The former is mandatory, the latter is not
 */
class plgAkeebasubsSamplefields extends JPlugin
{
	function onSubscriptionFormRender($userparams, $cache)
	{
		// Load the language
		$lang = JFactory::getLanguage();
		$lang->load('plg_akeebasubs_samplefields', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('plg_akeebasubs_samplefields', JPATH_ADMINISTRATOR, null, true);
	
		// Init the fields array which will be returned
		$fields = array();
		
		// ----- AGE GROUP FIELD -----
		// Get the current setting (or 0 if none)
		if(array_key_exists('agegroup', $cache['custom'])) {
			$current = $cache['custom']['agegroup'];
		} else {
			if(!is_object($userparams->params)) {
				$current = '';
			} else {
				$current = property_exists($userparams->params, 'agegroup') ? $userparams->params->agegroup : 0;
			}
		}
		// Setup the combobox parameters
		$options = array(
			JHTML::_('select.option',  0, JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_AGEGROUP_SELECT') ),
			JHTML::_('select.option',  1, '0-17' ),
			JHTML::_('select.option',  2, '18-24' ),
			JHTML::_('select.option',  3, '25-34' ),
			JHTML::_('select.option',  4, '35-44' ),
			JHTML::_('select.option',  5, '45-54' ),
			JHTML::_('select.option',  6, '55+' )
		);
		$html = JHTML::_('select.genericlist', $options, 'custom[agegroup]', array(), 'value', 'text', $current, 'agegroup');

		// Setup the field
		$field = array(
			'id'			=> 'agegroup',
			'label'			=> '* '.JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_AGEGROUP_LABEL'),
			'elementHTML'	=> $html,
			'invalidLabel'	=> JText::_('COM_AKEEBASUBS_LEVEL_ERR_REQUIRED'),
			'isValid'		=> $current != 0
		);
		// Add the field to the return output
		$fields[] = $field;
		
		// ----- GENDER FIELD -----
		// Get the current setting (or 0 if none)
		if(array_key_exists('gender', $cache['custom'])) {
			$current = $cache['custom']['gender'];
		} else {
			if(!is_object($userparams->params)) {
				$current = 0;
			} else {
				$current = property_exists($userparams->params, 'gender') ? $userparams->params->gender : 0;
			}
		}
		// Setup the combobox parameters
		$options = array(
			JHTML::_('select.option',  0, JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_GENDER_SELECT') ),
			JHTML::_('select.option',  1, JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_GENDER_MALE') ),
			JHTML::_('select.option',  2, JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_GENDER_FEMALE') ),
		);
		$html = JHTML::_('select.genericlist', $options, 'custom[gender]', array(), 'value', 'text', $current, 'gender');
		// Setup the field
		$field = array(
			'id'			=> 'gender',
			'label'			=> JText::_('PLG_AKEEBASUBS_SAMPLEFIELDS_GENDER_LABEL'),
			'elementHTML'	=> $html
		);
		// Add the field to the return output
		$fields[] = $field;
		
		// ----- ADD THE JAVASCRIPT -----
		$javascript = <<<ENDJS
(function($) {
	$(document).ready(function(){
		// Tell Akeeba Subscriptions how to fetch the extra field data
		addToValidationFetchQueue(plg_akeebasubs_samplefields_fetch);
		// Tell Akeeba Subscriptions how to validate the extra field data
		addToValidationQueue(plg_akeebasubs_samplefields_validate);
	});
})(akeeba.jQuery);

function plg_akeebasubs_samplefields_fetch()
{
	var result = {};
	
	(function($) {
		result.agegroup = $('#agegroup').val();
		result.gender = $('#gender').val();
	})(akeeba.jQuery);
	
	return result;
}

function plg_akeebasubs_samplefields_validate(response)
{
	(function($) {
		if(response.custom_validation.agegroup) {
			$('#agegroup_invalid').css('display','none');
			return true;
		} else {
			$('#agegroup_invalid').css('display','inline-block');
			return false;
		}
	})(akeeba.jQuery);
}

ENDJS;
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($javascript);
		
		// ----- RETURN THE FIELDS -----
		return $fields;
	}
	
	function onValidate($data)
	{
		$response = array(
			'isValid'			=> true,
			'custom_validation'	=> array()
		);
		
		$custom = $data->custom;
		
		if(!array_key_exists('agegroup',$custom)) $custom['agegroup'] = 0;
		
		$response['custom_validation']['agegroup'] = $custom['agegroup'] != 0;
		$response['valid'] = $response['custom_validation']['agegroup']; 
		
		return $response;
	}
}