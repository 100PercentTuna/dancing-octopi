/**
 * Customizer Preview Handler
 * 
 * Handles live preview updates in the WordPress Customizer.
 * Uses debouncing to prevent excessive refreshes while typing.
 * 
 * @package Kunaal_Theme
 * @since 4.11.2
 */
(function($) {
    'use strict';
    
    // ========================================
    // DEBOUNCE UTILITY
    // Prevents rapid successive calls
    // ========================================
    function debounce(func, wait) {
        var timeout;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }
    
    // ========================================
    // LIVE TEXT UPDATES (No Refresh)
    // These update in real-time without page reload
    // ========================================
    
    // Author First Name
    wp.customize('kunaal_author_first_name', function(value) {
        value.bind(debounce(function(newval) {
            $('.nameLine span:first-child').text(newval);
            $('.hero-name').each(function() {
                var $this = $(this);
                var lastName = $this.find('.hero-name-last').text();
                $this.html(newval + ' <span class="hero-name-last">' + lastName + '</span>');
            });
        }, 150));
    });
    
    // Author Last Name
    wp.customize('kunaal_author_last_name', function(value) {
        value.bind(debounce(function(newval) {
            $('.surname').text(newval);
            $('.hero-name-last').text(newval);
        }, 150));
    });
    
    // Author Tagline
    wp.customize('kunaal_author_tagline', function(value) {
        value.bind(debounce(function(newval) {
            $('.subtitle').text(newval);
        }, 150));
    });
    
    // Hero Annotation (About page)
    wp.customize('kunaal_about_hero_annotation', function(value) {
        value.bind(debounce(function(newval) {
            $('.hero-annotation').text(newval);
        }, 150));
    });
    
    // Contact Headline
    wp.customize('kunaal_contact_headline', function(value) {
        value.bind(debounce(function(newval) {
            $('.ledgerHeadline').text(newval);
        }, 150));
    });
    
    // Contact Intro
    wp.customize('kunaal_contact_intro', function(value) {
        value.bind(debounce(function(newval) {
            $('.ledgerIntro').text(newval);
        }, 150));
    });
    
    // Contact Response Time
    wp.customize('kunaal_contact_response_time', function(value) {
        value.bind(debounce(function(newval) {
            $('.ledgerResponse').text(newval);
        }, 150));
    });
    
    // Footer Disclaimer
    wp.customize('kunaal_footer_disclaimer', function(value) {
        value.bind(debounce(function(newval) {
            $('.footerDisclaimer').text(newval);
        }, 150));
    });
    
    // ========================================
    // SECTION LABELS (About Page)
    // ========================================
    
    // Books Label
    wp.customize('kunaal_about_books_label', function(value) {
        value.bind(debounce(function(newval) {
            $('.about-books .gallery-label-title').text(newval);
        }, 150));
    });
    
    // Map Label
    wp.customize('kunaal_about_map_label', function(value) {
        value.bind(debounce(function(newval) {
            $('.about-map .gallery-label-title').text(newval);
        }, 150));
    });
    
    // Interests Label
    wp.customize('kunaal_about_interests_label', function(value) {
        value.bind(debounce(function(newval) {
            $('.about-interests .gallery-label-title').text(newval);
        }, 150));
    });
    
    // Inspirations Label
    wp.customize('kunaal_about_inspirations_label', function(value) {
        value.bind(debounce(function(newval) {
            $('.about-inspirations .gallery-label-title').text(newval);
        }, 150));
    });
    
    // Connect Heading
    wp.customize('kunaal_about_connect_heading', function(value) {
        value.bind(debounce(function(newval) {
            $('.about-connect-title').text(newval);
        }, 150));
    });
    
    // ========================================
    // SECTION VISIBILITY TOGGLES
    // These require a refresh but are toggled
    // ========================================
    
    var sectionToggles = [
        'kunaal_about_hero_show',
        'kunaal_about_bio_show',
        'kunaal_about_map_show',
        'kunaal_about_books_show',
        'kunaal_about_interests_show',
        'kunaal_about_inspirations_show',
        'kunaal_about_stats_show',
        'kunaal_about_connect_show'
    ];
    
    // Visibility toggles need refresh - debounced
    sectionToggles.forEach(function(setting) {
        wp.customize(setting, function(value) {
            value.bind(debounce(function() {
                wp.customize.preview.send('refresh');
            }, 500));
        });
    });

})(jQuery);









