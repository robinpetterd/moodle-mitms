<?php

    require_once '../../config.php';

?>
// Bind functionality to page on load
$(function() {

    ///
    /// Position dialog
    ///
    (function() {
        var url = '<?php echo $CFG->wwwroot ?>/hierarchy/type/position/assign/';

        mitmsSingleSelectDialog(
            'position',
            url+'find.php?user='+user_id,
            'positionid',
            'positiontitle'
        );
    })();


    ///
    /// Organisation dialog
    ///
    (function() {
        var url = '<?php echo $CFG->wwwroot ?>/hierarchy/type/organisation/assign/';

        mitmsSingleSelectDialog(
            'organisation',
            url+'find.php?user='+user_id,
            'organisationid',
            'organisationtitle'
        );
    })();


    ///
    /// Manager dialog
    ///
    (function() {
        var url = '<?php echo $CFG->wwwroot ?>/hierarchy/type/position/assign/';

        mitmsSingleSelectDialog(
            'manager',
            url+'manager.php?user='+user_id,
            'managerid',
            'managertitle'
        );
    })();


    ///
    /// Competency dialog
    ///
    (function() {
        var url = '<?php echo $CFG->wwwroot ?>/hierarchy/type/competency/assign/';

        mitmsSingleSelectDialog(
            'competency',
            url+'find.php?user='+user_id,
            'competencyid',
            'competencytitle',
            function() {
                var jsonurl = '<?php echo $CFG->wwwroot ?>/hierarchy/type/competency/evidence/competency_scale.json.php';
                compid = $('input[name=competencyid]').val();

                var profinput = $('body.hierarchy-type-competency-evidence select#id_proficiency');
                // only do JSON request if a proficiency select found to fill
                if(profinput) {
                    // used by add competency evidence page to populate proficiency pulldown based on competency chosen
                    $.getJSON(jsonurl, {competencyid:compid}, function(scales) {
                        var i, htmlstr = '';
                        for (i in scales) {
                            htmlstr += '<option value="'+scales[i].name+'">'+scales[i].value+'</option>';
                        }
                        profinput.removeAttr('disabled').html(htmlstr);
                    });
                }
            }
        );
    })();



});
