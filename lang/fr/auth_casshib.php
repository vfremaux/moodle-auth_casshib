<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_cas', language 'fr'.
 *
 * @package   auth_casshib
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['privacy:metadata'] = 'Ce plugin ne détient pas directement de données utilisateur';

$string['CASform'] = 'Choix du mode d\'authentification';
$string['accesCAS'] = 'Utilisateurs CAS';
$string['accesNOCAS'] = 'Autres utilisateurs';
$string['auth_casdescription'] = 'Cette méthode utilise un serveur CAS (Central Authentication Service) pour authentifier les utilisateurs dans un environnement Single Sign On (SSO). Il est aussi possible d\'utiliser une simple authentification LDAP. Si le nom d\'utilisateur et le mot de passe donnés sont valides suivant le CAS, Moodle crée un nouvel utilisateur dans sa base de données, en héritant si nécessaire des attributs LDAP de l\'utilisateur. Lors des connexions ultérieures, seuls le nom d\'utilisateur et le mot de passe sont vérifiés.';
$string['auth_casnotinstalled'] = 'Impossible d\'utiliser l\'authentification CAS. Le module PHP LDAP n\'est pas installé.';
$string['auth_casshib_auth_user_create'] = 'Créer les utilisateurs en externe';
$string['auth_casshib_baseuri'] = 'Adresse URI du serveur CAS (ne rien mettre s\'il n\'y en a pas).<br />par exemple, si le serveur CAS répond à l\'adresse « host.domaine.fr/cas/ », la valeur à indiquer ici est « cas/ ».';
$string['auth_casshib_baseuri_key'] = 'URI de base';
$string['auth_casshib_broken_password'] = 'Vous ne pouvez pas continuer sans changer de mot de passe, et aucun moyen n\'est disponible pour le changer. Veuillez contacter l\'administrateur de votre Moodle.';
$string['auth_casshib_cantconnect'] = 'La partie LDAP du module CAS ne peut pas se connecter au serveur {$a}';
$string['auth_casshib_casversion'] = 'Version du protocole CAS';
$string['auth_casshib_certificate_check'] = 'Veuillez sélectionner « Oui » si vous voulez valider le certificat du serveur';
$string['auth_casshib_certificate_check_key'] = 'Validation serveur';
$string['auth_casshib_certificate_path'] = 'Chemin d\'accès au fichier CA chain (format PEM) pour valider le certificat du serveur';
$string['auth_casshib_certificate_path_empty'] = 'Si vous activez la validation du serveur, vous devez indiquer un chemin d\'accès au certificat';
$string['auth_casshib_certificate_path_key'] = 'Chemin d\'accès certificat';
$string['auth_casshib_changepasswordurl'] = 'URL pour changement de mot de passe';
$string['auth_casshib_create_user'] = 'Veuillez activer cette option si vous voulez insérer dans la base de données de Moodle les utilisateurs authentifiés par le CAS. Dans le cas contraire, seuls les utilisateurs déjà présents dans la base de données de Moodle pourront se connecter.';
$string['auth_casshib_create_user_key'] = 'Créer l\'utilisateur';
$string['auth_casshib_enabled'] = 'Veuillez activer cette option si vous voulez utiliser l\'authentification CAS.';
$string['auth_casshib_hostname'] = 'Nom d\'hôte du serveur,<br />par exemple : « host.domaine.fr »';
$string['auth_casshib_hostname_key'] = 'Nom d\'hôte';
$string['auth_casshib_invalidcaslogin'] = 'Désolé, la connexion a échoué ! Vous n\'êtes pas autorisé à vous connecter à la plateforme.';
$string['auth_casshib_language'] = 'Langue des pages d\'authentification';
$string['auth_casshib_language_key'] = 'Langue';
$string['auth_casshib_logincas'] = 'Accès par connexion sécurisée';
$string['auth_casshib_logout_return_url'] = 'Indiquer ici l\'URL vers laquelle les utilisateurs CAS seront redirigés après s\'être déconnectés.<br />Si le champ n\'est pas renseigné, les utilisateurs seront redirigés vers la page où Moodle redirige normalement les utilisateurs';
$string['auth_casshib_logout_return_url_key'] = 'URL de redirection alternative après déconnexion';
$string['auth_casshib_logoutcas'] = 'Veuillez sélectionner « Oui » si vous voulez vous déconnecter de CAS lors de la déconnexion de Moodle';
$string['auth_casshib_logoutcas_key'] = 'Option de déconnexion CAS';
$string['auth_casshib_multiauth'] = 'Veuillez sélectionner « Oui » si vous voulez utilisez l\'authentification multiple (CAS + d\'autres méthodes d\'authentification)';
$string['auth_casshib_multiauth_key'] = 'Authentification multiple';
$string['auth_casshib_notify'] = 'Notifier';
$string['auth_casshib_notify_desc'] = 'Notifier les administrateurs des erreurs d\'authentification CAS';
$string['auth_casshib_port'] = 'Port utilisé par le serveur CAS';
$string['auth_casshib_port_key'] = 'Port';
$string['auth_casshib_proxycas'] = 'Veuillez sélectionner « Oui » si vous souhaitez vous connecter en mode proxy CAS';
$string['auth_casshib_proxycas_key'] = 'Mode proxy';
$string['auth_casshib_redirectonfailure'] = 'Redirecton sur faute ou déconnexion';
$string['auth_casshib_redirectonfailure_desc'] = '';
$string['auth_casshib_server_settings'] = 'Configuration du serveur CAS';
$string['auth_casshib_shib_settings'] = 'Réglages Shibboleth additionnels';
$string['auth_casshib_text'] = 'Connexion sécurisée';
$string['auth_casshib_use_cas'] = 'Utiliser CAS';
$string['auth_casshib_version'] = 'Version du protocole CAS à utiliser';
$string['auth_casshibdescription'] = 'Cette méthode utilise un serveur CAS server (Central Authentication Service) pour authentifier les utilisateurs sur une source unique d\'authentification partagé entre plusieurs applications.';
$string['auth_casshibnotinstalled'] = 'L\'authentification CASSHIB ne peut pas fonctionner. Le module PHP LDAP doit être installé.';
$string['capture_cas_settings'] = 'Capturer tous les utilisateurs CAS, réglages et activer le CAS SHIB.';
$string['invisible'] = 'Redirection invisible sur faute ou déconnexion';
$string['locallogout'] = 'Déconnexion locale';
$string['locallogoutmessage'] = 'Vous avez refermé votre session locale sur la plate-forme pédagogique. Pour rentrer à nouveau sur Moodle, fermez cette fenêtre et utilisez à nouveau le lien dans votre portail Toutatice.';
$string['locallogoutmessage2'] = 'Notez que votre session principale sur Toutatice est toujours active. Pour plus de sécurité, nous vous conseillons de refermer complètement votre navigateur à la fin de votre session de travail sur Toutatice.';
$string['locallogouttitle'] = 'Déconnexion locale';
$string['nocasconnect'] = 'Pas de connexion CAS disponible';
$string['nocasconnectmailsubject_tpl'] = 'Erreur connexion serveur CAS %%CASURL%% à partir de %%SITE%%';
$string['nocasconnectmailtitle_tpl'] = '[%%SITE%%] Erreur connexion CAS';
$string['nocasconnectmessage'] = 'Le serveur central d\'authentification n\'est pas disponible ou joignable. Nous ne pouvons vous connecter à Moodle. Cette situation a été signalée aux administrateur. Merci de bien vouloir patienter jusqu\'au rétablissement du service.';
$string['noldapserver'] = 'Aucun serveur LDAP n\'est configuré pour CASSHIB ! Synchronisation désactivée.';
$string['pluginname'] = 'Serveur CAS (SSO) Shibboleth';
$string['quick_config'] = 'Configuration rapide';
$string['remotelogoutmessage'] = 'Bienvenue sur Moodle.<br/<br/>Pour utiliser ce service, connectez vous à votre portail central <a href="/auth/casent/redirecttocas.php">ici</a>';
$string['unknownincomingmailbody_tpl'] = '%%USERNAME%% a été authentifié par le CASSHIB mais n\'est pas connu sur le site %%SITE%%';
$string['unknownincomingmailtitle_tpl'] = '[%%SITE%%] Utilisateur CASSHIB non connu';
$string['unknownincominguser'] = 'L\'utilisateur est authentifié mais pas connu sur le site.';
$string['visible'] = 'Redirection après présentation d\'un message';

$string['unknownincomingmessage'] = '<p>vous semblez avoir correctement été authentifié sur le serveur d\'authentification central auquel nous sommes raccordés,
mais nous ne connaissons pas votre identité localement sur cet établissement.
</p>
<p>Merci de bien vouloir vous rediriger sur <a href="{$a}">votre ENT</a></p>';