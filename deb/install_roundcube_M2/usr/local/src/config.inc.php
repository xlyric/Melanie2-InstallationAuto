<?php

/*
 +-----------------------------------------------------------------------+
 | Local configuration for the Roundcube Webmail installation.           |
 |                                                                       |
 | This is a sample configuration file only containing the minumum       |
 | setup required for a functional installation. Copy more options       |
 | from defaults.inc.php to this file to override the defaults.          |
 |                                                                       |
 | This file is part of the Roundcube Webmail client                     |
 | Copyright (C) 2005-2013, The Roundcube Dev Team                       |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 +-----------------------------------------------------------------------+
*/

$config = array();

//$config['devel_mode'] = true;

// Database connection string (DSN) for read+write operations
// Format (compatible with PEAR MDB2): db_provider://user:password@host/database
// Currently supported db_providers: mysql, pgsql, sqlite, mssql or sqlsrv
// For examples see http://pear.php.net/manual/en/package.database.mdb2.intro-dsn.php
// NOTE: for SQLite use absolute path: 'sqlite:////full/path/to/sqlite.db?mode=0646'
$config['db_dsnw'] = 'pgsql://roundcube:roundcube@127.0.0.1/roundcube';


// The mail host chosen to perform the log-in.
// Leave blank to show a textbox at login, give a list of hosts
// to display a pulldown menu or set one host as string.
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// Supported replacement variables:
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %s - domain name after the '@' from e-mail address provided at login screen
// For example %n = mail.domain.tld, %t = domain.tld
//$config['default_host'] = 'tls://web-srv.mce.com';

$config['default_host'] = 'serverimap';


// By default IMAP capabilities are readed after connection to IMAP server
// In some cases, e.g. when using IMAP proxy, there's a need to refresh the list
// after login. Set to True if you've got this case.
$config['imap_force_caps'] = false;

// By default list of subscribed folders is determined using LIST-EXTENDED
// extension if available. Some servers (dovecot 1.x) returns wrong results
// for shared namespaces in this case. http://trac.roundcube.net/ticket/1486225
// Enable this option to force LSUB command usage instead.
// Deprecated: Use imap_disabled_caps = array('LIST-EXTENDED')
$config['imap_force_lsub'] = false;

// Some server configurations (e.g. Courier) doesn't list folders in all namespaces
// Enable this option to force listing of folders in all namespaces
$config['imap_force_ns'] = true;

// SMTP server host (for sending mails).
// To use SSL/TLS connection, enter hostname with prefix ssl:// or tls://
// If left blank, the PHP mail() function is used
// Supported replacement variables:
// %h - user's IMAP hostname
// %n - hostname ($_SERVER['SERVER_NAME'])
// %t - hostname without the first part
// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
// %z - IMAP domain (IMAP hostname without the first part)
// For example %n = mail.domain.tld, %t = domain.tld
$config['smtp_server'] = 'serverimap';


// SMTP port (default is 25; use 587 for STARTTLS or 465 for the
// deprecated SSL over SMTP (aka SMTPS))
//  -- $config['smtp_port'] = 587;
// $config['smtp_port'] = 587;


// SMTP username (if required) if you use %u as the username Roundcube
// will use the current username for login
$config['smtp_user'] = '%u';

// SMTP password (if required) if you use %p as the password Roundcube
// will use the current user's password for login
$config['smtp_pass'] = '%p';

// SMTP HELO host
// Hostname to give to the remote server for SMTP 'HELO' or 'EHLO' messages
// Leave this blank and you will get the server variable 'server_name' or
// localhost if that isn't defined.
$config['smtp_helo_host'] = 'ip-84-39-52-52.rev.cloudwatt.com';

if (!isset($_SERVER["HTTP_X_MINEQPROVENANCE"]) || strcasecmp($_SERVER["HTTP_X_MINEQPROVENANCE"], "intranet") === 0) {
  // provide an URL where a user can get support for this Roundcube installation
  // PLEASE DO NOT LINK TO THE ROUNDCUBE.NET WEBSITE HERE!
  $config['support_url'] = 'https://ip-84-39-52-52.rev.cloudwatt.com/webmail/aide/';
}

// replace Roundcube logo with this image
// specify an URL relative to the document root of this Roundcube installation
// an array can be used to specify different logos for specific template files, '*' for default logo
// for example array("*" => "/images/roundcube_logo.png", "messageprint" => "/images/roundcube_logo_print.png")
//$config['skin_logo'] = "/images/melanie2_logo_beta.png";
$config['skin_logo'] = "/images/mce_logo2.png";

// add this user-agent to message headers when sending
$rcmail_config['useragent'] = 'MceWebV2/RC' . RCMAIL_VERSION;

// use this name to compose page titles
$rcmail_config['product_name'] = '';

// this key is used to encrypt the users imap password which is stored
// in the session record (and the client cookie if remember password is enabled).
// please provide a string of exactly 24 chars.
// YOUR KEY MUST BE DIFFERENT THAN THE SAMPLE VALUE FOR SECURITY REASONS
$config['des_key'] = 'mceweb2-.54IttcRUHbte$Uot';


// List of active plugins (in plugins/ directory)
//$config['plugins'] =  array('mce', 'mce_logs', 'mce_contacts', 'mce_larry', 'mce_applications', 'mce_courrielleur', 'mce_moncompte', 'mce_acl', 'mce_sondage', 'mce_labels_sync', 'calendar', 'tasklist');
//$config['plugins'] =  array('mel','mel_logs','mel_ldap_auth','mel_larry','annuaire','mel_contacts','calendar', 'libcalendaring','tasklist' );
$config['plugins'] =  array('mel_logs','mel', 'mel_contacts', 'mel_larry', 'mel_courrielleur', 'mel_acl', 'mel_sondage', 'mel_labels_sync','jqueryui', 'libcalendaring', 'calendar','tasklist','mel_moncompte','right_panel','mel_portail','mel_junk','managesieve','attachment_reminder', 'newmail_notifier');


// Add it to the plugins list in config.inc.php to enable the user option
//  set the global preference
$config['use_subscriptions'] = false; // or false

// skin name: folder from skins/
$config['skin'] = 'mel_larry';

// system error reporting, sum of: 1 = log; 4 = show, 8 = trace
$config['debug_level'] = 1;

// use this folder to store log files (must be writeable for apache user)
// This is used by the 'file' log driver.
$config['log_dir'] = '/var/log/roundcube';

// Log successful/failed logins to <log_dir>/userlogins or to syslog
$config['log_logins'] = true;

// Log session authentication errors to <log_dir>/session or to syslog
$config['log_session'] = true;

// Log SQL queries to <log_dir>/sql or to syslog
$config['sql_debug'] = true;

// Log IMAP conversation to <log_dir>/imap or to syslog
$config['imap_debug'] = true;

// Log LDAP conversation to <log_dir>/ldap or to syslog
$config['ldap_debug'] = true;

// Log SMTP conversation to <log_dir>/smtp or to syslog
$config['smtp_debug'] = true;

// Session lifetime in minutes
$rcmail_config['session_lifetime'] = 8*60;
//$rcmail_config['session_lifetime'] = 1;

// Session name. Default: 'roundcube_sessid'
$rcmail_config['session_name'] = 'roundcube_sessid';

// Session authentication cookie name. Default: 'roundcube_sessauth'
$rcmail_config['session_auth_name'] = 'roundcube_sessauth';

// Backend to use for session storage. Can either be 'db' (default), 'memcache' or 'php'
// If set to 'memcache', a list of servers need to be specified in 'memcache_hosts'
// Make sure the Memcache extension (http://pecl.php.net/package/memcache) version >= 2.0.0 is installed
// Setting this value to 'php' will use the default session save handler configured in PHP
//$config['session_storage'] = 'php';
//$config['session_storage'] = 'memcache';
//$config['session_storage'] = 'db';

$config['session_storage'] = 'php';


// Use these hosts for accessing memcached
// Define any number of hosts in the form of hostname:port or unix:///path/to/socket.file
$config['memcache_hosts'] = array('localhost:11211'); // e.g. array( 'localhost:11211', '192.168.1.12:11211', 'unix:///var/tmp/memcached.sock' );



// Maximum number of recipients per message. Default: 0 (no limit)
$rcmail_config['max_recipients'] = 300;

// Use this charset as fallback for message decoding
$config['default_charset'] = 'UTF-8';

// Set identities access level:
// 0 - many identities with possibility to edit all params
// 1 - many identities with possibility to edit all params but not email address
// 2 - one identity with possibility to edit all params
// 3 - one identity with possibility to edit all params but not email address
// 4 - one identity with possibility to edit only signature
$rcmail_config['identities_level'] = 3;

// use this format for date display (date or strftime format)
$rcmail_config['date_format'] = 'd/m/Y';

// use this format for detailed date/time formatting (derived from date_format and time_format)
$rcmail_config['date_long'] = 'd/m/Y H:i';

// store draft message is this mailbox
// leave blank if draft messages should not be stored
// NOTE: Use folder names with namespace prefix (INBOX. on Courier-IMAP)
$rcmail_config['drafts_mbox'] = 'Drafts';

// store spam messages in this mailbox
// NOTE: Use folder names with namespace prefix (INBOX. on Courier-IMAP)
$rcmail_config['junk_mbox'] = 'Junk';

// store sent message is this mailbox
// leave blank if sent messages should not be stored
// NOTE: Use folder names with namespace prefix (INBOX. on Courier-IMAP)
$rcmail_config['sent_mbox'] = 'Sent';

// move messages to this folder when deleting them
// leave blank if they should be deleted directly
// NOTE: Use folder names with namespace prefix (INBOX. on Courier-IMAP)
$rcmail_config['trash_mbox'] = 'Trash';

// display these folders separately in the mailbox list.
// these folders will also be displayed with localized names
// NOTE: Use folder names with namespace prefix (INBOX. on Courier-IMAP)
$rcmail_config['default_folders'] = array();

// default messages sort column. Use empty value for default server's sorting,
// or 'arrival', 'date', 'subject', 'from', 'to', 'fromto', 'size', 'cc'
$config['message_sort_col'] = 'date';

// default messages sort order
$config['message_sort_order'] = 'DESC';

// These cols are shown in the message list. Available cols are:
// subject, from, to, fromto, cc, replyto, date, size, status, flag, attachment, 'priority'
$config['list_cols'] = array('attachment', 'labels', 'status', 'from', 'subject', 'date');

// This indicates which type of address book to use. Possible choises:
// 'sql' (default), 'ldap' and ''.
// If set to 'ldap' then it will look at using the first writable LDAP
// address book as the primary address book and it will not display the
// SQL address book in the 'Address Book' view.
// If set to '' then no address book will be displayed or only the
// addressbook which is created by a plugin (like CardDAV).
$rcmail_config['address_book_type'] = '';

// sort contacts by this col (preferably either one of name, firstname, surname)
$config['addressbook_sort_col'] = 'name';

/*
 * Config Fédérateur directory
*/
$rcmail_config['ldap_public']['amande'] = array(
		'name'          => 'Amande',
		// Replacement variables supported in host names:
		// %h - user's IMAP hostname
		// %n - hostname ($_SERVER['SERVER_NAME'])
		// %t - hostname without the first part
		// %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
		// %z - IMAP domain (IMAP hostname without the first part)
		// For example %n = mail.domain.tld, %t = domain.tld
		'hosts'         => array('ldapmaster-srv.mce.com'), 
		'port'          => 389,
		'use_tls'	=> false,
		'ldap_version'  => 3,       // using LDAPv3
		'network_timeout' => 10,    // The timeout (in seconds) for connect + bind arrempts. This is only supported in PHP >= 5.3.0 with OpenLDAP 2.x
		'user_specific' => false,   // If true the base_dn, bind_dn and bind_pass default to the user's IMAP login.
		// %fu - The full username provided, assumes the username is an email
		//       address, uses the username_domain value if not an email address.
		// %u  - The username prior to the '@'.
		// %d  - The domain name after the '@'.
		// %dc - The domain name hierarchal string e.g. "dc=test,dc=domain,dc=com"
		// %dn - DN found by ldap search when search_filter/search_base_dn are used
		'base_dn'         => 'ou=boites,ou=mce,o=gouv,c=fr',
		//'bind_dn'       => '',
		//'bind_pass'     => '',
		// It's possible to bind for an individual address book
		// The login name is used to search for the DN to bind with
		'search_base_dn' => '',
		'search_filter'  => '',   // e.g. '(&(objectClass=posixAccount)(uid=%u))'
		// DN and password to bind as before searching for bind DN, if anonymous search is not allowed
		'search_bind_dn' => '',
		'search_bind_pw' => '',
		// Default for %dn variable if search doesn't return DN value
		'search_dn_default' => '',
		// Optional authentication identifier to be used as SASL authorization proxy
		// bind_dn need to be empty
		'auth_cid'       => '',
		// SASL authentication method (for proxy auth), e.g. DIGEST-MD5
		'auth_method'    => '',
		// Indicates if the addressbook shall be hidden from the list.
		// With this option enabled you can still search/view contacts.
		'hidden'        => true,
		// Indicates if the addressbook shall not list contacts but only allows searching.
		'searchonly'    => true,
		// Indicates if we can write to the LDAP directory or not.
		// If writable is true then these fields need to be populated:
		// LDAP_Object_Classes, required_fields, LDAP_rdn
		'writable'       => false,
		// To create a new contact these are the object classes to specify
		// (or any other classes you wish to use).
		'LDAP_Object_Classes' => array('top', 'inetOrgPerson'),
		// The RDN field that is used for new entries, this field needs
		// to be one of the search_fields, the base of base_dn is appended
		// to the RDN to insert into the LDAP directory.
		'LDAP_rdn'       => 'cn',
		// The required fields needed to build a new contact as required by
		// the object classes (can include additional fields not required by the object classes).
		'required_fields' => array('cn', 'sn', 'uid', 'mail'),
		'search_fields'   => array('cn'),  // fields to search in
		// mapping of contact fields to directory attributes
		//   for every attribute one can specify the number of values (limit) allowed.
		//   default is 1, a wildcard * means unlimited
		'fieldmap' => array(
				// Roundcube  => LDAP:limit
				'username'    => 'uid',
				'name'        => 'cn',
				'fullname'    => 'cn',
				'surname'     => 'sn',
				'firstname'   => 'displayName',
				'email'       => 'mail',
		),
		// Map of contact sub-objects (attribute name => objectClass(es)), e.g. 'c' => 'country'
		'sub_fields' => array(),
		// Generate values for the following LDAP attributes automatically when creating a new record
		'autovalues' => array(),
		'sort'          => 'cn',    // The field to sort the listing by.
		'scope'         => 'sub',   // search mode: sub|base|list
		'filter'        => '',      // used for basic listing (if not empty) and will be &'d with search queries. example: status=act
		'fuzzy_search'  => true,    // server allows wildcard search
		'vlv'           => true,   // Enable Virtual List View to more efficiently fetch paginated data (if server supports it)
		'numsub_filter' => '(objectClass=organizationalUnit)',   // with VLV, we also use numSubOrdinates to query the total number of records. Set this filter to get all numSubOrdinates attributes for counting
		'sizelimit'     => '500',     // Enables you to limit the count of entries fetched. Setting this to 0 means no limit.
		'timelimit'     => '10',     // Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit.
		'referrals'     => true|false,  // Sets the LDAP_OPT_REFERRALS option. Mostly used in multi-domain Active Directory setups

);
$rcmail_config['ldap_public']['amande_group'] = array(
        'name'          => 'Amande Groupe',
        // Replacement variables supported in host names:
        // %h - user's IMAP hostname
        // %n - hostname ($_SERVER['SERVER_NAME'])
        // %t - hostname without the first part
        // %d - domain (http hostname $_SERVER['HTTP_HOST'] without the first part)
        // %z - IMAP domain (IMAP hostname without the first part)
        // For example %n = mail.domain.tld, %t = domain.tld
        'hosts'         => array('ldapmaster-srv.mce.com'),
        'port'          => 389,
        'use_tls'	=> false,
    	'ldap_version'  => 3,       // using LDAPv3
        'network_timeout' => 10,    // The timeout (in seconds) for connect + bind arrempts. This is only supported in PHP >= 5.3.0 with OpenLDAP 2.x
        'user_specific' => false,   // If true the base_dn, bind_dn and bind_pass default to the user's IMAP login.
        'base_dn'       => 'ou=boites,ou=mce,o=gouv,c=fr',
        // It's possible to bind for an individual address book
        // The login name is used to search for the DN to bind with
        'search_base_dn' => '',
    	'search_filter'  => '',   // e.g. '(&(objectClass=posixAccount)(uid=%u))'
    	// DN and password to bind as before searching for bind DN, if anonymous search is not allowed
    	'search_bind_dn' => '',
        'search_bind_pw' => '',
        // Default for %dn variable if search doesn't return DN value
        'search_dn_default' => '',
        // Optional authentication identifier to be used as SASL authorization proxy
        // bind_dn need to be empty
        'auth_cid'       => '',
        // SASL authentication method (for proxy auth), e.g. DIGEST-MD5
      	'auth_method'    => '',
      	// Indicates if the addressbook shall be hidden from the list.
        // With this option enabled you can still search/view contacts.
        'hidden'        => true,
        // Indicates if the addressbook shall not list contacts but only allows searching.
        'searchonly'    => true,
        // Indicates if we can write to the LDAP directory or not.
        // If writable is true then these fields need to be populated:
        // LDAP_Object_Classes, required_fields, LDAP_rdn
    	  'writable'       => false,
        // To create a new contact these are the object classes to specify
        // (or any other classes you wish to use).
        'LDAP_Object_Classes' => array('top', 'posixGroup'),
        // The RDN field that is used for new entries, this field needs
        // to be one of the search_fields, the base of base_dn is appended
        // to the RDN to insert into the LDAP directory.
        'LDAP_rdn'       => 'uid',
        // The required fields needed to build a new contact as required by
    	  // the object classes (can include additional fields not required by the object classes).
    	  'required_fields' => array('dn', 'cn', 'sn'),
        'search_fields'   => array('cn'),  // fields to search in
        // mapping of contact fields to directory attributes
        //   for every attribute one can specify the number of values (limit) allowed.
        //   default is 1, a wildcard * means unlimited
    	  'fieldmap' => array(
  	        // Roundcube  => LDAP:limit
    			  'name'        => 'cn',
  	        'surname'     => 'sn',
  	        'username'     => 'dn',
	          'members'   => 'members',
        ),
        // Map of contact sub-objects (attribute name => objectClass(es)), e.g. 'c' => 'country'
        'sub_fields' => array(),
        // Generate values for the following LDAP attributes automatically when creating a new record
        'autovalues' => array(),
        'sort'          => 'cn',    // The field to sort the listing by.
    	'scope'         => 'sub',   // search mode: sub|base|list
        'filter'        => '(&(objectClass=posixGroup)(!(mineqTypeEntree=NUNI)))',      // used for basic listing (if not empty) and will be &'d with search queries. example: status=act
    	  'fuzzy_search'  => true,    // server allows wildcard search
        'vlv'           => true,   // Enable Virtual List View to more efficiently fetch paginated data (if server supports it)
    	  'numsub_filter' => '(objectClass=organizationalUnit)',   // with VLV, we also use numSubOrdinates to query the total number of records. Set this filter to get all numSubOrdinates attributes for counting
        'sizelimit'     => '500',     // Enables you to limit the count of entries fetched. Setting this to 0 means no limit.
        'timelimit'     => '10',     // Sets the number of seconds how long is spend on the search. Setting this to 0 means no limit.
        'referrals'     => true|false,  // Sets the LDAP_OPT_REFERRALS option. Mostly used in multi-domain Active Directory setups

);

// An ordered array of the ids of the addressbooks that should be searched
// when populating address autocomplete fields server-side. ex: array('sql','Verisign');
$rcmail_config['autocomplete_addressbooks'] = array('amande');

// The minimum number of characters required to be typed in an autocomplete field
// before address books will be searched. Most useful for LDAP directories that
// may need to do lengthy results building given overly-broad searches
$rcmail_config['autocomplete_min_length'] = 3;

// Number of parallel autocomplete requests.
// If there's more than one address book, n parallel (async) requests will be created,
// where each request will search in one address book. By default (0), all address
// books are searched in one request.
$rcmail_config['autocomplete_threads'] = 2;

// Max. numer of entries in autocomplete popup. Default: 15.
$rcmail_config['autocomplete_max'] = 50;

// show address fields in this order
// available placeholders: {street}, {locality}, {zipcode}, {country}, {region}
$rcmail_config['address_template'] = '{street}<br/>{locality} {zipcode}<br/>{country} {region}';

// Matching mode for addressbook search (including autocompletion)
// 0 - partial (*abc*), default
// 1 - strict (abc)
// 2 - prefix (abc*)
// Note: For LDAP sources fuzzy_search must be enabled to use 'partial' or 'prefix' mode
$rcmail_config['addressbook_search_mode'] = 2;

// Defaults of the addressbook search field configuration.
$config['addressbook_search_mods'] = array('name'=>0, 'firstname'=>0, 'surname'=>0, 'email'=>0, '*'=>0);  // Example: array('name'=>1, 'firstname'=>1, 'surname'=>1, 'email'=>1, '*'=>1);

// default setting if preview pane is enabled
$rcmail_config['preview_pane'] = true;

// Mark as read when viewed in preview pane (delay in seconds)
// Set to -1 if messages in preview pane should not be marked as read
$rcmail_config['preview_pane_mark_read'] = -1;

// Encoding of long/non-ascii attachment names:
// 0 - Full RFC 2231 compatible
// 1 - RFC 2047 for 'name' and RFC 2231 for 'filename' parameter (Thunderbird's default)
// 2 - Full 2047 compatible
$rcmail_config['mime_param_folding'] = 0;

// Set true if deleted messages should not be displayed
// This will make the application run slower
$rcmail_config['skip_deleted'] = true;

// Default interval for auto-refresh requests (in seconds)
// These are requests for system state updates e.g. checking for new messages, etc.
// Setting it to 0 disables the feature.
$rcmail_config['refresh_interval'] = 60*30;

// Type of IMAP indexes cache. Supported values: 'db', 'apc' and 'memcache'.
$rcmail_config['imap_cache'] = 'memcache';

// Enables messages cache. Only 'db' cache is supported.
$rcmail_config['messages_cache'] = '';

// Lifetime of IMAP indexes cache. Possible units: s, m, h, d, w
$rcmail_config['imap_cache_ttl'] = '20d';

// Lifetime of messages cache. Possible units: s, m, h, d, w
$rcmail_config['messages_cache_ttl'] = '20d';

// Maximum cached message size in kilobytes.
// Note: On MySQL this should be less than (max_allowed_packet - 30%)
$rcmail_config['messages_cache_threshold'] = 150;

// don't let users set pagesize to more than this value if set
$rcmail_config['max_pagesize'] = 2000;

// use this folder to store temp files (must be writeable for apache user)
$rcmail_config['temp_dir'] = '/tmp';

// expire files in temp_dir after 48 hours
// possible units: s, m, h, d, w
$rcmail_config['temp_dir_ttl'] = '1h';

// Enables display of email address with name instead of a name (and address in title)
$rcmail_config['message_show_email'] = true;

// When replying:
// -1 - don't cite the original message
// 0  - place cursor below the original message
// 1  - place cursor above original message (top posting)
$rcmail_config['reply_mode'] = 1;

$rcmail_config['previewpane_layout'] = 'right';

$config['timezone'] = 'Europe/Paris';

