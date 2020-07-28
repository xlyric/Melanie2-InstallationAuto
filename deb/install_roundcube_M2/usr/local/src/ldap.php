<?php
/**
 * Ce fichier est développé pour la gestion de la librairie Mélanie2
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM M2 Copyright © 2017  PNE Annuaire et Messagerie/MEDDE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace LibMelanie\Config;

/**
 * Configuration de l'application LDAP pour Melanie2
 *
 * @author PNE Messagerie/Apitech
 * @package Librairie Mélanie2
 * @subpackage Config
 */
class Ldap {
    /**
     * Configuration du choix de serveur utilisé pour l'authentification
     * @var string
     */
    public static $AUTH_LDAP = "ldap.test";
    /**
     * Configuration du choix de serveur utilisé pour la recherche dans l'annuaire
     * @var string
     */
    public static $SEARCH_LDAP = "ldap.test";
    /**
     * Configuration du choix de serveur utilisé pour l'autocomplétion
     * @var string
     */
    public static $AUTOCOMPLETE_LDAP = "ldap.test";
    /**
     * Configuration du choix de serveur maitre, utilisé pour l'écriture
     * @var string
     */
    public static $MASTER_LDAP = "ldap.test";

    /**
     * Configuration des serveurs LDAP
     * Chaque clé indique le nom du serveur ldap et sa configuration de connexion
     * hostname, port, dn
     * informations
     */

 public static $SERVERS = array(
            /* Serveur LDAP IDA de test */
            "ldap.test" => array(
                    /* Host vers le serveur d'annuaire, précédé par ldaps:// si connexion SSL */
                    "hostname" => "ldap://ldap.test",
                    /* Port de connexion au LDAP */
                    "port" => 389,
                    /* Base DN de recherche */
                    "base_dn" => "ou=boites,ou=mce,o=gouv,c=fr",
                    /* Base de recherche pour les objets de partage */
                    "shared_base_dn" => "ou=boites,ou=mce,o=gouv,c=fr",
                    /* Version du protocole LDAP */
                    "version" => 3,
                    /* Connexion TLS */
                    "tls" => false,
          // Configuration des attributs et filtres de recherche
          // Filtre de recherche pour la méthode get user infos
          "get_user_infos_filter" => "(uid=%%username%%)",
          // Liste des attributs à récupérer pour la méthode get user infos
          "get_user_infos_attributes" => array(
              'cn',
              'mail',
              'displayname',
              'uid',
              'departmentnumber',
              'calfburl',
              'mailHost',
              'mcemelaccesinterneta',
              'mcemelaccesinternetu',
              'mceTypeCompte',
              'mcevacation',
              'fullname'
          ),
          // Filtre de recherche pour la méthode get user bal partagees
          "get_user_bal_partagees_filter" => "(mcevacation=%%username%%*)",
          // Liste des attributs à récupérer pour la méthode get user balp
          "get_user_bal_partagees_attributes" => array(
              'cn',
              'mail',
              'uid',
              'mcemelaccesinterneta',
              'mcemelaccesinternetu',
              'mcevacation'
          ),
          // Filtre de recherche pour la méthode get user bal emission
          "get_user_bal_emission_filter" => "(mcevacation=%%username%%:*)",
          // Liste des attributs à récupérer pour la méthode get user bal emission
          "get_user_bal_emission_attributes" => array(
              'cn',
              'mail',
              'uid',
          ),
          // Filtre de recherche pour la méthode get user bal gestionnaire
          "get_user_bal_gestionnaire_filter" => "(mineqmelpartages=%%username%%:G)",
          // Liste des attributs à récupérer pour la méthode get user bal gestionnaire
          "get_user_bal_gestionnaire_attributes" => array(
              'cn',
              'mail',
              'uid',
              'mcevacation'
          ),
          // Filtre de recherche pour la méthode get user infos from email
          "get_user_infos_from_email_filter" => "(&(mineqmelmailemission=%%email%%)(objectClass=mineqMelBoite))",
          // Liste des attributs à récupérer pour la méthode get user infos from email
          "get_user_infos_from_email_attributes" => array(
              'cn',
              'mail',
              'uid',
              'departmentnumber',
              'calfburl',
              'mailhost'
          ),
          // Gestion du mapping des champs LDAP
          "mapping" => array(
              "user_cn" => 'cn', // CN
              "user_mel_partages" => 'mcedelegation', // Partages
              "user_mel_response" => 'mcevacation', // Message d'absence
              "user_type_entree" => 'mceTypeCompte', // Type d'entrée
              "user_mel_accesinterneta" => 'mcemelaccesinterneta', // Droit d'accès depuis internet donné par admin
              "user_mel_accesinternetu" => 'mcemelaccesinternetu', // Droit d'accès depuis internet accepté par user
              "user_employeenumber" => 'employeenumber', // Matricule
              "user_street" => 'street', // 24/08/10
              "user_postalcode" => 'postalcode', // 24/08/10
              "user_locality" => 'l', // 24/08/10
              "user_info" => 'info', // 24/08/10
              "user_description" => 'description', // 24/08/10
              "user_phonenumber" => 'telephonenumber', // 24/08/10
              "user_faxnumber" => 'facsimiletelephonenumber', // 24/08/10
              "user_mobilephone" => 'mobile', // 24/08/10
              "user_roomnumber" => 'roomnumber', // 24/08/10
              "user_title" => 'title', // 24/08/10
              "user_businesscat" => 'businesscategory', // 24/08/10
              "user_mel_accessynchroa" => 'mcemelaccessynchroa', // Droit d'accès synchro mobile donné par admin
              "user_mel_accessynchrou" => 'mcemelaccessynchrou', // Droit d'accès synchro mobile accepté par user
              "user_mission" => 'mcemission',
              "user_photo" => 'jpegphoto',
              "user_gender" => 'gender'
          ),
      )

  );


}

