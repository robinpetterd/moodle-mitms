<?php

    require_once '../../config.php';

?>

// Bind functionality to page on load
$(function() {

    var handler = new mitmsDialog_handler_addcompetency();

    mitmsDialogs['addcompetency'] = new mitmsDialog(
        'addcompetency',
        'show-add-dialog',
        {},
        '<?php echo $CFG->wwwroot ?>/hierarchy/item/add.php?type=competency',
        handler
    );
});


// Create handler for the addcompetency dialog
mitmsDialog_handler_addcompetency = function() {};
mitmsDialog_handler_addcompetency.prototype = new mitmsDialog_handler();

/**
 * Do handler specific binding
 *
 * @return void
 */
mitmsDialog_handler_addcompetency.prototype.every_load = function() {

    var handler = this;

    $('#addcompetency #id_submitbutton').click(function() {
        var formdata = $('#addcompetency #mform1');

        // submit form
        handler._dialog._request(
            '<?php echo $CFG->wwwroot ?>/hierarchy/item/add.php?'+formdata.serialize(),
            handler,
            'submission'
        );

        return false;
    });

    $('#addcompetency #id_cancel').click(function() {
        handler._dialog.hide();
        return false;
    });
}

/**
 * Handle form submission
 *
 * @param   post request response
 * @return  boolean
 */
mitmsDialog_handler_addcompetency.prototype.submission = function(response) {

    if (response.substr(0,8) == 'newcomp:') {
        // competency created, grab info and close popup
        if(match = response.match(/^newcomp:([0-9]*):(.*)$/)) {
            var compid = match[1];
            var compname = match[2];
            $('input[name=competencyid]').val(compid);
            $('span#competencytitle').text(compname);

            var profinput = $('body.hierarchy-type-competency-evidence select#id_proficiency');
            var jsonurl = '<?php echo $CFG->wwwroot ?>/hierarchy/type/competency/evidence/competency_scale.json.php';
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

            this._dialog.hide();
            return false;
        }
    }

    // Failed, rerender form
    return true;
}
