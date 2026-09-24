<?php
// Every editable table and column is explicitly allowed here, never taken from a URL.
function field(
    string $label,
    string $type = 'text',
    bool $required = false,
    array $choices = [],
    string $help = '',
): array {
    return compact('label', 'type', 'required', 'choices', 'help');
}
$availability = [
    'enquire' => 'Enquire with team',
    'available' => 'Available',
    'sold_out' => 'Sold out',
    'coming_soon' => 'Coming soon',
];
$entities = [
    'locations' => [
        'label' => 'Locations',
        'title' => 'name',
        'fields' => [
            'name' => field('Location name', 'text', true),
            'description' => field('Location description', 'textarea'),
            'cover_image' => field('Location image', 'image'),
        ],
    ],
    'estates' => [
        'label' => 'Estates',
        'title' => 'name',
        'fields' => [
            'name' => field('Estate name', 'text', true),
            'slug' => field(
                'URL name',
                'slug',
                true,
                [],
                'Lowercase letters, numbers and hyphens; keep stable once shared.',
            ),
            'location_id' => field('Location', 'locations', true),
            'summary' => field('Short introduction', 'textarea', true),
            'description' => field('Full description', 'textarea', true),
            'address' => field('Estate address', 'text'),
            'category' => field('Offer category', 'select', true, [
                'land' => 'Land',
                'home' => 'Completed homes',
                'mixed' => 'Land and completed homes',
            ]),
            'availability' => field('Sales availability', 'select', true, $availability),
            'development_status' => field('Development status', 'text', true),
            'amenities' => field(
                'Published / planned features',
                'textarea',
                false,
                [],
                'One feature per line. The site explains that installed status must be confirmed.',
            ),
            'documentation' => field('Approved documentation information', 'textarea'),
            'cover_image' => field('Main estate image', 'image'),
            'image_kind' => field('Main image classification', 'select', true, [
                'rendering' => 'Architectural rendering',
                'photograph' => 'Property photograph',
            ]),
            'brochure' => field('Brochure (PDF)', 'pdf'),
            'latitude' => field('Latitude', 'latitude'),
            'longitude' => field('Longitude', 'longitude'),
            'featured' => field('Feature this estate', 'checkbox'),
            'published' => field('Publish estate', 'checkbox'),
        ],
    ],
    'property_options' => [
        'label' => 'Plots & pricing',
        'title' => 'name',
        'fields' => [
            'estate_id' => field('Estate', 'estates', true),
            'name' => field('Option name', 'text', true),
            'size_sqm' => field('Size in square metres', 'decimal', true),
            'kind' => field('Offer type', 'select', true, [
                'land' => 'Land',
                'home' => 'Completed home',
            ]),
            'bedrooms' => field('Bedrooms (completed homes only)', 'number'),
            'availability' => field('Sales availability', 'select', true, $availability),
            'outright_price' => field('Outright price (NGN)', 'decimal'),
            'instalment_total' => field('Instalment total (NGN)', 'decimal'),
            'deposit' => field('Initial deposit (NGN)', 'decimal'),
            'duration_months' => field('Payment duration (months)', 'number'),
            'payment_schedule' => field('Payment schedule', 'textarea'),
            'additional_charges' => field('Additional charges', 'textarea'),
            'price_includes' => field('Exactly what the price includes', 'textarea', true),
            'price_updated' => field('Price checked on', 'date'),
            'price_verified' => field(
                'Company has verified the pricing',
                'checkbox',
                false,
                [],
                'Requires an outright price, price date, inclusions, and additional-charge statement. Partial instalment plans cannot be published.',
            ),
            'published' => field('Publish option', 'checkbox'),
        ],
    ],
    'prototypes' => [
        'label' => 'Building designs',
        'title' => 'name',
        'fields' => [
            'name' => field('Design name', 'text', true),
            'slug' => field('URL name', 'slug', true),
            'bedrooms' => field('Bedrooms', 'number', true),
            'bathrooms' => field('Bathrooms', 'number'),
            'description' => field('Design description', 'textarea', true),
            'specifications' => field('Specifications', 'textarea'),
            'offer_includes' => field('What the offer includes / excludes', 'textarea', true),
            'cover_image' => field('Architectural rendering', 'image'),
            'floor_plan' => field('Floor plan (image or PDF)', 'document'),
            'published' => field('Publish design', 'checkbox'),
        ],
    ],
    'compatibility' => [
        'label' => 'Design compatibility',
        'title' => 'id',
        'fields' => [
            'option_id' => field('Estate and plot option', 'property_options', true),
            'prototype_id' => field('Building design', 'prototypes', true),
            'notes' => field(
                'Private approval notes',
                'textarea',
                false,
                [],
                'Record who approved suitability and when. Never infer it from plot size alone.',
            ),
            'approved' => field('Company approves this specific association', 'checkbox'),
        ],
    ],
    'media' => [
        'label' => 'Images & documents',
        'title' => 'alt',
        'fields' => [
            'estate_id' => field('Estate (choose one owner)', 'estates'),
            'prototype_id' => field('Design (choose one owner)', 'prototypes'),
            'alt' => field('Descriptive alternative text', 'text', true),
            'kind' => field('Media classification', 'select', true, [
                'rendering' => 'Architectural rendering',
                'photograph' => 'Property photograph',
                'floor_plan' => 'Floor plan',
                'brochure' => 'Brochure',
            ]),
            'path' => field('Upload file', 'document', true),
            'sort_order' => field('Display order', 'number'),
        ],
    ],
    'faqs' => [
        'label' => 'FAQs',
        'title' => 'question',
        'fields' => [
            'question' => field('Question', 'text', true),
            'answer' => field(
                'Answer',
                'textarea',
                true,
                [],
                'Use {{phone}}, {{email}}, and {{address}} to pull live contact settings.',
            ),
            'sort_order' => field('Display order', 'number'),
            'published' => field('Publish answer', 'checkbox'),
        ],
    ],
    'articles' => [
        'label' => 'News & updates',
        'title' => 'title',
        'fields' => [
            'title' => field('Title', 'text', true),
            'slug' => field('URL name', 'slug', true),
            'category' => field('Category', 'select', true, [
                'Project update' => 'Project update',
                'Guide' => 'Guide',
                'News' => 'News',
            ]),
            'summary' => field('Short introduction', 'textarea', true),
            'body' => field('Article text', 'textarea', true),
            'cover_image' => field('Cover image', 'image'),
            'published' => field('Publish article', 'checkbox'),
        ],
    ],
    'testimonials' => [
        'label' => 'Testimonials',
        'title' => 'name',
        'fields' => [
            'name' => field('Customer name', 'text', true),
            'attribution' => field('Attribution', 'text'),
            'quote' => field('Approved customer words', 'textarea', true),
            'approved' => field('Company confirms permission and authenticity', 'checkbox'),
            'published' => field('Publish testimonial', 'checkbox'),
        ],
    ],
];
function relation_choices(string $type): ?array
{
    $queries = [
        'locations' => 'SELECT id,name FROM locations ORDER BY name',
        'estates' => 'SELECT id,name FROM estates ORDER BY name',
        'prototypes' => 'SELECT id,name FROM prototypes ORDER BY name',
        'property_options' =>
            "SELECT o.id,CONCAT(e.name,' — ',o.name) AS name FROM property_options o JOIN estates e ON e.id=o.estate_id ORDER BY e.name,o.size_sqm",
    ];
    return isset($queries[$type]) ? array_column(rows($queries[$type]), 'name', 'id') : null;
}
