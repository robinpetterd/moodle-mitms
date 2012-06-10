<?php
require_once('../../../config.php');
require_once($CFG->libdir . '/completionlib.php');

define('COMPLETION_REPORT_PAGE', 25);

// Get course
$course = get_record('course', 'id', required_param('course', PARAM_INT));
if(!$course) {
    print_error('invalidcourseid');
}

// Sort (default lastname, optionally firstname)
$sort = optional_param('sort','',PARAM_ALPHA);
$firstnamesort = $sort == 'firstname';

// CSV format
$format = optional_param('format','',PARAM_ALPHA);
$excel = $format == 'excelcsv';
$csv = $format == 'csv' || $excel;

// Paging
$start   = optional_param('start', 0, PARAM_INT);
$sifirst = optional_param('sifirst', 'all', PARAM_ALPHA);
$silast  = optional_param('silast', 'all', PARAM_ALPHA);
$start = optional_param('start',0,PARAM_INT);

// Whether to show idnumber
// TODO: This should really not be using a config option 'intended' for
// gradebook, but that option is also used in quiz reports as well. There ought
// to be a generic option somewhere.
$idnumbers = $CFG->grade_report_showuseridnumber;

function csv_quote($value) {
    global $excel;
    if($excel) {
        $tl=textlib_get_instance();
        return $tl->convert('"'.str_replace('"',"'",$value).'"','UTF-8','UTF-16LE');
    } else {
        return '"'.str_replace('"',"'",$value).'"';
    }
}

require_login($course);

// Check basic permission
$context=get_context_instance(CONTEXT_COURSE,$course->id);
require_capability('coursereport/progress:view',$context);

// Get group mode
$group=groups_get_course_group($course,true); // Supposed to verify group
if($group===0 && $course->groupmode==SEPARATEGROUPS) {
    require_capability('moodle/site:accessallgroups',$context);
}

// Get data on activities, criteria and progress of all users, and give error if we've
// nothing to display (no users, no activities or no criteria)
$reportsurl = $CFG->wwwroot.'/course/report.php?id='.$course->id;
$completion = new completion_info($course);
$activities = $completion->get_activities();

if(empty($activities)) {
    print_error('err_noactivities','completion',$reportsurl);
}

// Generate where clause
$where = array();
$ilike = sql_ilike();

if ($sifirst !== 'all') {
    $where[] = "u.firstname $ilike '$sifirst%'";
}

if ($silast !== 'all') {
    $where[] = "u.lastname $ilike '$silast%'";
}

// Get user match count
$total = $completion->get_num_tracked_users(implode(' AND ', $where), $group);

// Total user count
$grandtotal = $completion->get_num_tracked_users('', $group);

// If no users in this course what-so-ever
if (!$grandtotal) {
    print_box_start('errorbox errorboxcontent boxaligncenter boxwidthnormal');
    print '<p class="nousers">'.get_string('err_nousers','completion').'</p>';
    print '<p><a href="'.$CFG->wwwroot.'/course/report.php?id='.$course->id.'">'.get_string('continue').'</a></p>';
    print_box_end();
    print_footer($course);
    exit;
}

// Get user data
$progress = array();

if ($total) {
    $progress = $completion->get_progress_all(
        implode(' AND ', $where),
        $group,
        $firstnamesort ? 'u.firstname ASC' : 'u.lastname ASC',
        $csv ? 0 : COMPLETION_REPORT_PAGE,
        $csv ? 0 : $start
    );
}

if($csv) {
    header('Content-Disposition: attachment; filename=progress.'.
        preg_replace('/[^a-z0-9-]/','_',strtolower($course->shortname)).'.csv');
    // Unicode byte-order mark for Excel
    if($excel) {
        header('Content-Type: text/csv; charset=UTF-16LE');
        print chr(0xFF).chr(0xFE);
        $sep="\t".chr(0);
        $line="\n".chr(0);
    } else {
        header('Content-Type: text/csv; charset=UTF-8');
        $sep=",";
        $line="\n";
    }
} else {
    // Navigation and header
    $strreports = get_string("reports");
    $strcompletion = get_string('completionreport','completion');
    $navlinks = array();
    $navlinks[] = array('name' => $strreports, 'link' => "../../report.php?id=$course->id", 'type' => 'misc');
    $navlinks[] = array('name' => $strcompletion, 'link' => null, 'type' => 'misc');
    print_header($strcompletion,$course->fullname,build_navigation($navlinks));

    // Handle groups (if enabled)
    groups_print_course_menu($course,$CFG->wwwroot.'/course/report/progress/?course='.$course->id);
}

// Build link for paging
$link = $CFG->wwwroot.'/course/report/progress/?course='.$course->id;
if (strlen($sort)) {
    $link .= '&amp;sort='.$sort;
}
$link .= '&amp;start=';

// Build the the page by Initial bar
$initials = array('first', 'last');
$alphabet = explode(',', get_string('alphabet'));

$pagingbar = '';
foreach ($initials as $initial) {
    $var = 'si'.$initial;

    $pagingbar .= ' <div class="initialbar '.$initial.'initial">';
    $pagingbar .= get_string($initial.'name').':&nbsp;';

    if ($$var == 'all') {
        $pagingbar .= '<strong>'.get_string('all').'</strong> ';
    }
    else {
        $pagingbar .= '<a href="'.$link.'">'.get_string('all').'</a> ';
    }

    foreach ($alphabet as $letter) {
        if ($$var === $letter) {
            $pagingbar .= '<strong>'.$letter.'</strong> ';
        }
        else {
            $pagingbar .= '<a href="'.$link.'&amp;'.$var.'='.$letter.'">'.$letter.'</a> ';
        }
    }

    $pagingbar .= '</div>';
}

// Do we need a paging bar?
if($total > COMPLETION_REPORT_PAGE) {

    // Paging bar
    $pagingbar .= '<div class="paging">';
    $pagingbar .= get_string('page').': ';

    // Display previous link
    if ($start > 0) {
        $pstart = max($start - COMPLETION_REPORT_PAGE, 0);
        $pagingbar .= '(<a class="previous" href="'.$link.$pstart.'">'.get_string('previous').'</a>)&nbsp;';
    }

    // Create page links
    $curstart = 0;
    $curpage = 0;
    while ($curstart < $total) {
        $curpage++;

        if ($curstart == $start) {
            $pagingbar .= '&nbsp;'.$curpage.'&nbsp;';
        }
        else {
            $pagingbar .= '&nbsp;<a href="'.$link.$curstart.'">'.$curpage.'</a>&nbsp;';
        }

        $curstart += COMPLETION_REPORT_PAGE;
    }

    // Display next link
    $nstart = $start + COMPLETION_REPORT_PAGE;
    if ($nstart < $total) {
        $pagingbar .= '&nbsp;(<a class="next" href="'.$link.$nstart.'">'.get_string('next').'</a>)';
    }

    $pagingbar .= '</div>';
}

// Okay, let's draw the table of progress info,

// Start of table
if(!$csv) {
    print '<br class="clearer"/>'; // ugh

    $total_header = ($total == $grandtotal) ? $total : "{$total}/{$grandtotal}";
    print_heading(get_string('allparticipants').": {$total_header}", '', 3);

    print $pagingbar;

    if (!$total) {
        print_heading(get_string('nothingtodisplay'));
        print_footer($course);
        exit;
    }

    print '<table id="completion-progress" class="generaltable flexible boxaligncenter" style="text-align:left"><tr style="vertical-align:top">';

    // User heading / sort option
    print '<th scope="col" class="completion-sortchoice">';
    if($firstnamesort) {
        print
            get_string('firstname').' / <a href="./?course='.$course->id.'">'.
            get_string('lastname').'</a>';
    } else {
        print '<a href="./?course='.$course->id.'&amp;sort=firstname">'.
            get_string('firstname').'</a> / '.
            get_string('lastname');
    }
    print '</th>';

    if($idnumbers) {
        print '<th>'.get_string('idnumber').'</th>';
    }

} else {
    if($idnumbers) {
        print $sep;
    }
}

// Activities
foreach($activities as $activity) {
    $activity->datepassed = $activity->completionexpected && $activity->completionexpected <= time();
    $activity->datepassedclass=$activity->datepassed ? 'completion-expired' : '';

    if($activity->completionexpected) {
        $datetext=userdate($activity->completionexpected,get_string('strftimedate','langconfig'));
    } else {
        $datetext='';
    }

    // Some names (labels) come URL-encoded and can be very long, so shorten them
    $activity->name=shorten_text(urldecode($activity->name));

    if($csv) {
        print $sep.csv_quote(strip_tags($activity->name)).$sep.csv_quote($datetext);
    } else {
        print '<th scope="col" class="'.$activity->datepassedclass.'">'.
            '<a href="'.$CFG->wwwroot.'/mod/'.$activity->modname.
            '/view.php?id='.$activity->id.'">'.
            '<img src="'.$CFG->modpixpath.'/'.$activity->modname.'/icon.gif" class="icon" alt=\"'.
            get_string('modulename',$activity->modname).'" /> <span class="completion-activityname">'.
            format_string($activity->name).'</span></a>';
        if($activity->completionexpected) {
            print '<div class="completion-expected"><span>'.$datetext.'</span></div>';
        }
        print '</th>';
    }
}

if($csv) {
    print $line;
} else {
    print '</tr>';
}

// Row for each user
foreach($progress as $user) {
    // User name
    if($csv) {
        print csv_quote(fullname($user));
        if($idnumbers) {
            print $sep.csv_quote($user->idnumber);
        }
    } else {
        print '<tr><th scope="row"><a href="'.$CFG->wwwroot.'/user/view.php?id='.
            $user->id.'&amp;course='.$course->id.'">'.fullname($user).'</a></th>';
        if($idnumbers) {
            print '<td>'.htmlspecialchars($user->idnumber).'</td>';
        }
    }

    // Progress for each activity
    foreach($activities as $activity) {

        // Get progress information and state
        if(array_key_exists($activity->id,$user->progress)) {
            $thisprogress=$user->progress[$activity->id];
            $state=$thisprogress->completionstate;
            $date=userdate($thisprogress->timemodified);
        } else {
            $state=COMPLETION_INCOMPLETE;
            $date='';
        }

        // Work out how it corresponds to an icon
        switch($state) {
            case COMPLETION_INCOMPLETE : $completiontype='n'; break;
            case COMPLETION_COMPLETE : $completiontype='y'; break;
            case COMPLETION_COMPLETE_PASS : $completiontype='pass'; break;
            case COMPLETION_COMPLETE_FAIL : $completiontype='fail'; break;
        }

        $completionicon='completion-'.
            ($activity->completion==COMPLETION_TRACKING_AUTOMATIC ? 'auto' : 'manual').
            '-'.$completiontype;

        $describe=get_string('completion-alt-auto-'.$completiontype,'completion');
        $a=new StdClass;
        $a->state=$describe;
        $a->date=$date;
        $a->user=fullname($user);
        $a->activity=strip_tags($activity->name);
        $fulldescribe=get_string('progress-title','completion',$a);

        if($csv) {
            print $sep.csv_quote($describe).$sep.csv_quote($date);
        } else {
            print '<td class="completion-progresscell '.$activity->datepassedclass.'"'.
                '<img src="'.$CFG->pixpath.'/i/'.$completionicon.'.gif'.
                '" alt="'.$describe.'" title="'.$fulldescribe.'" /></td>';
        }
    }

    if($csv) {
        print $line;
    } else {
        print '</tr>';
    }
}

if($csv) {
    exit;
}
print '</table>';
print $pagingbar;

print '<ul class="progress-actions"><li><a href="index.php?course='.$course->id.
    '&amp;format=csv">'.get_string('csvdownload','completion').'</a></li>
    <li><a href="index.php?course='.$course->id.'&amp;format=excelcsv">'.
    get_string('excelcsvdownload','completion').'</a></li></ul>';

print_footer($course);
