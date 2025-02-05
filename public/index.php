<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;
use WMDE\BannerServer\Kernel;

require __DIR__ . '/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if ( !isset( $_SERVER['APP_ENV'] ) && !isset( $_ENV['APP_ENV'] ) ) {
	if ( !class_exists( Dotenv::class ) ) {
		throw new \RuntimeException( 'APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.' );
	}
	( new Dotenv() )->load( __DIR__ . '/../.env' );
}

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
$debug = (bool)( $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ( 'prod' !== $env ) );

// The default `umask` value on our banner server is `022`. This is restricting group write permissions.
// 002 allows for all others in the same group to also create+modify files.
// The web server and the deployment user are both in the same group, which allows the deployment user to
// delete old deployments where the web server has written cache files
umask( 0002 );
if ( $debug ) {
	umask( 0000 );

	Debug::enable();
}

if ( $trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? $_ENV['TRUSTED_PROXIES'] ?? false ) {
	Request::setTrustedProxies( explode( ',', $trustedProxies ), Request::HEADER_X_FORWARDED_FOR ^ Request::HEADER_X_FORWARDED_HOST );
}

if ( $trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? $_ENV['TRUSTED_HOSTS'] ?? false ) {
	Request::setTrustedHosts( explode( ',', $trustedHosts ) );
}

$kernel = new Kernel( $env, $debug );
$request = Request::createFromGlobals();
$response = $kernel->handle( $request );
$response->send();
$kernel->terminate( $request, $response );
