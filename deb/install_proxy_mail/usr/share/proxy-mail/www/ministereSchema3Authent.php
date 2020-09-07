<?php
/**
*  Projet MCE
*
* @package
* @author        MTES
* @coyright      2019 - MTES
* @license       MTES
* @link          http://
* @since         version 1.0
* @filesource
*/
//--------------------------------------------------

include_once "baseAuthent.php";

//--------------------------------------------------

/** Classe ministereAuthent
*
* Permet de specialiser le comportement de l'authentification.
* Derive de baseAuthent qui decrit le comportement par défaut.
*
* Particularité du  schema 3:
*   Dans ce schema l'identifiant de connexion contient
*   l'id de l'entree  dans l'annuaire authent
*
*
*/

class ministereSchema3Authent extends baseAuthent {
  /**
  * Initialisation à partir de l'identifiant de connexion.
  * L'identifiant contient l'id du compte utilisateur,
  */
  function init($identifiant,$userpass,$prot) {
    $codeErreur=0;
    $this->userpass=$userpass;
    $this->protocol = $prot;
    $this->userIdAuth=$identifiant;

    // récupérer l'adresse mail
    $this->infoUser = $this->getInfosFromRhId($this->userIdAuth,$codeErreur);
    if(isset($this->infoUser[0][$this->myConfig["attribut"]["mail"]][0])){
        $this->mbxCibleId = $this->infoUser[0][$this->myConfig["attribut"]["mail"]][0];
        $this->mbxId = $this->infoUser[0][$this->myConfig["attribut"]["mail"]][0];
    }else{
        $this->mbxCibleId = "";
        $this->mbxId = "";
    }
    $this->userIdAuthDN = $this->infoUser[0]["dn"];
    return $codeErreur;
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
  public function testRequete ()
  {
    $retour = 0;
    if (!isset($this->userIdAuth) || !isset($this->userpass))
    $retour = $this->ERR_PARAMETRE_MANQUANT;
    /* else if (!$this->isCompteValide($this->userIdAuth))
    $retour = $this->ERR_UID_MAL_FORME;
    else if (!$this->isBalValide($this->mbxId))
    $retour = $this->ERR_BAL_MAL_FORMEE;*/
    return $retour;
  }
  /**
  * Verification sur le mailhost
  * Le nom du serveur de mail est recupere dans la configuration en fonction
  * du mailhost de l'utilisateur passé en parametre
  * Si le nom du serveur de mail est identique au mailhost de l'utilisateur,
  * on recupere l'ip V4 correspondant au hote via gethostbyname qu'on passe en
  * reference a la variable mailhost
  * la fonction retourne true si le mailhost correspond. false dans le cas contraire.
  *
  * @param string  &$mailhost,
  * @param string &$error
  * @return boolean true or false
  */
  public function verifyMailhost(&$mailhost,&$error)
  {
    $retour = false;
    if ( isset( $mailhost) ) {
      $mailserver = $this->getMailServer($mailhost);
      if ( isset($mailserver) && ($mailserver == $mailhost) ) {
        $backend_ip = gethostbyname($mailserver);
        $mailhost=(isset($backend_ip))?$backend_ip :$mailserver;
        $this->flog(LOG_DEBUG,"Mail host : ".$mailhost);
        $retour = true;
      } else {
        $this->fail("Mail host incohérent : ".$mailserver." : ". $mailhost);
        $error = 8;
        $retour = false;
      }
    }else {
      $this->fail("Pas de mail host");
      $error = 8;
      $retour=false;
    }
    return $retour;
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
    if (!isset($infosUser[0][$this->myConfig["attribut"]["mailhost"]][0])){
      $error = $this->ERR_NO_MAILHOST;
      $retour = false;
    }
    return $retour;
  }

  /**
  * Recupere le mailhost de l'utilisateur
  * Retourne le mailhost
  *
  * @param array $info
  * @return string chaine de caracteres representant le mailhost
  */
  public function getMailHost($info){
    foreach ($info[0][$this->myConfig["attribut"]["mailhost"]] as $value) {
      $tmpmailhost = explode('@',$value);
      if(count($tmpmailhost)>1 && in_array($tmpmailhost[1],$this->myConfig['backend']['backend_server'])){
        $mailhost = $tmpmailhost[1];
      }
    }
    return $mailhost;
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
      if($this->infoUser == 0){$this->infoUser = $this->getInfos($this->userIdAuth,$error);}

      $this->infoMbx=$this->infoUser;

      if(strstr($this->userIdAuth,$this->myConfig["separateurs"]["separUserBal"])){// si l'uid contient ".-." on découpe et on récupère uniquement mineqmelroutage
        $decoupe = explode($this->myConfig["separateurs"]["separUserBal"],$this->userIdAuth);
        $info_bal = $this->getInfos($decoupe[1],$error);
        $this->infoMbx[0][$this->myConfig["attribut"]["mailhost"]] = $info_bal[0][$this->myConfig["attribut"]["mailhost"]];
        $this->infoMbx[0][$this->myConfig["attribut"]["mail"]][0] = $info_bal[0][$this->myConfig["attribut"]["mail"]][0];
      }

    }
    $this->cyrusUserPass = $this->userpass;
    return $this->infoMbx;
  }

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

      if ($this->userIdAuth!=""){
        //connection a l'annuaire d'authentification
        $conn=ldap_connect($this->myConfig['serveurs']['ldap_auth_server']);
        if (! $conn)
        {
          $this->flog(LOG_ERR,"Authentification LDAP fail to connect server : ".$this->myConfig['serveurs']['ldap_auth_server']);
          $error = ERR_CONNEXION_LDAP_IMPOSSIBLE;
        }else {
          ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, $this->myConfig['versions']['ldap_auth_version']);
          if(strlen($this->userIdAuthDN) > 0){
            $userdn=$this->userIdAuthDN;
          }else{
            $userdn=sprintf($this->myConfig['filters']['ldap_binduser'],$this->userIdAuth);
          }

          $bind=ldap_bind($conn,$userdn,rawurldecode($this->userpass));
          if (! $bind)
          {
            $server = $this->myConfig['serveurs']['ldap_auth_server'];
            $erreurmessage = ldap_error($conn);
            $this->flog(LOG_ERR,"Auth FAIL : LDAP failed to authenticate : $userdn | message : $erreurmessage");

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
  }
}
?>
