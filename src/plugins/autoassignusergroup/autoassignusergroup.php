<?php
/**
 * @package    PlgSystemAutoAssignUserGroup
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Mail\MailHelper;

/**
 * Techjoomla AutoAssignUserGroup plugin
 *
 * This plugin will help auto assign user group on registration based on email domain
 *
 * @since  1.0
 */
class PlgUserAutoassignusergroup extends JPlugin
{
	/**
	 * Adds user into groups based on email domain
	 *
	 * @param   array    $user     The date
	 * @param   boolean  $isnew    Is new
	 * @param   boolean  $success  Is success
	 * @param   string   $msg      The message
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		// Check for user is successfully registered and new
		if ($isnew && $success)
		{
			// Getting user email domain
			$atIndex = strrpos($user['email'], '@');
			$domain = substr($user['email'], $atIndex + 1);

			// Getting domain mapped groups
			$fields = $this->params->get('domainUsergroupMap');

			foreach ($fields as $field)
			{
				// Checking for domain mapped group
				if ($domain == $field->domain)
				{
					// Merging the domain group and user default group.
					$groups = array_unique(array_merge($field->groups, $user['groups']));

					try
					{
						foreach ($groups as $v)
						{
							UserHelper::addUserToGroup($user['id'], $v);
						}
					}
					catch (Exception $e)
					{
						Log::addLogger(
								array(
									// Sets file name
									'text_file' => 'autoassignusergroup.log.php',
									'text_entry_format' => '{DATETIME}				{PRIORITY}			{MESSAGE}'
								),
								Log::ALL,
								array('plg_autoassignusergroup')
							);
					}
				}
			}

			return;
		}
	}
}
