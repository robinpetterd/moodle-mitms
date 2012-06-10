<?php

    require_once('../../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->dirroot.'/hierarchy/lib.php');

    ///
    /// Setup / loading data
    ///

    $sitecontext = get_context_instance(CONTEXT_SYSTEM);

    // Get params
    $type        = optional_param('type', -1, PARAM_SAFEDIR);
    $edit        = optional_param('edit', -1, PARAM_BOOL);
    $hide        = optional_param('hide', 0, PARAM_INT);
    $show        = optional_param('show', 0, PARAM_INT);
    $moveup      = optional_param('moveup', 0, PARAM_INT);
    $movedown    = optional_param('movedown', 0, PARAM_INT);

    if (file_exists($CFG->dirroot.'/hierarchy/type/'.$type.'/lib.php')) {
        require_once($CFG->dirroot.'/hierarchy/type/'.$type.'/lib.php');
        $hierarchy = new $type();
    } else {
        error('error:hierarchytypenotfound', 'hierarchy', $type);
    }

    // Cache user capabilities
    $can_add = has_capability('moodle/local:create'.$type.'frameworks', $sitecontext);
    $can_edit = has_capability('moodle/local:update'.$type.'frameworks', $sitecontext);
    $can_delete = has_capability('moodle/local:delete'.$type.'frameworks', $sitecontext);

    if ($can_add || $can_edit || $can_delete) {
        $navbaritem = $hierarchy->get_editing_button($edit);
        $editingon = !empty($USER->{$type.'editing'});
    } else {
        $navbaritem = '';
        $editingon = false;
    }

    // Setup page and check permissions
    admin_externalpage_setup($type.'frameworkmanage', $navbaritem, array('type'=>$type));

    ///
    /// Process any actions
    ///

    if ($editingon) {
        // Hide or show a framework
        if ($hide or $show or $moveup or $movedown) {
            require_capability('moodle/local:update'.$type.'frameworks', $sitecontext);
            // Hide an item
            if ($hide) {
                $hierarchy->hide_framework($hide);
            } elseif ($show) {
                $hierarchy->show_framework($show);
            } elseif ($moveup) {
                $hierarchy->move_framework($moveup, true);
            } elseif ($movedown) {
                $hierarchy->move_framework($movedown, false);
            }
        }

    } // End of editing stuff

///
/// Load hierarchy frameworks after any changes
///

// Get frameworks for this page
$frameworks = $hierarchy->get_frameworks(array('depth_count'=>1, 'custom_field_count'=>1, 'item_count'=>1));

///
/// Generate / display page
///
$str_edit     = get_string('edit');
$str_delete   = get_string('delete');
$str_moveup   = get_string('moveup');
$str_movedown = get_string('movedown');
$str_hide     = get_string('hide');
$str_show     = get_string('show');

if ($frameworks) {

    // Create display table
    $table = new stdclass();
    $table->class = 'generaltable edit'.$type;

    // Setup column headers
    $table->head = array(get_string('name', $type), get_string($type.'plural', $type), get_string('depths', $type),
        get_string("{$type}customfields", $type));

    // Add edit column
    if ($editingon && $can_edit) {
        $table->head[] = get_string('edit');
    }

    // Add rows to table
    $rowcount = 1;
    foreach ($frameworks as $framework) {
        $row = array();

        $cssclass = !$framework->visible ? 'class="dimmed"' : '';

        $row[] = "<a $cssclass href=\"{$CFG->wwwroot}/hierarchy/framework/view.php?type={$type}&frameworkid={$framework->id}\">{$framework->fullname}</a>";
        $row[] = $framework->item_count;
        $row[] = $framework->depth_count;
        $row[] = $framework->custom_field_count;

        // Add edit link
        $buttons = array();
        if ($editingon && $can_edit) {
            $buttons[] = "<a href=\"{$CFG->wwwroot}/hierarchy/framework/edit.php?type={$type}&id={$framework->id}\" title=\"$str_edit\">".
                "<img src=\"{$CFG->pixpath}/t/edit.gif\" class=\"iconsmall\" alt=\"$str_edit\" /></a>";
            if ($framework->visible) {
                $buttons[] = "<a href=\"{$CFG->wwwroot}/hierarchy/framework/index.php?type={$type}&hide={$framework->id}\" title=\"$str_hide\">".
                    "<img src=\"{$CFG->pixpath}/t/hide.gif\" class=\"iconsmall\" alt=\"$str_hide\" /></a>";
            } else {
                $buttons[] = "<a href=\"{$CFG->wwwroot}/hierarchy/framework/index.php?type={$type}&show={$framework->id}\" title=\"$str_show\">".
                    "<img src=\"{$CFG->pixpath}/t/show.gif\" class=\"iconsmall\" alt=\"$str_show\" /></a>";
            }
        }
        if ($editingon && $can_delete) {
            $buttons[] = "<a href=\"{$CFG->wwwroot}/hierarchy/framework/delete.php?type={$type}&id={$framework->id}\" title=\"$str_delete\">".
                "<img src=\"{$CFG->pixpath}/t/delete.gif\" class=\"iconsmall\" alt=\"$str_delete\" /></a>";
        }
        if ($editingon && $can_edit) {
            if ($rowcount != 1) {
                $buttons[] = "<a href=\"index.php?type={$type}&moveup={$framework->id}\" title=\"$str_moveup\">".
                   "<img src=\"{$CFG->pixpath}/t/up.gif\" class=\"iconsmall\" alt=\"$str_moveup\" /></a> ";
            } else {
                $buttons[] = "<img src=\"{$CFG->pixpath}/spacer.gif\"  class=\"iconsmall\"  alt=\"\" /> ";
            }
            if ($rowcount != count($frameworks)) {
                $buttons[] = "<a href=\"index.php?type={$type}&movedown={$framework->id}\" title=\"$str_movedown\">".
                    "<img src=\"{$CFG->pixpath}/t/down.gif\" class=\"iconsmall\" alt=\"$str_movedown\" /></a>";
            } else {
                $buttons[] = "<img src=\"{$CFG->pixpath}/spacer.gif\"  class=\"iconsmall\"  alt=\"\" /> ";
            }
            $rowcount++;
        }

        if ($buttons) {
            $row[] = implode($buttons, ' ');
        }

        $table->data[] = $row;
    }
}

// Display page

$navlinks = array();    // Breadcrumbs
$navlinks[] = array('name'=>get_string("{$type}frameworks", $type), 'link'=>'', 'type'=>'misc');

admin_externalpage_print_header('', $navlinks);

print_heading(get_string('frameworks', $type));

$plural = get_string($type.'plural', $type);
$name = 'Configure ' . $plural;
if($guide = get_record('block_guides_guide', 'name', $name)) {
    echo '<p><a href="'. $CFG->wwwroot . '/guides/view.php?startguide=' .
        $guide->id . '">Step-by-step guide to configuring ' . $plural . '</a></p>';
}
if($type == 'competency') {
    echo '<p>' . mitms_captivate_popup('Video help with competency frameworks', 'setting up a competency framework') . '</p>';
} else {
    echo '<p>' . mitms_captivate_popup('Video help with '.$plural, 'setting up ' . strtolower($plural)) . '</p>';
}

if ($frameworks) {
    print_table($table);
} else {
    echo '<p>'.get_string('noframeworks', $type).'</p><br>';
}


// Editing buttons
if ($can_add) {
    echo '<div class="buttons">';

    // Print button for creating new framework
    print_single_button($CFG->wwwroot.'/hierarchy/framework/edit.php?type='.$type, array('type'=>$type), get_string('addnewframework', $type), 'get');

    echo '</div>';
}

// Display scales
if (file_exists($CFG->dirroot.'/hierarchy/type/'.$type.'/scale/lib.php')) {
    include($CFG->dirroot.'/hierarchy/type/'.$type.'/scale/lib.php');
    $scales = $hierarchy->get_scales();
    call_user_func("{$type}_scale_display_table", $scales, $editingon);
} 

print_footer();
