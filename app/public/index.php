<?php
/**
 * Kumbia PHP Framework
 * PHP version 5
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbiaphp.com/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbiaphp.com so we can send you a copy immediately.
 * 
 * @author    Andres Felipe Gutierrez <andresfelipe@vagoogle.net>
 * @copyright 2007-2008 Emilio Rafael Silveira Tovar <emilio.rst at gmail.com>
 * @copyright 2007-2008 Deivinson Jose Tejeda Brito <deivinsontejeda at gmail.com>
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU/GPL
 * @version   SVN:$id
 */

/**
 * Establece política de informe de errores
 */
error_reporting(E_ALL ^ E_STRICT);

/**
 * Define el APP_PATH
 *
 * APP_PATH:
 * - Ruta al directorio de la aplicación (por defecto la ruta al directorio app)
 * - Esta ruta se utiliza para cargar los archivos de la aplicación
 **/
define('APP_PATH', dirname(dirname(__FILE__)) . '/');

/**
 * Define el PUBLIC_PATH
 *
 * PUBLIC_PATH:
 * - Ruta al directorio public de la aplicación (por defecto ruta al directorio app/public)
 * - Esta ruta la utiliza el cliente (el navegador web) para acceder a los recursos
 *   y es relativa al DOCUMENT_ROOT del servidor web
 **/
define('PUBLIC_PATH', dirname($_SERVER['SCRIPT_NAME']) . '/');

/**
 * Define el BASE_PATH
 *
 * BASE_PATH:
 * - Path para generar la Url en los links a acciones y controladores
 * - Esta ruta la utiliza Kumbia como base para generar las Urls para acceder de lado de
 *   cliente (con el navegador web) y es relativa al DOCUMENT_ROOT del servidor web
 **/
define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))) . '/');
/**
 * Si se usa multiaplicacion
 **/
//define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))). basename(APP_PATH) . '/');
/**
 * Si se usa urls bonitas sin rewrite
 **/
//define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))) . '/index.php/');
/**
 * Si se usa multiaplicacion y urls bonitas sin rewrite
 **/
//define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))). basename(APP_PATH) . '/index.php/');
/**
 * Si se usa urls feas
 **/
//define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))) . '/index.php?url=');
/**
 * Si se usa multiaplicacion y urls feas
 **/
//define('BASE_PATH', dirname(dirname(dirname(PUBLIC_PATH))). basename(APP_PATH) . '/index.php?url=');

/**
 * Define el LIBRARY_PATH
 *
 * LIBRARY_PATH:
 * - Ruta al directorio de librerias compartidas del framework (por defecto la ruta al directorio library)
 **/
define('LIBRARY_PATH', dirname(APP_PATH) . '/library/');

/**
 * Define el LIBRARY_PATH
 *
 * LIBRARY_PATH:
 * - Ruta al directorio que contiene el núcleo de Kumbia (por defecto la ruta al directorio library/kumbia)
 **/
define('CORE_PATH', LIBRARY_PATH . 'kumbia/');

/**
 * @see KumbiaException
 */
require CORE_PATH.'kumbia_exception.php';
/**
 * Inicializar el ExceptionHandler
 */
set_exception_handler(array('KumbiaException', 'handle_exception'));

/**
 * Si la url se paso por metodo GET (compatibilidad con Cherokee, etc)
 **/
if(isset($_GET['url'])) {
	$url = $_GET['url'];
} else {
	/**
	 * Obtiene la Url partiendo de PHP_SELF (urls bonitas sin rewrite con Apache)
	 **/
	preg_match("/^.*index.php\/?(.*)$/", $_SERVER['PHP_SELF'], $url);
	$url = isset($url[1]) ? $url[1] : '';
}

/**
 * @see Config
 */
require CORE_PATH.'config/config.php';
/**
 * @see Cache
 **/
require CORE_PATH . 'cache/cache.php';
/**
 * Lee la configuracion
 */
$config = Config::read('config.ini');

/**
 * Desactiva la cache
 **/
if(!$config['application']['production']) {
	Cache::active(false);
} elseif ($template = Cache::get($url, 'kumbia.templates')) { //verifica cache de template para la url
	echo $template;
	exit(0);
}

/**
 * Inicia la sesion
 **/
session_start();

/**
 * @see Kumbia
 */
require CORE_PATH.'kumbia.php';
/**
 * Atender la petición
 */
Kumbia::main($url);