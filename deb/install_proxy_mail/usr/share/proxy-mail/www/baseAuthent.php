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

/*--------------------------------------------------
* Lecture du fichier de config à l'exterieur de la classe
$ config passée en parametre du constructeur
* par DINSIC
* aout 2017
**************************************************/


/*--------------------------------------------------
* Evolutions suite au modif de schema et d'algorithme décidés
* en pleiniere 07/2017
* pas DINSIC
* Juillet 2017
**************************************************/

/*--------------------------------------------------
* Ajout de la fonctionnalité d'authentification sur LDAP externe
* pas ministère MTES
* Mai 2017
**************************************************/

/** Classe baseAuthent
*
* Contient le comportement par defaut du processus d'authentification.
* Le constructeur charge sa configuration depuis le fichier ini.
* Pour parametrer le comportement, modifier le configFile.
* Pour modifier ce comportement et l'adapter, il convient d'intervenir
* dans la classe dérivee ministereAuthent.
*
*
* @package
* @category
* @author        DINSIC
* @link          http://
*/
abstract class baseAuthent
{

  //donnes chargées depuis le fichier de configuration
  //--------------------------------------------------
  public $myConfig = 0;
  // données chargées depuis le LDAP
  public $infoUser = 0;

  // identifiant de l'utilisateur sur le LDAP auth
  //--------------------------------------------------
  public $userIdAuth = "";
  public $userIdAuthDN = "";

  // uid de la mailbox utilisateur
  public $mbxId = "";

  //infos sur la mbx utilisateur
  public $infoMbx = "";

  // uid de la mailbox a laquelle on souhaite acceder (utilisateur ou partagee)
  public $mbxCibleId = "";

  //infos sur la mbx cible
  public $infoMbxCible = "";

  //mdp de la requete;
  public $userpass = "";

  //mdp transmis à cyrus
  public $cyrusUserPass="";


  // port;
  public $port="";

  public $protocol = "";

  //fichier de configuration
  //------------------------
  //  public $configFile = "ministere_config.ini";

  //Codes d'erreur
  //--------------
  public  $ERR_PROTOCOL_INVALIDE         = 1;
  public  $ERR_UID_MAL_FORME             = 2;
  public  $ERR_BAL_MAL_FORMEE            = 3;
  public  $ERR_IDENTIFIANT_INVALIDE      = 4;
  public  $ERR_CONNEXION_LDAP_IMPOSSIBLE = 5;
  public  $ERR_COMPTE_INEXISTANT         = 6;
  public  $ERR_BAD_NOM_DOMAINE           = 7;
  public  $ERR_NO_MAILHOST               = 8;
  public  $ERR_BAD_PRIVILEGE             = 9;
  public  $ERR_PARAMETRE_MANQUANT        = 99;

  public $TAB_ERREUR;
  //methodes (specialisables dans ministereAuthent)
  //-------------------------------------------------------------------------------

  /**
  * constructeur
  *
  */
  function __construct($myConfig)
  {
    //lecture du fichier de configuration
    // avec process_sections mis à true
    $this->myConfig = $myConfig;//parse_ini_file($this->configFile,true);

    $this->TAB_ERREUR[0] = "Pas d'erreur";
    $this->TAB_ERREUR[1] = "ERR_PROTOCOL_INVALIDE";
    $this->TAB_ERREUR[2] = "ERR_UID_MAL_FORME";
    $this->TAB_ERREUR[3] = "ERR_BAL_MAL_FORMEE";
    $this->TAB_ERREUR[4] = "ERR_IDENTIFIANT_INVALIDE";
    $this->TAB_ERREUR[5] = "ERR_CONNEXION_LDAP_IMPOSSIBLE";
    $this->TAB_ERREUR[6] = "ERR_COMPTE_INEXISTANT";
    $this->TAB_ERREUR[7] = "ERR_BAD_NOM_DOMAINE";
    $this->TAB_ERREUR[8] = "ERR_NO_MAILHOST";
    $this->TAB_ERREUR[9] = "ERR_BAD_PRIVILEGE";
    $this->TAB_ERREUR[99]= "ERR_PARAMETRE_MANQUANT";
  }

  /**
  * Initialisation à partir de l'identifiant de connexion.
  * L'identifiant peut s'interpreter de differentes manieres
  * en fonction du choix de fonctionnement
  * Cette fonction est abstraite
  */
  abstract function init($identifiant,$userpass,$port);


  /**
  * Vérifie si un compte est valide
  * Retourne vrai si le compte passe en parametre est ok
  *
  * @param string $compte
  * @return boolean true or false
  */
  function isCompteValide($compte)
  {
    $retour =  ( preg_match($this->myConfig['regex']['reguser'], $compte ));
    return $retour;
  }

  /**
  * Vérifie si ile mdp est en clair
  *
  * @param string $pass
  * @return boolean true or false
  */
  function isPwdClair($pass)
  {
    $reg = "/^\{[a-zA-Z][a-zA-Z0-9-]*\}/";
    $retour =  !( preg_match($reg, $pass ));
    return $retour;
  }

  /**
  * Vérifie si le mdp indique une verification SASL
  *
  * @param string $pass
  * @return boolean true or false
  */
  function isPwdSASL($pass)
  {
    $reg = "/^\{SASL}/";
    $retour =  ( preg_match($reg, $pass ));
    return $retour;
  }



  /**
  * Vérifie si une bal est valide
  * Retourne vrai si la bal passee en parametre est ok
  *
  * @param string $bal
  * @return boolean true or false
  */
  function isBalValide($bal)
  {
    $retour =  ( preg_match($this->myConfig['regex']['regbal'], $bal ) );
    return $retour;
  }


  /**
  * Test la requete d'authentification
  * Retourne un entier indiquant si la requete d'authentification reçue est valide
  *
  * @param string $username nom de l'utilisateur, = identifiant sauf en cas de boite fonctionnelle
  * @param string $balname  nom de la boite, = identifiant sauf en cas de boite fonctionnelle
  * @param string $pass
  * @return integer
  */
  abstract public function testRequete ();


  /**
  * Authentification de l'utilisateur
  * Retourne vrai si l'utilisateur est correctement authentifie ds l'annuaire d'authent.
  *
  * @retunn boolean true or false
  */
  public function authuser(&$error)
  {
    $retour = false;
    if ($this->userIdAuth==0){
      if ($this->infoMbx!=""){
        $this->userIdAuth = $this->infoMbx[0]["mceuseridauth"][0];
      }
      if ($this->userIdAuth!=""){
        //connection a l'annuaire d'authentification
        $conn=ldap_connect($this->myConfig['serveurs']['ldap_auth_server']);
        if (! $conn)
        {
          $this->flog(LOG_ERR,"Authentification LDAP fail to connect server : ".$this->myConfig['serveurs']['ldap_auth_server']);
          $error = ERR_CONNEXION_LDAP_IMPOSSIBLE;
        }
        else {
          ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->myConfig['versions']['ldap_auth_version']);
          if(strlen($this->userIdAuthDN) > 0){
            $userdn=$this->userIdAuthDN;
          }else{
            $userdn=sprintf($this->myConfig['filters']['ldap_binduser'],$this->userIdAuth);
          }

          // $this->flog("User dn: ".$userdn);
          $bind=ldap_bind($conn,$userdn,$this->userpass);
          if (! $bind)
          {
            $server = $this->myConfig['serveurs']['ldap_auth_server'];
            $erreurmessage = ldap_error($conn);
            $this->flog(LOG_ERR,"Auth FAIL : LDAP failed to authenticate user : $this->userIdAuth");
            $this->flog(LOG_ERR,"Auth FAIL : LDAP failed to authenticate userdn : $userdn");
            //$this->flog("Auth FAIL : LDAP failed to authenticate server : $server");
            //$this->flog("Auth FAIL : LDAP failed to authenticate erreur : $erreurmessage");

            $error = $this->ERR_IDENTIFIANT_INVALIDE;
          }
          else {
            $retour = true;
          }
        }
      }
    }
    return $retour;
  }

  /**
  * Recupere le mailHost de l'utilisateur
  * Retourne le mailhost
  *
  * @param array $info
  * @return string chaine de caracteres representant le mailhost
  */
  public function getMailHost($info){
    return $info[0]["mailhost"][0];
  }

  /**
  * Recupère le port associe au protocol
  *
  * @param string $protocol,
  * @param string $error
  * @return integer, le numero de port
  */
  public function getPort()
  {
    $retour = 0;
    if ( isset($this->protocol) && isset($this->myConfig['ports']['port'][$this->protocol]) )
    $retour = $this->myConfig['ports']['port'][$this->protocol];
    else
    $error = $this->ERR_PROTOCOL_INVALIDE;
    $this->port =$retour;
    return $retour;
  }

  /**
  * Renvoit le nom d'hote du serveur de mail en fonction
  * du mailhost de l'utilisateur
  *
  * @param string $mailhost,
  * @return string, chaine de caracteres representant le serveur
  */
  public function getMailServer($mailhost)
  {
    return $this->myConfig['backend']['backend_server'][$mailhost];
  }

  /**
  * Obtention d'informations sur le compte
  * de la mbx
  * @param string $error
  * @return array
  */
  public function getInfosMbx(&$error)
  {
    $retour = $this->infoMbx;
    if ($retour=="")
    {
      $retour = $this->getInfos($this->mbxId,$error);
      $this->infoMbx=$retour;
    }
    if ($this->userIdAuth!= "" && $error == 0)
    $this->userIdAuth = $this->infoMbx[0]["mceuseridauth"][0];

    //recup du mdp de l'annuaire technique, passage à cyrus
    if (isset($this->infoMbx[0]["userpassword"][0]))
    //si le mot de passe est en clair ds l'annuaire technique on le passe à cyrus
    if ($this->isPwdClair($this->infoMbx[0]["userpassword"][0]))
    $this->cyrusUserPass = $this->infoMbx[0]["userpassword"][0];
    //si le mot de passe indique une verification SASL, on passe à cyrus le mot de passe transmis dans la requete
    else //if ($this->isPwdSASL($this->infoMbx[0]["userpassword"][0]))
    $this->cyrusUserPass = $this->userpass;
    return $retour;
  }

  /**
  * Obtention d'informations sur le compte
  * de la mbx cible
  * @param string $error
  * @return array
  */
  public function getInfosMbxCible(&$error)
  {
    $retour = $this->infoMbxCible;
    if ($retour==""){
      $retour = $this->getInfos($this->mbxCibleId,$error);
      $this->infoMbxCible=$retour;
    }
    if ($this->isPwdClair($this->infoMbxCible[0]["userpassword"][0]))
    $this->cyrusUserPass = $this->infoMbxCible[0]["userpassword"][0];
    elseif ($this->isPwdSASL($this->infoMbxCible[0]["userpassword"][0])) {
      $json_pass =  ['uid'=>$this->mbxId,'password'=>$this->userpass];
      $this->cyrusUserPass = json_encode($json_pass);
    }

    return $retour;
  }


  /**
  * Obtention d'informations sur le compte
  *
  * @param string $compte
  * @param string $error
  * @return array
  */
  public function getFilteredInfos($compte,$filter,&$error)
  {

    $infos=0;

    //Connection a l'annuaire technique
    $conn=ldap_connect($this->myConfig['serveurs']['ldap_tech_server']);
    if (! $conn)
    {
      $this->flog(LOG_ERR,"GetInfo LDAP fail to connect server : ".$this->myConfig['serveurs']['ldap_tech_server']);
      $error = $this->ERR_CONNEXION_LDAP_IMPOSSIBLE;
    }
    else {
      ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->myConfig['versions']['ldap_tech_version']);
      if(strlen($this->myConfig['connexion']['ldap_bindDN']) > 0){
        $bind=ldap_bind($conn,$this->myConfig['connexion']['ldap_bindDN'],$this->myConfig['connexion']['ldap_bindpwd']);
      }else{
        $bind=ldap_bind($conn);
      }
      if (! $bind)
      {
        $this->flog(LOG_ERR,"GetInfo LDAP fail to bind server : ".$this->myConfig['serveurs']['ldap_tech_server']);
        $error = $this->ERR_CONNEXION_LDAP_IMPOSSIBLE;
      }
      else {
        $query=sprintf($filter,$compte);
        // $this->flog($query);
        $result=ldap_search($conn, $this->myConfig['connexion']['ldap_baseDN'], $query);
        $info = ldap_get_entries($conn, $result);

        if ($info["count"] >= 1)
        {
          $infos = $info;
        } else $error = $this->ERR_COMPTE_INEXISTANT;
      }
    }
    return $infos;
  }


  /**
  * Obtention d'informations sur le compte
  *
  * @param string $compte
  * @param string $error
  * @return array
  */
  public function getInfos($compte,&$error)
  {
    return $this->getFilteredInfos($compte,$this->myConfig['filters']['ldap_tech_filter'],$error);
  }

  /**
  * Obtention d'informations sur le compte
  * à partir de l'id rh du compte
  * @param string $compte
  * @param string $error
  * @return array
  */
  public function getInfosFromRhId($compte,&$error)
  {
    return $this->getFilteredInfos($compte,$this->myConfig['filters']['ldap_tech_filter'],$error);
  }


  /**
  * Verification des infos du compte
  *
  * @param string $username,
  * @param array $infosUser,
  * @param string &$error
  * @return boolean true or false
  */
  public function verifyInfosUser($username,$infosUser,&$error)
  {

    $retour = true;
    if (!isset($infosUser[0]["mailhost"][0])){
      $error = $this->ERR_NO_MAILHOST;
      $retour = false;
    }
    if ($retour){
      if (!isset($infosUser[0]["mcedomain"])){
        $error = $this->ERR_BAD_NOM_DOMAINE;
        $retour = false;
      }
      else{
        $maildomain = $infosUser[0]["mcedomain"][0];
        //verification que le domaine est couvert
        //---------------------------------------
        $tabDomain = $this->myConfig['domaine']['ldap_domain'];
        $fini = count($tabDomain)==0;
        $trouve = false;
        $cpt=0;
        while (!$fini){
          if ($maildomain == $tabDomain[$cpt]){
            $fini=true;
            $trouve=true;
          }
          else {
            $cpt++;
            $fini = ($cpt == count($tabDomain));
          }
        }
        if (!$trouve){
          $error = $this->ERR_BAD_NOM_DOMAINE;
          $retour = false;
        }else{
          // si une méthode d'authentification est défini, on récupère les infos
          if(isset($infosUser[0]["mcemethodeauth"])){
            $this->flog(LOG_DEBUG,"MCEUserIdAuth : ".$infosUser[0]["mceuseridauth"][0]);
            $this->setInfoUserAuth($infosUser[0]["mcemethodeauth"][0],$infosUser[0]["mceuseridauth"][0]);
          }
        }
      }
    }
    return $retour;
  }

  /**********************************
  * Mise a jour des informations d'authentification si besoin
  *
  * @param  methodeauth information sur le serveur d'authentification
  * @param  useridauth identifiant de l'utilisateur sur ce serveur
  *
  **********************************/
  public function setInfoUserAuth($methodeauth,$userIdAuth){

    $conn=ldap_connect($this->myConfig['serveurs']['ldap_tech_server']);
    if (! $conn)
    {
      $this->flog(LOG_ERR,"SetInfo LDAP fail to connect server : ".$this->myConfig['serveurs']['ldap_auth_server']);
      $error = $this->ERR_CONNEXION_LDAP_IMPOSSIBLE;
    }
    else {
      ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->myConfig['versions']['ldap_tech_version']);
      $bind=ldap_bind($conn,$this->myConfig['connexion']['ldap_bindDN'],$this->myConfig['connexion']['ldap_bindpwd']);
      if (! $bind)
      {
        $this->flog(LOG_ERR,"SetInfo LDAP fail to bind server : ".$this->myConfig['serveurs']['ldap_auth_server']);
        $error = $this->ERR_CONNEXION_LDAP_IMPOSSIBLE;
      }else{
        $query=sprintf("(MCEMethodeAuth=%s)",$methodeauth);
        // récupération du labeledURI qui contient le serveur, le baseDN de recherche de l'utilisateur et l'attribut a récupérer
        $result=ldap_search($conn,"ou=authentifications,ou=ressources,ou=mce,o=gouv,c=fr" , $query);
        $info = ldap_get_entries($conn, $result);

        if ($info["count"] >= 1)
        {
          $infos = $info;

          $URL = $infos[0]["labeleduri"][0];
          preg_match('/^(?P<type>\w+:\/\/)(?P<serveur>[\w\.\d-]*)\/(?P<baseDN>[\w\=,]*).*\((?P<filtre>.*)\)/',$URL, $matches);
          // ldaps://ldap.m2.e2.rie.gouv.fr/ou=organisation,dc=equipement,dc=gouv,dc=fr?dn?sub?(uid=%s)
          $this->flog(LOG_DEBUG,"DEBUG URL : ".$URL);
          $this->flog(LOG_DEBUG,"DEBUG SERVEUR : ".$matches['serveur']);
          $this->flog(LOG_DEBUG,"DEBUG baseDN : ".$matches['baseDN']);
          $this->flog(LOG_DEBUG,"DEBUG filtre : ".$matches['filtre']);

          $this->myConfig['serveurs']['ldap_auth_server'] = $matches['serveur'];
          ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);

          $ldapauth = ldap_connect($matches['type'].$matches['serveur']) or die("Impossible de se connecter au serveur LDAP.");
          if ($ldapauth) {
            if(ldap_set_option($ldapauth, LDAP_OPT_PROTOCOL_VERSION, 3)){
              $this->flog(LOG_DEBUG,"DEBUG version LDAP 3 OK");
            }else{
              $this->flog(LOG_ERR,"DEBUG version LDAP 3 ERREUR");
            }
            // Authentification anonyme
            $ldapbindauth = ldap_bind($ldapauth);
            // récupération du DN de l'utilisateur
            if($ldapbindauth){
              $sr=ldap_search($ldapauth, $matches['baseDN'], "(uid=$userIdAuth)", array("dn"));
              $info = ldap_get_entries($ldapauth, $sr);
              if (!isset($info[0])){
                $error = $this->ERR_COMPTE_INEXISTANT;
                $this->flog(LOG_ERR,"DEBUG compte inexistant dans l'annuaire d'authentification");
              }
              else{
                $this->flog(LOG_DEBUG,"DEBUG dn : ".$info[0]["dn"]);
                $this->myConfig['filters']['ldap_binduser'] = $info[0]["dn"];
                $this->myConfig['serveurs']['ldap_auth_server'] = $matches['type'].$matches['serveur'];
              }
            }else{
              $erreurmessage = ldap_error($ldapauth);
              $this->flog(LOG_ERR,"DEBUG erreur d'authentification au serveur : ".$erreurmessage);
            }
          }else{
            $this->flog(LOG_ERR,"DEBUG erreur de connection au serveur : ".$this->myConfig['serveurs']['ldap_auth_server']);
          }

        } else $error = $this->ERR_COMPTE_INEXISTANT;
      }
    }
  }


  /**
  * Verification des infos de la bal
  *
  * @param string &$error
  * @return boolean true or false
  */
  public function verifyInfosBal(&$error)
  {
    $retour = true;
    if (!isset($this->infoMbxCible[0]["mcedelegation"])){
      // si le compte n'appartient pas a la bal, retourner ERR_BAD_PRIVILEGE
      $retour = false;
      $error = $this->ERR_BAD_PRIVILEGE;
    }
    else{
      //recherche du user
      $cpt=0;
      $trouve=false;
      $nbDeleg=$this->infoMbxCible[0]["mcedelegation"]['count'];

      $fini=($nbDeleg==0);
      while (!$fini){
        $monUser= $this->infoMbxCible[0]["mcedelegation"][$cpt];
        $tabUser = explode( $this->myConfig['separateurs']['separUserDroits'], $monUser);
        if (isset($tabUser[1])) $monUser = $tabUser[0];

        if ($this->userIdAuth==$monUser){
          $fini=true;
          $trouve=true;
        }
        else{
          $cpt++;
          $fini=($nbDeleg==$cpt);
        }
      }
      $retour = $trouve;
      if (!$retour) $error = $this->ERR_BAD_PRIVILEGE;
    }
    return $retour;
  }

  /**
  *
  * Log dans un fichier les traces de l'exécution
  *
  * @param string $message
  * @return void
  */
  public function flog($level,$message)
  {
		if (isset($this->myConfig['trace']['loglevel']))
    {
      $date = date("Y-m-d H:i");
      if($level < $this->myConfig['trace']['loglevel'])
      {
        if($this->myConfig['trace']['logtype'] == "file")
        {
          file_put_contents($this->myConfig['trace']['logfile'],$date." ".$this->userIdAuth."[".getmypid()."]"." : ".$message."\n", FILE_APPEND);
        }else{
          openlog("NginxProxy", LOG_PID | LOG_PERROR, LOG_MAIL);
          syslog($level,$date." ".$this->userIdAuth." : ".$message."\n");
          closelog();
        }
      }
    }
  }

  /**
  * Construction du header de retour en cas d'echec de l'authentification
  *
  * @param string $msg
  * @return void
  */
  public function fail($msg='')
  {

    $this->flog(LOG_INFO,"Auth FAIL : $msg  IP:". $_SERVER["HTTP_CLIENT_IP"]." p:".$_SERVER["HTTP_AUTH_PROTOCOL"]);

    header("Auth-Status: Invalid login or password");
    #header("Auth-Server: 127.0.0.1");
    #header("Auth-Port: 143");
    #header("Auth-Wait: 5");

    if ($msg) {
      header("Auth-Message: $msg");
    }
  }

  /**
  * Construction du header de retour en cas de succes de l'authentification
  *
  * @param string $user
  * @param string $server
  * @param string $port
  * @return void
  */
  function pass($user,$server,$port,$password)
  {
    $this->flog(LOG_INFO,"Auth OK,user : $user server $server IP:". $_SERVER["HTTP_CLIENT_IP"]. " p:".$_SERVER["HTTP_AUTH_PROTOCOL"] ." port:" .$port);
    header("Auth-Status: OK");
    header("Auth-Server: $server");
    header("Auth-Port: $port");
    header("Auth-User: $user");
    // header("Auth-Pass: $password");
  }

}
