<?php
/**
 * @author Dan Marsden, Svetlana Kim
 */
require_once($CFG->dirroot.'/lib/formslib.php');

class plagiarism_setup_form extends moodleform {

/// Define the form
    function definition () {
        global $CFG;

        $mform =& $this->_form;
        $choices = array('No','Yes');
        $mform->addElement('html', get_string('crotexplain', 'plagiarism_crot'));
        $mform->addElement('checkbox', 'crot_use', get_string('usecrot', 'plagiarism_crot'));

        $mform->addElement('textarea', 'crot_student_disclosure', get_string('studentdisclosure','plagiarism_crot'),'wrap="virtual" rows="6" cols="50"');
        $mform->addHelpButton('crot_student_disclosure', 'studentdisclosure', 'plagiarism_crot');
        $mform->setDefault('crot_student_disclosure', get_string('studentdisclosuredefault','plagiarism_crot'));

        $mform->addElement('text', 'crot_grammarsize', get_string('grammar_size', 'plagiarism_crot'));
        $mform->addHelpButton('crot_grammarsize', 'grammarsize', 'plagiarism_crot');
        $mform->setDefault('crot_grammarsize', '30');
        $mform->addRule('crot_grammarsize', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_windowsize', get_string('window_size', 'plagiarism_crot'));
        $mform->addHelpButton('crot_windowsize', 'windowsize', 'plagiarism_crot');
        $mform->setDefault('crot_windowsize', '60');
        $mform->addRule('crot_windowsize', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_colours', get_string('colours', 'plagiarism_crot'));
        $mform->addHelpButton('crot_colours', 'colour', 'plagiarism_crot');
        $mform->setDefault('crot_colours', '#FF0000,#0000FF, #A0A000, #00A0A0');
        
        $mform->addElement('text', 'crot_clusterdist', get_string('cluster_distance', 'plagiarism_crot'));
        $mform->addHelpButton('crot_clusterdist', 'clusterdistance', 'plagiarism_crot');
        $mform->setDefault('crot_clusterdist', '100');
        $mform->addRule('crot_clusterdist', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_clustersize', get_string('cluster_size', 'plagiarism_crot'));
        $mform->addHelpButton('crot_clustersize', 'clustersize', 'plagiarism_crot');
        $mform->setDefault('crot_clustersize', '2');
        $mform->addRule('crot_clustersize', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_threshold', get_string('default_threshold', 'plagiarism_crot'));
        $mform->addHelpButton('crot_threshold', 'defaultthreshold', 'plagiarism_crot');
        $mform->setDefault('crot_threshold', '0');
        $mform->addRule('crot_threshold', null, 'numeric', null, 'client');
        // Global Search settings
        $mform->addElement('html', get_string('global_search_settings', 'plagiarism_crot'));
        $mform->addElement('text', 'crot_global_threshold', get_string('global_search_threshold', 'plagiarism_crot'));
        $mform->addHelpButton('crot_global_threshold', 'globalsearchthreshold', 'plagiarism_crot');
        $mform->setDefault('crot_global_threshold', '90');
        $mform->addRule('crot_global_threshold', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_live_key', get_string('MS_live_key', 'plagiarism_crot'));
        $mform->addHelpButton('crot_live_key', 'MSlivekey', 'plagiarism_crot');
        $mform->addRule('crot_live_key', null, 'required', null, 'client');
        
        $mform->addElement('text', 'crot_global_search_query_size', get_string('global_search_query_size', 'plagiarism_crot'));
        $mform->addHelpButton('crot_global_search_query_size', 'globalsearchquerysize', 'plagiarism_crot');
        $mform->setDefault('crot_global_search_query_size', '7');
        $mform->addRule('crot_global_search_query_size', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_percentage_of_search_queries', get_string('percentage_of_search_queries', 'plagiarism_crot'));
        $mform->addHelpButton('crot_percentage_of_search_queries', 'percentageofsearchqueries', 'plagiarism_crot');
        $mform->setDefault('crot_percentage_of_search_queries', '40');
        $mform->addRule('crot_percentage_of_search_queries', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_number_of_web_documents', get_string('number_of_web_documents', 'plagiarism_crot'));
        $mform->addHelpButton('crot_number_of_web_documents', 'numberofwebdocuments', 'plagiarism_crot');
        $mform->setDefault('crot_number_of_web_documents', '10');
        $mform->addRule('crot_number_of_web_documents', null, 'numeric', null, 'client');
        
        $mform->addElement('text', 'crot_culture_info', get_string('culture_info', 'plagiarism_crot'));
        $mform->addHelpButton('crot_culture_info', 'cultureinfo', 'plagiarism_crot');
        $mform->setDefault('crot_culture_info', 'en-us');
        
        $mform->addElement('text', 'crot_max_file_size', get_string('max_file_size', 'plagiarism_crot'));
        $mform->addHelpButton('crot_max_file_size', 'maxfilesize', 'plagiarism_crot');
        $mform->setDefault('crot_max_file_size', '1000000');
        $mform->addRule('crot_max_file_size', null, 'numeric', null, 'client');
        // Tools
        $mform->addElement('html', get_string('tools', 'plagiarism_crot'));
        $mform->addElement('checkbox', 'delall', get_string('clean_tables', 'plagiarism_crot'));
        $mform->addHelpButton('delall', 'cleantables', 'plagiarism_crot');
        $mform->addElement('checkbox', 'testglobal', get_string('test_global_serach', 'plagiarism_crot'));
        $mform->addElement('checkbox', 'registration', get_string('registration', 'plagiarism_crot'));

        $this->add_action_buttons(true);
    }
}

