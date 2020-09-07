<?php

/**
*  Projet MCE
*
* @package
* @author        DINSIC
* @coyright      2016 - Dinsic
* @license       DINSIC
* @link          http://
* @since         version 1.1
* @filesource
*/


//--------------------------------------------------

include "ministereSchema1Authent.php";
include "ministereSchema2Authent.php";
include "ministereSchema3Authent.php";


//--------------------------------------------------

/**
* Script d'authentification MCE
*
* Squelette commun d'authentification MCE.
* Les parametres sont dans le fichier de configuration ministere_config.ini
* On y trouve:
* - les parametres lies a l'infra (noms de serveurs ldap ...)
* - les parametres de fonctionnement (champ de separateur user/balf, ...)
*
* Le squelette s'appuie sur les methodes de la classe baseAuthent (cf baseAuthent.php)
* Toutes les methodes sont specialisables dans la classe derivee ministereAuthent (cf ministereAuthent.php)
*
* Pour adapter le processus d'authentification a un ministere:
*    - la modification du parametrage s'effectue par modification du fichier de configuration
*    - les modifications d'algorithme s'effectuent par specialisation des methodes de
*      baseAuthent dans ministereAuthent.
*
* @package
* @category
* @author        DINSIC
* @link          http://
*/


//--------------------------------------------------

date_default_timezone_set('Europe/Paris');
$error = 0;
$myConfig = parse_ini_file('ministere_config.ini',true);

if(isset($myConfig['serveurs']['schema'])){
  $myAuthent = new $myConfig['serveurs']['schema']($myConfig);
}else{
  $error = 99;
}

if (!$error) {

  $infosUser = 0;
  $infoBal = 0;

  $mailhost=0;
  $prot = 0;
  //Recuperation des elements de la requete d'authentification
  //----------------------------------------------------------

  $identifiant= trim(rawurldecode($_SERVER["HTTP_AUTH_USER"]));//$_GET['nom']
  $userpass=$_SERVER["HTTP_AUTH_PASS"];//$_GET['pass']
  $protocol=$_SERVER["HTTP_AUTH_PROTOCOL"];//$_GET['protocol']

  $codeErreur=$myAuthent->init($identifiant,$userpass,$protocol);
  if ($codeErreur){
    $myAuthent->fail('Requête invalide '.$identifiant.'-'.$protocol);
    $error=$codeErreur;
  }
  $myAuthent->flog(LOG_DEBUG,'DN utilisateur : '.$myAuthent->userIdAuthDN );
  //    $username=$identifiant;
  //    $balname = $identifiant;


  //on verifie que la requete est valide
  //------------------------------------
  $requeteInvalide = $myAuthent->testRequete();
  if ($requeteInvalide){
    $myAuthent->fail('Requête invalide : '.$myAuthent->mbxId);
    $error = $requeteInvalide;
  }
}
if (!$error) {
  //on verifie que le protocol demandé est valide
  //---------------------------------------------
  $port = $myAuthent->getPort();
  if (!$port){
    $myAuthent->fail("Protocole invalide");
  }
}


if (!$error) {
  //on recupere les infos utilisateur dans l'annuaire technique
  //-----------------------------------------------------------
  $infosUser = $myAuthent->getInfosMbx($error);
  // $myAuthent->flog('InfoUser : '.print_r($infosUser,true));
  if ($error){
    $myAuthent->fail($myAuthent->mbxId .', impossible de recuperer les infos user');
  }else{
    //on verifie la validite des infos utilisateur
    //--------------------------------------------
    if (!$myAuthent->verifyInfosUser($myAuthent->mbxId,$infosUser,$error)){
      $myAuthent->fail($myAuthent->mbxId .'-'.$myConfig['attribut']['mailhost'].', compte utilisateur mal configure');
    }else{
      $mailhost = $myAuthent->getMailHost($infosUser);
      $myAuthent->flog(LOG_DEBUG,'Mail host : '.$mailhost);
    }
  }
}

if (!$error) {
  //on authentifie l'utilisateur sur l'annuaire d'authentification
  //--------------------------------------------------------------
  $myAuthent->flog(LOG_DEBUG,'DN utilisateur : '.$myAuthent->userIdAuthDN );
  if (!$myAuthent->authuser($error)) {
    $myAuthent->fail('DN utilisateur : '.$myAuthent->userIdAuthDN .' invalide');
  }
}

if (!$error) {
  //cas d'une boite partagee
  //------------------------
  if ($myAuthent->mbxCibleId!="" && $myAuthent->mbxId!= $myAuthent->mbxCibleId) {
    //on recupere les infos de la bal partagee
    //----------------------------------------
    $infosBal = $myAuthent->getInfosMbxCible($error);
    if ($error)
    $myAuthent->fail($myAuthent->mbxCibleId .', impossible de recuperer les infos bal');
    else
    //on verifie la validite des infos de la bal
    //------------------------------------------
    if (!$myAuthent->verifyInfosBal($error))
    $myAuthent->fail($myAuthent->mbxCibleId .', bal mal configure');
    else $mailhost = $myAuthent->getMailHost($myAuthent->infoMbxCible);
  }
}
if (!$error) {
  //Verification du mailhost
  //------------------------
  if (!$myAuthent->verifyMailhost($mailhost,$error)){
    $myAuthent->fail($mailhost .', Probleme avec le mailhost');
  }
}

if (!$error) $myAuthent->pass($myAuthent->userIdAuth, $mailhost, $myAuthent->port, $myAuthent->cyrusUserPass);
else $myAuthent->fail('Code Erreur:  '. $myAuthent->TAB_ERREUR[$error]);

?>
