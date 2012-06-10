<?PHP



//General functions
$string['modulename'] = 'Certificato';
$string['modulenameplural'] = 'Certificati';
$string['certificatename'] = 'Nome del certificato';
$string['certificate:view'] = 'View Certificate';
$string['certificate:manage'] = 'Manage Certificate';
$string['certificate:printteacher'] = 'Stampa i decenti';
$string['certificate:student'] = 'Get Certificate';

//Adding an instance
$string['addlinklabel'] = 'Add another linked activity option';
$string['addlinktitle'] = 'Click to add another linked activity option';
$string['issueoptions'] = 'Issue Options';
$string['textoptions'] = 'Text Options';
$string['designoptions'] = 'Design Options';
$string['lockingoptions'] = 'Locking Options';
$string['certificatetype'] = 'Tipo di certificate';
$string['emailteachers'] = 'Email Teachers';
$string['emailothers'] = 'Altre email';
$string['savecertificate'] = 'Save Certificates';
$string['deliver'] = 'Delivery';
$string['download'] = 'Force download';
$string['openbrowser'] = 'Open in new window';
$string['emailcertificate'] = 'Email (Must also choose save!)';
$string['border'] = 'Border';
$string['borderstyle'] = 'Border Image';
$string['borderlines'] = 'Linee';
$string['bordercolor'] = 'Border Linee';
$string['borderblack'] = 'Nero';
$string['borderbrown'] = 'Marrone';
$string['borderblue'] = 'Blu';
$string['bordergreen'] = 'Verde';
$string['printwmark'] = 'Stampa il watermark';
$string['datehelp'] = 'Date';
$string['dateformat'] = 'Formato della data';
$string['receiveddate'] = "Date Received";
$string['courseenddate'] = 'Course End Date (Must be set!)';
$string['printcode'] = 'Stampa il numero del certificato';
$string['printgrade'] = 'Stampa la valutazione';
$string['coursegradeoption'] = 'Valutazione del course';
$string['nogrades'] = 'No grades available';
$string['gradeformat'] = 'Grade Format';
$string['gradepercent'] = 'Percentage Grade';
$string['gradepoints'] = 'Points Grade';
$string['gradeletter'] = 'Letter Grade';
$string['printhours'] = 'Print Credit Hours';
$string['printsignature'] = 'Stampa l\'autografo';
$string['sigline'] = 'Linea';
$string['printteacher'] = 'Stampa i decenti';
$string['customtext'] = 'Testi specifici';
$string['printdate'] = 'Print Date';
$string['printseal'] = 'Stampa un logo';
$string['lockgrade'] = 'Vincola alla valutazione';
$string['requiredgrade'] = 'Required grade';
$string['coursetime'] = 'Required course time';
$string['linkedactivity'] = 'Linked Activity';
$string['minimumgrade'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Required Grade';
$string['activitylocklabel'] = 'Linked Activity/Minimum Grade %';
$string['coursetimedependency'] = 'Minimum required minutes in course';
$string['activitydependencies'] = 'Dependent activities';

//Strings for verification block 
$string['configcontent'] = 'Config content';
$string['validate'] = 'Verify';
$string['certificate'] = 'Verification for certificate code:';
$string['verifycertificate'] = 'Verify Certificate';
$string['dontallowall'] = 'Do not allow all';
$string['cert'] = '#';
$string['notfound'] = 'The certificate number could not be validated.';
$string['back'] = 'Back';
$string['to'] = 'Awarded to';
$string['course'] = 'For';
$string['date'] = 'On';

//Certificate view, index, report strings
$string['incompletemessage'] = 'In order to download your certificate, you must first complete all required '.
                               'activities. Please return to the course to complete your coursework.';
$string['awardedto'] = 'Awarded To';
$string['issued'] = 'Issued';
$string['notissued'] = 'Not Issued';
$string['notissuedyet'] = 'Not issued yet';
$string['notreceived'] = 'You have not received this certificate';
$string['getcertificate'] = 'Get your certificate';
$string['report'] = 'Report';
$string['code'] = 'Numero del certificato';
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
$string['errorlockgrade'] = 'La percentuale della tua valutazione ($a->current %%) &egrave; inferiore a quella minima necessaria ($a->needed %%).';
$string['errorlocksurvey'] = 'You need to complete all course surveys before receving your certificate.';
$string['errorlockgradecourse'] = 'Your current course grade ($a->current%%) is below the grade required ($a->needed%%) to receive your certificate.';
$string['errorlocktime'] = 'You must first meet the requirement for time spent working in this course before receving your certificate.';
$string['errorlockmod'] = 'You must first meet all course activity grade requirements before receving your certificate.';

//Email text
$string['emailstudenttext'] = 'Attached is your certificate for $a->course.';
$string['awarded'] = 'Certificato generato';
$string['emailteachermail'] = '
$a->student ha acquisito il certificato \"$a->certificate\" del corso $a->course.

You can review it here:

    $a->url';
$string['emailteachermailhtml'] = '
$a->student ha acquisito il certificato: \'<i>$a->certificate</i>\'
del corso $a->course.

You can review it here:

    <a href=\"$a->url\">Certificate Report</a>.';

//Names of type folders
$string['typeportrait'] = 'Portrait';
$string['typeletter_portrait'] = 'Portrait (letter)';
$string['typelandscape'] = 'Landscape';
$string['typeletter_landscape'] = 'Landscape (letter)';
$string['typeunicode_landscape'] = 'Unicode (landscape)';
$string['typeunicode_portrait'] = 'Unicode (portrait)';

//Print to certificate strings
$string['grade'] = 'Grade';
$string['coursegrade'] = 'Course Grade';
$string['credithours'] = 'Credit Hours';

$string['titlelandscape'] = 'CERTIFICATO di COMPLETAMENTO';
$string['introlandscape'] = 'Questo documento certifica che';
$string['statementlandscape'] = 'ha completato con successo il corso';

$string['titleletterlandscape'] = 'CERTIFICATO di COMPLETAMENTO';
$string['introletterlandscape'] = 'Questo documento certifica che';
$string['statementletterlandscape'] = 'ha completato con successo il corso';

$string['titleportrait'] = 'CERTIFICATO di COMPLETAMENTO';
$string['introportrait'] = 'Questo documento certifica che';
$string['statementportrait'] = 'ha completato con successo il corso';
$string['ondayportrait'] = 'in data';

$string['titleletterportrait'] = 'CERTIFICATO di COMPLETAMENTO';
$string['introletterportrait'] = 'Questo documento certifica che';
$string['statementletterportrait'] = 'ha completato con successo il corso';

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
?>