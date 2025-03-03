<?php

namespace Webkul\Installer\Database\Seeders\Shop;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Webkul\Installer\Database\Seeders\Category\CategoryTableSeeder;

class ThemeCustomizationTableSeeder extends Seeder
{
    /**
     * Attribute Type Fields.
     *
     * @var array
     */
    public $attributeTypeFields = [
        'text'        => 'text_value',
        'textarea'    => 'text_value',
        'price'       => 'float_value',
        'boolean'     => 'boolean_value',
        'select'      => 'integer_value',
        'multiselect' => 'text_value',
        'datetime'    => 'datetime_value',
        'date'        => 'date_value',
        'file'        => 'text_value',
        'image'       => 'text_value',
        'checkbox'    => 'text_value',
    ];

    /**
     * Base path for the images.
     */
    const BASE_PRODUCT_PATH = 'packages/Webkul/Installer/src/Resources/assets/images/seeders/products/';
    /**
     * Base path for the images.
     */
    const BASE_PATH = 'packages/Webkul/Installer/src/Resources/assets/images/seeders/theme/';

    /**
     * Seed the application's database.
     *
     * @param  array  $parameters
     * @return void
     */
    public function run($parameters = [])
    {
        DB::table('theme_customizations')->delete();

        DB::table('theme_customization_translations')->delete();

        $now = Carbon::now();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        $appUrl = config('app.url');

        DB::table('theme_customizations')
            ->insert([
                [
                    'id'         => 1,
                    'type'       => 'image_carousel',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.image-carousel.name', [], $defaultLocale),
                    'sort_order' => 1,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 2,
                    'type'       => 'static_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.offer-information.name', [], $defaultLocale),
                    'sort_order' => 2,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 3,
                    'type'       => 'category_carousel',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.categories-collections.name', [], $defaultLocale),
                    'sort_order' => 3,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 4,
                    'type'       => 'product_carousel',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.new-products.name', [], $defaultLocale),
                    'sort_order' => 4,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 5,
                    'type'       => 'static_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.top-collections.name', [], $defaultLocale),
                    'sort_order' => 5,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 6,
                    'type'       => 'static_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.toke-bag-information.name', [], $defaultLocale),
                    'sort_order' => 6,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 7,
                    'type'       => 'product_carousel',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.featured-collections.name', [], $defaultLocale),
                    'sort_order' => 7,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 8,
                    'type'       => 'static_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.game-container.name', [], $defaultLocale),
                    'sort_order' => 8,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 9,
                    'type'       => 'product_carousel',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.all-products.name', [], $defaultLocale),
                    'sort_order' => 9,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 10,
                    'type'       => 'static_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.t-shirt-information.name', [], $defaultLocale),
                    'sort_order' => 10,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 11,
                    'type'       => 'footer_links',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.footer-links.name', [], $defaultLocale),
                    'sort_order' => 11,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'id'         => 12,
                    'type'       => 'services_content',
                    'name'       => trans('installer::app.seeders.shop.theme-customizations.services-content.name', [], $defaultLocale),
                    'sort_order' => 12,
                    'status'     => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale, 'mm', 'jp'];

        foreach ($locales as $locale) {
            DB::table('theme_customization_translations')
                ->insert([
                    /**
                     * Customizations for current locale
                     */
                    [
                        'theme_customization_id' => 1,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'images' => [
                                [
                                    'title' => trans('installer::app.seeders.shop.theme-customizations.image-carousel.sliders.title', [], $locale),
                                    'link'  => '',
                                    'image' => $this->storeFileIfExists('theme/1', 'sliders/' . $locale . '/1.webp', 'sliders/en/1.webp'),
                                ],
                                [
                                    'title' => trans('installer::app.seeders.shop.theme-customizations.image-carousel.sliders.title', [], $locale),
                                    'link'  => '',
                                    'image' => $this->storeFileIfExists('theme/1', 'sliders/' . $locale . '/2.webp', 'sliders/en/2.webp'),
                                ],
                                [
                                    'title' => trans('installer::app.seeders.shop.theme-customizations.image-carousel.sliders.title', [], $locale),
                                    'link'  => '',
                                    'image' => $this->storeFileIfExists('theme/1', 'sliders/' . $locale . '/3.webp', 'sliders/en/3.webp'),
                                ],
                                [
                                    'title' => trans('installer::app.seeders.shop.theme-customizations.image-carousel.sliders.title', [], $locale),
                                    'link'  => '',
                                    'image' => $this->storeFileIfExists('theme/1', 'sliders/' . $locale . '/4.webp', 'sliders/en/4.webp'),
                                ],
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 2,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'html' => '<div class="home-offer"><h1>' . trans('installer::app.seeders.shop.theme-customizations.offer-information.content.title', [], $locale) . '</h1></div>',
                            'css'  => '.home-offer h1 {display: block;font-weight: 500;text-align: center;font-size: 22px;font-family: Technor, Z06-Walone, Noto Sans JP;background-color: #E8EDFE;padding-top: 20px;padding-bottom: 20px;}@media (max-width:768px){.home-offer h1 {font-size:18px;padding-top: 10px;padding-bottom: 10px;}@media (max-width:525px) {.home-offer h1 {font-size:14px;padding-top: 6px;padding-bottom: 6px;}}',
                        ]),
                    ],
                    [
                        'theme_customization_id' => 3,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'filters' => [
                                'parent_id'  => 1,
                                'sort'       => 'asc',
                                'limit'      => 10,
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 4,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'title'   => trans('installer::app.seeders.shop.theme-customizations.new-products.options.title', [], $locale),
                            'filters' => [
                                'new'   => 1,
                                'sort'  => 'asc',
                                'limit' => 10,
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 5,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'html' => '<div class="top-collection-container"><div class="top-collection-header"><h2>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '</h2></div><div class="top-collection-grid container"><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/1.webp', 'static/en/1.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-1', [], $locale) . '</h3></div><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/2.webp', 'static/en/2.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-2', [], $locale) . '</h3></div><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/3.webp', 'static/en/3.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-3', [], $locale) . '</h3></div><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/4.webp', 'static/en/4.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-4', [], $locale) . '</h3></div><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/5.webp', 'static/en/5.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-5', [], $locale) . '</h3></div><div class="top-collection-card"><img src="" data-src="' . $this->storeFileIfExists('theme/5', 'static/' . $locale . '/6.webp', 'static/en/6.webp') . '" class="lazy" width="396" height="396" alt="' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.title', [], $locale) . '"><h3>' . trans('installer::app.seeders.shop.theme-customizations.top-collections.content.sub-title-6', [], $locale) . '</h3></div></div></div>',

                            'css'  => '.top-collection-container {overflow: hidden;}.top-collection-header {padding-left: 15px;padding-right: 15px;text-align: center;font-size: 70px;line-height: 90px;color: #060C3B;margin-top: 80px;}.top-collection-header h2 {max-width: 595px;margin-left: auto;margin-right: auto;font-family: Technor, Z06-Walone, Noto Sans JP;}.top-collection-grid {display: flex;flex-wrap: wrap;gap: 32px;justify-content: center;margin-top: 60px;width: 100%;margin-right: auto;margin-left: auto;padding-right: 90px;padding-left: 90px;}.top-collection-card {position: relative;background: #f9fafb;overflow:hidden;border-radius:20px;}.top-collection-card img {border-radius: 16px;max-width: 100%;text-indent:-9999px;transition: transform 300ms ease;transform: scale(1);}.top-collection-card:hover img {transform: scale(1.05);transition: all 300ms ease;}.top-collection-card h3 {color: #060C3B;font-size: 30px;font-family: Technor, Z06-Walone, Noto Sans JP;transform: translateX(-50%);width: max-content;left: 50%;bottom: 30px;position: absolute;margin: 0;font-weight: inherit;}@media not all and (min-width: 525px) {.top-collection-header {margin-top: 28px;font-size: 20px;line-height: 1.5;}.top-collection-grid {gap: 10px}}@media not all and (min-width: 768px) {.top-collection-header {margin-top: 30px;font-size: 28px;line-height: 3;}.top-collection-header h2 {line-height:2; margin-bottom:20px;} .top-collection-grid {gap: 14px}} @media not all and (min-width: 1024px) {.top-collection-grid {padding-left: 30px;padding-right: 30px;}}@media (max-width: 768px) {.top-collection-grid { row-gap:15px; column-gap:0px;justify-content: space-between;margin-top: 0px;} .top-collection-card{width:48%} .top-collection-card img {width:100%;} .top-collection-card h3 {font-size:24px; bottom: 16px;}}@media (max-width:520px) { .top-collection-grid{padding-left: 15px;padding-right: 15px;} .top-collection-card h3 {font-size:18px; bottom: 10px;}}',
                        ]),
                    ],
                    [
                        'theme_customization_id' => 6,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'html' => '<div class="section-gap toke-bag-information container"> <div class="inline-col-wrapper"> <div class="inline-col-image-wrapper"> <img src="" data-src="' . $this->storeFileIfExists('theme/6', 'static/' . $locale . '/7.webp', 'static/en/7.webp') . '" class="lazy" width="632" height="510" alt="' . trans('installer::app.seeders.shop.theme-customizations.toke-bag-information.content.title', [], $locale) . '"> </div> <div class="inline-col-content-wrapper"> <h2 class="inline-col-title"> ' . trans('installer::app.seeders.shop.theme-customizations.toke-bag-information.content.title', [], $locale) . ' </h2> <p class="inline-col-description">' . trans('installer::app.seeders.shop.theme-customizations.toke-bag-information.content.description', [], $locale) . '</p> <button class="primary-button max-md:rounded-lg max-md:px-4 max-md:py-2.5 max-md:text-sm">' . trans('installer::app.seeders.shop.theme-customizations.toke-bag-information.content.btn-title', [], $locale) . '</button> </div> </div> </div>',

                            'css'  => '.section-gap{margin-top:80px}.direction-ltr{direction:ltr}.direction-rtl{direction:rtl}.inline-col-wrapper{display:grid;grid-template-columns:auto 1fr;grid-gap:60px;align-items:center}.inline-col-wrapper .inline-col-image-wrapper{overflow:hidden}.inline-col-wrapper .inline-col-image-wrapper img{max-width:100%;height:auto;border-radius:16px;text-indent:-9999px}.inline-col-wrapper .inline-col-content-wrapper{display:flex;flex-wrap:wrap;gap:20px;max-width:464px}.inline-col-wrapper .inline-col-content-wrapper .inline-col-title{max-width:442px;font-size:60px;font-weight:400;color:#060c3b;line-height:70px;font-family:Technor, Z06-Walone, Noto Sans JP;margin:0}.inline-col-wrapper .inline-col-content-wrapper .inline-col-description{margin:0;font-size:18px;color:#6e6e6e;font-family:Poppins, Z06-Walone, Noto Sans JP}@media (max-width:991px){.inline-col-wrapper{grid-template-columns:1fr;grid-gap:16px}.inline-col-wrapper .inline-col-content-wrapper{gap:10px}} @media (max-width:768px){.inline-col-wrapper .inline-col-image-wrapper img {width:100%;} .inline-col-wrapper .inline-col-content-wrapper .inline-col-title{font-size:28px !important;line-height:normal !important}} @media (max-width:525px){.inline-col-wrapper .inline-col-content-wrapper .inline-col-title{font-size:20px !important;} .inline-col-description{font-size:16px} .inline-col-wrapper{grid-gap:10px}}',
                        ]),
                    ],
                    [
                        'theme_customization_id' => 7,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'title'   => trans('installer::app.seeders.shop.theme-customizations.featured-collections.options.title', [], $locale),
                            'filters' => [
                                'featured' => 1,
                                'sort'     => 'desc',
                                'limit'    => 10,
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 8,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'html' => '<div class="section-game"><div class="section-title"> <h2>' . trans('installer::app.seeders.shop.theme-customizations.game-container.content.title', [], $locale) . '</h2> </div> <div class="section-gap container"> <div class="collection-card-wrapper"> <div class="single-collection-card"> <img src="" data-src="' . $this->storeFileIfExists('theme/8', 'static/' . $locale . '/8.webp', 'static/en/8.webp') . '" class="lazy" width="615" height="600" alt="' . trans('installer::app.seeders.shop.theme-customizations.game-container.content.title', [], $locale) . '"> <h3 class="overlay-text">' . trans('installer::app.seeders.shop.theme-customizations.game-container.content.sub-title-1', [], $locale) . '</h3> </div> <div class="single-collection-card"> <img src="" data-src="' . $this->storeFileIfExists('theme/8', 'static/' . $locale . '/9.webp', 'static/en/9.webp') . '" class="lazy" width="615" height="600" alt="' . trans('installer::app.seeders.shop.theme-customizations.game-container.content.title', [], $locale) . '"> <h3 class="overlay-text"> ' . trans('installer::app.seeders.shop.theme-customizations.game-container.content.sub-title-2', [], $locale) . ' </h3> </div> </div> </div> </div>',

                            'css'  => '.section-game {overflow: hidden;}.section-title,.section-title h2{font-weight:400;font-family:Technor, Z06-Walone, Noto Sans JP}.section-title{margin-top:80px;padding-left:15px;padding-right:15px;text-align:center;line-height:90px}.section-title h2{font-size:70px;color:#060c3b;max-width:595px;margin:auto}.collection-card-wrapper{display:flex;flex-wrap:wrap;justify-content:center;gap:30px}.collection-card-wrapper .single-collection-card{position:relative;background:#f9fafb;overflow:hidden;border-radius:20px;}.collection-card-wrapper .single-collection-card img{border-radius:16px;background-color:#f5f5f5;max-width:100%;height:auto;text-indent:-9999px;transition: transform 300ms ease;transform: scale(1);}.collection-card-wrapper .single-collection-card:hover img { transform: scale(1.05); transition: all 300ms ease; }.collection-card-wrapper .single-collection-card .overlay-text{font-size:50px;font-weight:400;max-width:234px;font-style:italic;color:#060c3b;font-family:Technor, Z06-Walone, Noto Sans JP;position:absolute;bottom:30px;left:30px;margin:0}@media (max-width:1024px){.section-title{padding:0 30px}}@media (max-width:991px){.collection-card-wrapper{flex-wrap:wrap}}@media (max-width:768px) {.collection-card-wrapper .single-collection-card .overlay-text{font-size:32px; bottom:20px}.section-title{margin-top:32px}.section-title h2{font-size:28px;line-height:normal}} @media (max-width:525px){.collection-card-wrapper .single-collection-card .overlay-text{font-size:18px; bottom:10px} .section-title{margin-top:28px}.section-title h2{font-size:20px;} .collection-card-wrapper{gap:10px; 15px; row-gap:15px; column-gap:0px;justify-content: space-between;margin-top: 15px;} .collection-card-wrapper .single-collection-card {width:48%;}}',
                        ]),
                    ],
                    [
                        'theme_customization_id' => 9,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'title'   => trans('installer::app.seeders.shop.theme-customizations.all-products.options.title', [], $locale),
                            'filters' => [
                                'sort'  => 'desc',
                                'limit' => 10,
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 10,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'html' => '<div class="section-gap t-shirt-information container"> <div class="inline-col-wrapper direction-rtl"> <div class="inline-col-image-wrapper"> <img src="" data-src="' . $this->storeFileIfExists('theme/10', 'static/' . $locale . '/10.webp', 'static/en/10.webp') . '" class="lazy" width="632" height="510" alt="' . trans('installer::app.seeders.shop.theme-customizations.t-shirt-information.content.title', [], $locale) . '"> </div> <div class="inline-col-content-wrapper direction-ltr"> <h2 class="inline-col-title"> ' . trans('installer::app.seeders.shop.theme-customizations.t-shirt-information.content.title', [], $locale) . ' </h2> <p class="inline-col-description">' . trans('installer::app.seeders.shop.theme-customizations.t-shirt-information.content.description', [], $locale) . '</p> <button class="primary-button max-md:rounded-lg max-md:px-4 max-md:py-2.5 max-md:text-sm">' . trans('installer::app.seeders.shop.theme-customizations.t-shirt-information.content.btn-title', [], $locale) . '</button> </div> </div> </div>',

                            'css'  => '.section-gap{margin-top:80px}.direction-ltr{direction:ltr}.direction-rtl{direction:rtl}.inline-col-wrapper{display:grid;grid-template-columns:auto 1fr;grid-gap:60px;align-items:center}.inline-col-wrapper .inline-col-image-wrapper{overflow:hidden}.inline-col-wrapper .inline-col-image-wrapper img{max-width:100%;height:auto;border-radius:16px;text-indent:-9999px}.inline-col-wrapper .inline-col-content-wrapper{display:flex;flex-wrap:wrap;gap:20px;max-width:464px}.inline-col-wrapper .inline-col-content-wrapper .inline-col-title{max-width:442px;font-size:60px;font-weight:400;color:#060c3b;line-height:70px;font-family:Technor, Z06-Walone, Noto Sans JP;margin:0}.inline-col-wrapper .inline-col-content-wrapper .inline-col-description{margin:0;font-size:18px;color:#6e6e6e;font-family:Poppins, Z06-Walone, Noto Sans JP}@media (max-width:991px){.inline-col-wrapper{grid-template-columns:1fr;grid-gap:16px}.inline-col-wrapper .inline-col-content-wrapper{gap:10px}}@media (max-width:768px) {.inline-col-wrapper .inline-col-image-wrapper img {max-width:100%;}.inline-col-wrapper .inline-col-content-wrapper{max-width:100%;justify-content:center; text-align:center} .section-gap{padding:0 30px; gap:20px;margin-top:24px} .t-shirt-information{margin-top:32px;}} @media (max-width:525px){.inline-col-wrapper .inline-col-content-wrapper{gap:10px} .inline-col-wrapper .inline-col-content-wrapper .inline-col-title{font-size:20px;line-height:normal} .section-gap{padding:0 15px; gap:15px;margin-top:10px} .t-shirt-information{margin-top:28px;}  .inline-col-description{font-size:16px !important} .inline-col-wrapper{grid-gap:15px}',
                        ]),
                    ],
                    [
                        'theme_customization_id' => 11,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'column_1' => [
                                [
                                    'url'        => $appUrl . '/page/about-us',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.about-us', [], $locale),
                                    'sort_order' => 1,
                                ],
                                [
                                    'url'        => $appUrl . '/contact-us',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.contact-us', [], $locale),
                                    'sort_order' => 2,
                                ],
                                [
                                    'url'        => $appUrl . '/page/customer-service',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.customer-service', [], $locale),
                                    'sort_order' => 3,
                                ],
                                [
                                    'url'        => $appUrl . '/page/whats-new',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.whats-new', [], $locale),
                                    'sort_order' => 4,
                                ],
                                [
                                    'url'        => $appUrl . '/page/terms-of-use',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.terms-of-use', [], $locale),
                                    'sort_order' => 5,
                                ],
                                [
                                    'url'        => $appUrl . '/page/terms-conditions',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.terms-conditions', [], $locale),
                                    'sort_order' => 6,
                                ],
                            ],

                            'column_2' => [
                                [
                                    'url'        => $appUrl . '/page/privacy-policy',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.privacy-policy', [], $locale),
                                    'sort_order' => 1,
                                ],
                                [
                                    'url'        => $appUrl . '/page/payment-policy',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.payment-policy', [], $locale),
                                    'sort_order' => 2,
                                ],
                                [
                                    'url'        => $appUrl . '/page/shipping-policy',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.shipping-policy', [], $locale),
                                    'sort_order' => 3,
                                ],
                                [
                                    'url'        => $appUrl . '/page/refund-policy',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.refund-policy', [], $locale),
                                    'sort_order' => 4,
                                ],
                                [
                                    'url'        => $appUrl . '/page/return-policy',
                                    'title'      => trans('installer::app.seeders.shop.theme-customizations.footer-links.options.return-policy', [], $locale),
                                    'sort_order' => 5,
                                ],
                            ],
                        ]),
                    ],
                    [
                        'theme_customization_id' => 12,
                        'locale'                 => $locale,
                        'options'                => json_encode([
                            'services' => [
                                [
                                    'title'         => trans('installer::app.seeders.shop.theme-customizations.services-content.title.free-shipping', [], $locale),
                                    'description'   => trans('installer::app.seeders.shop.theme-customizations.services-content.description.free-shipping-info', [], $locale),
                                    'service_icon'  => 'icon-truck',
                                ],
                                [
                                    'title'         => trans('installer::app.seeders.shop.theme-customizations.services-content.title.product-replace', [], $locale),
                                    'description'   => trans('installer::app.seeders.shop.theme-customizations.services-content.description.product-replace-info', [], $locale),
                                    'service_icon'  => 'icon-product',
                                ],
                                [
                                    'title'         => trans('installer::app.seeders.shop.theme-customizations.services-content.title.emi-available', [], $locale),
                                    'description'   => trans('installer::app.seeders.shop.theme-customizations.services-content.description.emi-available-info', [], $locale),
                                    'service_icon'  => 'icon-dollar-sign',
                                ],
                                [
                                    'title'         => trans('installer::app.seeders.shop.theme-customizations.services-content.title.time-support', [], $locale),
                                    'description'   => trans('installer::app.seeders.shop.theme-customizations.services-content.description.time-support-info', [], $locale),
                                    'service_icon'  => 'icon-support',
                                ],
                            ],
                        ]),
                    ],
                ]);
        }

        DB::table('products')->delete();

        $defaultLocale = $parameters['default_locale'] ?? config('app.locale');

        $now = Carbon::now();

        $locales = $parameters['allowed_locales'] ?? [$defaultLocale];

        $localeProductsData = $this->prepareProductsData($locales);

        $products = Arr::map($localeProductsData[$defaultLocale], function ($row) {
            return Arr::only($row, ['parent_id', 'sku', 'type', 'attribute_family_id', 'created_at', 'updated_at']);
        });

        // Category seeder.
        $seeder = new CategoryTableSeeder;

        $seeder->defaultCategories($parameters);

        DB::table('products')->insert($products);

        $createdProducts = DB::table('products')->get();

        $attributes = DB::table('attributes')->get();

        $productsAttributeValues = [];

        $attributeValues = [];

        foreach ($localeProductsData as $locale => $productsData) {
            $productsFlatData = Arr::map($localeProductsData[$locale], function ($row) {
                return Arr::except($row, ['color', 'size', 'parent_id']);
            });

            DB::table('product_flat')->insert($productsFlatData);

            $skipAttributes = ['product_id', 'parent_id', 'type', 'attribute_family_id', 'locale', 'channel', 'created_at', 'updated_at'];

            $localeSpecificAttributes = ['name', 'url_key', 'short_description', 'description', 'meta_title', 'meta_keywords', 'meta_description'];

            foreach ($productsData as $productData) {
                foreach ($productData as $attributeCode => $value) {
                    if (in_array($attributeCode, $skipAttributes)) {
                        continue;
                    }

                    if ($locale !== 'en' && ! in_array($attributeCode, $localeSpecificAttributes)) {
                        continue;
                    }

                    $attribute = $attributes->where('code', $attributeCode)->first();

                    $uniqueId = implode('|', array_filter([
                        $attribute->value_per_channel ? 'default' : null,
                        $attribute->value_per_locale ? $locale : null,
                        $productData['product_id'],
                        $attribute->id,
                    ]));

                    if (array_key_exists($uniqueId, $productsAttributeValues)) {
                        continue;
                    }

                    $attributeTypeValues = array_fill_keys(array_values($this->attributeTypeFields), null);

                    $attributeValues[] = array_merge($attributeTypeValues, [
                        'attribute_id'                               => $attribute->id,
                        'product_id'                                 => $productData['product_id'],
                        $this->attributeTypeFields[$attribute->type] => $value,
                        'channel'                                    => $attribute->value_per_channel ? 'default' : null,
                        'locale'                                     => $attribute->value_per_locale ? $locale : null,
                        'unique_id'                                  => $uniqueId,
                        'json_value'                                 => null,
                    ]);
                }
            }
        }

        DB::table('product_attribute_values')->insert($attributeValues);

        foreach ($createdProducts as $product) {
            $product = (array) $product;

            DB::table('product_channels')->insert([
                'product_id' => $product['id'],
                'channel_id' => 1,
            ]);
        }

        // Product Inventories
        DB::table('product_inventories')->insert([
            [
                'id'                   => 1,
                'product_id'           => 1,
                'vendor_id'            => 0,
                'inventory_source_id'  => 1,
                'qty'                  => 5,
            ],
        ]);

        // Product Categories
        DB::table('product_categories')->insert([
            [
                'product_id'  => 1,
                'category_id' => 2,
            ],
        ]);

        DB::table('product_images')->insert([
            [
                'id'         => 1,
                'type'       => 'image',
                'path'       => $this->productImages('product/1', '1.webp'),
                'product_id' => 1,
                'position'   => 1,
            ],
        ]);
    }

    /**
     * Store image in storage.
     *
     * @return void
     */
    public function storeFileIfExists($targetPath, $file, $default = null)
    {
        if (file_exists(base_path(self::BASE_PATH . $file))) {
            return 'storage/' . Storage::putFile($targetPath, new File(base_path(self::BASE_PATH . $file)));
        }

        if (! $default) {
            return;
        }

        if (file_exists(base_path(self::BASE_PATH . $default))) {
            return 'storage/' . Storage::putFile($targetPath, new File(base_path(self::BASE_PATH . $default)));
        }
    }


    /**
     * Retrieve all product data in array format.
     *
     * @return array
     */
    public function prepareProductsData($locales)
    {
        $products = [];

        $currentDate = Carbon::now();

        $now = $currentDate->format('Y-m-d H:i:s');

        foreach ($locales as $locale) {
            $products[$locale] = [
                [
                    'sku'                  => 'SP-001',
                    'type'                 => 'simple',
                    'product_number'       => null,
                    'name'                 => trans('installer::app.seeders.sample-products.product-flat.1.name', [], $locale),
                    'short_description'    => trans('installer::app.seeders.sample-products.product-flat.1.short-description', [], $locale),
                    'description'          => trans('installer::app.seeders.sample-products.product-flat.1.description', [], $locale),
                    'url_key'              => 'arctic-cozy-knit-unisex-beanie',
                    'new'                  => 1,
                    'featured'             => 1,
                    'status'               => 1,
                    'meta_title'           => trans('installer::app.seeders.sample-products.product-flat.1.meta-title', [], $locale),
                    'meta_keywords'        => trans('installer::app.seeders.sample-products.product-flat.1.meta-keywords', [], $locale),
                    'meta_description'     => trans('installer::app.seeders.sample-products.product-flat.1.meta-description', [], $locale),
                    'price'                => 14,
                    'special_price'        => null,
                    'special_price_from'   => null,
                    'special_price_to'     => null,
                    'weight'               => 1.23,
                    'created_at'           => $now,
                    'locale'               => $locale,
                    'channel'              => 'default',
                    'attribute_family_id'  => 1,
                    'product_id'           => 1,
                    'updated_at'           => $now,
                    'parent_id'            => null,
                    'visible_individually' => 1,
                ]
            ];
        }

        return $products;
    }

    /**
     * Store image in storage.
     *
     * @return string|null
     */
    public function productImages($targetPath, $file, $default = null)
    {
        if (file_exists(base_path(self::BASE_PRODUCT_PATH . $file))) {
            return Storage::putFile($targetPath, new File(base_path(self::BASE_PRODUCT_PATH . $file)));
        }

        if (! $default) {
            return;
        }

        if (file_exists(base_path(self::BASE_PATH . $default))) {
            return Storage::putFile($targetPath, new File(base_path(self::BASE_PRODUCT_PATH . $default)));
        }
    }
}
