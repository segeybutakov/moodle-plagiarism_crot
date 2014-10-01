<?php

    if (!defined('MOODLE_INTERNAL')) {
        die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
    }
// overrides php limits
$maxtimelimit = ini_get('max_execution_time');
ini_set('max_execution_time', 18000);
$maxmemoryamount = ini_get('memory_limit');
// set large amount of memory for the processing
// fingeprint calcualtion mey be very memory consuming especially for large documents from the internet
ini_set('memory_limit', '1024M');

// store current time for perf measurements
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

global $CFG, $DB;

require_once($CFG->dirroot.'/plagiarism/crot/lib.php');
require_once($CFG->dirroot.'/plagiarism/crot/locallib.php');
require_once($CFG->dirroot."/course/lib.php");
require_once($CFG->dirroot."/mod/assignment/lib.php");
require_once($CFG->dirroot.'/config.php');

$plagiarismsettings = (array)get_config('plagiarism');
$gram_size 	= $plagiarismsettings['crot_grammarsize']; 
$window_size	= $plagiarismsettings['crot_windowsize'];
$query_size 	= $plagiarismsettings['crot_global_search_query_size'];
$msnkey 	= $plagiarismsettings['crot_live_key'];
$culture_info	= $plagiarismsettings['crot_culture_info'];
$globs = $plagiarismsettings['crot_percentage_of_search_queries'];
$todown = $plagiarismsettings['crot_number_of_web_documents'];

if (empty($gram_size)||empty($window_size)) {
        die('The plugin is not properly set. Please set the plugin in admin/plugins/plagiarism prevention menu');    /// the initial settigns were not properly set up
}

// main loop on crot_files table - check if there are files marked for the check up 
//$link = mysql_connect("$CFG->dbhost", "$CFG->dbuser", "$CFG->dbpass") or die("Could not connect");
//mysql_select_db("$CFG->dbname") or die ("Could not select database");
$sql_query = "SELECT cf.* FROM {plagiarism_crot_files} cf where cf.status = 'queue'";
$files = $DB->get_records_sql($sql_query);
if (!empty($files)){
    foreach ($files as $afile){
        try {
        $afile->status = 'in_processing';     
        $DB->update_record('plagiarism_crot_files', $afile);
        
        echo "\nfile $afile->id was not processed yet. start processing now ... \n" ;
		$atime = microtime();
        $atime = explode(" ",$atime);
        $atime = $atime[1] + $atime[0];
        $astarttime = $atime;
        
        $fs = get_file_storage();
        $file = $fs->get_file_by_id($afile->file_id);
        
        $filename = $file->get_filename(); // get file name
        
        $arrfilename = explode(".",$filename);
        $ext = $arrfilename[count($arrfilename)-1];// get file extension
        $l1 = $afile->path[0].$afile->path[1];
        $l2 = $afile->path[2].$afile->path[3];
        $apath= $CFG->dataroot."/filedir/$l1/$l2/$afile->path";  // get file path
        
        // call tokenizer to get plain text and store it in plagiarism_crot_documents
        $atext = tokenizer ($apath, $ext);   
        $atext = mb_substr($atext, 0, 999000);
      // insert into plagiarism_crot_documents  
      //  $docrecord = new object();
        $docrecord->crot_submission_id = $afile->id;
	    $docrecord->content = addslashes($atext);
       $docid = $DB->insert_record('plagiarism_crot_documents', $docrecord);
       
        //$content = addslashes($atext);
        //$query = "INSERT INTO {$CFG->prefix}plagiarism_crot_documents (content, crot_submission_id) VALUES ('$content','$afile->id')";
        //mysql_query($query);
      //  $docid = mysql_insert_id();
        
        // fingerprinting - calculate and store the fingerprints into the table
		$atext=mb_strtolower($atext, "utf-8");
		
		// get f/print
		$fingerp = array();
		$fingerp = GetFingerprint($atext);
    
    // store fingerprint
		foreach ($fingerp as $fp) {
			$hashrecord->position = $fp->position;
			$hashrecord->crot_doc_id = $docid;
			$hashrecord->value = $fp->value;
			$DB->insert_record("plagiarism_crot_fingerprint", $hashrecord);	
		}
        
        // local search
        echo "starting local search \n";
        $plagiarismvalues = $DB->get_records_menu('plagiarism_crot_config', array('cm'=>$afile->cm),'','name,value');
        if ($plagiarismvalues['crot_local']==1) {
			// comparing fingerprints and updating plagiarism_crot_spair table
			// select all submissions that has at least on common f/print with the current document
			$sql_query ="SELECT id
				FROM {$CFG->prefix}plagiarism_crot_documents asg
					WHERE exists (
						select * from
						{$CFG->prefix}plagiarism_crot_fingerprint fp1
						inner join {$CFG->prefix}plagiarism_crot_fingerprint fp2 on fp1.value = fp2.value
							where fp2.crot_doc_id = asg.id and fp1.crot_doc_id = $docid
					)";
			$pair_submissions = $DB->get_records_sql($sql_query);
		
			foreach ($pair_submissions as $pair_submission){
				// check if id exists in web_doc table then don't compare because
				// we consider only local documents here
				if ($webdoc = $DB->get_record("plagiarism_crot_web_documents", array('document_id'=>$pair_submission->id)))
					continue;
				//compare two fingerprints to get the number of same hashes
				if ($docid!=$pair_submission->id){
					$sql_query = "select sum(case when cnt1 < cnt2 then cnt1 else cnt2 end) cnt
						from
						(
							select count(*) as cnt1, (
								select count(*)
								from {$CFG->prefix}plagiarism_crot_fingerprint fp2
								where 		fp2.crot_doc_id 	= $docid  
									and	fp2.value		= fp1.value
							) as cnt2
							from {$CFG->prefix}plagiarism_crot_fingerprint fp1
							where fp1.crot_doc_id = $pair_submission->id
							group by fp1.value
						) t";
					$similarnumber = $DB->get_record_sql($sql_query);
					// takes id1 id2 and create/update record with the number of similar hashes
					$sql_query ="SELECT * FROM {$CFG->prefix}plagiarism_crot_spair 
							where (submission_a_id = $afile->id and submission_b_id = $pair_submission->id) 
							OR (submission_a_id = $pair_submission->id and submission_b_id = $afile->id)";
					$pair = $DB->get_record_sql($sql_query);
					if (! $pair){
						// insert
						$pair_record->submission_a_id = $docid;
						$pair_record->submission_b_id = $pair_submission->id;
						$pair_record->number_of_same_hashes = $similarnumber->cnt;
						$DB->insert_record("plagiarism_crot_spair", $pair_record);	
					} else {
						// TODO update		
					}
				}	// end of comparing with local documents
			}
		} // end for local search
        
        if ($plagiarismvalues['crot_global']==1) {
			// global search
			echo "\nfile $afile->id is selected for global search. Starting global search\n";
			// strip text
			$atext = StripText($atext," ");
			// create search queries
			$words = array();
			$words = preg_split("/[\s]+/", trim(StripText($atext, " ")));
			$max = sizeof($words) - $query_size +1;
			$queries = array ();
			for ($i=0; $i < $max; $i++) {
				$query = "";
				for ($j=$i; ($j-$i)<$query_size; $j++){
					$query = $query." ".$words[$j];
				}
				$queries[] = $query;
			} 	// queries are ready!
			
            // create list of URLs
			srand((float) microtime() * 10000000);

			// randomly select x% of queries
			$rand_keys = array_rand($queries, (sizeof($queries)/100)*$globs);
			$narr = array();
			foreach ($rand_keys as $mkey) {
				$narr[]=$queries[$mkey];
			}
			$queries = $narr;

			$tarr = getTopResults($queries,$todown,$msnkey,$culture_info);
			$k=0;
			// get top results
			foreach($tarr as $manUrl) {	
				//get content of downloaded web document
				// in php ini allow_url_fopen = On
				$path = $manUrl->mainUrl;
				// get content from the remote file
				$mega = array ();

				// get content  and get encoding
				if (trim($path)!="")  {
					try {
					  $result = getremotecontent( $path );
					  if (trim($result)==""){
					  	continue;
					  }
					}
					catch (Exception $e) {
					  print_error("exception in downloading!\n");
					  $result = "Was not able to download the respective resource";
					}
				}
				else {
					continue;
				}

				$result = mb_ereg_replace('#\s{2,}#',' ',$result);  

				// split into strings and remove empty ones
				$strs  = explode ("\n", $result);
				$result = "";  
				foreach ($strs as $st) 	{
					$st = trim($st);
					if ($st!="")  {
						$result = $result.mb_ereg_replace('/\s\s+/', ' ', $st)." \n";
					}
				}
				// insert doc into crot_doc table
				$wdocrecord->crot_submission_id = 0;	
				$wdocrecord->content = addslashes($result);
			$wdocid = $DB->insert_record("plagiarism_crot_documents", $wdocrecord);
                //$wdoc_content = addslashes($result);
                //$query = "INSERT INTO {$CFG->prefix}plagiarism_crot_documents (content, crot_submission_id) VALUES ('$wdoc_content','0')";
               // mysql_query($query);
                //$wdocid = mysql_insert_id();
                	
				// insert doc into web_doc table
				$webdocrecord->document_id = $wdocid;
				$webdocrecord->link=urlencode($manUrl->mainUrl);
				$webdocrecord->link_live=urlencode($manUrl->msUrl);
				$webdocrecord->is_from_cache=false;
				$webdocrecord->related_doc_id = $docid;
				$webdocid = $DB->insert_record("plagiarism_crot_web_documents", $webdocrecord);
                //$weblink = urlencode($manUrl->mainUrl);
                //$weblinklive = urlencode($manUrl->msUrl);
                //$query = "INSERT INTO {$CFG->prefix}plagiarism_crot_web_documents (document_id,link,link_live,is_from_cache,related_doc_id) VALUES ('$wdocid','$weblink','$weblinklive','false','$docid')";			
                //mysql_query($query);
                //$webdocid = mysql_insert_id();
				// fingerprinting - calculate and store the fingerprints into the table
				$result=mb_convert_case($result, MB_CASE_LOWER, "UTF-8");

				$fingerp = array();
				try {
					$fingerp = GetFingerprint($result);
				}
				catch (Exception $e)
				{
					print_error("exception in FP calc\n");
					continue;
				}

				// store fingerprint
				foreach ($fingerp as $fp)
				{
					$hashrecord->position = $fp->position;
					$hashrecord->crot_doc_id = $wdocid;		
					$hashrecord->value = $fp->value;
					$DB->insert_record("plagiarism_crot_fingerprint", $hashrecord);	
				}

				//compare two fingerprints to get the number of same hashes
				$sql_query = "select sum(case when cnt1 < cnt2 then cnt1 else cnt2 end) cnt
					from
					(
						select count(*) as cnt1, (
							select count(*)
							from {$CFG->prefix}plagiarism_crot_fingerprint fp2
							where 		fp2.crot_doc_id 	= $docid  
								and	fp2.value		= fp1.value
						) as cnt2
						from {$CFG->prefix}plagiarism_crot_fingerprint fp1
						where fp1.crot_doc_id = $wdocid
						group by fp1.value
					) t";
				try {
					$similarnumber = $DB->get_record_sql($sql_query);
				}
				catch (Exception $e) {
					print_error("exception in query\n");
					continue;
				}
				// check that the number of same hashes is not null
				if(!is_null($similarnumber->cnt) && $similarnumber->cnt!=0 ){
					// add record to pair table
					$pair_record->submission_a_id = $docid;
					$pair_record->submission_b_id = $wdocid;
					$pair_record->number_of_same_hashes = $similarnumber->cnt;
					$ppair = $DB->insert_record("plagiarism_crot_spair", $pair_record);
				} else {
					//if null then remove the web document and its fingerprint
					// remove from doc
					$DB->delete_records("plagiarism_crot_documents", array("id"=>$wdocid));
					// remove from fingerprints
					$DB->delete_records("plagiarism_crot_fingerprint", array("crot_doc_id"=>$wdocid));
				}					
			}	
		
		} // end global search
        
        $afile->status = 'end_processing';     
        $DB->update_record('plagiarism_crot_files', $afile);
        echo "\nfile $afile->id was sucessfully processed\n" ;
        }
        catch (Exception $e)
        {
            print_error("Error in processing file $afile->id!\n");
            continue;
        }
    }// end of the main loop
}
else{
    echo "No uploaded assignments to process!";
    }    
// set back normal values for php limits
ini_set('max_execution_time', $maxtimelimit);
ini_set('memory_limit', $maxmemoryamount);

// calc and display exec time
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);
echo "\nThe uploaded assignments were processed by crot in ".$totaltime." seconds\n";

// Online Text/TYPE-IN Processing 
// @author Hamman Samuel (hwsamuel@ualberta.ca)
/*
$sql_query = "SELECT cf.* FROM {plagiarism_crot_files} cf where cf.status = 'onlinetext'";
$files = $DB->get_records_sql($sql_query);

if (!empty($files)){
    foreach ($files as $afile){
        try {
        $afile->status = 'in_processing';     
        $DB->update_record('plagiarism_crot_files', $afile);
        
        echo "\nfile $afile->id was not processed yet. start processing now ... \n" ;
        
        $atime = microtime();
        $atime = explode(" ",$atime);
        $atime = $atime[1] + $atime[0];
        $astarttime = $atime;
                
        // call tokenizer to get plain text and store it in plagiarism_crot_documents
        $atext = $afile->path; //tokenizer ($apath, $ext);   
        $atext = mb_substr($atext, 0, 999000);
      
        $docrecord->crot_submission_id = $afile->id;
        $docrecord->content = addslashes($atext);
        $docid = $DB->insert_record('plagiarism_crot_documents', $docrecord);
        
        // fingerprinting - calculate and store the fingerprints into the table
      $atext=mb_strtolower($atext, "utf-8");
		
		// get f/print
		$fingerp = array();
		$fingerp = GetFingerprint($atext);
    
    // store fingerprint
		foreach ($fingerp as $fp) {
			$hashrecord->position = $fp->position;
			$hashrecord->crot_doc_id = $docid;
			$hashrecord->value = $fp->value;
			$DB->insert_record("plagiarism_crot_fingerprint", $hashrecord);	
		}
        // local search
        echo "starting local search \n";
        $plagiarismvalues = $DB->get_records_menu('plagiarism_crot_config', array('cm'=>$afile->cm),'','name,value');
        if ($plagiarismvalues['crot_local']==1) {
			// comparing fingerprints and updating plagiarism_crot_spair table
			// select all submissions that has at least on common f/print with the current document
			$sql_query ="SELECT id
				FROM {$CFG->prefix}plagiarism_crot_documents asg
					WHERE exists (
						select * from
						{$CFG->prefix}plagiarism_crot_fingerprint fp1
						inner join {$CFG->prefix}plagiarism_crot_fingerprint fp2 on fp1.value = fp2.value
							where fp2.crot_doc_id = asg.id and fp1.crot_doc_id = $docid
					)";
			$pair_submissions = $DB->get_records_sql($sql_query);
		
			foreach ($pair_submissions as $pair_submission){
				// check if id exists in web_doc table then don't compare because
				// we consider only local documents here
				if ($webdoc = $DB->get_record("plagiarism_crot_web_documents", array('document_id'=>$pair_submission->id)))
					continue;
				//compare two fingerprints to get the number of same hashes
				if ($docid!=$pair_submission->id){
					$sql_query = "select sum(case when cnt1 < cnt2 then cnt1 else cnt2 end) cnt
						from
						(
							select count(*) as cnt1, (
								select count(*)
								from {$CFG->prefix}plagiarism_crot_fingerprint fp2
								where 		fp2.crot_doc_id 	= $docid  
									and	fp2.value		= fp1.value
							) as cnt2
							from {$CFG->prefix}plagiarism_crot_fingerprint fp1
							where fp1.crot_doc_id = $pair_submission->id
							group by fp1.value
						) t";
					$similarnumber = $DB->get_record_sql($sql_query);
					// takes id1 id2 and create/update record with the number of similar hashes
					$sql_query ="SELECT * FROM {$CFG->prefix}plagiarism_crot_spair 
							where (submission_a_id = $afile->id and submission_b_id = $pair_submission->id) 
							OR (submission_a_id = $pair_submission->id and submission_b_id = $afile->id)";
					$pair = $DB->get_record_sql($sql_query);
					if (! $pair){
						// insert
						$pair_record->submission_a_id = $docid;
						$pair_record->submission_b_id = $pair_submission->id;
						$pair_record->number_of_same_hashes = $similarnumber->cnt;
						$DB->insert_record("plagiarism_crot_spair", $pair_record);	
					} else {
						// TODO update		
					}
				}	// end of comparing with local documents
			}
		} // end for local search
        
        if ($plagiarismvalues['crot_global']==1) {
			// global search
			echo "\nfile $afile->id is selected for global search. Starting global search\n";
			// strip text
			$atext = StripText($atext," ");
			// create search queries
			$words = array();
			$words = preg_split("/[\s]+/", trim(StripText($atext, " ")));
			$max = sizeof($words) - $query_size +1;
			$queries = array ();
			for ($i=0; $i < $max; $i++) {
				$query = "";
				for ($j=$i; ($j-$i)<$query_size; $j++){
					$query = $query." ".$words[$j];
				}
				$queries[] = $query;
			} 	// queries are ready!
			
            // create list of URLs
			srand((float) microtime() * 10000000);

			// randomly select x% of queries
			$rand_keys = array_rand($queries, (sizeof($queries)/100)*$globs);
			$narr = array();
			foreach ($rand_keys as $mkey) {
				$narr[]=$queries[$mkey];
			}
			$queries = $narr;

			$tarr = getTopResults($queries,$todown,$msnkey,$culture_info);
			$k=0;
			// get top results
			foreach($tarr as $manUrl) {	
				//get content of downloaded web document
				// in php ini allow_url_fopen = On
				$path = $manUrl->mainUrl;
				// get content from the remote file
				$mega = array ();

				// get content  and get encoding
				if (trim($path)!="")  {
					try {
					  $result = getremotecontent( $path );
					  if (trim($result)==""){
					  	continue;
					  }
					}
					catch (Exception $e) {
					  print_error("exception in downloading!\n");
					  $result = "Was not able to download the respective resource";
					}
				}
				else {
					continue;
				}

				$result = mb_ereg_replace('#\s{2,}#',' ',$result);  

				// split into strings and remove empty ones
				$strs  = explode ("\n", $result);
				$result = "";  
				foreach ($strs as $st) 	{
					$st = trim($st);
					if ($st!="")  {
						$result = $result.mb_ereg_replace('/\s\s+/', ' ', $st)." \n";
					}
				}
				// insert doc into crot_doc table
				$wdocrecord->crot_submission_id = 0;	
				$wdocrecord->content = addslashes($result);
			$wdocid = $DB->insert_record("plagiarism_crot_documents", $wdocrecord);
                //$wdoc_content = addslashes($result);
                //$query = "INSERT INTO {$CFG->prefix}plagiarism_crot_documents (content, crot_submission_id) VALUES ('$wdoc_content','0')";
               // mysql_query($query);
                //$wdocid = mysql_insert_id();
                	
				// insert doc into web_doc table
				$webdocrecord->document_id = $wdocid;
				$webdocrecord->link=urlencode($manUrl->mainUrl);
				$webdocrecord->link_live=urlencode($manUrl->msUrl);
				$webdocrecord->is_from_cache=false;
				$webdocrecord->related_doc_id = $docid;
				$webdocid = $DB->insert_record("plagiarism_crot_web_documents", $webdocrecord);
                //$weblink = urlencode($manUrl->mainUrl);
                //$weblinklive = urlencode($manUrl->msUrl);
                //$query = "INSERT INTO {$CFG->prefix}plagiarism_crot_web_documents (document_id,link,link_live,is_from_cache,related_doc_id) VALUES ('$wdocid','$weblink','$weblinklive','false','$docid')";			
                //mysql_query($query);
                //$webdocid = mysql_insert_id();
				// fingerprinting - calculate and store the fingerprints into the table
				$result=mb_convert_case($result, MB_CASE_LOWER, "UTF-8");

				$fingerp = array();
				try {
					$fingerp = GetFingerprint($result);
				}
				catch (Exception $e)
				{
					print_error("exception in FP calc\n");
					continue;
				}

				// store fingerprint
				foreach ($fingerp as $fp)
				{
					$hashrecord->position = $fp->position;
					$hashrecord->crot_doc_id = $wdocid;		
					$hashrecord->value = $fp->value;
					$DB->insert_record("plagiarism_crot_fingerprint", $hashrecord);	
				}

				//compare two fingerprints to get the number of same hashes
				$sql_query = "select sum(case when cnt1 < cnt2 then cnt1 else cnt2 end) cnt
					from
					(
						select count(*) as cnt1, (
							select count(*)
							from {$CFG->prefix}plagiarism_crot_fingerprint fp2
							where 		fp2.crot_doc_id 	= $docid  
								and	fp2.value		= fp1.value
						) as cnt2
						from {$CFG->prefix}plagiarism_crot_fingerprint fp1
						where fp1.crot_doc_id = $wdocid
						group by fp1.value
					) t";
				try {
					$similarnumber = $DB->get_record_sql($sql_query);
				}
				catch (Exception $e) {
					print_error("exception in query\n");
					continue;
				}
				// check that the number of same hashes is not null
				if(!is_null($similarnumber->cnt) && $similarnumber->cnt!=0 ){
					// add record to pair table
					$pair_record->submission_a_id = $docid;
					$pair_record->submission_b_id = $wdocid;
					$pair_record->number_of_same_hashes = $similarnumber->cnt;
					$ppair = $DB->insert_record("plagiarism_crot_spair", $pair_record);
				} else {
					//if null then remove the web document and its fingerprint
					// remove from doc
					$DB->delete_records("plagiarism_crot_documents", array("id"=>$wdocid));
					// remove from fingerprints
					$DB->delete_records("plagiarism_crot_fingerprint", array("crot_doc_id"=>$wdocid));
				}					
			}	
		
		} // end global search
        
        $afile->status = 'end_processing';     
        $DB->update_record('plagiarism_crot_files', $afile);
        echo "\nfile $afile->id was sucessfully processed\n" ;
        }
        catch (Exception $e)
        {
            print_error("Error in processing file $afile->id!\n");
            continue;
        }
    }// end of the main loop
}
else{
    echo "No TYPE-IN assignments to process!";
    }    
// set back normal values for php limits
ini_set('max_execution_time', $maxtimelimit);
ini_set('memory_limit', $maxmemoryamount);

// calc and display exec time
$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = ($endtime - $starttime);
echo "\nThe TYPE-IN assignments were processed by crot in ".$totaltime." seconds\n";
*/
?>
