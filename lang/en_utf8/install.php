<?PHP // $Id$ 
      // install.php - created with Moodle 1.7 beta + (2006101003)


$string['admindirerror'] = 'The admin directory specified is incorrect';
$string['admindirname'] = 'Admin Directory';
$string['admindirsetting'] = 'A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br /> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['admindirsettinghead'] = 'Setting the admin directory ...';
$string['admindirsettingsub'] = 'A very few webhosts use /admin as a special URL for you to access a 
    control panel or something.  Unfortunately this conflicts with the 
    standard location for the Moodle admin pages.  You can fix this by 
    renaming the admin directory in your installation, and putting that 
    new name here.  For example: <br /> <br /><b>moodleadmin</b><br /> <br />
    This will fix admin links in Moodle.';
$string['caution'] = 'Caution';
$string['chooselanguage'] = 'Choose a language';
$string['chooselanguagehead'] = 'Choose a language';
$string['chooselanguagesub'] = 'Please choose a language for the installation ONLY. You will be able to choose site and user languages on a later screen.';
$string['compatibilitysettings'] = 'Checking your PHP settings ...';
$string['compatibilitysettingshead'] = 'Checking your PHP settings ...';
$string['compatibilitysettingssub'] = 'Your server should pass all these tests to make Moodle run properly';
$string['configfilenotwritten'] = 'The installer script was not able to automatically create a config.php file containing your chosen settings, probably because the Moodle directory is not writeable. You can manually copy the following code into a file named config.php within the root directory of Moodle.';
$string['configfilewritten'] = 'config.php has been successfully created';
$string['configurationcomplete'] = 'Configuration completed';
$string['configurationcompletehead'] = 'Configuration completed';
$string['configurationcompletesub'] = 'Moodle made an attempt to save your configuration in a file in the root of your Moodle installation.';
$string['database'] = 'Database';
$string['databasecreationsettings'] = 'Now you need to configure the database settings where most Moodle data
    will be stored.  This database will be created automatically by the installer
    with the settings specified below.<br />
    <br /> <br />
       <b>Type:</b> fixed to \"mysql\" by the installer<br />
       <b>Host:</b> fixed to \"localhost\" by the installer<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> fixed to \"root\" by the installer<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasecreationsettingshead'] = 'Now you need to configure the database settings where most Moodle data
    will be stored.  This database will be created automatically by the installer
    with the settings specified below.';
$string['databasecreationsettingssub'] = '<b>Type:</b> fixed to \"mysql\" by the installer<br />
       <b>Host:</b> fixed to \"localhost\" by the installer<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> fixed to \"root\" by the installer<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettings'] = 'Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.<br />
    <br /> <br />
       <b>Type:</b> mysql or postgres7<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettingshead'] = 'Now you need to configure the database where most Moodle data
    will be stored.  This database must already have been created
    and a username and password created to access it.';
$string['databasesettingssub'] = '<b>Type:</b> mysql or postgres7<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> optional prefix to use for all table names';
$string['databasesettingssub_mssql'] = '<b>Type:</b> SQL*Server (non UTF-8) <b><strong  class=\"errormsg\">Experimental! (not for use in production)</strong></b><br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (mandatory)';
$string['databasesettingssub_mssql_n'] = '<b>Type:</b> SQL*Server (UTF-8 enabled)<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (mandatory)';
$string['databasesettingssub_mysql'] = '<b>Type:</b> MySQL<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (optional)';
$string['databasesettingssub_mysqli'] = '<b>Type:</b> Improved MySQL<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (optional)';
$string['databasesettingssub_oci8po'] = '<b>Type:</b> Oracle<br />
       <b>Host:</b> not used, must be left blank<br />
       <b>Name:</b> given name of the tnsnames.ora connection<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (mandatory, 2cc. max)';
$string['databasesettingssub_odbc_mssql'] = '<b>Type:</b> SQL*Server (over ODBC) <b><strong  class=\"errormsg\">Experimental! (not for use in production)</strong></b><br />
       <b>Host:</b> given name of the DSN in the ODBC control panel<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (mandatory)';
$string['databasesettingssub_postgres7'] = '<b>Type:</b> PostgreSQL<br />
       <b>Host:</b> eg localhost or db.isp.com<br />
       <b>Name:</b> database name, eg moodle<br />
       <b>User:</b> your database username<br />
       <b>Password:</b> your database password<br />
       <b>Tables Prefix:</b> prefix to use for all table names (mandatory)';
$string['databasesettingswillbecreated'] = '<b>Note:</b> The installer will try to create the database automatically if not exists.';
$string['dataroot'] = 'Data Directory';
$string['datarooterror'] = 'The \'Data Directory\' you specified could not be found or created.  Either correct the path or create that directory manually.';
$string['datarootpublicerror'] = 'The \'Data Directory\' you specified is directly accessible via web, you must use different directory.';
$string['dbconnectionerror'] = 'We could not connect to the database you specified. Please check your database settings.';
$string['dbcreationerror'] = 'Database creation error. Could not create the given database name with the settings provided';
$string['dbhost'] = 'Host Server';
$string['dbpass'] = 'Password';
$string['dbprefix'] = 'Tables prefix';
$string['dbtype'] = 'Type';
$string['dbwrongencoding'] = 'The selected database is running under one non-recommended encoding ($a). It would be better to use one Unicode (UTF-8) encoded database instead. Anyway, you can bypass this test by selecting the \"Skip DB Encoding Test\" check below, but you could experience problems in the future.';
$string['dbwronghostserver'] = 'You must follow \"Host\" rules as explained above.';
$string['dbwrongnlslang'] = 'The NLS_LANG environment variable in your web server must use the AL32UTF8 charset. See PHP documentation about how to configure OCI8 properly.';
$string['dbwrongprefix'] = 'You must follow \"Tables Prefix\" rules as explained above.';
$string['directorysettings'] = '<p>Please confirm the locations of this Moodle installation.</p>

<p><b>Web Address:</b>
Specify the full web address where Moodle will be accessed.  
If your web site is accessible via multiple URLs then choose the 
most natural one that your students would use.  Do not include 
a trailing slash.</p>

<p><b>Moodle Directory:</b>
Specify the full directory path to this installation
Make sure the upper/lower case is correct.</p>

<p><b>Data Directory:</b>
You need a place where Moodle can save uploaded files.  This
directory should be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it must not be accessible 
directly via the web.</p>';
$string['directorysettingshead'] = 'Please confirm the locations of this Moodle installation';
$string['directorysettingssub'] = '<b>Web Address:</b>
Specify the full web address where Moodle will be accessed.  
If your web site is accessible via multiple URLs then choose the 
most natural one that your students would use.  Do not include 
a trailing slash.
<br />
<br />
<b>Moodle Directory:</b>
Specify the full directory path to this installation
Make sure the upper/lower case is correct.
<br />
<br />
<b>Data Directory:</b>
You need a place where Moodle can save uploaded files.  This
directory must be readable AND WRITEABLE by the web server user 
(usually \'nobody\' or \'apache\'), but it must not be accessible 
directly via the web.';
$string['dirroot'] = 'Moodle Directory';
$string['dirrooterror'] = 'The \'Moodle Directory\' setting seems to be incorrect - we can\'t find a Moodle installation there. The value below has been reset.';
$string['download'] = 'Download';
$string['downloadlanguagebutton'] = 'Download the &quot;$a&quot; language pack';
$string['downloadlanguagehead'] = 'Download language pack';
$string['downloadlanguagenotneeded'] = 'You may continue the installation process using the default language pack, \"$a\".';
$string['downloadlanguagesub'] = 'You now have the option of downloading a language pack and continuing the installation process in this language.<br /><br />If you are unable to download the language pack, the installation process will continue in English. (Once the installation process is complete, you will have the opportunity to download and install additional language packs.)';
$string['environmenthead'] = 'Checking your environment ...';
$string['environmentsub'] = 'We are checking if the various components of your system meet the system requirements';
$string['fail'] = 'Fail';
$string['fileuploads'] = 'File Uploads';
$string['fileuploadserror'] = 'This should be on';
$string['fileuploadshelp'] = '<p>File uploading seems to be disabled on your server.</p>

<p>Moodle can still be installed, but without this ability, you will not be able 
   to upload course files or new user profile images.</p>

<p>To enable file uploading you (or your system administrator) will need to 
   edit the main php.ini file on your system and change the setting for 
   <b>file_uploads</b> to \'1\'.</p>';
$string['gdversion'] = 'GD version';
$string['gdversionerror'] = 'The GD library should be present to process and create images';
$string['gdversionhelp'] = '<p>Your server does not seem to have GD installed.</p>

<p>GD is a library that is required by PHP to allow Moodle to process images 
   (such as the user profile icons) and to create new images (such as 
   the log graphs).  Moodle will still work without GD - these features 
   will just not be available to you.</p>

<p>To add GD to PHP under Unix, compile PHP using the --with-gd parameter.</p>

<p>Under Windows you can usually edit php.ini and uncomment the line referencing php_gd2.dll.</p>';
$string['globalsquotes'] = 'Insecure Handling of Globals';
$string['globalsquoteserror'] = 'Fix your PHP settings: disable register_globals and/or enable magic_quotes_gpc';
$string['globalsquoteshelp'] = '<p>Combination of disabled Magic Quotes GPC and enabled Register Globals both at the same time is not recommended.</p>

<p>The recommended setting is <b>magic_quotes_gpc = On</b> and <b>register_globals = Off</b> in your php.ini</p>

<p>If you don\'t have access to your php.ini, you might be able to place the following line in a file 
   called .htaccess within your Moodle directory:</p>
   <blockquote><div>php_value magic_quotes_gpc On</div></blockquote>
   <blockquote><div>php_value register_globals Off</div></blockquote>
';
$string['installation'] = 'Installation';
$string['langdownloaderror'] = 'Unfortunately the language \"$a\" was not installed. The installation process will continue in English.';
$string['langdownloadok'] = 'The language \"$a\" was installed successfully. The installation process will continue in this language.';
$string['magicquotesruntime'] = 'Magic Quotes Run Time';
$string['magicquotesruntimeerror'] = 'This should be off';
$string['magicquotesruntimehelp'] = '<p>Magic quotes runtime should be turned off for Moodle to function properly.</p>

<p>Normally it is off by default ... see the setting <b>magic_quotes_runtime</b> in your php.ini file.</p>

<p>If you don\'t have access to your php.ini, you might be able to place the following line in a file 
   called .htaccess within your Moodle directory:</p>
   <blockquote><div>php_value magic_quotes_runtime Off</div></blockquote>
';
$string['memorylimit'] = 'Memory Limit';
$string['memorylimiterror'] = 'The PHP memory limit is set quite low ... you may run into problems later.';
$string['memorylimithelp'] = '<p>The PHP memory limit for your server is currently set to $a.</p>

<p>This may cause Moodle to have memory problems later on, especially 
   if you have a lot of modules enabled and/or a lot of users.</p>

<p>We recommend that you configure PHP with a higher limit if possible, like 40M.  
   There are several ways of doing this that you can try:</p>
<ol>
<li>If you are able to, recompile PHP with <i>--enable-memory-limit</i>.  
    This will allow Moodle to set the memory limit itself.</li>
<li>If you have access to your php.ini file, you can change the <b>memory_limit</b> 
    setting in there to something like 40M.  If you don\'t have access you might 
    be able to ask your administrator to do this for you.</li>
<li>On some PHP servers you can create a .htaccess file in the Moodle directory 
    containing this line:
    <blockquote><div>php_value memory_limit 40M</div></blockquote>
    <p>However, on some servers this will prevent <b>all</b> PHP pages from working 
    (you will see errors when you look at pages) so you\'ll have to remove the .htaccess file.</p></li>
</ol>';
$string['mssql'] = 'SQL*Server (mssql)';
$string['mssql_n'] = 'SQL*Server with UTF-8 support (mssql_n)';
$string['mssqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MSSQL extension so that it can communicate with SQL*Server.  Please check your php.ini file or recompile PHP.';
$string['mysql'] = 'MySQL (mysql)';
$string['mysqli'] = 'Improved MySQL (mysqli)';
$string['mysqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MySQL extension so that it can communicate with MySQL.  Please check your php.ini file or recompile PHP.';
$string['mysqliextensionisnotpresentinphp'] = 'PHP has not been properly configured with the MySQLi extension so that it can communicate with MySQL.  Please check your php.ini file or recompile PHP.  MySQLi extension is not available for PHP 4.';
$string['oci8po'] = 'Oracle (oci8po)';
$string['ociextensionisnotpresentinphp'] = 'PHP has not been properly configured with the OCI8 extension so that it can communicate with Oracle.  Please check your php.ini file or recompile PHP.';
$string['odbc_mssql'] = 'SQL*Server over ODBC (odbc_mssql)';
$string['odbcextensionisnotpresentinphp'] = 'PHP has not been properly configured with the ODBC extension so that it can communicate with SQL*Server.  Please check your php.ini file or recompile PHP.';
$string['pass'] = 'Pass';
$string['pgsqlextensionisnotpresentinphp'] = 'PHP has not been properly configured with the PGSQL extension so that it can communicate with PostgreSQL.  Please check your php.ini file or recompile PHP.';
$string['phpversion'] = 'PHP version';
$string['phpversionerror'] = 'PHP version must be at least 4.3.0 or 5.1.0 (5.0.x has a number of known problems).';
$string['phpversionhelp'] = '<p>Moodle requires a PHP version of at least 4.3.0 or 5.1.0 (5.0.x has a number of known problems).</p>
<p>You are currently running version $a</p>
<p>You must upgrade PHP or move to a host with a newer version of PHP!<br />
(In case of 5.0.x you could also downgrade to 4.4.x version)</p>';
$string['postgres7'] = 'PostgreSQL (postgres7)';
$string['postgresqlwarning'] = '<strong>Note:</strong> If you experience some connection problems, you can try to set Host Server field as
 host=\'postgresql_host\' port=\'5432\' dbname=\'postgresql_database_name\' user=\'postgresql_user\' password=\'postgresql_user_password\'
and to leave empty the Database, User and Password fields. More information on <a href=\"http://docs.moodle.org/en/Installing_Postgres_for_PHP\">Moodle Docs</a>';
$string['safemode'] = 'Safe Mode';
$string['safemodeerror'] = 'Moodle may have trouble with safe mode on';
$string['safemodehelp'] = '<p>Moodle may have a variety of problems with safe mode on, not least is that 
   it probably won\'t be allowed to create new files.</p>
   
<p>Safe mode is usually only enabled by paranoid public web hosts, so you may have 
   to just find a new web hosting company for your Moodle site.</p>
   
<p>You can try continuing the install if you like, but expect a few problems later on.</p>';
$string['sessionautostart'] = 'Session Auto Start';
$string['sessionautostarterror'] = 'This should be off';
$string['sessionautostarthelp'] = '<p>Moodle requires session support and will not function without it.</p>

<p>Sessions can be enabled in the php.ini file ... look for the session.auto_start parameter.</p>';
$string['skipdbencodingtest'] = 'Skip DB Encoding Test';
$string['welcomep10'] = '$a->installername ($a->installerversion)';
$string['welcomep20'] = 'You are seeing this page because you have successfully installed and 
    launched the <strong>$a->packname $a->packversion</strong> package in your computer. Congratulations!';
$string['welcomep30'] = 'This release of the <strong>$a->installername</strong> includes the applications 
    to create an environment in which <strong>Moodle</strong> will operate, namely:';
$string['welcomep40'] = 'The package also includes <strong>Moodle $a->moodlerelease ($a->moodleversion)</strong>.';
$string['welcomep50'] = 'The use of all the applications in this package is governed by their respective 
    licences. The complete <strong>$a->installername</strong> package is 
    <a href=\"http://www.opensource.org/docs/definition_plain.html\">open source</a> and is distributed 
    under the <a href=\"http://www.gnu.org/copyleft/gpl.html\">GPL</a> license.';
$string['welcomep60'] = 'The following pages will lead you through some easy to follow steps to 
    configure and set up <strong>Moodle</strong> on your computer. You may accept the default 
    settings or, optionally, amend them to suit your own needs.';
$string['welcomep70'] = 'Click the \"Next\" button below to continue with the set up of <strong>Moodle</strong>.';
$string['wwwroot'] = 'Web address';
$string['wwwrooterror'] = 'The \'Web Address\' does not appear to be valid - this Moodle installation doesn\'t appear to be there. The value below has been reset.';

// cli installer strings
$string['aborting'] = '\nAborting ...\n';
$string['abortinstallation']= 'Aborting installtion ...\n';
$string['adminemail'] = 'Email :';
$string['adminfirstname'] = 'First Name :';
$string['admininfo'] = 'Administrator Details';
$string['adminlastname'] = 'Last Name :';
$string['adminpassword'] = 'Password :';
$string['adminusername'] = 'Username :';
$string['askcontinue'] = 'Continue (yes/no) :';
$string['availabledbtypes']='\nAvailable db types \n';
$string['availablelangs']='List of available languages \n';
$string['cannotconnecttodb'] = 'Cannot connect to db \n';
$string['checkingphpsettings']='\n\nChecking PHP Settings\n\n';
$string['configfilecreated'] = 'Configuration file successfully created\n ';
$string['configfiledoesnotexist'] = 'Configuration file does not exist !!!';
$string['configurationfileexist']='Configuration file already exist!\n';
$string['creatingconfigfile'] =' Creating configuration file ...\n';
$string['databasehost']='Database host :';
$string['databasename']='Database name : ';
$string['databasepass']='Database password :';
$string['databasesettingsformoodle']='\n\nDatabase settings for Moodle\n\n';
$string['databasetype']='Database type :';
$string['databaseuser']='Database user :';
$string['disagreelicense'] = 'Upgrade cannot proceed due to disagreement to GPL!';
$string['downloadlanguagepack']='\n\nDo you want to download language pack now(yes/no) :';
$string['downloadsuccess'] = 'Language Pack Downloaded Successfuly';
$string['doyouagree'] = 'Do you agree ? (yes/no):';
$string['errorsinenvironment'] ='Errors in the environment !\n';
$string['inputdatadirectory']='Data Directory :';
$string['inputwebadress']='Web address :';
$string['inputwebdirectory']='Moodle Directory :';
$string['installationiscomplete'] = 'Installtion is completed !\n';
$string['invalidargumenthelp']='
    Error: Invalid argument(s)
    Usage: \$php cliupgrade.php OPTIONS
    Use --help option to get more help\n';
$string['invalidemail'] = 'Invalid Email';
$string['invalidhost'] = 'Invalid Host ';
$string['invalidint']='Error: value is not an integer \n';
$string['invalidintrange'] = 'Error: Value is outside valid range\n';
$string['invalidpath'] = 'Invalid Path ';
$string['invalidsetelement']= 'Error: Value given is not in the given options \n';
$string['invalidtextvalue'] = 'Invalid Text Value';
$string['invalidurl'] = 'Invalid URL ';
$string['invalidvalueforlanguage']='Invalid value for --lang option. Type --help for more help\n';
$string['invalidyesno'] = 'Error: value is not a valid yes/no argument \n';
$string['locationanddirectories']= '\n\nLocation and directories \n\n';
$string['newline'] = '\n';
$string['pearargerror']='PEAR library cannot identify the arguments\n';
$string['releasenoteslink'] = 'For information about this version of Moodle, please see Release Notes at $a';
$string['selectlanguage']='\n\nSelecting a language for installation\n';
$string['sitefullname'] = 'Site full name :';
$string['siteinfo'] = 'Site Details';
$string['sitenewsitems'] = 'News Items :';
$string['siteshortname'] = 'Site short name :';
$string['sitesummary'] ='Site summary :';
$string['tableprefix']='Table prefix :';
$string['unsafedirname'] = 'Error: Unsafe characters in directory name. valid characters are a-zA-Z0-9_-\n';
$string['upgradingactivitymodule']= 'Upgrading Activity Module';
$string['upgradingbackupdb'] = 'Upgrading Backup Database';
$string['upgradingblocksdb'] = 'Upgrading Blocks Database';
$string['upgradingblocksplugin'] = 'Upgrading Blocks Plugin';
$string['upgradingcompleted'] = 'Upgrading completed...\n';
$string['upgradingcourseformatplugin'] = 'Upgrading Course Format Pluggin';
$string['upgradingenrolplugin'] = 'Upgrading Enrol Plugin';
$string['upgradinggradeexportplugin'] = 'Upgrading Grade Export Plugin';
$string['upgradinggradeimportplugin'] = 'Upgrading Grame Import Plugin';
$string['upgradinggradereportplugin'] = 'Upgrading Grade Report Plugin';
$string['upgradinglocaldb'] = 'Upgrading Local Database';
$string['upgradingmessageoutputpluggin'] = 'Upgrading Message Output Plugin';
$string['upgradingqtypeplugin'] = 'Upgrading Question/type Plugin';
$string['upgradingrpcfunctions'] = 'Upgrading RPC Functions';
$string['usagehelp']='
Synopsis:
\$php cliupgrade.php OPTIONS\n
OPTIONS
--lang              Valid installed language for installation. Default is English(en)
--webaddr           Web address for the Moodle site
--moodledir         Location of the moodle web folder
--datadir           Location of the moodle data folder (should not be web visible)
--dbtype            Database type. Default it mysql
--dbhost            Database host. Default localhost
--dbname            Database name. Default is moodle
--dbuser            Database user. Default is blank
--dbpass            Database password. Default is blank
--prefix            Table prefix for above database tables. Default is mdl
--verbose           0 No output, 1 Summarized output(Default), 2 Detailed output
--interactivelevel  0 Non interactive, 1 semi interactive(Default), 2 interactive
--agreelicense      Yes(Default) or No
--confirmrelease    Yes(Default) or No
--sitefullname      Full name for the site. Default is : Moodle Site (Please Change Site Name!!)
--siteshortname     Short name for the site. Default is moodle
--sitesummary       Summary of the site. Default is blank
--adminfirstname    First name of the admin. Default is Admin
--adminlastname     Last name of the admin. Default is User
--adminusername     Username for the admin. Default is admin
--adminpassword     Password for the admin. Default is admin
--adminemail        Email address of admin. Default is root@localhost
--help              print out this help\n
Usage:
\$php cliupgrade.php --lang=en --webaddr=http://www.example.com --moodledir=/var/www/html/moodle --datadir=/var/moodledata --dbtype=mysql --dbhost=localhost --dbname=moodle --dbuser=root --prefix=mdl --agreelicense=yes --confirmrelease=yes --sitefullname=\"Example Moodle Site\" --siteshortname=moodle --sitesummary=siteforme --adminfirstname=Admin --adminlastname=User --adminusername=admin --adminpassword=admin --adminemail=admin@example.com --verbose=1 --interactivelevel=2 \n';
$string['versionerror'] = 'User aborted due to version Error ';
$string['welcometext']='\n\n---Welcome to moodle commandline installer---\n\n';
$string['writetoconfigfilefaild'] = 'Error: Write to config file failed ';
$string['yourchoice']='\nYour choice :';

?>
