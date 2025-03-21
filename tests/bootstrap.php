<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;

require dirname( __DIR__ ) . '/vendor/autoload.php';

if ( file_exists( dirname( __DIR__ ) . '/config/bootstrap.php' ) ) {
	require dirname( __DIR__ ) . '/config/bootstrap.php';
} elseif ( method_exists( Dotenv::class, 'bootEnv' ) ) {
	( new Dotenv() )->bootEnv( dirname( __DIR__ ) . '/.env' );
}

// Workaround for https://github.com/symfony/symfony/issues/53812
// The problem is that FrameworkBundle registers an error handler but does not have a way to unregister it.
// The issue was closed, so we probably need to use the workaround until newer Symfony/PHPUnit versions fix it,
// or we use the regular Symfony WebTestCase instead of our custom WebRouteTestCase.
// The current setup code in FrameworkBundle will check if `ErrorHandler` is already registered and won't register it again.
set_exception_handler( [ new ErrorHandler(), 'handleException' ] );
