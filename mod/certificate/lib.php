<?php //$Id: lib.php,v 1.21.2.14 2008/05/22 16:38:50 mchurch Exp $
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// Copyright (C) Pro Moodle, www.promoodle.com                           //
// License http://www.gnu.org/copyleft/gpl.html                          //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/grade/lib.php');
require_once($CFG->dirroot.'/grade/querylib.php'); 
define ('CERTCOURSETIMEID', -1);

// STANDARD FUNCTIONS ////////////////////////////////////////////////////////

/************************************************************************
 * Given an object containing all the necessary data,                   * 
 * (defined by the form in mod.html) this function                      *
 * will create a new instance and return the id number                  *
 * of the new instance.                                                 *
 ************************************************************************/

function certificate_add_instance($certificate) {
    
    $certificate->timemodified = time();

    if (!empty($certificate->requiredgrade)) {
        $certificate->lockgrade = 1;
    } else {
        $certificate->lockgrade = 0;
    }

    if ($returnid = insert_record("certificate", $certificate)) {
        $certificate->id = $returnid;

        if (isset($certificate->linkid) and is_array($certificate->linkid)) {
            foreach ($certificate->linkid as $key => $linkid) {
                if ($linkid > 0) {
                    $clm->certificate_id = $certificate->id;
                    $clm->linkid = $linkid;
                    $clm->linkgrade = $certificate->linkgrade[$key];
                    $clm->timemodified = $certificate->timemodified;
                    $retval = insert_record('certificate_linked_modules', $clm) and $retval;
                }
            }
        }
        if (isset($certificate->coursetime)) {
            $clm->certificate_id = $certificate->id;
            $clm->linkid = CERTCOURSETIMEID;
            $clm->linkgrade = $certificate->coursetime;
            $clm->timemodified = $certificate->timemodified;
            $retval = insert_record('certificate_linked_modules', $clm) and $retval;
        }

        $event = NULL;
        $event->name        = $certificate->name;
        $event->description = '';
        $event->courseid    = $certificate->course;
        $event->groupid     = 0;
        $event->userid      = 0;
        $event->modulename  = 'certificate';
        $event->instance    = $returnid;
        $event->description = '';
        add_event($event);
    }

    return $returnid;
}

/************************************************************************
 * Updates an instance of a certificate                                 *
 ************************************************************************/
function certificate_update_instance($certificate) {
    
    $certificate->timemodified = time();
    $certificate->id = $certificate->instance;

    if (!empty($certificate->requiredgrade)) {
        $certificate->lockgrade = 1;
    } else {
        $certificate->lockgrade = 0;
    }

    if ($returnid = update_record('certificate', $certificate)) {

        if (isset($certificate->linkid) and is_array($certificate->linkid)) {
            foreach ($certificate->linkid as $key => $linkid) {
                if (isset($certificate->linkentry[$key])) {
                    if ($linkid > 0) {
                        unset($clm);
                        $clm->id = $certificate->linkentry[$key];
                        $clm->certificate_id = $certificate->id;
                        $clm->linkid = $linkid;
                        $clm->linkgrade = $certificate->linkgrade[$key];
                        $clm->timemodified = $certificate->timemodified;
                        $retval = update_record('certificate_linked_modules', $clm) and $retval;
                    } else {
                        $retval = delete_records('certificate_linked_modules', 'id',
                                                 $certificate->linkentry[$key]) and $retval;
                    }
                } else if ($linkid > 0) {
                    unset($clm);
                    $clm->certificate_id = $certificate->id;
                    $clm->linkid = $linkid;
                    $clm->linkgrade = $certificate->linkgrade[$key];
                    $clm->timemodified = $certificate->timemodified;
                    $retval = insert_record('certificate_linked_modules', $clm) and $retval;
                }
            }
        }
        if (isset($certificate->coursetime)) {
            $clm->certificate_id = $certificate->id;
            $clm->linkid = CERTCOURSETIMEID;
            $clm->linkgrade = $certificate->coursetime;
            $clm->timemodified = $certificate->timemodified;
            if ($oldrec = get_record('certificate_linked_modules', 'certificate_id', $certificate->id, 
                                     'linkid', CERTCOURSETIMEID)) {
                $clm->id = $oldrec->id;
                $retval = update_record('certificate_linked_modules', $clm) and $retval;
            } else {
                $retval = insert_record('certificate_linked_modules', $clm) and $retval;
            }
        }

        if ($event->id = get_field('event', 'id', 'modulename', 'certificate', 'instance', $certificate->id)) {
            $event->name        = $certificate->name;

            update_event($event);
        } else {
            $event = NULL;
            $event->name        = $certificate->name;
            $event->description = '';
            $event->courseid    = $certificate->course;
            $event->groupid     = 0;
            $event->userid      = 0;
            $event->modulename  = 'certificate';
            $event->instance    = $certificate->id;

            add_event($event);
        }
    } else {
        delete_records('event', 'modulename', 'certificate', 'instance', $certificate->id);
    }

    return $returnid;
}

/************************************************************************
 * Deletes an instance of a certificate                                 *
 ************************************************************************/
function certificate_delete_instance($id) {

    if (!$certificate = get_record('certificate', 'id', $id)) {
        return false;
        }

    $result = true;

    delete_records('certificate_issues', 'certificateid', $certificate->id);
    delete_records('certificate_linked_modules', 'certificate_id', $certificate->id);

        if (!delete_records('certificate', 'id', $certificate->id)) {
            $result = false;
        }
        
        return $result;
    }

/************************************************************************
 * Deletes any files associated with this field                         *
 ************************************************************************/
function delete_certificate_files($certificate='') {
        global $CFG;

        require_once($CFG->libdir.'/filelib.php');

        $dir = $CFG->dataroot.'/'.$certificate->course.'/'.$CFG->moddata.'/certificate/'.$certificate->id.'/'.$user->id;
        if ($certificateid) {
            $dir .= '/'.$certificateid;
        }

        return fulldelete($dir);
    }

/************************************************************************
 * Returns information about received certificate.                      * 
 * Used for user activity reports.                                      *
 ************************************************************************/
function certificate_user_outline($course, $user, $mod, $certificate) {
    if ($issue = get_record('certificate_issues', 'certificateid', $certificate->id, 'userid', $user->id)) {
        $result->info = get_string('issued', 'certificate');
        $result->time = $issue->certdate;
     } 
        if (!$issue = get_record('certificate_issues', 'certificateid', $certificate->id, 'userid', $user->id)) {
        $result->info = get_string('notissued', 'certificate');
     }
     return $result;
    }
    return NULL;


/************************************************************************
 * Returns information about received certificate.                      * 
 * Used for user activity reports.                                      *
 ************************************************************************/
function certificate_user_complete($course, $user, $mod, $certificate) {
    if ($issue = get_record('certificate_issues', 'certificateid', $certificate->id, 'userid', $user->id)) {

            print_simple_box_start();
            echo get_string('issued', 'certificate').": ";
            echo userdate($issue->certdate);
    
            certificate_print_user_files($user->id);
    
            echo '<br />';
    
           } else {
           print_string('notissuedyet', 'certificate');
    }           
            print_simple_box_end();
}

/************************************************************************
 * Must return an array of user records (all data) who are participants *
 * for a given instance of certificate.                                 *
 ************************************************************************/
function certificate_get_participants($certificateid) {
    global $CFG;

    //Get students
    $participants = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}certificate_issues a
                                 WHERE a.certificateid = '$certificateid' and
                                       u.id = a.userid");
    return $participants;
}

// NON-STANDARD FUNCTIONS ////////////////////////////////////////////////

/************************************************************************
 * Prints the header in view.  Used to help prevent FPDF header errors. *
 ************************************************************************/    
function view_header($course, $certificate, $cm) {
    global $CFG;
      
    $strcertificates = get_string('modulenameplural', 'certificate');
    $strcertificate  = get_string('modulename', 'certificate');      
    $navigation = build_navigation('', $cm);
    print_header_simple(format_string($certificate->name), '', $navigation, '', '', true, update_module_button($cm->id, $course->id, $strcertificate), navmenu($course, $cm));

    $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    if (has_capability('mod/certificate:manage', $context)) {
        $numusers = certificate_count_issues($certificate);
        echo "<div class=\"reportlink\"><a href=\"report.php?id=$cm->id\">".
              get_string('viewcertificateviews', 'certificate', $numusers)."</a></div>";
    }

    if (!empty($certificate->intro)) {
        print_box(format_text($certificate->intro), 'generalbox', 'intro');
    }
} 

/************************************************************************
 * Creates a directory file name, suitable for make_upload_directory()  *
 * @param $userid int The user id                                       *
 * @return string path to file area                                     *
 ************************************************************************/
function certificate_file_area_name($userid) {
    global $course, $certificate, $CFG;
    return $course->id.'/moddata/certificate/'.$certificate->id.'/'.$userid;
}

/************************************************************************
 * Makes an upload directory                                            *
 * @param $userid int The user id                                       *
 ************************************************************************/    
function certificate_file_area($userid) {
    return make_upload_directory(certificate_file_area_name($userid));
}

/************************************************************************
 * Function to be run periodically according to the moodle cron         *
 * This needs to be done                                                *
 ************************************************************************/    
function certificate_cron () {
    global $CFG;

    return true;
}

/************************************************************************
 * Return list of certificate issues that have not been mailed out      *
 * for currently enrolled students                                      *
 * @return array                                                        *
 ************************************************************************/  
function certificate_get_unmailed_certificates($course, $user) {

    global $CFG, $course;
    return get_records_sql("SELECT s.*, a.course, a.name
                              FROM {$CFG->prefix}certificate_issues s, 
                                   {$CFG->prefix}certificate a,
                                   {$CFG->prefix}user us
                             WHERE s.mailed = 0 
                               AND s.certificate = a.id
                               AND s.userid = us.userid
                               AND a.course = us.course");
}

/************************************************************************
 * Alerts teachers by email of received certificates. First checks      *
 * whether the option to email teachers is set for this certificate.    *
 * Sends an email to ALL teachers in the course.                        *
 ************************************************************************/    
function certificate_email_teachers($certificate) {
    global $course, $USER, $CFG;

    if ($certificate->emailteachers == 0) {          // No need to do anything
        return;
    }
    $certrecord = certificate_get_issue($course, $USER, $certificate->id);
    $student = $certrecord->studentname;
    $cm = get_coursemodule_from_instance("certificate", $certificate->id, $course->id);
    if (groupmode($course, $cm) == SEPARATEGROUPS) {   // Separate groups are being used
        if (!$group = user_group($course->id, $USER->id)) {             // Try to find a group
            $group->id = 0;                                             // Not in a group, never mind
        }
        $teachers = get_group_teachers($course->id, $group->id);        // Works even if not in group
    } else {
        $teachers = get_course_teachers($course->id);
    }

    if ($teachers) {

        $strcertificates = get_string('modulenameplural', 'certificate');
        $strcertificate  = get_string('modulename', 'certificate');
        $strawarded  = get_string('awarded', 'certificate');

        foreach ($teachers as $teacher) {
            unset($info);

            $info->student = $student;
            $info->course = format_string($course->fullname,true);     
            $info->certificate = format_string($certificate->name,true);
            $info->url = $CFG->wwwroot.'/mod/certificate/report.php?id='.$cm->id;
            $from = $student;
            $postsubject = $strawarded.': '.$info->student.' -> '.$certificate->name;
            $posttext = certificate_email_teachers_text($info);
            $posthtml = certificate_email_teachers_html($info);
            $posthtml = ($teacher->mailformat == 1) ? certificate_email_teachers_html($info) : '';

            @email_to_user($teacher, $from, $postsubject, $posttext, $posthtml);  // If it fails, oh well, too bad.
            set_field("certificate_issues","mailed","1","certificateid", $certificate->id, "userid", $USER->id);

        }
    }
}

/************************************************************************
 * Alerts others by email of received certificates. First checks        *
 * whether the option to email others is set for this certificate.      *
 * Uses the email_teachers info.                                        *
 * Code suggested by Eloy Lafuente                                      *
 ************************************************************************/    
function certificate_email_others ($certificate) {    
    global $course, $USER, $CFG;

    if ($certificate->emailothers) {          

       $certrecord = certificate_get_issue($course, $USER, $certificate->id);
       $student = $certrecord->studentname;
       $cm = get_coursemodule_from_instance("certificate", $certificate->id, $course->id);

       $others = explode(',', $certificate->emailothers);
        if ($others) {
            $strcertificates = get_string('modulenameplural', 'certificate');
            $strcertificate  = get_string('modulename', 'certificate');
            $strawarded  = get_string('awarded', 'certificate');
            foreach ($others as $other) {
                $other = trim($other);
                if (validate_email($other)) {
                    $destination->email = $other;
                    unset($info);
                    $info->student = $student;
                    $info->course = format_string($course->fullname,true);     
                    $info->certificate = format_string($certificate->name,true);
                    $info->url = $CFG->wwwroot.'/mod/certificate/report.php?id='.$cm->id;
                    $from = $student;
                    $postsubject = $strawarded.': '.$info->student.' -> '.$certificate->name;
                    $posttext = certificate_email_teachers_text($info);
                    $posthtml = certificate_email_teachers_html($info);
    
                    @email_to_user($destination, $from, $postsubject, $posttext, $posthtml);  // If it fails, oh well, too bad.
                    set_field("certificate_issues","mailed","1","certificateid", $certificate->id, "userid", $USER->id);
                }
            }
        }
    }
}

/************************************************************************
 * Creates the text content for emails to teachers -- needs to be finished with cron
 * @param $info object The info used by the 'emailteachermail' language string
 * @return string                                                       *
 ************************************************************************/    
function certificate_email_teachers_text($info) {
    $posttext = get_string('emailteachermail', 'certificate', $info)."\n";
    return $posttext;
}

/************************************************************************
 * Creates the html content for emails to teachers                      *
 * @param $info object The info used by the 'emailteachermailhtml' language string
 * @return string                                                       *
 ************************************************************************/    
function certificate_email_teachers_html($info) {
    global $CFG;

    $posthtml  = '<font face="sans-serif">';
    $posthtml .= '<p>'.get_string('emailteachermailhtml', 'certificate', $info).'</p>';
    $posthtml .= '</font>';
    return $posthtml;
}

/************************************************************************
 * Sends the student their issued certificate from moddata as an email  *
 * attachment.                                                          *
 ************************************************************************/   
function certificate_email_students($user) {
    global $course, $certificate, $CFG; 

    $certrecord = certificate_get_issue($course, $user);
    if ($certrecord->mailed > 0)    {
        return;
    }

    $teacher = get_teacher($course->id);
    $strawarded = get_string('awarded', 'certificate');
    $info->username = fullname($user);
    $info->certificate = format_string($certificate->name,true);
    $info->course = format_string($course->fullname,true);         
    $from = fullname($teacher);
    $subject = $info->course.': '.$info->certificate;
    $message = get_string('emailstudenttext', 'certificate', $info)."\n";

    // Make the HTML version more XHTML happy  (&amp;)
    $messagehtml = text_to_html(get_string('emailstudenttext', 'certificate', $info));
    $user->mailformat = 0;  // Always send HTML version as well
    $attachment= $course->id.'/moddata/certificate/'.$certificate->id.'/'.$user->id.'/'.$certificate->name.'.pdf';
    $attachname= $certificate->name.'.pdf';

    set_field("certificate_issues","mailed","1","certificateid", $certificate->id, "userid", $user->id);
    return email_to_user($user, $from, $subject, $message, $messagehtml, $attachment, $attachname);
}

/************************************************************************
 * Count certificates issued. Used for report link.                     *
 ************************************************************************/
function certificate_count_issues($certificate) {
    global $CFG;

}

/************************************************************************
 * Produces a list of links to the issued certificates.  Used for report.*
 * @param $userid int optional id of the user. If 0 then $USER->id is used.*
 * @param $return boolean optional defaults to false.                   *
 * @return string optional                                              *
 ************************************************************************/
function certificate_print_user_files($userid=0) {
    global $CFG, $USER;
    
    $filearea = certificate_file_area_name($userid);

    $output = '';

    if ($basedir = certificate_file_area($userid)) {
        if ($files = get_directory_list($basedir)) {
            require_once($CFG->libdir.'/filelib.php');
            foreach ($files as $file) {
                
                $icon = mimeinfo('icon', $file);
                
                if ($CFG->slasharguments) {
                    $ffurl = "$CFG->wwwroot/file.php/$filearea/$file";
                } else {
                    $ffurl = "$CFG->wwwroot/file.php?file=/$filearea/$file";
                }
            
                $output .= '<img align="middle" src="'.$CFG->pixpath.'/f/'.$icon.'" height="16" width="16" alt="'.$icon.'" />'.
                        '<a href="'.$ffurl.'" target="_blank">'.$file.'</a><br />';
            }
        }
    }

    $output = '<div class="files">'.$output.'</div>';

    return $output;
}

/************************************************************************
 * Returns user information about issued certificates -- used for index and review.*
 ************************************************************************/
function certificate_get_issue($course, $user) {
    global $certificate;
    if (record_exists("certificate_issues", "certificateid", $certificate->id, "userid", $user->id)) {
        $issue = get_record("certificate_issues", "certificateid", $certificate->id, "userid", $user->id);
    }
    return get_record("certificate_issues", "certificateid", $certificate->id, "userid", $user->id);
}

/************************************************************************
 * Returns a list of issued certificates - sorted for report.           *
 ************************************************************************/
function certificate_get_issues($certificate, $user, $sort="u.studentname ASC") {
    global $CFG;

   return get_records_sql("SELECT u.*,u.picture, s.code, s.timecreated, s.certdate, s.studentname, s.reportgrade
                              FROM {$CFG->prefix}certificate_issues s, 
                                   {$CFG->prefix}user u
                             WHERE s.certificateid = '$certificate' 
                               AND s.userid = u.id
                               AND s.certdate > 0
                             ORDER BY $sort");
}

/************************************************************************
 * Inserts preliminary user data when a certificate is viewed.          *
 * Prevents form from issuing a certificate upon browser refresh.       *
 ************************************************************************/
function certificate_prepare_issue($course, $user) {
    global $USER, $certificate;
    if (record_exists("certificate_issues", "certificateid", $certificate->id, "userid", $user->id)) {
    return;
} else 
    $timecreated = time();
    $code = certificate_generate_code();
    $studentname = certificate_generate_studentname($course, $user);
    $newrec = new Object();
    $newrec->certificateid = $certificate->id;
    $newrec->userid = $user->id;
    $newrec->timecreated = $timecreated;
    $newrec->studentname = addslashes($studentname);
    $newrec->code = addslashes($code);
    $newrec->classname = addslashes($course->fullname);
    
    insert_record("certificate_issues", $newrec, false);
}

/************************************************************************
 * Inserts final user data when a certificate is created.               *
 ************************************************************************/
function certificate_issue($course, $user) {
    global $USER, $certificate;
    $certrecord = certificate_get_issue($course, $USER, $certificate->id);

    if($certificate->printgrade > 0) {
   if($certificate->printgrade == 1) {
	$grade = certificate_print_course_grade($course->id);    
} else if($certificate->printgrade > 1) {
    $grade = certificate_print_mod_grade($course, $certificate->printgrade);
}
    if ($certificate->gradefmt == 1) {
	$certrecord->reportgrade = addslashes($grade->percentage);
}
    if ($certificate->gradefmt == 2) {          
	$certrecord->reportgrade = addslashes($grade->points);
}
    if($certificate->gradefmt == 3) {
	$certrecord->reportgrade = addslashes($grade->letter);
   }
}
    $date = certificate_generate_date($certificate, $course);
    $certrecord->certdate = $date;
    update_record('certificate_issues', addslashes_object($certrecord));
    certificate_email_teachers($certificate);
    certificate_email_others($certificate);
}

/************************************************************************
 * Used for course participation report (in case certificate is added). *
 ************************************************************************/
function certificate_get_view_actions() {
    return array('view','view all','view report');
}
function certificate_get_post_actions() {
    return array('received');
}

/************************************************************************
 * Get certificate types indexed and sorted by name for mod_form.       *
 * @return array The index is the name of the certificate type, the     *
 * value its name from the language string                              *
 ************************************************************************/
function certificate_types() {
    $types = array();
    $names = get_list_of_plugins('mod/certificate/type');
    foreach ($names as $name) {
        $types[$name] = get_string('type'.$name, 'certificate');
    }
    asort($types);
    return $types;
}

/************************************************************************
 * Get border images for mod_form.                                      *
 ************************************************************************/
function certificate_get_borders () {
    global $CFG;
/// load border files
    $my_path = "$CFG->dirroot/mod/certificate/pix/borders";
    $borderstyleoptions = array();
    if ($handle = opendir($my_path)) {
        while (false !== ($file = readdir($handle))) {
        if (strpos($file, '.png',1)||strpos($file, '.jpg',1) ) {
                $i = strpos($file, '.'); 
                if($i > 1) {
                /// Set the style name
                    $borderstyleoptions[$file] = substr($file, 0, $i);
               
                }
            }
        }
        closedir($handle);
    }

/// Sort borders
    ksort($borderstyleoptions);

/// Add default borders
    $borderstyleoptions['none'] = get_string('no');
    return $borderstyleoptions;
    }

/************************************************************************
 * Get seal images for mod_form.                                        *
 ************************************************************************/
function certificate_get_seals () {
    global $CFG;

    $my_path = "$CFG->dirroot/mod/certificate/pix/seals";
        $sealoptions = array();
        if ($handle = opendir($my_path)) {
        while (false !== ($file = readdir($handle))) {
        if (strpos($file, '.png',1)||strpos($file, '.jpg',1) ) {
                $i = strpos($file, '.');
                if($i > 1) {
                    $sealoptions[$file] = substr($file, 0, $i);
                }
            }
        }
        closedir($handle);
    }
        ksort($sealoptions);

    $sealoptions['none'] = get_string('no');
    return $sealoptions;
    }
	
/************************************************************************
 * Get watermark images for mod_form.                                   *
 ************************************************************************/
function certificate_get_watermarks () {
    global $CFG;
/// load watermark files
    $my_path = "$CFG->dirroot/mod/certificate/pix/watermarks";
    $wmarkoptions = array();
    if ($handle = opendir($my_path)) {
        while (false !== ($file = readdir($handle))) {
        if (strpos($file, '.png',1)||strpos($file, '.jpg',1) ) {
            $i = strpos($file, '.');
                if($i > 1) {
                    $wmarkoptions[$file] = substr($file, 0, $i);

                }
            }
        }
        closedir($handle);
    }

/// Order watermarks
    ksort($wmarkoptions);

    $wmarkoptions['none'] = get_string('no');
    return $wmarkoptions;
    
    }

/************************************************************************
 * Get signature images for mod_form.                                   *
 ************************************************************************/
function certificate_get_signatures () {
    global $CFG;

/// load signature files
    $my_path = "$CFG->dirroot/mod/certificate/pix/signatures";
    $signatureoptions = array();
    if ($handle = opendir($my_path)) {
        while (false !== ($file = readdir($handle))) {
            if (strpos($file, '.png',1)||strpos($file, '.jpg',1) ) {
                $i = strpos($file, '.');
                if($i > 1) {
                    $signatureoptions[$file] = substr($file, 0, $i);
                }
            }
        }
        closedir($handle);
    }
    ksort($signatureoptions);

    $signatureoptions['none'] = get_string('no');
    return $signatureoptions;
}

/************************************************************************
 * Search through all the modules for grade data for mod_form.          *
 ************************************************************************/
function certificate_get_mod_grades() {
    global $course, $CFG;

    $strgrade = get_string('grade', 'certificate');
    /// Collect modules data
    get_all_mods($course->id, $mods, $modnames, $modnamesplural, $modnamesused);

    $printgrade = array();
    $sections = get_all_sections($course->id); // Sort everything the same as the course
    for ($i=0; $i<=$course->numsections; $i++) {
        // should always be true
        if (isset($sections[$i])) {
            $section = $sections[$i];
            if ($section->sequence) {
                switch ($course->format) {
                    case "topics":
                    $sectionlabel = get_string("topic");
                    break;
                    case "weeks":
                    $sectionlabel = get_string("week");
                    break;
                    default:
                    $sectionlabel = get_string("section");
                }

                $sectionmods = explode(",", $section->sequence);
                foreach ($sectionmods as $sectionmod) {
                    if (empty($mods[$sectionmod])) {
                        continue;
                    }
                    $mod = $mods[$sectionmod];
                        $mod->courseid = $course->id;
                        $instance = get_record("$mod->modname", "id", "$mod->instance");
                        if ($grade_items = grade_get_grade_items_for_activity($mod)) {
							$mod_item = grade_get_grades($course->id, 'mod', $mod->modname, $mod->instance);
    $item = reset($mod_item->items);
        if(isset($item->grademax)){
                            $printgrade[$mod->id] = $sectionlabel.' '.$section->section.' : '.$instance->name.' '.$strgrade;
                        }
				    }
                }
            }
        }
    }
    if (isset($printgrade)) {
        $gradeoptions['0'] = get_string('no');
        $gradeoptions['1'] = get_string('coursegrade', 'certificate');
        foreach ($printgrade as $key => $value) {
            $gradeoptions[$key] = $value;
        }
    } else { 
        $gradeoptions['0'] = get_string('nogrades', 'certificate'); 
    }
    return ($gradeoptions);
}

/************************************************************************
 * Get the course outcomes for for mod_form print outcome.              *
 ************************************************************************/
function certificate_get_outcomes() {
    global $course, $CFG;

 // get all outcomes in course
$grade_seq = new grade_tree($course->id, false, true, '', false);
if ($grade_items = $grade_seq->items) {

// list of item for menu
$printoutcome = array();
foreach ($grade_items as $grade_item) {
    if(isset($grade_item->outcomeid)){
	$itemmodule = $grade_item->itemmodule;
    $printoutcome[$grade_item->id] = $itemmodule.': '.$grade_item->get_name();
}
        }
    }
    if (isset($printoutcome)) {
        $outcomeoptions['0'] = get_string('no');
        foreach ($printoutcome as $key => $value) {
            $outcomeoptions[$key] = $value;
        }
    } else { 
        $outcomeoptions['0'] = get_string('nooutcomes', 'certificate'); 
    }
    return ($outcomeoptions);
}

// FUNCTIONS NEEDED TO PRINT A CERTIFICATE////////////////////////////////

/************************************************************************
 * Prepare to print an activity grade.                                  *
 ************************************************************************/
function certificate_print_mod_grade($course, $moduleid) {
    global $USER, $CFG;
    $cm = get_record("course_modules", "id", $moduleid);
    $module = get_record("modules", "id", $cm->module);

    if ($grade_item = grade_get_grades($course->id, 'mod', $module->name, $cm->instance, $USER->id)) {
        $item = reset($grade_item->items);
        $modinfo->name = utf8_decode(get_field($module->name, 'name', 'id', $cm->instance));
    $grade = $item->grades[$USER->id]->grade;
    $item->gradetype = GRADE_TYPE_VALUE;
    $item->courseid = $course->id;

        $modinfo->points = grade_format_gradevalue($grade, $item, true, GRADE_DISPLAY_TYPE_REAL, $decimals=2);
		   
        $modinfo->percentage = grade_format_gradevalue($grade, $item, true, GRADE_DISPLAY_TYPE_PERCENTAGE, $decimals=2);

        $modinfo->letter = grade_format_gradevalue($grade, $item, true, GRADE_DISPLAY_TYPE_LETTER, $decimals=0);
		   
        return $modinfo;
    }
    return false;
}

/************************************************************************
 * Prepare to print an outcome.                                         * 
 ************************************************************************/
function certificate_print_outcome($course, $id) {
    global $USER, $CFG, $certificate;

$id = $certificate->printoutcome;
if ($grade_item = new grade_item(array('id'=>$id))) {
    $outcomeinfo->name = $grade_item->get_name();
    $outcome = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$USER->id));
    $outcomeinfo->grade = grade_format_gradevalue($outcome->finalgrade, $grade_item, true, GRADE_DISPLAY_TYPE_REAL);		   
   return $outcomeinfo;
    }
    return false;
}

/************************************************************************
 * Prepare to print the course grade.                                   * 
 ************************************************************************/
function certificate_print_course_grade($id){
    global $course, $USER;
    $course = get_record("course", "id", $id);
 if ($course_item = grade_item::fetch_course_item($course->id)) {

    $grade = new grade_grade(array('itemid'=>$course_item->id, 'userid'=>$USER->id));
    $course_item->gradetype = GRADE_TYPE_VALUE;

        $coursegrade->points = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_REAL, $decimals=2);
    
	    $coursegrade->percentage = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_PERCENTAGE, $decimals=2);
		
	    $coursegrade->letter = grade_format_gradevalue($grade->finalgrade, $course_item, true, GRADE_DISPLAY_TYPE_LETTER, $decimals=0);
	
    return $coursegrade;
    }
    return false;
}

/************************************************************************
* Sends text to output given the following params.                      *
* @param int $x horizontal position in pixels                           *
* @param int $y vertical position in pixels                             *
* @param char $align L=left, C=center, R=right                          *
* @param string $font any available font in font directory              *
* @param char $style ''=normal, B=bold, I=italic, U=underline           *
* @param int $size font size in points                                  *
* @param string $text the text to print                                 *
* @return null                                                          *
 ************************************************************************/
function cert_printtext( $x, $y, $align, $font, $style, $size, $text) {
    global $pdf;
    $pdf->setFont("$font", "$style", $size);
    $pdf->SetXY( $x, $y);
    $pdf->Cell( 500, 0, "$text", 0, 1, "$align");
}

/************************************************************************
 * Creates rectangles for line border.                                  *
 ************************************************************************/
function draw_frame($certificate, $orientation) {
    global $pdf, $certificate;

    if ($certificate->bordercolor != 'none') {

        switch ($orientation) {
            case 'L':
            
    // create outer line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 26, 30, 790, 530, 'F');
             //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 32, 36, 778, 518, 'F');
             
    // create middle line border in selected color
            if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 41, 45, 760, 500, 'F');
             //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 42, 46, 758, 498, 'F');
            
    // create inner line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 52, 56, 738, 478, 'F');
             //white rectangle
            $pdf->SetFillColor( 255, 255, 255);  
            $pdf->Rect( 56, 60, 730, 470, 'F');
            break;
            
            case 'P':
    // create outer line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 20, 20, 560, 800, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 26, 26, 548, 788, 'F');
            
    // create middle line border in selected color
            if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 35, 35, 530, 770, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 36, 36, 528, 768, 'F');
            
    // create inner line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 46, 46, 508, 748, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 50, 50, 500, 740, 'F');
            break;
        }
    }
}

/************************************************************************
 * Creates rectangles for line border for letter size paper.            *
 ************************************************************************/
function draw_frame_letter($certificate, $orientation) {
    global $pdf, $certificate;

    if ($certificate->bordercolor != 'none') {

        switch ($orientation) {
            case 'L':
    // create outer line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 26, 25, 741, 555, 'F'); 
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255); 
            $pdf->Rect( 32, 31, 729, 542, 'F');
             
    // create middle line border in selected color
            if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 41, 40, 711, 525, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 42, 41, 709, 523, 'F');
            
    // create inner line border in selected color
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0);
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51);
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204);
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0);
        }
            $pdf->Rect( 52, 51, 689, 503, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);  
            $pdf->Rect( 56, 55, 681, 495, 'F');
            break;
            
            case 'P':
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 25, 20, 561, 751, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 31, 26, 549, 739, 'F');
            
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 
            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 40, 35, 531, 721, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);
            $pdf->Rect( 41, 36, 529, 719, 'F');
            
        if ($certificate->bordercolor == 1)    {
            $pdf->SetFillColor( 0, 0, 0); //black
        } 
            if ($certificate->bordercolor == 2)    {
            $pdf->SetFillColor(153, 102, 51); //brown
        } 

            if ($certificate->bordercolor == 3)    {
            $pdf->SetFillColor( 0, 51, 204); //blue
        } 
            if ($certificate->bordercolor == 4)    {
            $pdf->SetFillColor( 0, 180, 0); //green
        }
            $pdf->Rect( 51, 46, 509, 699, 'F');
            //white rectangle
            $pdf->SetFillColor( 255, 255, 255);  
            $pdf->Rect( 55, 50, 501, 691, 'F');
            break;
        }
    }
}

/************************************************************************
 * Prints border images from the borders folder in PNG or JPG formats.  *
 ************************************************************************/
function print_border($border, $orientation) {
    global $CFG, $pdf;

    switch($border) {
        case '0':
        case '':
        case 'none':
        break;
        default:
        switch ($orientation) {
            case 'L':
        if(file_exists("$CFG->dirroot/mod/certificate/pix/borders/$border")) {
            $pdf->Image( "$CFG->dirroot/mod/certificate/pix/borders/$border", 10, 10, 820, 580);
        }
        break;
            case 'P':
        if(file_exists("$CFG->dirroot/mod/certificate/pix/borders/$border")) {
            $pdf->Image( "$CFG->dirroot/mod/certificate/pix/borders/$border", 10, 10, 580, 820);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prints border images for letter size paper.                          *
 ************************************************************************/
function print_border_letter($border, $orientation) {
    global $CFG, $pdf;

    switch($border) {
        case '0':
        case '':
        case 'none':
        break;
        default:
        switch ($orientation) {
            case 'L':
        if(file_exists("$CFG->dirroot/mod/certificate/pix/borders/$border")) {
            $pdf->Image( "$CFG->dirroot/mod/certificate/pix/borders/$border", 12, 10, 771, 594);
        }
        break;
            case 'P':
        if(file_exists("$CFG->dirroot/mod/certificate/pix/borders/$border")) {
            $pdf->Image( "$CFG->dirroot/mod/certificate/pix/borders/$border", 10, 10, 594, 771);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prints watermark images.                                             *
 ************************************************************************/
function print_watermark($wmark, $orientation) {
    global $CFG, $pdf;

    switch($wmark) {
        case '0':
        case '':
        case 'none':
        break;
        default:
        switch ($orientation) {
            case 'L':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/watermarks/$wmark")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/watermarks/$wmark", 122, 90, 600, 420);
            }
            break;
            case 'P':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/watermarks/$wmark")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/watermarks/$wmark", 78, 130, 450, 480);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prints watermark images for letter size paper.                       *
 ************************************************************************/
function print_watermark_letter($wmark, $orientation) {
    global $CFG, $pdf;

    switch($wmark) {
        case '0':
        case '':
        case 'none':
        break;
        default:
        switch ($orientation) {
            case 'L':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/watermarks/$wmark")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/watermarks/$wmark", 160, 110, 500, 400);
            }
            break;
            case 'P':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/watermarks/$wmark")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/watermarks/$wmark", 83, 130, 450, 480);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prints signature images or a line.                                   *
 ************************************************************************/
function print_signature($sig, $orientation, $x, $y, $w, $h) {
    global $CFG, $pdf;

    switch ($orientation) {
        case 'L':
        switch($sig) {
            case '0':
            case '':
            case 'none':
            break;
            default:
            if(file_exists("$CFG->dirroot/mod/certificate/pix/signatures/$sig")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/signatures/$sig", $x, $y, $w, $h);
            }
            break;
        }
        break;
        case 'P':
        switch($sig) {
            case '0':
            case '':
            case 'none':
            break;
            default:
            if(file_exists("$CFG->dirroot/mod/certificate/pix/signatures/$sig")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/signatures/$sig", $x, $y, $w, $h);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prints seal images.                                                  *
 ************************************************************************/
function print_seal($seal, $orientation, $x, $y, $w, $h) {
    global $CFG, $pdf;

    switch($seal) {
        case '0':
        case '':
        case 'none':
        break;
        default:
        switch ($orientation) {
            case 'L':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/seals/$seal")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/seals/$seal", $x, $y, $w, $h);
            }
            break;
            case 'P':
            if(file_exists("$CFG->dirroot/mod/certificate/pix/seals/$seal")) {
                $pdf->Image( "$CFG->dirroot/mod/certificate/pix/seals/$seal", $x, $y, $w, $h);
            }
            break;
        }
        break;
    }
}

/************************************************************************
 * Prepare to be print the date -- defaults to time.                    *
 ************************************************************************/
function certificate_generate_date($certificate, $course) {
    $timecreated = time();
    if($certificate->printdate == '0')    {
	$certdate = $timecreated;
    } 
        if ($certificate->printdate == '1') {
            $certdate = $timecreated;
        }
        if ($certificate->printdate == '2') {
            if ($course->enrolenddate) {
            $certdate = $course->enrolenddate;
        } else $certdate = $timecreated;
        }
return $certdate;
}

/************************************************************************
 * Generates the student name to be printed on the certificate.         *
 ************************************************************************/
function certificate_generate_studentname($course, $user) {
    $studentname = fullname($user);
    return $studentname;
}

/************************************************************************
 * Generates a 10-digit code of random letters and numbers.             *
 ************************************************************************/
function certificate_generate_code() {
    return (random_string(10));
}

/************************************************************************
 * Grade/Lock functions-functions for conditionally locking certificate.*
 * Mike Churchward                                                      *
 ************************************************************************/
function certificate_grade_condition() {
    global $certificate, $course;

    $restrict_errors = '';

    if (!certificate_is_available_time($certificate->id, $course->id)) {
        $restrict_errors[] = get_string('errorlocktime', 'certificate');
    }
    if (!certificate_is_available_mod($certificate->id, $course->id)) {
        $restrict_errors[] = get_string('errorlockmod', 'certificate');
    }
    if ($certificate->lockgrade == 1) {
        $coursegrade = certificate_print_course_grade($course->id);
        if ($certificate->requiredgrade > $coursegrade->points) {
            $a->current = $coursegrade->points;
            $a->needed = $certificate->requiredgrade;
            $restrict_errors[] = get_string('errorlockgradecourse', 'certificate', $a);
         }
    }

    return $restrict_errors;
}

function certificate_get_possible_linked_activities(&$course, $certid) {
    global $CFG;

    $lacts[0] = '-- none --';
    if (record_exists('modules', 'name', 'quiz')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'quiz a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'quiz\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Quiz: '.$name;
            }
        }
    }

    if (record_exists('modules', 'name', 'assignment')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'assignment a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'assignment\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Assignment: '.$name;
            }
        }
    }

    if (record_exists('modules', 'name', 'questionnaire')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'questionnaire a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'questionnaire\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Questionnaire: '.$name;
            }
        }
    }

    if (record_exists('modules', 'name', 'lesson')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'lesson a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'lesson\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Lesson: '.$name;
            }
        }
    }

    if (record_exists('modules', 'name', 'feedback')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'feedback a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'feedback\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Feedback: '.$name;
            }
        }
    }

    if (record_exists('modules', 'name', 'survey')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'survey a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'survey\' AND cm.module = m.id AND a.course = '.$course->id; 
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Survey: '.$name;
            }
        }
    }
    $sql = 'SELECT DISTINCT cm.id,a.name ' .
           'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'scorm a,'.
           $CFG->prefix.'modules m '.
           'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
           'm.name = \'scorm\' AND cm.module = m.id AND a.course = '.$course->id; 
    if ($mods = get_records_sql_menu($sql)) {
        foreach ($mods as $key => $name) {
            $lacts[$key] = 'Scorm: '.$name;
        }
    }

    if (record_exists('modules', 'name', 'facetoface')) {
        $sql = 'SELECT DISTINCT cm.id,a.name ' .
               'FROM '.$CFG->prefix.'course_modules cm,'.$CFG->prefix.'facetoface a,'.
               $CFG->prefix.'modules m '.
               'WHERE cm.course = '.$course->id.' AND cm.instance = a.id AND '.
               'm.name = \'facetoface\' AND cm.module = m.id AND a.course = '.$course->id;
        if ($mods = get_records_sql_menu($sql)) {
            foreach ($mods as $key => $name) {
                $lacts[$key] = 'Face-to-face: '.$name;
            }
        }
    }

    return $lacts;
}

function certificate_get_linked_activities($certid) {

    return get_records('certificate_linked_modules', 'certificate_id', $certid, 'id',
                       'linkid,id,certificate_id,linkgrade,timemodified');
}

function certificate_activity_completed(&$activity, &$cm, $userid=0) {
    global $CFG, $USER;
    static $quizid, $questid, $assid, $lessid, $feedid, $survid, $scormid, $facetofaceid;

    if (!$userid) {
        $userid = $USER->id;
    }

    if (empty($quizid)) {
        $quizid = get_field('modules', 'id', 'name', 'quiz');
        $questid = get_field('modules', 'id', 'name', 'questionnaire');
        $assid = get_field('modules', 'id', 'name', 'assignment');
        $lessid = get_field('modules', 'id', 'name', 'lesson');
        $feedid = get_field('modules', 'id', 'name', 'feedback');
        $survid = get_field('modules', 'id', 'name', 'survey');
        $scormid = get_field('modules', 'id', 'name', 'scorm');
        $facetofaceid = get_field('modules', 'id', 'name', 'facetoface');
    }

    /// If the module is not visible, it can't be accessed by students (assignment module
    /// will give us errors), so return true if its not visible.
    if (!empty($cm)) {
        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (!$cm->visible and !has_capability('moodle/course:viewhiddenactivities', $context)) {
            return true;
        }
        if ($cm->module == $quizid) {
            require_once($CFG->dirroot.'/mod/quiz/locallib.php');
            $quiz = get_record('quiz', 'id', $cm->instance);
            $score = quiz_get_best_grade($quiz, $userid);
            $grade = (int)(((float)$score / (float)$quiz->grade) * 100.0);
            return ($grade >= (int)$activity->linkgrade);
    
        } else if ($cm->module == $assid) {
            require_once($CFG->dirroot.'/mod/assignment/lib.php');
            $assignment = get_record('assignment', 'id', $cm->instance);
            require_once ("$CFG->dirroot/mod/assignment/type/$assignment->assignmenttype/assignment.class.php");
            $assignmentclass = "assignment_$assignment->assignmenttype";
            $assignmentinstance = new $assignmentclass($cm->id, $assignment, $cm);
            if (!($submission = $assignmentinstance->get_submission($userid))) {
                return false;
            } else if ($assignmentinstance->assignment->grade <= 0) {
                return true;
            } else { 
                $grade = (int)(((float)$submission->grade / (float)$assignmentinstance->assignment->grade) * 100.0);
                return ($grade >= (int)$activity->linkgrade);
            }
    
        } else if ($cm->module == $questid) {
            return (get_record('questionnaire_attempts', 'qid', $cm->instance, 'userid', $userid) !== false);
            
        } else if ($cm->module == $feedid) {
            return (get_record('feedback_completed', 'id', $cm->instance, 'userid', $userid) !== false);
    
        } else if ($cm->module == $survid) {
            return (get_record('survey_answers', 'id', $cm->instance, 'userid', $userid) !== false);
    
        } else if ($cm->module == $scormid) {
            require_once($CFG->dirroot.'/mod/scorm/locallib.php');
            $scorm = get_record('scorm', 'id', $cm->instance);
            $score = scorm_grade_user($scorm, $userid);
            if (($scorm->grademethod % 10) == 0) { // GRADESCOES
                if (!$scorm->maxgrade = count_records_select('scorm_scoes',"scorm='$scormid' AND " . sql_isnotempty('scorm_scoes', 'launch', false, true))) { 
                    return NULL;
                }
            } else {
                $return->maxgrade = $scorm->maxgrade;
                $grade = (int)(((float)$score / (float)$scorm->maxgrade) * 100.0);
                return ($grade >= (int)$activity->linkgrade);
            }

        } else if ($cm->module == $lessid) {
            require_once($CFG->dirroot.'/mod/lesson/locallib.php');
            if (!($lesson = get_record('lesson', 'id', $cm->instance))) {
                return true;
            } else {
                $ntries = count_records("lesson_grades", "lessonid", $lesson->id, "userid", $userid) - 1;
                $gradeinfo = lesson_grade($lesson, $ntries);
                return ($gradeinfo->grade >= (int)$activity->linkgrade);
    
            }

        } else if ($cm->module == $facetofaceid) {

            require_once($CFG->libdir.'/gradelib.php');
            $grading_info = grade_get_grades($cm->course, 'mod', 'facetoface', $cm->instance, $userid);
            if (empty($grading_info)) {
                return false;
            }
            $grade = $grading_info->items[0]->grades[$userid]->grade;
            return ($grade >= (int)$activity->linkgrade);

        } else {
            return true;
        }
    } else {
        return true;
    }
}

function certificate_is_available_time($certid, $courseid, $userid=0) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    if ($linked_acts = certificate_get_linked_activities($certid)) {
        $message = '';
        foreach ($linked_acts as $key => $activity) {
            if ($activity->linkid == CERTCOURSETIMEID) {
                require_once('timinglib.php');
                if (($activity->linkgrade != 0) &&
                    ((tl_get_course_time($courseid, $userid)/60) < $activity->linkgrade)) {
                    return false;
                }
            }
        }
    }
    return true;
}
function certificate_is_available_mod($certid, $courseid, $userid=0) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    if ($linked_acts = certificate_get_linked_activities($certid)) {
        $message = '';
        foreach ($linked_acts as $key => $activity) {
                $cm = get_record('course_modules', 'id', $activity->linkid);
                if (!certificate_activity_completed($activity, $cm, $userid)) {
                    return false;
                }
            }
        }
    return true;
}

/************************************************************************
 * Upgrade functions - functions basically used only once...
 ************************************************************************/

/**
 * Upgrade any grade locks to the new system.
 * 
 */
function certificate_upgrade_grading_info() {
    global $CFG;

    $status = true;
    $select = 'lockgrade > 0';
    if ($records = get_records_select('certificate', $select)) {
        foreach ($records as $record) {
            if ($record->lockgrade == 1) {
            /// Course grade. Leave as is.
            } else {
            /// Activity grade. Create a new activity link.
                $newrec = new Object();
                $newrec->certificate_id = $record->id;
                $newrec->linkid = $record->lockgrade;
                $newrec->linkgrade = $record->requiredgrade;
                $newrec->timemodified = $record->timemodified;
                if (insert_record('certificate_linked_modules', $newrec)) {
                    set_field('certificate', 'lockgrade', 0, 'id', $record->id);
                    set_field('certificate', 'requiredgrade', 0, 'id', $record->id);
                } else {
                    $status = false;
                }
            }
        }
    }
    return $status;
}
?>