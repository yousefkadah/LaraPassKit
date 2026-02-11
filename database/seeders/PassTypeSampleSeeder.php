<?php

namespace Database\Seeders;

use App\Models\MediaLibraryAsset;
use App\Models\PassTypeSample;
use Illuminate\Database\Seeder;

class PassTypeSampleSeeder extends Seeder
{
    /**
     * Seed the application's database with system samples.
     */
    public function run(): void
    {
        $slots = [
            'icon' => 'sample-assets/icon.svg',
            'logo' => 'sample-assets/logo.svg',
            'strip' => 'sample-assets/strip.svg',
            'thumbnail' => 'sample-assets/thumbnail.svg',
            'background' => 'sample-assets/background.svg',
            'footer' => 'sample-assets/footer.svg',
        ];

        $assets = [];

        foreach ($slots as $slot => $path) {
            $assets[$slot] = MediaLibraryAsset::updateOrCreate(
                [
                    'source' => 'system',
                    'slot' => $slot,
                ],
                [
                    'owner_user_id' => null,
                    'path' => $path,
                    'url' => '/'.$path,
                    'width' => 512,
                    'height' => 512,
                    'mime' => 'image/svg+xml',
                    'size_bytes' => 0,
                ]
            );
        }

        $sampleImageIds = collect($assets)->mapWithKeys(fn ($asset, $slot) => [$slot => $asset->id])->all();

        $passTypes = [
            'generic',
            'coupon',
            'eventTicket',
            'boardingPass',
            'storeCard',
            'loyalty',
            'stampCard',
            'offer',
            'transit',
        ];

        $sampleSets = [
            [
                'suffix' => 'Starter',
                'description' => 'Starter experience',
                'header' => 'Member',
                'primary' => 'Access',
                'secondary' => 'Zone A',
                'auxiliary' => 'Seat 12',
            ],
            [
                'suffix' => 'Plus',
                'description' => 'Enhanced perks',
                'header' => 'VIP',
                'primary' => 'Priority',
                'secondary' => 'Zone B',
                'auxiliary' => 'Seat 24',
            ],
            [
                'suffix' => 'Premium',
                'description' => 'Premium access',
                'header' => 'Premium',
                'primary' => 'All-Access',
                'secondary' => 'Zone C',
                'auxiliary' => 'Seat 36',
            ],
        ];

        foreach ($passTypes as $passType) {
            foreach ($sampleSets as $index => $sample) {
                $name = ucfirst($passType).' '.$sample['suffix'];

                PassTypeSample::updateOrCreate(
                    [
                        'source' => 'system',
                        'name' => $name,
                        'pass_type' => $passType,
                        'platform' => null,
                    ],
                    [
                        'owner_user_id' => null,
                        'description' => $sample['description'],
                        'fields' => [
                            'description' => $name,
                            'organizationName' => 'Placeholder Co.',
                            'logoText' => strtoupper(substr($passType, 0, 3)),
                            'backgroundColor' => ['#f1f5f9', '#e0f2fe', '#fef9c3'][$index % 3],
                            'foregroundColor' => '#111827',
                            'labelColor' => '#6b7280',
                            'headerFields' => [
                                ['key' => 'header1', 'label' => 'Type', 'value' => $sample['header']],
                            ],
                            'primaryFields' => [
                                ['key' => 'primary1', 'label' => 'Access', 'value' => $sample['primary']],
                            ],
                            'secondaryFields' => [
                                ['key' => 'secondary1', 'label' => 'Section', 'value' => $sample['secondary']],
                            ],
                            'auxiliaryFields' => [
                                ['key' => 'aux1', 'label' => 'Seat', 'value' => $sample['auxiliary']],
                            ],
                            'backFields' => [
                                ['key' => 'terms', 'label' => 'Notes', 'value' => 'Placeholder terms and details.'],
                            ],
                            'transitType' => in_array($passType, ['boardingPass', 'transit'], true)
                                ? 'PKTransitTypeGeneric'
                                : null,
                        ],
                        'images' => $sampleImageIds,
                    ]
                );
            }
        }
    }
}
