<?php
/**
 * Ce fichier est développé pour la gestion de la librairie Mélanie2
 * Cette Librairie permet d'accèder aux données sans avoir à implémenter de couche SQL
 * Des objets génériques vont permettre d'accèder et de mettre à jour les données
 *
 * ORM M2 Copyright (C) 2015  PNE Annuaire et Messagerie/MEDDE
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
    public static $AUTH_LDAP = "ldapmaster-srv.mce.com";
    /**
     * Configuration du choix de serveur utilisé pour la recherche dans l'annuaire
     * @var string
     */
    public static $SEARCH_LDAP = "ldapmaster-srv.mce.com";
    /**
     * Configuration du choix de serveur utilisé pour l'autocomplétion
     * @var string
     */
    public static $AUTOCOMPLETE_LDAP = "ldapmaster-srv.mce.com";
    /**
     * Configuration du choix de serveur maitre, utilisé pour l'écriture
     * @var string
     */
    public static $MASTER_LDAP = "ldapmaster-srv.mce.com";

    /**
     * Configuration des serveurs LDAP
     * Chaque clé indique le nom du serveur ldap et sa configuration de connexion
     * hostname, port, dn
     * informations
     */
    public static $SERVERS = array(
        /* Serveur LDAP MA */
        "ldapmaster-srv.mce.com" => array(
                /* Host vers le serveur d'annuaire, précédé par ldaps:// si connexion SSL */
                "hostname" => "ldap://ldapmaster-srv.mce.com/",
                /* Port de connexion au LDAP */
                "port" => 389,
                /* Base DN de recherche */
                "base_dn" => "ou=mce,o=gouv,c=fr",
                /* Base de recherche pour les objets de partage */
                "shared_base_dn" => "ou=mce,o=gouv,c=fr",
                /* Version du protocole LDAP */
                "version" => 3,
                /* Connexion TLS */
                "tls" => false,
                // Configuration des attributs et filtres de recherche
                // Filtre de recherche pour la méthode get user infos
                "get_user_infos_filter" => "(uid=%%username%%)",
                // Liste des attributs à récupérer pour la méthode get user infos
                "get_user_infos_attributes" => array('cn','mail','uid','mailhost'),
                // Filtre de recherche pour la méthode get user bal partagees
                "get_user_bal_partagees_filter" => "(uid=%%username%%.-.*)",
                // Liste des attributs à récupérer pour la méthode get user balp
                "get_user_bal_partagees_attributes" => array('cn','mail','uid'),
                // Filtre de recherche pour la méthode get user bal emission
                "get_user_bal_emission_filter" => "(mcedelegation=%%username%%:*)",
                // Liste des attributs à récupérer pour la méthode get user bal emission
                "get_user_bal_emission_attributes" => array('cn','mail','uid'),
                // Filtre de recherche pour la méthode get user bal gestionnaire
                "get_user_bal_gestionnaire_filter" => "(mcedelegation=%%username%%:*)",
                // Liste des attributs à récupérer pour la méthode get user bal gestionnaire
                "get_user_bal_gestionnaire_attributes" => array('cn','mail','uid'),
                // Filtre de recherche pour la méthode get user infos from email
                "get_user_infos_from_email_filter" => "(mail=%%email%%)",
                // Liste des attributs à récupérer pour la méthode get user infos from email
                "get_user_infos_from_email_attributes" => array('cn','mail','uid','mailhost'),
        ),
    );
}
