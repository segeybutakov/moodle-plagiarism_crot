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
 * lib.php - Contains Plagiarism plugin specific functions called by Modules.
 *
 * @since 2.0
 * @package    plagiarism_crot
 * @subpackage plagiarism
 * @author     Dan Marsden, Sergey Butakov, Svetlana Kim
 * @copyright  2010 Dan Marsden, Sergey Butakov, Svetlana Kim
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

//get global class
global $CFG;
require_once($CFG->dirroot.'/plagiarism/lib.php');

///// Crot Class ////////////////////////////////////////////////////
class plagiarism_plugin_crot extends plagiarism_plugin {
     /**
     * hook to allow plagiarism specific information to be displayed beside a submission 
     * @param array  $linkarray contains all relevant information for the plugin to generate a link
     * @return string
     * 
     */
    public function get_links($linkarray) {
        //$userid, $file, $cmid, $course, $module
        global $DB, $CFG;
        $cmid = $linkarray['cmid'];
        $userid = $linkarray['userid'];
        $file = $linkarray['file'];
        $course = $linkarray['course'];
        $cid = $course->id;
        $output = '';
        //add link/information about this file to $output
            if (!$plagiarism_crot_files_rec = $DB->get_record("plagiarism_crot_files", array("file_id"=>$file->get_id()))) {
                $output .= '';// if there is no record in plagiarism_crot_files about this file then nothing to show
            }
            else {
                if (!$crot_doc_rec = $DB->get_record("plagiarism_crot_documents", array("crot_submission_id"=>$plagiarism_crot_files_rec->id))) {
                    $output .= '';// if there is no record in plagiarism_crot_documents about this file then nothing to show
                }
                else {
                    $sql_query = "SELECT max(number_of_same_hashes) as max FROM {$CFG->prefix}plagiarism_crot_submission_pair WHERE submission_a_id ='$crot_doc_rec->id' OR  submission_b_id = '$crot_doc_rec->id'";
                    if (!$similarity = $DB->get_record_sql($sql_query)) {// get maximum number of same hashes for the current document
                        $output .= '<br><b>'.get_string('no_similarities','plagiarism_crot').'</b>';
                    }
                    else {
                        $sql_query = "SELECT count(*) as cnt from {$CFG->prefix}plagiarism_crot_fingerprint where crot_doc_id = '$crot_doc_rec->id'";
                        $numbertotal = $DB->get_record_sql($sql_query);// get total number of hashes for the current document
                        $perc =  round(($similarity->max / $numbertotal->cnt) * 100, 2);
                        $output .= "<br><b> <a href=\"../../plagiarism/crot/index.php?id_a=$crot_doc_rec->id&user_id=$userid&cid=$cid\">".$perc."%</a></b>";
                    }
                }
            }
        return $output;
    }

    /* hook to save plagiarism specific settings on a module settings page
     * @param object $data - data from an mform submission.
    */
    public function save_form_elements($data) {
        global $DB;
        $plagiarismsettings = (array)get_config('plagiarism');
        if (!empty($plagiarismsettings['crot_use'])) {
            if (isset($data->crot_use)) {
                //array of posible plagiarism config options.
                $plagiarismelements = $this->config_options();
                //first get existing values
                $existingelements = $DB->get_records_menu('plagiarism_crot_config', array('cm'=>$data->coursemodule),'','name,id');
                foreach($plagiarismelements as $element) {
                    $newelement = new object();
                    $newelement->cm = $data->coursemodule;
                    $newelement->name = $element;
                    $newelement->value = (isset($data->$element) ? $data->$element : 0);
                    if (isset($existingelements[$element])) { //update
                        $newelement->id = $existingelements[$element];
                        $DB->update_record('plagiarism_crot_config', $newelement);
                    } else { //insert
                        $DB->insert_record('plagiarism_crot_config', $newelement);
                    }
                }

            }
        }
    }

    /**
     * hook to add plagiarism specific settings to a module settings page
     * @param object $mform  - Moodle form
     * @param object $context - current context
     */
    public function get_form_elements_module($mform, $context) {
        global $DB;
        $plagiarismsettings = (array)get_config('plagiarism');
        if (!empty($plagiarismsettings['crot_use'])) {
            $cmid = optional_param('update', 0, PARAM_INT); //there doesn't seem to be a way to obtain the current cm a better way - $this->_cm is not available here.
            if (!empty($cmid)) {
                $plagiarismvalues = $DB->get_records_menu('plagiarism_crot_config', array('cm'=>$cmid),'','name,value');
            }
            $plagiarismelements = $this->config_options();

            $ynoptions = array( 0 => get_string('no'), 1 => get_string('yes'));
            $mform->addElement('header', 'crotdesc', get_string('crot', 'plagiarism_crot'));
            $mform->addHelpButton('crotdesc', 'crot', 'plagiarism_crot');
            $mform->addElement('select', 'crot_use', get_string("usecrot", "plagiarism_crot"), $ynoptions);
            $mform->addElement('select', 'crot_local', get_string("comparestudents", "plagiarism_crot"), $ynoptions);
            $mform->disabledIf('crot_local', 'crot_use', 'eq', 0);
            $mform->setDefault('crot_local', '1');
            $mform->addElement('select', 'crot_global', get_string("compareinternet", "plagiarism_crot"), $ynoptions);
            $mform->disabledIf('crot_global', 'crot_use', 'eq', 0);
            
            foreach ($plagiarismelements as $element) {
                if (isset($plagiarismvalues[$element])) {
                    $mform->setDefault($element, $plagiarismvalues[$element]);
                }
            }
        }
        //Add elements to form using standard mform like:
        //$mform->addElement('hidden', $element);
        //$mform->disabledIf('plagiarism_draft_submit', 'var4', 'eq', 0);
    }

    /**
     * hook to allow a disclosure to be printed notifying users what will happen with their submission
     * @param int $cmid - course module id
     * @return string
     */
    public function print_disclosure($cmid) {
         global $DB, $OUTPUT;
         // check if this cmid has plagiarism enabled
         $select = 'cm = ? AND '.$DB->sql_compare_text('name').' = "crot_use"';
         if (! $crot_use = $DB->get_record_select('plagiarism_crot_config', $select, array($cmid))) {
            return;
         } else if ($crot_use->value == 0) {
            return;
         }
        $plagiarismsettings = (array)get_config('plagiarism');
        echo $OUTPUT->box_start('generalbox boxaligncenter', 'intro');
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        echo format_text($plagiarismsettings['crot_student_disclosure'], FORMAT_MOODLE, $formatoptions);
        echo $OUTPUT->box_end();
    }

    /**
     * hook to allow status of submitted files to be updated - called on grading/report pages.
     *
     * @param object $course - full Course object
     * @param object $cm - full cm object
     */
    public function update_status($course, $cm) {
        //called at top of submissions/grading pages - allows printing of admin style links or updating status
    }

    /**
     * called by admin/cron.php 
     *
     */
    public function cron() {
        //do any scheduled task stuff
        global $CFG;
        require_once($CFG->dirroot.'/plagiarism/crot/crot_cron.php');
        
    }
    public function config_options() {
        return array('crot_use','crot_local', 'crot_global');
    }
}

function crot_event_file_uploaded($eventdata) {
    global $DB;
    $result = true;
        //a file has been uploaded - submit this to the plagiarism prevention service.
        
    return $result;
}
function crot_event_files_done($eventdata) {
    global $DB;
    $result = true;
        //mainly used by assignment finalize - used if you want to handle "submit for marking" events
        //a file has been uploaded/finalised - submit this to the plagiarism prevention service.
    $plagiarismvalues = $DB->get_records_menu('plagiarism_crot_config', array('cm'=>$eventdata->cmid),'','name,value');
    if (empty($plagiarismvalues['crot_use'])) {
        return $result;
    }
    else {
        $status_value = array('queue','in_processing','end_processing');
        $modulecontext = get_context_instance(CONTEXT_MODULE, $eventdata->cmid);
        $fs = get_file_storage();
        if ($files = $fs->get_area_files($modulecontext->id, 'mod_assignment','submission', $eventdata->itemid)) {
            // put files that were submitted for marking into queue for check up
            foreach ($files as $file) {
                if ($file->get_filename()==='.') {
                    continue;
                }
                $newelement = new stdclass();
                $newelement->file_id = $file->get_id();
                $newelement->path = $file->get_contenthash();
                $newelement->status = $status_value[0]; 
                $newelement->time = time(); 
                $newelement->cm = $eventdata->cmid;    
                $newelement->courseid = $eventdata->courseid; 
                $result=$DB->insert_record('plagiarism_crot_files', $newelement);
                echo "\nfile ".$file->get_filename()." was queued up for plagiarism detection service\n";
            }
        }
        return $result;
    }
}

function crot_event_mod_created($eventdata) {
    $result = true;
        //a new module has been created - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}

function crot_event_mod_updated($eventdata) {
    $result = true;
        //a module has been updated - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}

function crot_event_mod_deleted($eventdata) {
    $result = true;
        //a module has been deleted - this is a generic event that is called for all module types
        //make sure you check the type of module before handling if needed.

    return $result;
}
