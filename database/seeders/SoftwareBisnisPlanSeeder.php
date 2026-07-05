<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SoftwareBisnisPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'sbp_is_active',
                'value' => '1',
                'type' => 'boolean',
            ],
            [
                'key' => 'sbp_tagline',
                'value' => 'Software Bisnis Plan: Mudah, Murah, dan Cepat',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_title',
                'value' => 'Susun Rencana Bisnis',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_highlighted_title',
                'value' => 'Lebih Cerdas',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_description',
                'value' => 'Platform all-in-one untuk penyusunan strategi, proyeksi keuangan otomatis (NPV, IRR, Payback Period), dan analisis SWOT berbasis AI.',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_callout',
                'value' => 'Semua dalam satu solusi terintegrasi.',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_primary_button_text',
                'value' => 'MULAI SEKARANG GRATIS',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_primary_button_url',
                'value' => '/contact',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_secondary_button_text',
                'value' => 'LIHAT FITUR',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_secondary_button_url',
                'value' => '/services',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_image',
                'value' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_image_alt',
                'value' => 'Dashboard Preview',
                'type' => 'text',
            ],
            [
                'key' => 'sbp_show_laptop_frame',
                'value' => '1',
                'type' => 'boolean',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => 'software_bisnis_plan',
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}
