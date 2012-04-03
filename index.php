<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
/*
|------------------------------------------------------------
|	ShareIt v1.0
| File Sharing App
|------------------------------------------------------------
| 
| File sharing initiator.
|
|------------------------------------------------------------
*/


# Include files
require_once( _AppPath.'libraries/app.php' );
require_once( _AppPath.'libraries/functions.php' );
require_once( _AppPath.'libraries/session.php' );
require_once( _AppPath.'libraries/user.php' );

# Model
require_once( _AppPath.'model/upload.php' );
require_once( _AppPath.'model/users.php' );
require_once( _AppPath.'model/folders.php' );
require_once( _AppPath.'model/meta.php' );
require_once( _AppPath.'model/configuration.php' );

# controller
require_once(	_AppPath.'controller/controller.php' );


# Load classes
$app = new app($config);
$app->session = new Session($app);
$app->functions = new functions($app);
$app->meta = new Meta($app);
$app->configuration = new Configuration($app);
$app->uploads = new Uploads($app);
$app->users = new Users($app);
$app->folders = new Folders($app);
$app->controller = new Controller($app);
$app->user = new User($app);
# Load app settings
$app->settings = $app->configuration->get();
# Load everything
$app->controller->invoke();



?>