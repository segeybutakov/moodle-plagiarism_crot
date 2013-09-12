<?php
/**
 *
 * @author Sergey Butakov, Svetlana Kim
 * 
 */

	require_once("../../config.php");
    global $CFG;

	require_once($CFG->dirroot."/course/lib.php");
//	require_once($CFG->dirroot."/mod/assignment/lib.php");

    $ida = required_param('id_a', PARAM_INT);   // doc id
    $user_id = required_param('user_id', PARAM_INT); 
    $cid = required_param('cid', PARAM_INT);    // course id

    if (! $course = $DB->get_record("course", array("id" => $cid))) {
        print_error(get_string('incorrect_courseid','plagiarism_crot'));
    }

    require_course_login($course);

    $strmodulename = get_string("block_name", "plagiarism_crot");
    $strassignment  = get_string("assignments", "plagiarism_crot");
    $strstudent = get_string("student_name", "plagiarism_crot");
    $strsimilar = get_string("similar", "plagiarism_crot");
    $strname = get_string('col_name','plagiarism_crot');
    $strcourse = get_string('col_course','plagiarism_crot');
    $strscore = get_string('col_similarity_score','plagiarism_crot');
    $strnoplagiarism = get_string('no_plagiarism','plagiarism_crot');

    if (!$submA = $DB->get_record("plagiarism_crot_documents", array("id" => $ida))) {
        print_error(get_string('incorrect_docAid','plagiarism_crot'));
    }
    if (!$subA = $DB->get_record("plagiarism_crot_files", array("id" => $submA->crot_submission_id)))  {
		print_error(get_string('incorrect_fileAid','plagiarism_crot'));
	}
    if (!$fileA = $DB->get_record("files", array("id" => $subA->file_id))) {
        print_error(get_string('incorrect_fileAid','plagiarism_crot'));
    }
    // sw define type of the assignment
    $asnAtype = $fileA->component;
    switch ($asnAtype) {
        case "assignsubmission_file":
            $asnAtable="assign";
	    $asnAsubm="assign_submission";
            break;
//sw 08/28
        case "assignsubmission_onlinetext":
            $asnAtable="assign";
	    $asnAsubm="assign_submission";
            break;
//sw 08/28 end
        case "mod_assignment":
    	    $asnAtable="assignment";
            $asnAsubm="assignment_submissions";
            break;
    }
    if (!$submissionA = $DB->get_record($asnAsubm, array("id" => $fileA->itemid))) {
        print_error(get_string('incorrect_submAid','plagiarism_crot'));
    }
    if (!$assignA = $DB->get_record($asnAtable, array("id" => $submissionA->assignment))) {
		print_error(get_string('incorrect_assignmentAid','plagiarism_crot'));
	}
    
    if(!has_capability('mod/assignment:grade', get_context_instance(CONTEXT_MODULE, $subA->cm))) {
        print_error(get_string('have_to_be_a_teacher', 'plagiarism_crot'));
    }
    // build navigation and header    
    $view_url = new moodle_url('/mod/'.$asnAtable.'/view.php', array('id' => $subA->cm));
    $PAGE->navbar->add($assignA->name,$view_url);
    $PAGE->navbar->add($strmodulename. " - " . $strassignment);
    $PAGE->set_title($course->shortname.": ".$assignA->name.": ".$strmodulename. " - " . $strassignment);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_url('/plagiarism/crot/index.php', array('ida' => $ida, 'user_id'=>$user_id, 'cid' => $cid));
    echo $OUTPUT->header();
   
    $plagiarismsettings = (array)get_config('plagiarism');
    $threshold = $plagiarismsettings['crot_threshold'];
		
	// fill the table with results
    $table = new html_table();
    $table->head  = array ($strstudent, $strsimilar);
    $table->align = array ("left", "left");
    $table->size = array('30%', '60%');
	
    // select all the assignments that have similarities with the current document
    $table2 = "<table border=2 width='100%'><tr><td width='50%'>$strname</td><td width='40%'>$strcourse</td><td  width='10%'>$strscore</td></tr>";
    $sql_query = "SELECT * FROM {$CFG->prefix}plagiarism_crot_submission_pair WHERE submission_a_id ='$ida' OR  submission_b_id = '$ida' order by number_of_same_hashes desc";
    $similars = $DB->get_records_sql($sql_query);
    $sql_query = "SELECT count(*) as cnt from {$CFG->prefix}plagiarism_crot_fingerprint where crot_doc_id = '$ida'";
    $numbertotal = $DB->get_record_sql($sql_query);// get total number of hashes in the current document
        
	// loop to select assignments with level of similarities above the threshold
	if (!empty($similars)){
        foreach ($similars as $asim){
		    if ($asim->submission_a_id == $ida){
			   $partner = $asim->submission_b_id;
		    }
            else {
			   $partner = $asim->submission_a_id;
		    }
		    // back from id to assignment id
		    $subm3 = $DB->get_record("plagiarism_crot_documents", array("id"=>$partner));
            $party = $partner;
		
		    if ($subm3->crot_submission_id == 0) {
			// web document
                $wwwdoc = $DB->get_record("plagiarism_crot_web_documents", array("document_id"=>$party));					
                $nURL = urldecode($wwwdoc->link);
                $namelink = substr($nURL,0,40);
                $courseBname = get_string('webdocument','plagiarism_crot');
		    }
		    else {
				$subm4 = $DB->get_record("plagiarism_crot_files", array("id"=>$subm3->crot_submission_id));
                $partner=$subm4->file_id;				
                if ($partns = $DB->get_record("files", array("id"=>$partner))) {
                    $namelink = $partns->author;// get author name
                    $courseB = $DB->get_record("course", array("id"=>$subm4->courseid));
                    $courseBname = $courseB->shortname;// get course shortname
                } 
                else {
			       $namelink = get_string('file_was_not_found','plagiarism_crot');
			       $courseBname = get_string('course_not_applicable','plagiarism_crot');
                }
		    }
		    // divide  the number of common by the total # of hashes to get the percentage
		    $perc =  round(($asim->number_of_same_hashes / $numbertotal->cnt) * 100, 2);
            $perc_link = "<a href=\"compare.php?ida=$ida&idb=$party\">".$perc."</a>";
		    //TODO add threshold here $CFG->block_crot_threshold
		    if ($perc > $threshold){
  				$table2 = $table2."<tr><td>$namelink</td><td>$courseBname</td><td>$perc_link %</td></tr>";
  			}
        }// end of the loop
	}
    else {
	    // no plagiarism have been detected OR check up was not performed yet
        $table2 = "<table border=2 width='100%'><tr><td>$strnoplagiarism</td></tr>";
	}
	$table2 = $table2."</table>";
    $user = $DB->get_record("user", array("id"=>$user_id));// get user of the current document
	$namelink = "<a href=\"../../user/view.php?id=$user_id\">".fullname($user)."</a>";
    $table->data[] = array ($namelink, $table2);
    echo $OUTPUT->box(get_string('assignments_not_displayed','plagiarism_crot',$threshold));
    echo html_writer::table($table);

//echo get_string('bing_search','plagiarism_crot')." <a href =\"http://www.bing.com\" target=\"_new\"><img src= \"http://www.bing.com/siteowner/s/siteowner/Logo_63x23_Dark.png\"> </a>";
echo $OUTPUT->footer($course);
?>