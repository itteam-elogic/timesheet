<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mark timesheet report emails as high-priority internal mail so Microsoft
 * Outlook / Microsoft 365 Focused Inbox is less likely to file them under Other.
 *
 * @param object $emailLib CodeIgniter email library instance
 * @param array  $options  Optional: alt (plain text), thread_topic
 */
function timesheet_apply_focused_inbox_headers($emailLib, $options = array())
{
	$alt = isset($options['alt']) ? $options['alt'] : '';
	$topic = isset($options['thread_topic']) ? $options['thread_topic'] : '';

	$emailLib->useragent = 'eLogicTech';
	$emailLib->set_priority(1);
	$emailLib->set_header('Importance', 'High');
	$emailLib->set_header('X-MSMail-Priority', 'High');
	$emailLib->set_header('X-MS-Exchange-Organization-BypassFocusedInbox', 'true');
	if ($topic !== '') {
		$emailLib->set_header('Thread-Topic', $topic);
	}
	$emailLib->reply_to('laxmikanth@elogictech.com', 'Laxmikanth');
	if ($alt !== '') {
		$emailLib->set_alt_message($alt);
	}
}
