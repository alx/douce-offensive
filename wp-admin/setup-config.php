<?php
/**
 * Retrieves and creates the wp-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the wp-config.php to be created using this page.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**#@+
 * These three defines are required to allow us to use require_wp_db() to load
 * the database class while being wp-content/db.php aware.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
/**#@-*/

require_once('../wp-includes/compat.php');
require_once('../wp-includes/functions.php');
require_once('../wp-includes/classes.php');

if (!file_exists('../wp-config-sample.php'))
	wp_die('Je suis d&eacute;sol&eacute;, mais il me faut partir d\'un fichier <code>wp-config-sample.php</code>. Veuillez remettre en ligne ce fichier depuis votre archive WordPress.');

$configFile = file('../wp-config-sample.php');

if ( !is_writable('../'))
	wp_die("Je suis d&eacute;sol&eacute;, mais je ne peux pas &eacute;crire dans le r&eacute;pertoire. Il vous faut soit modifier les permissions de votre r&eacute;pertoire WordPress, soit cr&eacute;er votre fichier <code>wp-config.php</code> manuellement.");

// Check if wp-config.php has been created
if (file_exists('../wp-config.php'))
	wp_die("<p>Le fichier 'wp-config.php' existe d&eacute;j&agrave;. Si vous devez mettre &agrave; z&eacute;ro les &eacute;l&eacute;ments de configuration de ce fichier, veuillez l'effacer avant de continuer. Vous pouvez <a href='install.php'>lancer l'installateur</a> maintenant.</p>");

// Check if wp-config.php exists above the root directory
if (file_exists('../../wp-config.php') && ! file_exists('../../wp-load.php'))
	wp_die("<p>Le fichier 'wp-config.php' existe déjà dans un répertoire supérieur à votre installation de WordPress. Si vous avez besoin de réinitialiser un élément de configuration de ce fichier, merci de l'effacer d'abord. Vous maintenant procéder <a href='install.php'>l'installation</a>.</p>");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display setup wp-config.php file header.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; Setup Configuration File</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Bienvenue dans WordPress. Avant de nous lancer, nous avons besoin de quelques informations &agrave; propos de la base de donn&eacute;es. Il vous faudra conna&icirc;tre les points suivants pour aller plus loin.</p>
<ol>
	<li>Le nom de la base de donn&eacute;es</li>
	<li>Votre identifiant de base de donn&eacute;es</li>
	<li>Votre mot de passe de base de donn&eacute;es</li>
	<li>L'h&ocirc;te de la base de donn&eacute;es</li>
	<li>Le pr&eacute;fixe de table (si vous voulez installer plus d'un blog WordPress sur la m&ecirc;me base de donn&eacute;es)</li>
</ol>
<p><strong>Si pour une raison quelconque la cr&eacute;ation automatique du fichier ne fonctionnait pas, ne paniquez pas : elle ne fait que remplir les informations de la base de donn&eacute;es dans un fichier de configuration. Vous pouvez donc simplement ouvrir <code>wp-config-sample.php</code> dans un &eacute;diteur de texte, y entrer vos informations, et enregistrer le fichier sous le nouveau nom <code>wp-config.php</code>.</strong></p>
<p>Normalement, ces informations vous ont &eacute;t&eacute; fournies par votre h&eacute;bergeur. Si vous ne les avez pas, il vous faudra le contacter pour continuer. Si vous &ecirc;tes pr&ecirc;t&hellip;</p>

<p class="step"><a href="setup-config.php?step=1" class="button">Allons-y !</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>Entrez ci-dessous les d&eacute;tails de connexion &agrave; votre base de donn&eacute;es. Si vous ne les connaissez pas avec certitude, contactez votre h&eacute;bergeur. </p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">Nom de la base de donn&eacute;es</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td>Le nom de la base dans laquelle vous voulez installer WP. </td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">Identifiant</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="username" /></td>
			<td>Votre identifiant MySQL.</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Mot de passe</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="password" /></td>
			<td>...et votre mot de passe MySQL.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">H&ocirc;te de la base de donn&eacute;es</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>Dans 99% des cas, vous n'aurez pas &agrave; modifier cette valeur.</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">Pr&eacute;fixe de table</label></th>
			<td><input name="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Si vous voulez installer plusieurs blogs WordPress dans une m&ecirc;me base de donn&eacute;es, modifiez ce champ.</td>
		</tr>
	</table>
	<p class="step"><input name="submit" type="submit" value="Valider" class="button" /></p>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if (empty($prefix)) $prefix = 'wp_';

	// Test the db connection.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( !empty($wpdb->error) )
		wp_die($wpdb->error->get_error_message());

	$handle = fopen('../wp-config.php', 'w');

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				fwrite($handle, str_replace("votre-nom-de-bdd", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'votre-utilisateur-de-bdd'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'votre-mdp-de-bdd'", "'$passwrd'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			case '$table_prefix  =':
				fwrite($handle, str_replace('wp_', $prefix, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod('../wp-config.php', 0666);

	display_header();
?>
<p>Tr&egrave;s bien, mon grand ! Nous sommes arriv&eacute;s au terme de cette partie de l'installation. WordPress peut maintenant communiquer avec votre base de donn&eacute;es. Si vous &ecirc;tes pr&ecirc;t, il est grand temps de&hellip;</p>

<p class="step"><a href="install.php" class="button">Lancer l'installateur&nbsp;!</a></p>
<?php
	break;
}
?>
</body>
</html>