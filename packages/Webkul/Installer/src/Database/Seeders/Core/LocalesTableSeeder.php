<?php

namespace Webkul\Installer\Database\Seeders\Core;

use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LocalesTableSeeder extends Seeder
{
    /**
     * Base path for the images.
     */
    const BASE_PATH = 'packages/Webkul/Installer/src/Resources/assets/images/seeders/locales/';
    const PATH = 'storage/locales/';

    /**
     * Locales list..
     *
     * @var array
     */
    protected $localess = [
            'ar'    => 'Arabic',
            'bn'    => 'Bengali',
            'de'    => 'German',
            'en'    => 'English',
            'es'    => 'Spanish',
            'fa'    => 'Persian',
            'fr'    => 'French',
            'he'    => 'Hebrew',
            'hi_IN' => 'Hindi',
            'it'    => 'Italian',
            'ja'    => '日本語',
            'jp'    => '日本語',
            'nl'    => 'Dutch',
            'mm'    => 'မြန်မာ',
            'pl'    => 'Polish',
            'pt_BR' => 'Brazilian Portuguese',
            'ru'    => 'Russian',
            'sin'   => 'Sinhala',
            'tr'    => 'Turkish',
            'uk'    => 'Ukrainian',
            'zh_CN' => 'Chinese',
    ];

    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('channels')->delete();

        DB::table('locales')->delete();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        // $mergedDefaultLocales = collect([])->merge([$defaultLocale, 'mm', 'jp'])->all();

        // $locales = $parameters['allowed_locales'] ?? $mergedDefaultLocales;

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale, 'mm', 'jp'];

        foreach ($locales as $key => $locale) {
            $logoPath = null;

            if (!file_exists(base_path(self::PATH.$locale.'.png'))) {
                $logoPath = Storage::putFileAs('locales', new File(base_path(self::BASE_PATH.$locale.'.png')), $locale.'.png');
            } else {
                $logoPath = base_path(self::PATH.$locale.'.png');
            }

            DB::table('locales')->insert([
                [
                    'id'        => $key + 1,
                    'code'      => $locale,
                    // 'name'      => trans('installer::app.seeders.core.locales.'.$locale, [], $defaultLocale),
                    'name'      => $this->localess[$locale],
                    'direction' => in_array($locale, ['ar', 'fa', 'he']) ? 'rtl' : 'ltr',
                    'logo_path' => $logoPath,
                ],
            ]);
        }
    }
}
