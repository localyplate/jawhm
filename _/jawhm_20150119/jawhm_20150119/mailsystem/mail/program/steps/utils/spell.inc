<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/utils/spell.inc                                         |
 |                                                                       |
 | This file is part of the Roundcube Webmail client                     |
 | Copyright (C) 2005-2011, The Roundcube Dev Team                       |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Invoke the configured or default spell checking engine.             |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Kris Steinhoff <steinhof@umich.edu>                           |
 +-----------------------------------------------------------------------+

 $Id: spell.inc 4815 2011-05-30 15:08:26Z alec $

*/

// read input
$lang = get_input_value('lang', RCUBE_INPUT_GET);
$data = file_get_contents('php://input');

// Get data string
$left = strpos($data, '<text>');
$right = strrpos($data, '</text>');
$data = substr($data, $left+6, $right-($left+6));
$data = html_entity_decode($data, ENT_QUOTES, RCMAIL_CHARSET);

$spellchecker = new rcube_spellchecker($lang);
$spellchecker->check($data);
$result = $spellchecker->get_xml();

// set response length
header("Content-Length: " . strlen($result));

// Don't use server's default Content-Type charset (#1486406)
header("Content-Type: text/xml; charset=" . RCMAIL_CHARSET);
print $result;
exit;
