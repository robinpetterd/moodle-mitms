<?php

require_once('../../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/hierarchy/type/competency/lib.php');
require_once($CFG->dirroot.'/local/js/lib/setup.php');
require_once($CFG->dirroot.'/idp/lib.php');


///
/// Setup / loading data
///

// Revision id
$revisionid = required_param('id', PARAM_INT);

// Parent id
$parentid = optional_param('parentid', 0, PARAM_INT);

// Framework id
$frameworkid = optional_param('frameworkid', 0, PARAM_INT);

// Only return generated tree html
$treeonly = optional_param('treeonly', false, PARAM_BOOL);

// No javascript parameters
$nojs = optional_param('nojs', false, PARAM_BOOL);
$returnurl = optional_param('returnurl', '', PARAM_TEXT);
$s = optional_param('s', '', PARAM_TEXT);

// string of params needed in non-js url strings
$urlparams = 'id='.$revisionid.'&amp;frameworkid='.$frameworkid.'&amp;nojs='.$nojs.'&amp;returnurl='.urlencode($returnurl).'&amp;s='.$s;

///
/// Permissions checks
///

admin_externalpage_setup('competencymanage');

// Check permissions
$sitecontext = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/local:idpaddcompetency', $sitecontext);

// Load plan this revision relates to
if (!$plan = get_plan_for_revision($revisionid)) {
    error('Revision plan could not be found');
}

// Setup hierarchy object
$hierarchy = new competency();

// Load framework
if (!$framework = $hierarchy->get_framework($frameworkid, true)) {
    $competencies = array();
    $currentlyassigned = array();
} else {

    // Load competencies to display
    $competencies = $hierarchy->get_items_by_parent($parentid, $revisionid);
    if (!$currentlyassigned = idp_get_user_competencies($plan->userid, $revisionid)) {
        $currentlyassigned = array();
    }
}

///
/// Display page
///

if(!$nojs) {
    if ($treeonly) {
        echo build_treeview(
            $competencies,
            get_string(($framework?'nocompetenciesinframework':'nocompetency'), 'competency'),
            $hierarchy,
            $currentlyassigned
        );
        exit;
    }

    // build Javascript Treeview

    // If parent id is not supplied, we must be displaying the main page
    if (!$parentid) {

        echo '<div class="selectcompetencies">'.PHP_EOL;
        echo '<h2>' . get_string('addcompetenciestoplan', 'idp') . '</h2>'.PHP_EOL;
        echo '<div class="selected">';
        echo '<p>' . get_string('selecteditems', 'hierarchy').'</p>'.PHP_EOL;
        echo populate_selected_items_pane($currentlyassigned);
        echo '</div>'.PHP_EOL;
        echo '<p>' . get_string('locatecompetency', $hierarchy->prefix).':'.'</p>'.PHP_EOL;
        $hierarchy->display_framework_selector('', true);
        echo '<ul class="treeview filetree">'.PHP_EOL;
    }

    echo build_treeview(
        $competencies,
        get_string(($framework?'nocompetenciesinframework':'nocompetency'), 'competency'),
        $hierarchy,
        $currentlyassigned
    );

    // If no parent id, close div
    if (!$parentid) {
        echo '</ul></div>';
    }
} else {
    // non JS version of page
    admin_externalpage_print_header();
    echo '<h2>'.get_string('addcompetenciestoplan', 'idp').'</h2>';
    echo '<p><a href="'.$returnurl.'">'.get_string('cancelwithoutassigning','hierarchy').'</a></p>';

    if ($framework && (empty($frameworkid) || $frameworkid == 0)) {

        echo build_nojs_frameworkpicker(
            $hierarchy,
            $CFG->wwwroot.'/hierarchy/type/competency/idp/find.php',
            array(
                'returnurl' => $returnurl,
                's' => $s,
                'nojs' => 1,
                'id' => $revisionid,
            )
        );

    } else {

        echo '<div id="nojsinstructions">';

        // If there's no frameworks defined, don't show the breadcrumbs because they'll confuse the user
        if ( $framework ){
            echo build_nojs_breadcrumbs($hierarchy,
                $parentid,
                $CFG->wwwroot.'/hierarchy/type/competency/idp/find.php',
                array(
                    'id' => $revisionid,
                    'returnurl' => $returnurl,
                    's' => $s,
                    'nojs' => $nojs,
                    'frameworkid' => $frameworkid,
                )
            );
        }

        echo '<p>';
        echo  get_string('clicktoassign', $hierarchy->prefix).' '.
                get_string('clicktoviewchildren', $hierarchy->prefix);
        echo '</p>';
        echo '</div>';
        echo '<div class="nojsselect">';
        echo build_nojs_treeview(
            $competencies,
            get_string(($framework?'nocompetenciesinframework':'nocompetency'), 'competency'),
            $CFG->wwwroot.'/hierarchy/type/competency/idp/save.php',
            array(
                'rowcount' => 0,
                's' => $s,
                'returnurl' => $returnurl,
                'nojs' => 1,
                'frameworkid' => $frameworkid,
                'id' => $revisionid,
            ),
            $CFG->wwwroot.'/hierarchy/type/competency/idp/find.php?'.$urlparams,
            $hierarchy->get_all_parents(),
            $currentlyassigned
        );

        echo '</div>';
    }
    print_footer();
}

