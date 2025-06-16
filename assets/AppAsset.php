<?php

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $css = [
        'css/app-core.css',           // Nowy, kompletny CSS
        'css/app-components.css',     // Pozostaw bez zmian
        'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css',
    ];
    
    public $js = [
        'js/app-core.js',           // Oryginalny JS (jeśli istnieje)
        'js/app-darkmode.js',       // NOWY plik z dark mode i loading
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
    
    /**
     * Publikuj asset z wymuszeniem odświeżenia cache
     */
    public function init()
    {
        parent::init();
        
        // Dodaj timestamp do CSS/JS w development
        if (YII_ENV_DEV) {
            $timestamp = time();
            foreach ($this->css as $key => $css) {
                if (strpos($css, 'http') !== 0) { // Tylko lokalne pliki
                    $this->css[$key] = $css . '?v=' . $timestamp;
                }
            }
            foreach ($this->js as $key => $js) {
                if (strpos($js, 'http') !== 0) { // Tylko lokalne pliki
                }
            }
        }
    }
}

class DashboardAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    
    public $js = [
        'js/app-dashboard.js',
    ];
    
    public $depends = [
        'app\assets\AppAsset',
    ];
}