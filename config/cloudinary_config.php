<?php
require '../vendor/autoload.php';
require_once '../includes/EnvManager.php';

// Load environment variables
EnvManager::load();

use Cloudinary\Cloudinary;

$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => EnvManager::get('CLOUDINARY_CLOUD_NAME', ''),
        'api_key'    => EnvManager::get('CLOUDINARY_API_KEY', ''),
        'api_secret' => EnvManager::get('CLOUDINARY_API_SECRET', '')
    ],
    'url' => [
        'secure' => true
    ]
]);

?>
