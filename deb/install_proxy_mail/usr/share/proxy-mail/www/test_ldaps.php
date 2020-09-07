<?php

//  Acces sur le port securise 636

$ldaphost = "ldaps://%%LDAPSERVER%%";
$ldapUsername  = "cn=ldapmaster,o=gouv,c=fr";
$ldapPassword = "%%LDAPSERVERPASS%%";


$ds = ldap_connect($ldaphost);

if(!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)){
   print "Probleme de config  LDAPv3\r\n";
}
else {
   // connexion au serveur
   $bth = ldap_bind($ds, $ldapUsername, $ldapPassword) or die("\r\nImpossible de se connecter au serveur\r\n");

   if ($bth) echo "Connexion ldaps OK\r\n";
}
?>
