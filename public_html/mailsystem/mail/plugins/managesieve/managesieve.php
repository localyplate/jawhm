<?php

/**
 * Managesieve (Sieve Filters)
 *
 * Plugin that adds a possibility to manage Sieve filters in Thunderbird's style.
 * It's clickable interface which operates on text scripts and communicates
 * with server using managesieve protocol. Adds Filters tab in Settings.
 *
 * @version 4.3
 * @author Aleksander Machniak <alec@alec.pl>
 *
 * Configuration (see config.inc.php.dist)
 *
 * Copyright (C) 2008-2011, The Roundcube Dev Team
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * $Id: managesieve.php 4983 2011-07-28 07:31:16Z alec $
 */

class managesieve extends rcube_plugin
{
    public $task = 'settings';

    private $rc;
    private $sieve;
    private $errors;
    private $form;
    private $tips = array();
    private $script = array();
    private $exts = array();
    private $headers = array(
        'subject'   => 'Subject',
        'sender'    => 'From',
        'recipient' => 'To',
    );


    function init()
    {
        // add Tab label/title
        $this->add_texts('localization/', array('filters','managefilters'));

        // register actions
        $this->register_action('plugin.managesieve', array($this, 'managesieve_actions'));
        $this->register_action('plugin.managesieve-save', array($this, 'managesieve_save'));

        // include main js script
        $this->include_script('managesieve.js');
    }

    function managesieve_start()
    {
        $this->rc = rcmail::get_instance();
        $this->load_config();

        // register UI objects
        $this->rc->output->add_handlers(array(
            'filterslist'    => array($this, 'filters_list'),
            'filtersetslist' => array($this, 'filtersets_list'),
            'filterframe'    => array($this, 'filter_frame'),
            'filterform'     => array($this, 'filter_form'),
            'filtersetform'  => array($this, 'filterset_form'),
        ));

        // Add include path for internal classes
        $include_path = $this->home . '/lib' . PATH_SEPARATOR;
        $include_path .= ini_get('include_path');
        set_include_path($include_path);

        $host = rcube_parse_host($this->rc->config->get('managesieve_host', 'localhost'));
        $port = $this->rc->config->get('managesieve_port', 2000);

        $host = rcube_idn_to_ascii($host);

        // try to connect to managesieve server and to fetch the script
        $this->sieve = new rcube_sieve($_SESSION['username'],
            $this->rc->decrypt($_SESSION['password']), $host, $port,
            $this->rc->config->get('managesieve_auth_type'),
            $this->rc->config->get('managesieve_usetls', false),
            $this->rc->config->get('managesieve_disabled_extensions'),
            $this->rc->config->get('managesieve_debug', false),
            $this->rc->config->get('managesieve_auth_cid'), 
            $this->rc->config->get('managesieve_auth_pw')
        );

        if (!($error = $this->sieve->error())) {

            $list = $this->sieve->get_scripts();
            $active = $this->sieve->get_active();
            $_SESSION['managesieve_active'] = $active;

            if (!empty($_GET['_set'])) {
                $script_name = get_input_value('_set', RCUBE_INPUT_GET);
            }
            else if (!empty($_SESSION['managesieve_current'])) {
                $script_name = $_SESSION['managesieve_current'];
            }
            else {
                // get active script
                if ($active) {
                    $script_name = $active;
                }
                else if ($list) {
                    $script_name = $list[0];
                }
                // create a new (initial) script
                else {
                    // if script not exists build default script contents
                    $script_file = $this->rc->config->get('managesieve_default');
                    $script_name = 'roundcube';
                    if ($script_file && is_readable($script_file))
                        $content = file_get_contents($script_file);

                // add script and set it active
                if ($this->sieve->save_script($script_name, $content))
                    if ($this->sieve->activate($script_name))
                        $_SESSION['managesieve_active'] = $script_name;
                }
            }

            if ($script_name)
                $this->sieve->load($script_name);

            $error = $this->sieve->error();
        }

        // finally set script objects
        if ($error) {
            switch ($error) {
                case SIEVE_ERROR_CONNECTION:
                case SIEVE_ERROR_LOGIN:
                    $this->rc->output->show_message('managesieve.filterconnerror', 'error');
                    break;
                default:
                    $this->rc->output->show_message('managesieve.filterunknownerror', 'error');
                    break;
            }

            raise_error(array('code' => 403, 'type' => 'php',
                'file' => __FILE__, 'line' => __LINE__,
                'message' => "Unable to connect to managesieve on $host:$port"), true, false);

            // to disable 'Add filter' button set env variable
            $this->rc->output->set_env('filterconnerror', true);
            $this->script = array();
        }
        else {
            $this->script = $this->sieve->script->as_array();
            $this->exts = $this->sieve->get_extensions();
            $this->rc->output->set_env('active_set', $_SESSION['managesieve_active']);
            $_SESSION['managesieve_current'] = $this->sieve->current;
        }

        return $error;
    }

    function managesieve_actions()
    {
        // Init plugin and handle managesieve connection
        $error = $this->managesieve_start();

        // Handle user requests
        if ($action = get_input_value('_act', RCUBE_INPUT_GPC)) {
            $fid = (int) get_input_value('_fid', RCUBE_INPUT_GET);

            if ($action == 'up' && !$error) {
                if ($fid && isset($this->script[$fid]) && isset($this->script[$fid-1])) {
                    if ($this->sieve->script->update_rule($fid, $this->script[$fid-1]) !== false
                        && $this->sieve->script->update_rule($fid-1, $this->script[$fid]) !== false) {
                        $result = $this->sieve->save();
                    }

                    if ($result) {
//                      $this->rc->output->show_message('managesieve.filtersaved', 'confirmation');
                        $this->rc->output->command('managesieve_updatelist', 'up', '', $fid);
                    } else
                        $this->rc->output->show_message('managesieve.filtersaveerror', 'error');
                }
            }
            else if ($action == 'down' && !$error) {
                if (isset($this->script[$fid]) && isset($this->script[$fid+1])) {
                    if ($this->sieve->script->update_rule($fid, $this->script[$fid+1]) !== false
                        && $this->sieve->script->update_rule($fid+1, $this->script[$fid]) !== false) {
                        $result = $this->sieve->save();
                    }

                    if ($result === true) {
//                      $this->rc->output->show_message('managesieve.filtersaved', 'confirmation');
                        $this->rc->output->command('managesieve_updatelist', 'down', '', $fid);
                    } else {
                        $this->rc->output->show_message('managesieve.filtersaveerror', 'error');
                    }
                }
            }
            else if ($action == 'delete' && !$error) {
                if (isset($this->script[$fid])) {
                    if ($this->sieve->script->delete_rule($fid))
                        $result = $this->sieve->save();

                    if ($result === true) {
                        $this->rc->output->show_message('managesieve.filterdeleted', 'confirmation');
                        $this->rc->output->command('managesieve_updatelist', 'delete', '', $fid);
                    } else {
                        $this->rc->output->show_message('managesieve.filterdeleteerror', 'error');
                    }
                }
            }
            else if ($action == 'setact' && !$error) {
                $script_name = get_input_value('_set', RCUBE_INPUT_GPC);
                $result = $this->sieve->activate($script_name);

                if ($result === true) {
                    $this->rc->output->set_env('active_set', $script_name);
                    $this->rc->output->show_message('managesieve.setactivated', 'confirmation');
                    $this->rc->output->command('managesieve_reset');
                    $_SESSION['managesieve_active'] = $script_name;
                } else {
                    $this->rc->output->show_message('managesieve.setactivateerror', 'error');
                }
            }
            else if ($action == 'deact' && !$error) {
                $result = $this->sieve->deactivate();

                if ($result === true) {
                    $this->rc->output->set_env('active_set', '');
                    $this->rc->output->show_message('managesieve.setdeactivated', 'confirmation');
                    $this->rc->output->command('managesieve_reset');
                    $_SESSION['managesieve_active'] = '';
                } else {
                    $this->rc->output->show_message('managesieve.setdeactivateerror', 'error');
                }
            }
            else if ($action == 'setdel' && !$error) {
                $script_name = get_input_value('_set', RCUBE_INPUT_GPC);
                $result = $this->sieve->remove($script_name);

                if ($result === true) {
                    $this->rc->output->show_message('managesieve.setdeleted', 'confirmation');
                    $this->rc->output->command('managesieve_reload');
                    $this->rc->session->remove('managesieve_current');
                } else {
                    $this->rc->output->show_message('managesieve.setdeleteerror', 'error');
                }
            }
            else if ($action == 'setget') {
                $script_name = get_input_value('_set', RCUBE_INPUT_GPC);
                $script = $this->sieve->get_script($script_name);

                if (PEAR::isError($script))
                    exit;

                $browser = new rcube_browser;

                // send download headers
                header("Content-Type: application/octet-stream");
                header("Content-Length: ".strlen($script));

                if ($browser->ie)
                    header("Content-Type: application/force-download");
                if ($browser->ie && $browser->ver < 7)
                    $filename = rawurlencode(abbreviate_string($script_name, 55));
                else if ($browser->ie)
                    $filename = rawurlencode($script_name);
                else
                    $filename = addcslashes($script_name, '\\"');

                header("Content-Disposition: attachment; filename=\"$filename.txt\"");
                echo $script;
                exit;
            }
            elseif ($action == 'ruleadd') {
                $rid = get_input_value('_rid', RCUBE_INPUT_GPC);
                $id = $this->genid();
                $content = $this->rule_div($fid, $id, false);

                $this->rc->output->command('managesieve_rulefill', $content, $id, $rid);
            }
            elseif ($action == 'actionadd') {
                $aid = get_input_value('_aid', RCUBE_INPUT_GPC);
                $id = $this->genid();
                $content = $this->action_div($fid, $id, false);

                $this->rc->output->command('managesieve_actionfill', $content, $id, $aid);
            }

            $this->rc->output->send();
        }

        $this->managesieve_send();
    }

    function managesieve_save()
    {
        // Init plugin and handle managesieve connection
        $error = $this->managesieve_start();

        // filters set add action
        if (!empty($_POST['_newset'])) {
            $name = get_input_value('_name', RCUBE_INPUT_POST);
            $copy = get_input_value('_copy', RCUBE_INPUT_POST);
            $from = get_input_value('_from', RCUBE_INPUT_POST);

            if (!$name)
                $error = 'managesieve.emptyname';
            else if (mb_strlen($name)>128)
                $error = 'managesieve.nametoolong';
            else if ($from == 'file') {
                // from file
                if (is_uploaded_file($_FILES['_file']['tmp_name'])) {
                    $file = file_get_contents($_FILES['_file']['tmp_name']);
                    $file = preg_replace('/\r/', '', $file);
                    // for security don't save script directly
                    // check syntax before, like this...
                    $this->sieve->load_script($file);
                    if (!$this->sieve->save($name)) {
                        $error = 'managesieve.setcreateerror';
                    }
                }
                else {  // upload failed
                    $err = $_FILES['_file']['error'];
                    $error = true;

                    if ($err == UPLOAD_ERR_INI_SIZE || $err == UPLOAD_ERR_FORM_SIZE) {
                        $msg = rcube_label(array('name' => 'filesizeerror',
                            'vars' => array('size' =>
                                show_bytes(parse_bytes(ini_get('upload_max_filesize'))))));
                    }
                    else {
                        $error = 'fileuploaderror';
                    }
                }
            }
            else if (!$this->sieve->copy($name, $from == 'set' ? $copy : '')) {
                $error = 'managesieve.setcreateerror';
            }

            if (!$error) {
                $this->rc->output->show_message('managesieve.setcreated', 'confirmation');
                $this->rc->output->command('parent.managesieve_reload', $name);
            } else if ($msg) {
                $this->rc->output->command('display_message', $msg, 'error');
            } else {
                $this->rc->output->show_message($error, 'error');
            }
        }
        // filter add/edit action
        else if (isset($_POST['_name'])) {
            $name = trim(get_input_value('_name', RCUBE_INPUT_POST, true));
            $fid  = trim(get_input_value('_fid', RCUBE_INPUT_POST));
            $join = trim(get_input_value('_join', RCUBE_INPUT_POST));

            // and arrays
            $headers = $_POST['_header'];
            $cust_headers = $_POST['_custom_header'];
            $ops = $_POST['_rule_op'];
            $sizeops = $_POST['_rule_size_op'];
            $sizeitems = $_POST['_rule_size_item'];
            $sizetargets = $_POST['_rule_size_target'];
            $targets = $_POST['_rule_target'];
            $act_types = $_POST['_action_type'];
            $mailboxes = $_POST['_action_mailbox'];
            $act_targets = $_POST['_action_target'];
            $area_targets = $_POST['_action_target_area'];
            $reasons = $_POST['_action_reason'];
            $addresses = $_POST['_action_addresses'];
            $days = $_POST['_action_days'];
            $subject = $_POST['_action_subject'];
            $flags = $_POST['_action_flags'];

            // we need a "hack" for radiobuttons
            foreach ($sizeitems as $item)
                $items[] = $item;

            $this->form['disabled'] = $_POST['_disabled'] ? true : false;
            $this->form['join']     = $join=='allof' ? true : false;
            $this->form['name']     = $name;
            $this->form['tests']    = array();
            $this->form['actions']  = array();

            if ($name == '')
                $this->errors['name'] = $this->gettext('cannotbeempty');
            else {
                foreach($this->script as $idx => $rule)
                    if($rule['name'] == $name && $idx != $fid) {
                        $this->errors['name'] = $this->gettext('ruleexist');
                        break;
                    }
            }

            $i = 0;
            // rules
            if ($join == 'any') {
                $this->form['tests'][0]['test'] = 'true';
            }
            else {
                foreach ($headers as $idx => $header) {
                    $header = $this->strip_value($header);
                    $target = $this->strip_value($targets[$idx], true);
                    $op     = $this->strip_value($ops[$idx]);

                    // normal header
                    if (in_array($header, $this->headers)) {
                        if (preg_match('/^not/', $op))
                            $this->form['tests'][$i]['not'] = true;
                        $type = preg_replace('/^not/', '', $op);

                        if ($type == 'exists') {
                            $this->form['tests'][$i]['test'] = 'exists';
                            $this->form['tests'][$i]['arg'] = $header;
                        }
                        else {
                            $this->form['tests'][$i]['type'] = $type;
                            $this->form['tests'][$i]['test'] = 'header';
                            $this->form['tests'][$i]['arg1'] = $header;
                            $this->form['tests'][$i]['arg2'] = $target;

                            if ($target == '')
                                $this->errors['tests'][$i]['target'] = $this->gettext('cannotbeempty');
                            else if (preg_match('/^(value|count)-/', $type) && !preg_match('/[0-9]+/', $target))
                                $this->errors['tests'][$i]['target'] = $this->gettext('forbiddenchars');
                        }
                    }
                    else
                        switch ($header) {
                        case 'size':
                            $sizeop     = $this->strip_value($sizeops[$idx]);
                            $sizeitem   = $this->strip_value($items[$idx]);
                            $sizetarget = $this->strip_value($sizetargets[$idx]);

                            $this->form['tests'][$i]['test'] = 'size';
                            $this->form['tests'][$i]['type'] = $sizeop;
                            $this->form['tests'][$i]['arg']  = $sizetarget.$sizeitem;

                            if ($sizetarget == '')
                                $this->errors['tests'][$i]['sizetarget'] = $this->gettext('cannotbeempty');
                            else if (!preg_match('/^[0-9]+(K|M|G)*$/i', $sizetarget))
                                $this->errors['tests'][$i]['sizetarget'] = $this->gettext('forbiddenchars');
                            break;
                        case '...':
                            $cust_header = $headers = $this->strip_value($cust_headers[$idx]);

                            if (preg_match('/^not/', $op))
                                $this->form['tests'][$i]['not'] = true;
                            $type = preg_replace('/^not/', '', $op);

                            if ($cust_header == '')
                                $this->errors['tests'][$i]['header'] = $this->gettext('cannotbeempty');
                            else {
                                $headers = preg_split('/[\s,]+/', $cust_header, -1, PREG_SPLIT_NO_EMPTY);

                                if (!count($headers))
                                    $this->errors['tests'][$i]['header'] = $this->gettext('cannotbeempty');
                                else {
                                    foreach ($headers as $hr)
                                        if (!preg_match('/^[a-z0-9-]+$/i', $hr))
                                            $this->errors['tests'][$i]['header'] = $this->gettext('forbiddenchars');
                                }
                            }

                            if (empty($this->errors['tests'][$i]['header']))
                                $cust_header = (is_array($headers) && count($headers) == 1) ? $headers[0] : $headers;

                            if ($type == 'exists') {
                                $this->form['tests'][$i]['test'] = 'exists';
                                $this->form['tests'][$i]['arg']  = $cust_header;
                            }
                            else {
                                $this->form['tests'][$i]['test'] = 'header';
                                $this->form['tests'][$i]['type'] = $type;
                                $this->form['tests'][$i]['arg1'] = $cust_header;
                                $this->form['tests'][$i]['arg2'] = $target;

                                if ($target == '')
                                    $this->errors['tests'][$i]['target'] = $this->gettext('cannotbeempty');
                                else if (preg_match('/^(value|count)-/', $type) && !preg_match('/[0-9]+/', $target))
                                    $this->errors['tests'][$i]['target'] = $this->gettext('forbiddenchars');
                            }
                            break;
                        }
                    $i++;
                }
            }

            $i = 0;
            // actions
            foreach($act_types as $idx => $type) {
                $type   = $this->strip_value($type);
                $target = $this->strip_value($act_targets[$idx]);

                switch ($type) {

                case 'fileinto':
                case 'fileinto_copy':
                    $mailbox = $this->strip_value($mailboxes[$idx]);
                    $this->form['actions'][$i]['target'] = $this->mod_mailbox($mailbox, 'in');
                    if ($type == 'fileinto_copy') {
                        $type = 'fileinto';
                        $this->form['actions'][$i]['copy'] = true;
                    }
                    break;

                case 'reject':
                case 'ereject':
                    $target = $this->strip_value($area_targets[$idx]);
                    $this->form['actions'][$i]['target'] = str_replace("\r\n", "\n", $target);

 //                 if ($target == '')
//                      $this->errors['actions'][$i]['targetarea'] = $this->gettext('cannotbeempty');
                    break;

                case 'redirect':
                case 'redirect_copy':
                    $this->form['actions'][$i]['target'] = $target;

                    if ($this->form['actions'][$i]['target'] == '')
                        $this->errors['actions'][$i]['target'] = $this->gettext('cannotbeempty');
                    else if (!check_email($this->form['actions'][$i]['target']))
                        $this->errors['actions'][$i]['target'] = $this->gettext('noemailwarning');

                    if ($type == 'redirect_copy') {
                        $type = 'redirect';
                        $this->form['actions'][$i]['copy'] = true;
                    }
                    break;

                case 'addflag':
                case 'setflag':
                case 'removeflag':
                    $_target = array();
                    if (empty($flags[$idx])) {
                        $this->errors['actions'][$i]['target'] = $this->gettext('noflagset');
                    }
                    else {
                        foreach ($flags[$idx] as $flag) {
                            $_target[] = $this->strip_value($flag);
                        }
                    }
                    $this->form['actions'][$i]['target'] = $_target;
                    break;

                case 'vacation':
                    $reason = $this->strip_value($reasons[$idx]);
                    $this->form['actions'][$i]['reason']    = str_replace("\r\n", "\n", $reason);
                    $this->form['actions'][$i]['days']      = $days[$idx];
                    $this->form['actions'][$i]['subject']   = $subject[$idx];
                    $this->form['actions'][$i]['addresses'] = explode(',', $addresses[$idx]);
// @TODO: vacation :mime, :from, :handle

                    if ($this->form['actions'][$i]['addresses']) {
                        foreach($this->form['actions'][$i]['addresses'] as $aidx => $address) {
                            $address = trim($address);
                            if (!$address)
                                unset($this->form['actions'][$i]['addresses'][$aidx]);
                            else if(!check_email($address)) {
                                $this->errors['actions'][$i]['addresses'] = $this->gettext('noemailwarning');
                                break;
                            } else
                                $this->form['actions'][$i]['addresses'][$aidx] = $address;
                        }
                    }

                    if ($this->form['actions'][$i]['reason'] == '')
                        $this->errors['actions'][$i]['reason'] = $this->gettext('cannotbeempty');
                    if ($this->form['actions'][$i]['days'] && !preg_match('/^[0-9]+$/', $this->form['actions'][$i]['days']))
                        $this->errors['actions'][$i]['days'] = $this->gettext('forbiddenchars');
                    break;
                }

                $this->form['actions'][$i]['type'] = $type;
                $i++;
            }

            if (!$this->errors) {
                // zapis skryptu
                if (!isset($this->script[$fid])) {
                    $fid = $this->sieve->script->add_rule($this->form);
                    $new = true;
                } else
                    $fid = $this->sieve->script->update_rule($fid, $this->form);

                if ($fid !== false)
                    $save = $this->sieve->save();

                if ($save && $fid !== false) {
                    $this->rc->output->show_message('managesieve.filtersaved', 'confirmation');
                    $this->rc->output->add_script(
                        sprintf("rcmail.managesieve_updatelist('%s', '%s', %d, %d);",
                            isset($new) ? 'add' : 'update', Q($this->form['name']),
                            $fid, $this->form['disabled']),
                        'foot');
                }
                else {
                    $this->rc->output->show_message('managesieve.filtersaveerror', 'error');
//                  $this->rc->output->send();
                }
            }
        }

        $this->managesieve_send();
    }

    private function managesieve_send()
    {
        // Handle form action
        if (isset($_GET['_framed']) || isset($_POST['_framed'])) {
            if (isset($_GET['_newset']) || isset($_POST['_newset'])) {
                $this->rc->output->send('managesieve.setedit');
            }
            else {
                $this->rc->output->send('managesieve.filteredit');
            }
        } else {
            $this->rc->output->set_pagetitle($this->gettext('filters'));
            $this->rc->output->send('managesieve.managesieve');
        }
    }

    // return the filters list as HTML table
    function filters_list($attrib)
    {
        // add id to message list table if not specified
        if (!strlen($attrib['id']))
            $attrib['id'] = 'rcmfilterslist';

        // define list of cols to be displayed
        $a_show_cols = array('managesieve.filtername');

        foreach($this->script as $idx => $filter)
            $result[] = array(
                'managesieve.filtername' => $filter['name'],
                'id' => $idx,
                'class' => $filter['disabled'] ? 'disabled' : '',
            );

        // create XHTML table
        $out = rcube_table_output($attrib, $result, $a_show_cols, 'id');

        // set client env
        $this->rc->output->add_gui_object('filterslist', $attrib['id']);
        $this->rc->output->include_script('list.js');

        // add some labels to client
        $this->rc->output->add_label('managesieve.filterdeleteconfirm');

        return $out;
    }

    // return the filters list as <SELECT>
    function filtersets_list($attrib)
    {
        // add id to message list table if not specified
        if (!strlen($attrib['id']))
            $attrib['id'] = 'rcmfiltersetslist';

        $list = $this->sieve->get_scripts();
        $active = $this->sieve->get_active();

        $select = new html_select(array('name' => '_set', 'id' => $attrib['id'],
            'onchange' => 'rcmail.managesieve_set()'));

        if ($list) {
            asort($list, SORT_LOCALE_STRING);

            foreach ($list as $set)
                $select->add($set . ($set == $active ? ' ('.$this->gettext('active').')' : ''), $set);
        }

        $out = $select->show($this->sieve->current);

        // set client env
        $this->rc->output->add_gui_object('filtersetslist', $attrib['id']);
        $this->rc->output->add_label(
            'managesieve.setdeleteconfirm',
            'managesieve.active',
            'managesieve.filtersetact',
            'managesieve.filtersetdeact'
        );

        return $out;
    }

    function filter_frame($attrib)
    {
        if (!$attrib['id'])
            $attrib['id'] = 'rcmfilterframe';

        $attrib['name'] = $attrib['id'];

        $this->rc->output->set_env('contentframe', $attrib['name']);
        $this->rc->output->set_env('blankpage', $attrib['src'] ?
        $this->rc->output->abs_url($attrib['src']) : 'program/blank.gif');

        return html::tag('iframe', $attrib);
    }

    function filterset_form($attrib)
    {
        if (!$attrib['id'])
            $attrib['id'] = 'rcmfiltersetform';

        $out = '<form name="filtersetform" action="./" method="post" enctype="multipart/form-data">'."\n";

        $hiddenfields = new html_hiddenfield(array('name' => '_task', 'value' => $this->rc->task));
        $hiddenfields->add(array('name' => '_action', 'value' => 'plugin.managesieve-save'));
        $hiddenfields->add(array('name' => '_framed', 'value' => ($_POST['_framed'] || $_GET['_framed'] ? 1 : 0)));
        $hiddenfields->add(array('name' => '_newset', 'value' => 1));

        $out .= $hiddenfields->show();

        $name     = get_input_value('_name', RCUBE_INPUT_POST);
        $copy     = get_input_value('_copy', RCUBE_INPUT_POST);
        $selected = get_input_value('_from', RCUBE_INPUT_POST);

        // filter set name input
        $input_name = new html_inputfield(array('name' => '_name', 'id' => '_name', 'size' => 30,
            'class' => ($this->errors['name'] ? 'error' : '')));

        $out .= sprintf('<label for="%s"><b>%s:</b></label> %s<br /><br />',
            '_name', Q($this->gettext('filtersetname')), $input_name->show($name));

        $out .="\n<fieldset class=\"itemlist\"><legend>" . $this->gettext('filters') . ":</legend>\n";
        $out .= '<input type="radio" id="from_none" name="_from" value="none"'
            .(!$selected || $selected=='none' ? ' checked="checked"' : '').'></input>';
        $out .= sprintf('<label for="%s">%s</label> ', 'from_none', Q($this->gettext('none')));

        // filters set list
        $list   = $this->sieve->get_scripts();
        $active = $this->sieve->get_active();

        $select = new html_select(array('name' => '_copy', 'id' => '_copy'));

        if (is_array($list)) {
            asort($list, SORT_LOCALE_STRING);

            foreach ($list as $set)
                $select->add($set . ($set == $active ? ' ('.$this->gettext('active').')' : ''), $set);

            $out .= '<br /><input type="radio" id="from_set" name="_from" value="set"'
                .($selected=='set' ? ' checked="checked"' : '').'></input>';
            $out .= sprintf('<label for="%s">%s:</label> ', 'from_set', Q($this->gettext('fromset')));
            $out .= $select->show($copy);
        }

        // script upload box
        $upload = new html_inputfield(array('name' => '_file', 'id' => '_file', 'size' => 30,
            'type' => 'file', 'class' => ($this->errors['name'] ? 'error' : '')));

        $out .= '<br /><input type="radio" id="from_file" name="_from" value="file"'
            .($selected=='file' ? ' checked="checked"' : '').'></input>';
        $out .= sprintf('<label for="%s">%s:</label> ', 'from_file', Q($this->gettext('fromfile')));
        $out .= $upload->show();
        $out .= '</fieldset>';

        $this->rc->output->add_gui_object('sieveform', 'filtersetform');

        return $out;
    }


    function filter_form($attrib)
    {
        if (!$attrib['id'])
            $attrib['id'] = 'rcmfilterform';

        $fid = get_input_value('_fid', RCUBE_INPUT_GPC);
        $scr = isset($this->form) ? $this->form : $this->script[$fid];

        $hiddenfields = new html_hiddenfield(array('name' => '_task', 'value' => $this->rc->task));
        $hiddenfields->add(array('name' => '_action', 'value' => 'plugin.managesieve-save'));
        $hiddenfields->add(array('name' => '_framed', 'value' => ($_POST['_framed'] || $_GET['_framed'] ? 1 : 0)));
        $hiddenfields->add(array('name' => '_fid', 'value' => $fid));

        $out = '<form name="filterform" action="./" method="post">'."\n";
        $out .= $hiddenfields->show();

        // 'any' flag
        if (sizeof($scr['tests']) == 1 && $scr['tests'][0]['test'] == 'true' && !$scr['tests'][0]['not'])
            $any = true;

        // filter name input
        $field_id = '_name';
        $input_name = new html_inputfield(array('name' => '_name', 'id' => $field_id, 'size' => 30,
            'class' => ($this->errors['name'] ? 'error' : '')));

        if ($this->errors['name'])
            $this->add_tip($field_id, $this->errors['name'], true);

        if (isset($scr))
            $input_name = $input_name->show($scr['name']);
        else
            $input_name = $input_name->show();

        $out .= sprintf("\n<label for=\"%s\"><b>%s:</b></label> %s<br /><br />\n",
            $field_id, Q($this->gettext('filtername')), $input_name);

        $out .= '<fieldset><legend>' . Q($this->gettext('messagesrules')) . "</legend>\n";

        // any, allof, anyof radio buttons
        $field_id = '_allof';
        $input_join = new html_radiobutton(array('name' => '_join', 'id' => $field_id, 'value' => 'allof',
            'onclick' => 'rule_join_radio(\'allof\')', 'class' => 'radio'));

        if (isset($scr) && !$any)
            $input_join = $input_join->show($scr['join'] ? 'allof' : '');
        else
            $input_join = $input_join->show();

        $out .= sprintf("%s<label for=\"%s\">%s</label>&nbsp;\n",
            $input_join, $field_id, Q($this->gettext('filterallof')));

        $field_id = '_anyof';
        $input_join = new html_radiobutton(array('name' => '_join', 'id' => $field_id, 'value' => 'anyof',
            'onclick' => 'rule_join_radio(\'anyof\')', 'class' => 'radio'));

        if (isset($scr) && !$any)
            $input_join = $input_join->show($scr['join'] ? '' : 'anyof');
        else
            $input_join = $input_join->show('anyof'); // default

        $out .= sprintf("%s<label for=\"%s\">%s</label>\n",
            $input_join, $field_id, Q($this->gettext('filteranyof')));

        $field_id = '_any';
        $input_join = new html_radiobutton(array('name' => '_join', 'id' => $field_id, 'value' => 'any',
            'onclick' => 'rule_join_radio(\'any\')', 'class' => 'radio'));

        $input_join = $input_join->show($any ? 'any' : '');

        $out .= sprintf("%s<label for=\"%s\">%s</label>\n",
            $input_join, $field_id, Q($this->gettext('filterany')));

        $rows_num = isset($scr) ? sizeof($scr['tests']) : 1;

        $out .= '<div id="rules"'.($any ? ' style="display: none"' : '').'>';
        for ($x=0; $x<$rows_num; $x++)
            $out .= $this->rule_div($fid, $x);
        $out .= "</div>\n";

        $out .= "</fieldset>\n";

        // actions
        $out .= '<fieldset><legend>' . Q($this->gettext('messagesactions')) . "</legend>\n";

        $rows_num = isset($scr) ? sizeof($scr['actions']) : 1;

        $out .= '<div id="actions">';
        for ($x=0; $x<$rows_num; $x++)
            $out .= $this->action_div($fid, $x);
        $out .= "</div>\n";

        $out .= "</fieldset>\n";

        $this->print_tips();

        if ($scr['disabled']) {
            $this->rc->output->set_env('rule_disabled', true);
        }
        $this->rc->output->add_label(
            'managesieve.ruledeleteconfirm',
            'managesieve.actiondeleteconfirm'
        );
        $this->rc->output->add_gui_object('sieveform', 'filterform');

        return $out;
    }

    function rule_div($fid, $id, $div=true)
    {
        $rule     = isset($this->form) ? $this->form['tests'][$id] : $this->script[$fid]['tests'][$id];
        $rows_num = isset($this->form) ? sizeof($this->form['tests']) : sizeof($this->script[$fid]['tests']);

        $out = $div ? '<div class="rulerow" id="rulerow' .$id .'">'."\n" : '';

        $out .= '<table><tr><td class="rowactions">';

        // headers select
        $select_header = new html_select(array('name' => "_header[]", 'id' => 'header'.$id,
            'onchange' => 'rule_header_select(' .$id .')'));
        foreach($this->headers as $name => $val)
            $select_header->add(Q($this->gettext($name)), Q($val));
        $select_header->add(Q($this->gettext('size')), 'size');
        $select_header->add(Q($this->gettext('...')), '...');

        // TODO: list arguments

        if ((isset($rule['test']) && $rule['test'] == 'header')
            && !is_array($rule['arg1']) && in_array($rule['arg1'], $this->headers))
            $out .= $select_header->show($rule['arg1']);
        else if ((isset($rule['test']) && $rule['test'] == 'exists')
            && !is_array($rule['arg']) && in_array($rule['arg'], $this->headers))
            $out .= $select_header->show($rule['arg']);
        else if (isset($rule['test']) && $rule['test'] == 'size')
            $out .= $select_header->show('size');
        else if (isset($rule['test']) && $rule['test'] != 'true')
            $out .= $select_header->show('...');
        else
            $out .= $select_header->show();

        $out .= '</td><td class="rowtargets">';

        if ((isset($rule['test']) && $rule['test'] == 'header')
            && (is_array($rule['arg1']) || !in_array($rule['arg1'], $this->headers)))
            $custom = is_array($rule['arg1']) ? implode(', ', $rule['arg1']) : $rule['arg1'];
        else if ((isset($rule['test']) && $rule['test'] == 'exists')
            && (is_array($rule['arg']) || !in_array($rule['arg'], $this->headers)))
            $custom = is_array($rule['arg']) ? implode(', ', $rule['arg']) : $rule['arg'];

        $out .= '<div id="custom_header' .$id. '" style="display:' .(isset($custom) ? 'inline' : 'none'). '">
            <input type="text" name="_custom_header[]" id="custom_header_i'.$id.'" '
            . $this->error_class($id, 'test', 'header', 'custom_header_i')
            .' value="' .Q($custom). '" size="20" />&nbsp;</div>' . "\n";

        // matching type select (operator)
        $select_op = new html_select(array('name' => "_rule_op[]", 'id' => 'rule_op'.$id,
            'style' => 'display:' .($rule['test']!='size' ? 'inline' : 'none'),
            'onchange' => 'rule_op_select('.$id.')'));
        $select_op->add(Q($this->gettext('filtercontains')), 'contains');
        $select_op->add(Q($this->gettext('filternotcontains')), 'notcontains');
        $select_op->add(Q($this->gettext('filteris')), 'is');
        $select_op->add(Q($this->gettext('filterisnot')), 'notis');
        $select_op->add(Q($this->gettext('filterexists')), 'exists');
        $select_op->add(Q($this->gettext('filternotexists')), 'notexists');
        $select_op->add(Q($this->gettext('filtermatches')), 'matches');
        $select_op->add(Q($this->gettext('filternotmatches')), 'notmatches');
		if (in_array('regex', $this->exts)) {
            $select_op->add(Q($this->gettext('filterregex')), 'regex');
            $select_op->add(Q($this->gettext('filternotregex')), 'notregex');
        }
		if (in_array('relational', $this->exts)) {
			$select_op->add(Q($this->gettext('countisgreaterthan')), 'count-gt');
			$select_op->add(Q($this->gettext('countisgreaterthanequal')), 'count-ge');
			$select_op->add(Q($this->gettext('countislessthan')), 'count-lt');
			$select_op->add(Q($this->gettext('countislessthanequal')), 'count-le');
			$select_op->add(Q($this->gettext('countequals')), 'count-eq');
			$select_op->add(Q($this->gettext('countnotequals')), 'count-ne');
			$select_op->add(Q($this->gettext('valueisgreaterthan')), 'value-gt');
			$select_op->add(Q($this->gettext('valueisgreaterthanequal')), 'value-ge');
			$select_op->add(Q($this->gettext('valueislessthan')), 'value-lt');
			$select_op->add(Q($this->gettext('valueislessthanequal')), 'value-le');
			$select_op->add(Q($this->gettext('valueequals')), 'value-eq');
			$select_op->add(Q($this->gettext('valuenotequals')), 'value-ne');
		}

        // target input (TODO: lists)

        if ($rule['test'] == 'header') {
            $out .= $select_op->show(($rule['not'] ? 'not' : '').$rule['type']);
            $target = $rule['arg2'];
        }
        else if ($rule['test'] == 'size') {
            $out .= $select_op->show();
            if (preg_match('/^([0-9]+)(K|M|G)*$/', $rule['arg'], $matches)) {
                $sizetarget = $matches[1];
                $sizeitem = $matches[2];
            }
        }
        else {
            $out .= $select_op->show(($rule['not'] ? 'not' : '').$rule['test']);
            $target = '';
        }

        $out .= '<input type="text" name="_rule_target[]" id="rule_target' .$id. '"
            value="' .Q($target). '" size="20" ' . $this->error_class($id, 'test', 'target', 'rule_target')
            . ' style="display:' . ($rule['test']!='size' && $rule['test'] != 'exists' ? 'inline' : 'none') . '" />'."\n";

        $select_size_op = new html_select(array('name' => "_rule_size_op[]", 'id' => 'rule_size_op'.$id));
        $select_size_op->add(Q($this->gettext('filterunder')), 'under');
        $select_size_op->add(Q($this->gettext('filterover')), 'over');

        $out .= '<div id="rule_size' .$id. '" style="display:' . ($rule['test']=='size' ? 'inline' : 'none') .'">';
        $out .= $select_size_op->show($rule['test']=='size' ? $rule['type'] : '');
        $out .= '<input type="text" name="_rule_size_target[]" id="rule_size_i'.$id.'" value="'.$sizetarget.'" size="10" ' 
            . $this->error_class($id, 'test', 'sizetarget', 'rule_size_i') .' />
            <input type="radio" name="_rule_size_item['.$id.']" value=""'
                . (!$sizeitem ? ' checked="checked"' : '') .' class="radio" />'.rcube_label('B').'
            <input type="radio" name="_rule_size_item['.$id.']" value="K"'
                . ($sizeitem=='K' ? ' checked="checked"' : '') .' class="radio" />'.rcube_label('KB').'
            <input type="radio" name="_rule_size_item['.$id.']" value="M"'
                . ($sizeitem=='M' ? ' checked="checked"' : '') .' class="radio" />'.rcube_label('MB').'
            <input type="radio" name="_rule_size_item['.$id.']" value="G"'
                . ($sizeitem=='G' ? ' checked="checked"' : '') .' class="radio" />'.rcube_label('GB');
        $out .= '</div>';
        $out .= '</td>';

        // add/del buttons
        $out .= '<td class="rowbuttons">';
        $out .= '<input type="button" id="ruleadd' . $id .'" value="'. Q($this->gettext('add')). '"
            onclick="rcmail.managesieve_ruleadd(' . $id .')" class="button" /> ';
        $out .= '<input type="button" id="ruledel' . $id .'" value="'. Q($this->gettext('del')). '"
            onclick="rcmail.managesieve_ruledel(' . $id .')" class="button' . ($rows_num<2 ? ' disabled' : '') .'"'
            . ($rows_num<2 ? ' disabled="disabled"' : '') .' />';
        $out .= '</td></tr></table>';

        $out .= $div ? "</div>\n" : '';

        return $out;
    }

    function action_div($fid, $id, $div=true)
    {
        $action   = isset($this->form) ? $this->form['actions'][$id] : $this->script[$fid]['actions'][$id];
        $rows_num = isset($this->form) ? sizeof($this->form['actions']) : sizeof($this->script[$fid]['actions']);

        $out = $div ? '<div class="actionrow" id="actionrow' .$id .'">'."\n" : '';

        $out .= '<table><tr><td class="rowactions">';

        // action select
        $select_action = new html_select(array('name' => "_action_type[]", 'id' => 'action_type'.$id,
            'onchange' => 'action_type_select(' .$id .')'));
        if (in_array('fileinto', $this->exts))
            $select_action->add(Q($this->gettext('messagemoveto')), 'fileinto');
        if (in_array('fileinto', $this->exts) && in_array('copy', $this->exts))
            $select_action->add(Q($this->gettext('messagecopyto')), 'fileinto_copy');
        $select_action->add(Q($this->gettext('messageredirect')), 'redirect');
        if (in_array('copy', $this->exts))
            $select_action->add(Q($this->gettext('messagesendcopy')), 'redirect_copy');
        if (in_array('reject', $this->exts))
            $select_action->add(Q($this->gettext('messagediscard')), 'reject');
        else if (in_array('ereject', $this->exts))
            $select_action->add(Q($this->gettext('messagediscard')), 'ereject');
        if (in_array('vacation', $this->exts))
            $select_action->add(Q($this->gettext('messagereply')), 'vacation');
        $select_action->add(Q($this->gettext('messagedelete')), 'discard');
        if (in_array('imapflags', $this->exts) || in_array('imap4flags', $this->exts)) {
            $select_action->add(Q($this->gettext('setflags')), 'setflag');
            $select_action->add(Q($this->gettext('addflags')), 'addflag');
            $select_action->add(Q($this->gettext('removeflags')), 'removeflag');
        }
        $select_action->add(Q($this->gettext('rulestop')), 'stop');

        $select_type = $action['type'];
        if (in_array($action['type'], array('fileinto', 'redirect')) && $action['copy']) {
            $select_type .= '_copy';
        }

        $out .= $select_action->show($select_type);
        $out .= '</td>';

        // actions target inputs
        $out .= '<td class="rowtargets">';
        // shared targets
        $out .= '<input type="text" name="_action_target[]" id="action_target' .$id. '" '
            .'value="' .($action['type']=='redirect' ? Q($action['target'], 'strict', false) : ''). '" size="40" '
            .'style="display:' .($action['type']=='redirect' ? 'inline' : 'none') .'" '
            . $this->error_class($id, 'action', 'target', 'action_target') .' />';
        $out .= '<textarea name="_action_target_area[]" id="action_target_area' .$id. '" '
            .'rows="3" cols="40" '. $this->error_class($id, 'action', 'targetarea', 'action_target_area')
            .'style="display:' .(in_array($action['type'], array('reject', 'ereject')) ? 'inline' : 'none') .'">'
            . (in_array($action['type'], array('reject', 'ereject')) ? Q($action['target'], 'strict', false) : '')
            . "</textarea>\n";

        // vacation
        $out .= '<div id="action_vacation' .$id.'" style="display:' .($action['type']=='vacation' ? 'inline' : 'none') .'">';
        $out .= '<span class="label">'. Q($this->gettext('vacationreason')) .'</span><br />'
            .'<textarea name="_action_reason[]" id="action_reason' .$id. '" '
            .'rows="3" cols="45" '. $this->error_class($id, 'action', 'reason', 'action_reason') . '>'
            . Q($action['reason'], 'strict', false) . "</textarea>\n";
        $out .= '<br /><span class="label">' .Q($this->gettext('vacationsubject')) . '</span><br />'
            .'<input type="text" name="_action_subject[]" id="action_subject'.$id.'" '
            .'value="' . (is_array($action['subject']) ? Q(implode(', ', $action['subject']), 'strict', false) : $action['subject']) . '" size="50" '
            . $this->error_class($id, 'action', 'subject', 'action_subject') .' />';
        $out .= '<br /><span class="label">' .Q($this->gettext('vacationaddresses')) . '</span><br />'
            .'<input type="text" name="_action_addresses[]" id="action_addr'.$id.'" '
            .'value="' . (is_array($action['addresses']) ? Q(implode(', ', $action['addresses']), 'strict', false) : $action['addresses']) . '" size="50" '
            . $this->error_class($id, 'action', 'addresses', 'action_addr') .' />';
        $out .= '<br /><span class="label">' . Q($this->gettext('vacationdays')) . '</span><br />'
            .'<input type="text" name="_action_days[]" id="action_days'.$id.'" '
            .'value="' .Q($action['days'], 'strict', false) . '" size="2" '
            . $this->error_class($id, 'action', 'days', 'action_days') .' />';
        $out .= '</div>';

        // flags
        $flags = array(
            'read'      => '\\Seen',
            'answered'  => '\\Answered',
            'flagged'   => '\\Flagged',
            'deleted'   => '\\Deleted',
            'draft'     => '\\Draft',
        );
        $flags_target = (array)$action['target'];

        $out .= '<div id="action_flags' .$id.'" style="display:' 
            . (preg_match('/^(set|add|remove)flag$/', $action['type']) ? 'inline' : 'none') . '"'
            . $this->error_class($id, 'action', 'flags', 'action_flags') . '>';
        foreach ($flags as $fidx => $flag) {
            $out .= '<input type="checkbox" name="_action_flags[' .$id .'][]" value="' . $flag . '"'
                . (in_array_nocase($flag, $flags_target) ? 'checked="checked"' : '') . ' />'
                . Q($this->gettext('flag'.$fidx)) .'<br>';
        }
        $out .= '</div>';

        // mailbox select
        if ($action['type'] == 'fileinto')
            $mailbox = $this->mod_mailbox($action['target'], 'out');
        else
            $mailbox = '';

        $this->rc->imap_connect();
        $select = rcmail_mailbox_select(array(
	        'realnames' => false,
	        'maxlength' => 100,
	        'id' => 'action_mailbox' . $id,
	        'name' => '_action_mailbox[]',
	        'style' => 'display:'.(!isset($action) || $action['type']=='fileinto' ? 'inline' : 'none')
	    ));
        $out .= $select->show($mailbox);
        $out .= '</td>';

        // add/del buttons
        $out .= '<td class="rowbuttons">';
        $out .= '<input type="button" id="actionadd' . $id .'" value="'. Q($this->gettext('add')). '"
            onclick="rcmail.managesieve_actionadd(' . $id .')" class="button" /> ';
        $out .= '<input type="button" id="actiondel' . $id .'" value="'. Q($this->gettext('del')). '"
            onclick="rcmail.managesieve_actiondel(' . $id .')" class="button' . ($rows_num<2 ? ' disabled' : '') .'"'
            . ($rows_num<2 ? ' disabled="disabled"' : '') .' />';
        $out .= '</td>';

        $out .= '</tr></table>';

        $out .= $div ? "</div>\n" : '';

        return $out;
    }

    private function genid()
    {
        $result = intval(rcube_timer());
        return $result;
    }

    private function strip_value($str, $allow_html=false)
    {
        if (!$allow_html)
            $str = strip_tags($str);

        return trim($str);
    }

    private function error_class($id, $type, $target, $elem_prefix='')
    {
        // TODO: tooltips
        if (($type == 'test' && ($str = $this->errors['tests'][$id][$target])) ||
            ($type == 'action' && ($str = $this->errors['actions'][$id][$target]))
        ) {
            $this->add_tip($elem_prefix.$id, $str, true);
            return ' class="error"';
        }

        return '';
    }

    private function add_tip($id, $str, $error=false)
    {
        if ($error)
            $str = html::span('sieve error', $str);

        $this->tips[] = array($id, $str);
    }

    private function print_tips()
    {
        if (empty($this->tips))
            return;

        $script = JS_OBJECT_NAME.'.managesieve_tip_register('.json_encode($this->tips).');';
        $this->rc->output->add_script($script, 'foot');
    }

    /**
     * Converts mailbox name from/to UTF7-IMAP from/to internal Sieve encoding
     * with delimiter replacement.
     *
     * @param string $mailbox Mailbox name
     * @param string $mode    Conversion direction ('in'|'out')
     *
     * @return string Mailbox name
     */
    private function mod_mailbox($mailbox, $mode = 'out')
    {
        $delimiter         = $_SESSION['imap_delimiter'];
        $replace_delimiter = $this->rc->config->get('managesieve_replace_delimiter');
        $mbox_encoding     = $this->rc->config->get('managesieve_mbox_encoding', 'UTF7-IMAP');

        if ($mode == 'out') {
            $mailbox = rcube_charset_convert($mailbox, $mbox_encoding, 'UTF7-IMAP');
            if ($replace_delimiter && $replace_delimiter != $delimiter)
                $mailbox = str_replace($replace_delimiter, $delimiter, $mailbox);
        }
        else {
            $mailbox = rcube_charset_convert($mailbox, 'UTF7-IMAP', $mbox_encoding);
            if ($replace_delimiter && $replace_delimiter != $delimiter)
                $mailbox = str_replace($delimiter, $replace_delimiter, $mailbox);
        }

        return $mailbox;
    }
}
