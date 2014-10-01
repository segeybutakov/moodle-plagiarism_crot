<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * plagiarism.php - allows the admin to configure plagiarism stuff
 *
 * @package   plagiarism_crot
 * @author    Dan Marsden <dan@danmarsden.com>, Sergey Butakov, Svetlana Kim
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

    require_once(dirname(dirname(__FILE__)) . '/../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/plagiarismlib.php');
    require_once($CFG->dirroot.'/plagiarism/crot/lib.php');
    require_once($CFG->dirroot.'/plagiarism/crot/plagiarism_form.php');
    
    require_login();
    admin_externalpage_setup('plagiarismcrot');

    $context = get_context_instance(CONTEXT_SYSTEM);

    require_capability('moodle/site:config', $context, $USER->id, true, "nopermissions");

    require_once('plagiarism_form.php');
    $mform = new plagiarism_setup_form();
    
        
    $plagiarismplugin = new plagiarism_plugin_crot();

    if ($mform->is_cancelled()) {
        redirect('');
    }

    echo $OUTPUT->header();

    if (($data = $mform->get_data()) && confirm_sesskey()) {
        if (!isset($data->crot_use)) {
            $data->crot_use = 0;
        }
        foreach ($data as $field=>$value) {
            if (strpos($field, 'crot')===0) {
                if ($tiiconfigfield = $DB->get_record('config_plugins', array('name'=>$field, 'plugin'=>'plagiarism'))) {
                    $tiiconfigfield->value = $value;
                    if (! $DB->update_record('config_plugins', $tiiconfigfield)) {
                        error("errorupdating");
                    }
                } else {
                    $tiiconfigfield = new stdClass();
                    $tiiconfigfield->value = $value;
                    $tiiconfigfield->plugin = 'plagiarism';
                    $tiiconfigfield->name = $field;
                    if (! $DB->insert_record('config_plugins', $tiiconfigfield)) {
                        error("errorinserting");
                    }
                }
            }
            if($field == 'delall' && $value == true) {
                clean_data();
            }
            if($field == 'testglobal' && $value == true) {
                test_global_search();
            }
            if($field == 'registration' && $value == true) {
                echo $OUTPUT->box("<b><a href=\"https://spreadsheets.google.com/viewform?formkey=dFRPVTRiSkNzSzI1cTVManUwNWVKZXc6MQ\" target=\"_new\">Please follow this link to register!</a></b>");
            }
            
        }
        notify(get_string('savedconfigsuccess', 'plagiarism_crot'), 'notifysuccess');
    }
    $plagiarismsettings = (array)get_config('plagiarism');
    $mform->set_data($plagiarismsettings);

function clean_data(){
    global $DB;
	// cleaning up all the tables for Crot plugin except teachers' settings: delete_records("plagiarism_crot_config")
	$DB->delete_records("plagiarism_crot_files");
    $DB->delete_records("plagiarism_crot_documents");
	$DB->delete_records("plagiarism_crot_fingerprint");
	$DB->delete_records("plagiarism_crot_spair");
	$DB->delete_records("plagiarism_crot_webdoc");
    notify(get_string('tables_cleaned_up','plagiarism_crot'), 'notifysuccess');
}

function test_global_search(){
	// method sends a few queries to test search
  	global $CFG;
	require_once($CFG->dirroot.'/plagiarism/crot/locallib.php');
	// testing global connectivity
	echo "Testing global connectivity...<br>";
	// read file from global bing web site
	$infile = @file_get_contents("http://www.bing.com/siteowner/s/siteowner/Logo_63x23_Dark.png", FILE_BINARY);
	if (strlen($infile)>0 && substr($infile,1,3)=='PNG'){
		// print the file size
		echo "<i>Bing.com is accessible from your server - <font color=\"green\"><b>OK</b></font></i><br><hr>";
	} else {
		echo "can not reach bing.com<br>";
	}
	// testing Bing search
    $plagiarismsettings = (array)get_config('plagiarism');
    $msnkey = $plagiarismsettings['crot_live_key'];
    $culture_info = $plagiarismsettings['crot_culture_info'];
    $todown = $plagiarismsettings['crot_number_of_web_documents'];
	$query = ("Crot for Moodle");
	$query = "'".trim($query)."'";
	
	echo "Testing global search settings for Bing...<br>";
	try {
		$request = 'http://api.bing.net/xml.aspx?Appid=' . $msnkey . 
		'&sources=web&Query=' . urlencode( $query) . 
		'&culture='.$culture_info. 
		'&Web.Options=DisableHostCollapsing+DisableQueryAlterations'.
		'&Options=DisableLocationDetection';
		echo "Sending query:".$request;		
	  	$searches = fetchBingResults($query, $todown, $msnkey, $culture_info);
	}
	catch (Exception $e) {
	  print_error("exception in querying Bing!\n");
	}
	$i=1;
	if ($searches){
		echo "<i>- <font color=\"green\"><b>OK</b></font></i><hr>";
		echo "<b>Search results:</b><br>";
		echo "Top links for <i>".$query."</i> query:<br>";
		foreach($searches as $hit){
			echo "link $i:".substr($hit,0,60)."<br>";
			$i++;
		}
	}else{
		echo "<i> - <font color=\"red\"><b>ERROR!!!</b></font></i><hr>";		
	}
	echo "<script type=\"text/javascript\">alert(\"Test is over\");</script>";
	flush();
}

    echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
    $mform->display();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
