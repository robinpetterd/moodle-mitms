<?PHP // $Id: certificate.php,v 3.1.9 2008/01/20

//General functions
$string['modulename'] = 'Certificate';
$string['modulenameplural'] = 'Certificates';
$string['certificatecoursename'] = 'Course Name';
$string['certificatename'] = 'Certificate Name';
$string['certificatetitle'] = 'Certificate Title';
$string['certificate:view'] = 'View Certificate';
$string['certificate:manage'] = 'Manage Certificate';
$string['certificate:printteacher'] = 'Print Teacher';
$string['certificate:student'] = 'Get Certificate';

//Adding an instance
$string['intro'] = 'Introduction';
$string['addlinklabel'] = 'Add another linked activity option';
$string['addlinktitle'] = 'Click to add another linked activity option';
$string['issueoptions'] = 'Issue Options';
$string['textoptions'] = 'Text Options';
$string['designoptions'] = 'Design Options';
$string['lockingoptions'] = 'Locking Options';
$string['certificatetype'] = 'Certificate Type';
$string['emailteachers'] = 'Email Teachers';
$string['emailothers'] = 'Email Others';
$string['savecertificate'] = 'Save Certificates';
$string['deliver'] = 'Delivery';
$string['download'] = 'Force download';
$string['openbrowser'] = 'Open in new window';
$string['emailcertificate'] = 'Email (Must also choose save!)';
$string['border'] = 'Border';
$string['borderstyle'] = 'Border Image';
$string['borderlines'] = 'Lines';
$string['bordercolor'] = 'Border Lines';
$string['borderblack'] = 'Black';
$string['borderbrown'] = 'Brown';
$string['borderblue'] = 'Blue';
$string['bordergreen'] = 'Green';
$string['printwmark'] = 'Watermark Image';
$string['datehelp'] = 'Date';
$string['dateformat'] = 'Date Format';
$string['receiveddate'] = 'Date Received';
$string['courseenddate'] = 'Course End Date (Must be set!)';
$string['gradedate'] = 'Grade Date';
$string['printcode'] = 'Print Code';
$string['printgrade'] = 'Print Grade';
$string['printoutcome'] = 'Print Outcome';
$string['nogrades'] = 'No grades available';
$string['gradeformat'] = 'Grade Format';
$string['gradepercent'] = 'Percentage Grade';
$string['gradepoints'] = 'Points Grade';
$string['gradeletter'] = 'Letter Grade';
$string['printhours'] = 'Print Credit Hours';
$string['printsignature'] = 'Signature Image';
$string['sigline'] = 'line';
$string['printteacher'] = 'Print Teacher Name(s)';
$string['customtext'] = 'Custom Text';
$string['printdate'] = 'Print Date';
$string['printseal'] = 'Seal or Logo Image';
$string['lockgrade'] = 'Lock by grade';
$string['requiredgrade'] = 'Required course grade';
$string['coursetime'] = 'Required course time';
$string['linkedactivity'] = 'Linked Activity';
$string['minimumgrade'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Required Grade';
$string['activitylocklabel'] = 'Linked Activity/Minimum Grade %';
$string['coursetimedependency'] = 'Minimum required minutes in course';
$string['activitydependencies'] = 'Dependent activities';

//Strings for verification block 
$string['entercode'] = 'Enter certificate code to verify:';
$string['validate'] = 'Verify';
$string['certificate'] = 'Verification for certificate code:';
$string['verifycertificate'] = 'Verify Certificate';
$string['notfound'] = 'The certificate number could not be validated.';
$string['back'] = 'Back';
$string['to'] = 'Awarded to';
$string['course'] = 'For';
$string['date'] = 'On';
$string['grade'] = 'Grade';

//Certificate view, index, report strings
$string['incompletemessage'] = 'In order to download your certificate, you must first complete all required '.'activities. Please return to the course to complete your coursework.';
$string['awardedto'] = 'Awarded To';
$string['issued'] = 'Issued';
$string['notissued'] = 'Not Issued';
$string['notissuedyet'] = 'Not issued yet';
$string['notreceived'] = 'You have not received this certificate';
$string['getcertificate'] = 'Get your certificate';
$string['report'] = 'Report';
$string['code'] = 'Code';
$string['viewed'] = 'You received this certificate on:';
$string['viewcertificateviews'] = 'View $a issued certificates';
$string['reviewcertificate'] = 'Review your certificate';
$string['openwindow'] = 'Click the button below to open your certificate
in a new browser window.';
$string['opendownload'] = 'Click the button below to save your certificate
to your computer.';
$string['openemail'] = 'Click the button below and your certificate
will be sent to you as an email attachment.';
$string['receivedcerts'] = 'Received certificates';
$string['errorlockgrade'] = 'Your current grade on $a->mod ($a->current%%) is below the grade required ($a->needed%%) to receive the certificate.';
$string['errorlocksurvey'] = 'You need to complete all course surveys before receving your certificate.';
$string['errorlockgradecourse'] = 'Your current course grade ($a->current%%) is below the grade required ($a->needed%%) to receive your certificate.';
$string['errorlocktime'] = 'You must first meet the requirement for time spent working in this course before receiving your certificate.';
$string['errorlockmod'] = 'You must first meet all course activity grade requirements before receiving your certificate.';

//Email text
$string['emailstudenttext'] = 'Attached is your certificate for $a->course.';
$string['awarded'] = 'Awarded';
$string['emailteachermail'] = '
$a->student has received their certificate: \'$a->certificate\'
for $a->course.

You can review it here:

    $a->url';
$string['emailteachermailhtml'] = '
$a->student has received their certificate: \'<i>$a->certificate</i>\'
for $a->course.

You can review it here:

    <a href=\"$a->url\">Certificate Report</a>.';

//Names of type folders
$string['typeportrait'] = 'Portrait';
$string['typeletter_portrait'] = 'Portrait (letter)';
$string['typelandscape'] = 'Landscape';
$string['typeletter_landscape'] = 'Landscape (letter)';
$string['typeunicode_landscape'] = 'Unicode (landscape)';
$string['typeunicode_portrait'] = 'Unicode (portrait)';

$string['titledefault'] = 'CERTIFICATE OF ACHIEVEMENT';

//Print to certificate strings
$string['grade'] = 'Grade';
$string['coursegrade'] = 'Course Grade';
$string['credithours'] = 'Credit Hours';

$string['introlandscape'] = 'This is to certify that';
$string['statementlandscape'] = 'has completed the course';

$string['introletterlandscape'] = 'This is to certify that';
$string['statementletterlandscape'] = 'has completed the course';

$string['introportrait'] = 'This is to certify that';
$string['statementportrait'] = 'has completed the course';
$string['ondayportrait'] = 'on this day';

$string['introletterportrait'] = 'This is to certify that';
$string['statementletterportrait'] = 'has completed the course';

//Certificate transcript strings
$string['notapplicable'] = 'N/A';
$string['certificatesfor'] = 'Certificates for';
$string['coursename'] = 'Course';
$string['viewtranscript'] = 'View Certificates';
$string['mycertificates'] = 'My Certificates';
$string['nocertificatesreceived'] = 'has not received any course certificates.';
$string['notissued'] = 'Not received';
$string['reportcertificate'] = 'Report Certificates';
$string['certificatereport'] = 'Certificates Report';
$string['printerfriendly'] = 'Printer-friendly page';

//Custom strings
$string['field1'] = 'Institution';
$string['field2'] = 'I am a';
$string['field3'] = 'I am training as';

?>
