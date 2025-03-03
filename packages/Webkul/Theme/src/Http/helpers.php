<?php

use Webkul\Theme\Facades\Themes;
use Webkul\Theme\ViewRenderEventManager;

if (! function_exists('themes')) {
    /**
     * Themes.
     *
     * @return \Webkul\Theme\Themes
     */
    function themes()
    {
        return Themes::getFacadeRoot();
    }
}

if (! function_exists('bagisto_asset')) {
    /**
     * Bagisto asset.
     *
     * @return string
     */
    function bagisto_asset(string $path, ?string $namespace = null)
    {
        return themes()->url($path, $namespace);
    }
}

if (! function_exists('bagisto_asset_image')) {
    /**
     * Bagisto asset.
     *
     * @return mixed
     */
    function bagisto_asset_image(string $path, ?string $namespace = null, $isLoop = false)
    {
        $paths = [];
        $pathss = '';
        if ($isLoop) {
            $array = explode(",", $path);

            $x = 0;
            for ($x; $x <= count($array) - 1; $x++) {
                // $paths[$x] = themes()->url($array[$x], null);
                $paths[$x] = themes()->url($array[$x], null);
                $pathss .= $paths[$x] . ",";
            }
            return $pathss;
        } else {
            return themes()->url($path, $namespace);
        }

    }
}

if (! function_exists('view_render_event')) {
    /**
     * View render event.
     *
     * @param  string  $eventName
     * @param  mixed  $params
     * @return mixed
     */
    function view_render_event($eventName, $params = null)
    {
        app()->singleton(ViewRenderEventManager::class);

        $viewEventManager = app()->make(ViewRenderEventManager::class);

        $viewEventManager->handleRenderEvent($eventName, $params);

        return $viewEventManager->render();
    }
}
