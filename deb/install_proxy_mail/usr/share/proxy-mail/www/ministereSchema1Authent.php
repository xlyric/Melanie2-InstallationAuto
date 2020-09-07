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
 * Particularité du  schema 1:
 *   Dans ce schema l'identifiant de connexion contient
 *   l'id de l'entree  dans l'annuaire technique
 *   OU
 *    l'id de l'entree user dans l'annaire technique + l'id de la boite partagee,
 *    ds le cas d'un acces à une boite partagée
 * @package
 * @category
 * @author        DINSIC
 * @link          http://
*/

class ministereSchema1Authent extends baseAuthent {

 /**
  * Initialisation à partir de l'identifiant de connexion.
  * L'identifiant contient soit l'id de la mbx utilisateur, eventuellement
  * completé de l'id d'une mbx partagée
  */

  function init($identifiant,$userpass,$prot) {
      $codeErreur = 0;
      $this->userpass=$userpass;
      $this->protocol = $prot;
      $tabUser = explode( $this->myConfig['separateurs']['separUserBal'], $identifiant);
      $this->userpass = $userpass;
      //si double entree
      if (isset($tabUser[1])){
        $this->mbxId = $tabUser[0];
        $this->mbxCibleId = $tabUser[1];
      } else {
        $this->mbxId=$identifiant;
        $this->mbxCibleId = $identifiant;
      }
      return $codeErreur;
  }

  /**
   * Test la requete d'authentification
   * Retourne un entier indiquant si la requete d'authentification reçue est valide
   *
   * @return integer
   */
  public function testRequete ()
  {
    $retour = 0;
    if (!isset($this->mbxCibleId) || !isset($this->userpass))
        $retour = $this->ERR_PARAMETRE_MANQUANT;
    else if (!$this->isBalValide($this->mbxId))
        $retour = $this->ERR_BAL_MAL_FORMEE;
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
