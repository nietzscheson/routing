<?php

// Las clases de Symfony que vamos a usar:
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

/**
 *  Edita esta variable para colocar la ruta y la extension de tus controladores
 *  "%s" sera reemplazado por el parametro _file de las rutas
 * Ejemplo:
 *   $filePattern = ./misarchivos/%s.php5
 *   _file = 'mipagina'
 *   Resultado: ./misarchivos/mipagina.php5
 */
$filePattern = './controllers/%s.php';

$parameters = array ();

try
{
   // Cargamos el autoloader que viene con Composer
   require_once __DIR__.'/vendor/composer/autoload.php';

   //Creamos nuestra coleccion de rutas
   $routes = new RouteCollection();

   /**
    * Configuramos todas nuestras rutas:
    * El primer parametro es el ID de la ruta
    * El segundo es una clase Ruta donde configuraremos nuestra expresion regular
    * En el caso del ej: '/portafolio/{id}'
    * {id} se convertira en una variable GET al final del ejemplo
    * Y el tercer parametro corresponde a un array donde asignaremos parametros adicionales
    * En este caso es muy importante que cada ruta tenga su parametro _file
    * Que sera quien indicara al script que archivo cargar
    * Mas documentacion: http://symfony.com/doc/current/components/routing.html
    */
   $routes->add('portafolio', new Route('/operaciones/{r}/perfil-cotizacion/{c}', array('_file' => 'portafolio')));
   $routes->add('usuarios', new Route('/usuarios', array('_file' => 'usuarios','_method'=>'indexAction')));

   // Symfony stuff:
   $context = new RequestContext($_SERVER['REQUEST_URI']);

   $matcher = new UrlMatcher($routes, $context);

   // GET[url] es enviado por el htaccess
   // Esta linea es la que se encarga de comparar las rutas y devolvernos los parametros necesarios
   $parameters = $matcher->match($_GET['url']);

} catch (Symfony\Component\Routing\Exception\ResourceNotFoundException $exc)
{
   // En caso de no encontrar la ruta,se cargara un archivo 404
   $parameters['_file'] = '404';
} catch (Exception $exc)
{
   // En caso de ocurrir otro error cargaremos un archivo 501
   $parameters['_file'] = '501';
}

// Mezclamos los parametros de la ruta con parametros get
// Dado que cuando no se usan estos sistemas de rutas generalmente se trabaja con GET
$_GET = array_merge($_GET, $parameters);

echo "<pre>";print_r($_GET);

// Armamos la ruta a nuestro archivo
$file = sprintf($filePattern, $parameters['_file']);

if ( ! file_exists($file))
{
   exit ('No existe el archivo: ' . $file);
}

// y finalmente cargamos el archivo
include_once $file;
