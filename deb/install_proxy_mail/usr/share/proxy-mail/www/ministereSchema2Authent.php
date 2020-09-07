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
include_once "baseAuthent.php";
//--------------------------------------------------
/** Classe ministereAuthent
*
* Permet de specialiser le comportement de l'authentification.
* Derive de baseAuthent qui decrit le comportement par défaut.
*
* Particularité du  schema 2:
*   Dans ce schema l'identifiant de connexion contient
*   l'id de l'entree  dans l'annuaire authent
*   +
*    l'id de l'entree dans l'annaire technique , qu'il s'agisse d'un acces bal ou balf
* @package
* @category
* @author        DINSIC
* @link          http://
*/
class ministereSchema2Authent extends baseAuthent {
  /**
  * Initialisation à partir de l'identifiant de connexion.
  * L'identifiant contient soit l'id de la mbx utilisateur, eventuellement
  * completé de l'id d'une mbx partagée
  */
  function init($identifiant,$userpass,$prot) {
    $codeErreur=0;
    $this->userpass=$userpass;
    $this->protocol = $prot;
    $tabUser = explode( $this->myConfig['separateurs']['separUserBal'], $identifiant);
    $this->userpass = $userpass;
    //si double entree
    if (isset($tabUser[1])){
      $this->userIdAuth=$tabUser[0];
      $this->mbxCibleId = $tabUser[1];
      $infoUser = $this->getInfosFromRhId($this->userIdAuth,$codeErreur);
      if ($codeErreur ==0){
        $this->mbxId = $infoUser[0]["uid"][0];
        $this->infoMbx = $infoUser;
      }
    }
    else {
      $codeErreur=$this->ERR_UID_MAL_FORME;//1;//$this->ERR_UID_MAL_FORME;
    }
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
    if (!isset($this->userIdAuth) || !isset($this->userpass)){
      $retour = $this->ERR_PARAMETRE_MANQUANT;
    }else if (!$this->isCompteValide($this->userIdAuth)){
      $retour = $this->ERR_UID_MAL_FORME;
    }else if (!$this->isBalValide($this->mbxId)){
      $retour = $this->ERR_BAL_MAL_FORMEE;
    }
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
        $retour = true;
      } else {
        $retour = false;
      }
    }else {
      $retour=false;
    }
    return $retour;
  }
}
?>
