# 11 - ADMIN & CUSTOMIZER
## Complete Specification - NO JSON

---

## CORE PRINCIPLE

**NO JSON TEXTAREAS.** All data entry must be:
- Individual text fields
- Image upload controls
- Select dropdowns
- Checkboxes
- Comma-separated simple lists (ISO codes only)

---

## CUSTOMIZER SECTIONS

```
Appearance > Customize
└── About Page
    ├── Hero Section
    │   ├── [toggle] Show Hero
    │   ├── [text] Name
    │   ├── [text] Tagline
    │   ├── [text] Annotation
    │   ├── [image] Photo 1
    │   ├── [image] Photo 2
    │   ├── [image] Photo 3
    │   └── [image] Photo 4
    │
    ├── Bio Section
    │   ├── [toggle] Show Bio
    │   ├── [toggle] Show Pull Quote
    │   ├── [textarea] Pull Quote Text
    │   └── [text] Pull Quote Attribution
    │
    ├── Bookshelf
    │   ├── [toggle] Show Bookshelf
    │   └── Books 1-8
    │       ├── [image] Cover
    │       ├── [text] Title
    │       ├── [text] Author
    │       └── [url] Link (optional)
    │
    ├── World Map
    │   ├── [toggle] Show Map
    │   ├── [text] Section Label
    │   ├── [text] Countries Visited (comma-separated ISO)
    │   ├── [text] Countries Lived (comma-separated ISO)
    │   ├── [text] Current Location (single ISO)
    │   └── Country Stories 1-10
    │       ├── [text] Country Code
    │       ├── [text] Years
    │       └── [textarea] Story
    │
    ├── Interests
    │   ├── [toggle] Show Interests
    │   ├── [text] Section Label
    │   └── Interests 1-20
    │       ├── [text] Name
    │       └── [image] Image
    │
    ├── Inspirations
    │   ├── [toggle] Show Inspirations
    │   ├── [text] Section Label
    │   └── Inspirations 1-8
    │       ├── [image] Photo
    │       ├── [text] Name
    │       ├── [text] Role
    │       ├── [text] Note
    │       └── [url] URL
    │
    ├── Stats
    │   ├── [toggle] Show Stats
    │   └── Stats 1-4
    │       ├── [number] Value
    │       └── [text] Label
    │
    ├── Atmospheric Images
    │   └── Images 1-12
    │       ├── [image] Image
    │       ├── [select] Type (strip/window/dual/bg)
    │       ├── [select] Position
    │       ├── [select] Clip Style
    │       ├── [toggle] Has Quote
    │       ├── [textarea] Quote Text
    │       ├── [text] Quote Attribution
    │       └── [text] Caption
    │
    └── Connect Section
        ├── [toggle] Show Connect
        └── Social Links (handled elsewhere)
```

---

## COMPLETE PHP IMPLEMENTATION

```php
function kunaal_about_customizer($wp_customize) {
    
    // ============================
    // PANEL: About Page
    // ============================
    $wp_customize->add_panel('kunaal_about_panel', array(
        'title' => 'About Page',
        'priority' => 30,
    ));
    
    // ============================
    // SECTION: Hero
    // ============================
    $wp_customize->add_section('kunaal_about_hero', array(
        'title' => 'Hero Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 10,
    ));
    
    // Show Hero
    $wp_customize->add_setting('kunaal_about_hero_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_hero_show', array(
        'label' => 'Show Hero Section',
        'section' => 'kunaal_about_hero',
        'type' => 'checkbox',
    ));
    
    // Name
    $wp_customize->add_setting('kunaal_about_hero_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_hero_name', array(
        'label' => 'Name',
        'description' => 'Displayed in the hero (default: site title)',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Tagline
    $wp_customize->add_setting('kunaal_about_hero_tagline', array(
        'default' => 'writer · analyst · curious mind',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_hero_tagline', array(
        'label' => 'Tagline',
        'description' => 'Short descriptor (use · to separate items)',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Annotation
    $wp_customize->add_setting('kunaal_about_hero_annotation', array(
        'default' => 'still figuring it out',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_hero_annotation', array(
        'label' => 'Handwritten Annotation',
        'description' => 'Small personal note (max 40 chars)',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Photos 1-4
    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("kunaal_about_hero_photo_{$i}", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_hero_photo_{$i}", array(
                'label' => "Photo {$i}",
                'description' => $i === 1 ? 'Primary photo (required)' : 'Optional',
                'section' => 'kunaal_about_hero',
                'mime_type' => 'image',
            )
        ));
    }
    
    // ============================
    // SECTION: Bio
    // ============================
    $wp_customize->add_section('kunaal_about_bio', array(
        'title' => 'Bio Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 20,
        'description' => 'Bio content comes from the page editor. Configure pull quote here.',
    ));
    
    // Show Bio
    $wp_customize->add_setting('kunaal_about_bio_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_bio_show', array(
        'label' => 'Show Bio Section',
        'section' => 'kunaal_about_bio',
        'type' => 'checkbox',
    ));
    
    // Show Pull Quote
    $wp_customize->add_setting('kunaal_about_pullquote_show', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_show', array(
        'label' => 'Show Pull Quote',
        'section' => 'kunaal_about_bio',
        'type' => 'checkbox',
    ));
    
    // Pull Quote Text
    $wp_customize->add_setting('kunaal_about_pullquote_text', array(
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_text', array(
        'label' => 'Pull Quote Text',
        'section' => 'kunaal_about_bio',
        'type' => 'textarea',
    ));
    
    // Pull Quote Attribution
    $wp_customize->add_setting('kunaal_about_pullquote_attr', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_attr', array(
        'label' => 'Pull Quote Attribution',
        'section' => 'kunaal_about_bio',
        'type' => 'text',
    ));
    
    // ============================
    // SECTION: Bookshelf
    // ============================
    $wp_customize->add_section('kunaal_about_books', array(
        'title' => 'Bookshelf',
        'panel' => 'kunaal_about_panel',
        'priority' => 30,
    ));
    
    // Show Bookshelf
    $wp_customize->add_setting('kunaal_about_books_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_books_show', array(
        'label' => 'Show Bookshelf',
        'section' => 'kunaal_about_books',
        'type' => 'checkbox',
    ));
    
    // Books 1-8
    for ($i = 1; $i <= 8; $i++) {
        // Cover
        $wp_customize->add_setting("kunaal_book_{$i}_cover", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_book_{$i}_cover", array(
                'label' => "Book {$i}: Cover",
                'section' => 'kunaal_about_books',
                'mime_type' => 'image',
            )
        ));
        
        // Title
        $wp_customize->add_setting("kunaal_book_{$i}_title", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_title", array(
            'label' => "Book {$i}: Title",
            'section' => 'kunaal_about_books',
            'type' => 'text',
        ));
        
        // Author
        $wp_customize->add_setting("kunaal_book_{$i}_author", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_author", array(
            'label' => "Book {$i}: Author",
            'section' => 'kunaal_about_books',
            'type' => 'text',
        ));
        
        // Link
        $wp_customize->add_setting("kunaal_book_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_url", array(
            'label' => "Book {$i}: Link (optional)",
            'section' => 'kunaal_about_books',
            'type' => 'url',
        ));
    }
    
    // ============================
    // SECTION: World Map
    // ============================
    $wp_customize->add_section('kunaal_about_map', array(
        'title' => 'World Map',
        'panel' => 'kunaal_about_panel',
        'priority' => 40,
    ));
    
    // Show Map
    $wp_customize->add_setting('kunaal_about_map_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_map_show', array(
        'label' => 'Show Map Section',
        'section' => 'kunaal_about_map',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_map_label', array(
        'default' => 'Places I\'ve Called Home',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_map',
        'type' => 'text',
    ));
    
    // Countries Visited (comma-separated)
    $wp_customize->add_setting('kunaal_about_map_visited', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_visited', array(
        'label' => 'Countries Visited',
        'description' => 'Comma-separated ISO codes (e.g., US, GB, JP, FR)',
        'section' => 'kunaal_about_map',
        'type' => 'text',
    ));
    
    // Countries Lived (comma-separated)
    $wp_customize->add_setting('kunaal_about_map_lived', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_lived', array(
        'label' => 'Countries Lived In',
        'description' => 'Comma-separated ISO codes',
        'section' => 'kunaal_about_map',
        'type' => 'text',
    ));
    
    // Current Location
    $wp_customize->add_setting('kunaal_about_map_current', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_current', array(
        'label' => 'Current Location',
        'description' => 'Single ISO code (e.g., IN)',
        'section' => 'kunaal_about_map',
        'type' => 'text',
    ));
    
    // Country Stories 1-10
    for ($i = 1; $i <= 10; $i++) {
        $wp_customize->add_setting("kunaal_map_story_{$i}_country", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_country", array(
            'label' => "Story {$i}: Country Code",
            'section' => 'kunaal_about_map',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_map_story_{$i}_years", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_years", array(
            'label' => "Story {$i}: Years",
            'section' => 'kunaal_about_map',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_map_story_{$i}_text", array(
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_text", array(
            'label' => "Story {$i}: Story",
            'description' => 'Max 200 characters',
            'section' => 'kunaal_about_map',
            'type' => 'textarea',
        ));
    }
    
    // ============================
    // SECTION: Interests
    // ============================
    $wp_customize->add_section('kunaal_about_interests', array(
        'title' => 'Interests',
        'panel' => 'kunaal_about_panel',
        'priority' => 50,
    ));
    
    // Show Interests
    $wp_customize->add_setting('kunaal_about_interests_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_interests_show', array(
        'label' => 'Show Interests Section',
        'section' => 'kunaal_about_interests',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_interests_label', array(
        'default' => 'Things That Fascinate Me',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_interests_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_interests',
        'type' => 'text',
    ));
    
    // Interests 1-20
    for ($i = 1; $i <= 20; $i++) {
        $wp_customize->add_setting("kunaal_interest_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_interest_{$i}_name", array(
            'label' => "Interest {$i}: Name",
            'section' => 'kunaal_about_interests',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_interest_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_interest_{$i}_image", array(
                'label' => "Interest {$i}: Image",
                'section' => 'kunaal_about_interests',
                'mime_type' => 'image',
            )
        ));
    }
    
    // ============================
    // SECTION: Inspirations
    // ============================
    $wp_customize->add_section('kunaal_about_inspirations', array(
        'title' => 'Inspirations',
        'panel' => 'kunaal_about_panel',
        'priority' => 60,
    ));
    
    // Show Inspirations
    $wp_customize->add_setting('kunaal_about_inspirations_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_show', array(
        'label' => 'Show Inspirations Section',
        'section' => 'kunaal_about_inspirations',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_inspirations_label', array(
        'default' => 'People Who Inspire Me',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_inspirations',
        'type' => 'text',
    ));
    
    // Inspirations 1-8
    for ($i = 1; $i <= 8; $i++) {
        $wp_customize->add_setting("kunaal_inspiration_{$i}_photo", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_inspiration_{$i}_photo", array(
                'label' => "Person {$i}: Photo",
                'section' => 'kunaal_about_inspirations',
                'mime_type' => 'image',
            )
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_name", array(
            'label' => "Person {$i}: Name",
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_role", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_role", array(
            'label' => "Person {$i}: Role/Title",
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_note", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_note", array(
            'label' => "Person {$i}: Note",
            'description' => 'Brief note about why they inspire you',
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_url", array(
            'label' => "Person {$i}: URL",
            'section' => 'kunaal_about_inspirations',
            'type' => 'url',
        ));
    }
    
    // ============================
    // SECTION: Stats
    // ============================
    $wp_customize->add_section('kunaal_about_stats', array(
        'title' => 'Stats Counters',
        'panel' => 'kunaal_about_panel',
        'priority' => 70,
    ));
    
    // Show Stats
    $wp_customize->add_setting('kunaal_about_stats_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_stats_show', array(
        'label' => 'Show Stats Section',
        'section' => 'kunaal_about_stats',
        'type' => 'checkbox',
    ));
    
    // Stats 1-4
    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("kunaal_stat_{$i}_value", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control("kunaal_stat_{$i}_value", array(
            'label' => "Stat {$i}: Number",
            'section' => 'kunaal_about_stats',
            'type' => 'number',
        ));
        
        $wp_customize->add_setting("kunaal_stat_{$i}_label", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_stat_{$i}_label", array(
            'label' => "Stat {$i}: Label",
            'description' => 'e.g., "essays", "countries", "languages"',
            'section' => 'kunaal_about_stats',
            'type' => 'text',
        ));
    }
    
    // ============================
    // SECTION: Atmospheric Images
    // ============================
    $wp_customize->add_section('kunaal_about_atmo', array(
        'title' => 'Atmospheric Images',
        'panel' => 'kunaal_about_panel',
        'priority' => 80,
    ));
    
    // Images 1-12
    for ($i = 1; $i <= 12; $i++) {
        // Image
        $wp_customize->add_setting("kunaal_atmo_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_atmo_{$i}_image", array(
                'label' => "Image {$i}",
                'section' => 'kunaal_about_atmo',
                'mime_type' => 'image',
            )
        ));
        
        // Type
        $wp_customize->add_setting("kunaal_atmo_{$i}_type", array(
            'default' => 'strip',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_type", array(
            'label' => "Image {$i}: Display Type",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'strip' => 'Full-bleed Strip',
                'window' => 'Window Cutout',
                'dual' => 'Dual (pair with next)',
                'background' => 'Background Layer',
                'hidden' => 'Hidden',
            ),
        ));
        
        // Position
        $wp_customize->add_setting("kunaal_atmo_{$i}_position", array(
            'default' => 'auto',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_position", array(
            'label' => "Image {$i}: Position",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'after_hero' => 'After Hero',
                'mid_bio' => 'Middle of Bio',
                'after_bio' => 'After Bio/Bookshelf',
                'after_map' => 'After Map',
                'after_interests' => 'After Interests',
                'after_inspirations' => 'After Inspirations',
                'before_closing' => 'Before Closing',
                'auto' => 'Auto-place',
            ),
        ));
        
        // Clip Style
        $wp_customize->add_setting("kunaal_atmo_{$i}_clip", array(
            'default' => 'straight',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_clip", array(
            'label' => "Image {$i}: Clip Style",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'straight' => 'Straight Edges',
                'angle_bottom' => 'Angle Bottom',
                'angle_top' => 'Angle Top',
                'angle_both' => 'Angle Both',
            ),
        ));
        
        // Has Quote
        $wp_customize->add_setting("kunaal_atmo_{$i}_has_quote", array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_has_quote", array(
            'label' => "Image {$i}: Show Quote Overlay",
            'section' => 'kunaal_about_atmo',
            'type' => 'checkbox',
        ));
        
        // Quote Text
        $wp_customize->add_setting("kunaal_atmo_{$i}_quote", array(
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_quote", array(
            'label' => "Image {$i}: Quote Text",
            'section' => 'kunaal_about_atmo',
            'type' => 'textarea',
        ));
        
        // Quote Attribution
        $wp_customize->add_setting("kunaal_atmo_{$i}_quote_attr", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_quote_attr", array(
            'label' => "Image {$i}: Quote Attribution",
            'section' => 'kunaal_about_atmo',
            'type' => 'text',
        ));
        
        // Caption
        $wp_customize->add_setting("kunaal_atmo_{$i}_caption", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_caption", array(
            'label' => "Image {$i}: Caption",
            'section' => 'kunaal_about_atmo',
            'type' => 'text',
        ));
    }
    
    // ============================
    // SECTION: Connect
    // ============================
    $wp_customize->add_section('kunaal_about_connect', array(
        'title' => 'Connect Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 90,
    ));
    
    // Show Connect
    $wp_customize->add_setting('kunaal_about_connect_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_connect_show', array(
        'label' => 'Show Connect Section',
        'section' => 'kunaal_about_connect',
        'type' => 'checkbox',
    ));
    
    // Heading
    $wp_customize->add_setting('kunaal_about_connect_heading', array(
        'default' => 'Let\'s Connect',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_connect_heading', array(
        'label' => 'Heading',
        'section' => 'kunaal_about_connect',
        'type' => 'text',
    ));
}
add_action('customize_register', 'kunaal_about_customizer');
```

---

## USER STORIES

### US-ADMIN-001: No JSON Input
- [ ] No JSON textareas anywhere
- [ ] Individual fields for all data
- [ ] Comma-separated lists only for ISO codes

### US-ADMIN-002: Image Previews
- [ ] Thumbnail in Customizer for all images
- [ ] Clear which slot each image belongs to

### US-ADMIN-003: Section Toggles
- [ ] Checkbox to show/hide each section
- [ ] Hidden sections don't break layout

### US-ADMIN-004: Helpful Descriptions
- [ ] Description text on complex fields
- [ ] Character limit guidance where relevant
- [ ] Format examples (e.g., ISO codes)

### US-ADMIN-005: Logical Organization
- [ ] Grouped under About Page panel
- [ ] Sections in page order
- [ ] Consistent naming

---

## FINAL CHECKLIST

- [ ] Panel: About Page with all sections
- [ ] Hero: toggle, name, tagline, annotation, 4 photos
- [ ] Bio: toggle, pull quote toggle/text/attr
- [ ] Bookshelf: toggle, 8 books with cover/title/author/url
- [ ] Map: toggle, label, visited/lived/current codes, 10 stories
- [ ] Interests: toggle, label, 20 interests with name/image
- [ ] Inspirations: toggle, label, 8 people with photo/name/role/note/url
- [ ] Stats: toggle, 4 stats with value/label
- [ ] Atmospheric: 12 images with type/position/clip/quote/caption
- [ ] Connect: toggle, heading
- [ ] NO JSON anywhere
- [ ] All fields sanitized properly

