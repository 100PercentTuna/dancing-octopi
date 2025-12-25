<?php
/**
 * Interest Icons Mapping
 * Maps interest keywords to emojis
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get emoji icon for an interest
 * 
 * @param string $interest The interest name
 * @return string The emoji icon
 */
function kunaal_get_interest_icon($interest) {
    $interest_lower = strtolower(trim($interest));
    
    $icons = array(
        // Food & Drinks
        'ramen' => '🍜',
        'noodles' => '🍜',
        'tacos' => '🌮',
        'mexican food' => '🌮',
        'pizza' => '🍕',
        'sushi' => '🍣',
        'coffee' => '☕',
        'tea' => '🍵',
        'wine' => '🍷',
        'beer' => '🍺',
        'cocktails' => '🍸',
        'cooking' => '👨‍🍳',
        'baking' => '🥐',
        'food' => '🍽️',
        'restaurants' => '🍽️',
        'bbq' => '🍖',
        'barbecue' => '🍖',
        'chocolate' => '🍫',
        'ice cream' => '🍦',
        'seafood' => '🦐',
        'vegetarian' => '🥗',
        'vegan' => '🌱',
        'dim sum' => '🥟',
        'dumplings' => '🥟',
        'curry' => '🍛',
        'indian food' => '🍛',
        'thai food' => '🍜',
        'chinese food' => '🥡',
        'korean food' => '🍲',
        'japanese food' => '🍱',
        'brunch' => '🥞',
        'breakfast' => '🍳',
        
        // Sports
        'soccer' => '⚽',
        'football' => '🏈',
        'college football' => '🏈',
        'nfl' => '🏈',
        'basketball' => '🏀',
        'nba' => '🏀',
        'tennis' => '🎾',
        'golf' => '⛳',
        'baseball' => '⚾',
        'hockey' => '🏒',
        'swimming' => '🏊',
        'running' => '🏃',
        'cycling' => '🚴',
        'biking' => '🚴',
        'skiing' => '⛷️',
        'snowboarding' => '🏂',
        'surfing' => '🏄',
        'hiking' => '🥾',
        'climbing' => '🧗',
        'boxing' => '🥊',
        'martial arts' => '🥋',
        'yoga' => '🧘',
        'cricket' => '🏏',
        'rugby' => '🏉',
        'volleyball' => '🏐',
        'f1' => '🏎️',
        'formula 1' => '🏎️',
        'racing' => '🏎️',
        'motorsports' => '🏎️',
        
        // Technology & Work
        'data visualization' => '📊',
        'data viz' => '📊',
        'dataviz' => '📊',
        'data' => '📊',
        'analytics' => '📈',
        'coding' => '💻',
        'programming' => '💻',
        'software' => '💻',
        'ai' => '🤖',
        'artificial intelligence' => '🤖',
        'machine learning' => '🤖',
        'tech' => '🔧',
        'technology' => '🔧',
        'startups' => '🚀',
        'entrepreneurship' => '🚀',
        'business' => '💼',
        'finance' => '💰',
        'economics' => '📉',
        'investing' => '📈',
        'crypto' => '₿',
        'blockchain' => '⛓️',
        'design' => '🎨',
        'ux' => '✏️',
        'ui' => '🖥️',
        'product' => '📦',
        'strategy' => '🎯',
        'consulting' => '📋',
        
        // History & Politics
        'history' => '📜',
        'ww2' => '⚔️',
        'ww2 history' => '⚔️',
        'world war 2' => '⚔️',
        'world war ii' => '⚔️',
        'ww1' => '⚔️',
        'military history' => '⚔️',
        'ancient history' => '🏛️',
        'roman history' => '🏛️',
        'medieval' => '🏰',
        'geopolitics' => '🌍',
        'politics' => '🏛️',
        'political science' => '🏛️',
        'international relations' => '🌐',
        'diplomacy' => '🤝',
        'elections' => '🗳️',
        'democracy' => '🗳️',
        
        // Arts & Culture
        'music' => '🎵',
        'jazz' => '🎷',
        'rock' => '🎸',
        'classical music' => '🎻',
        'piano' => '🎹',
        'guitar' => '🎸',
        'movies' => '🎬',
        'film' => '🎬',
        'cinema' => '🎬',
        'documentaries' => '🎥',
        'photography' => '📷',
        'art' => '🎨',
        'painting' => '🖼️',
        'sculpture' => '🗿',
        'museums' => '🏛️',
        'architecture' => '🏗️',
        'theater' => '🎭',
        'theatre' => '🎭',
        'dance' => '💃',
        'ballet' => '🩰',
        'opera' => '🎭',
        'poetry' => '📝',
        
        // Reading & Writing
        'reading' => '📚',
        'books' => '📚',
        'literature' => '📖',
        'writing' => '✍️',
        'fiction' => '📖',
        'non-fiction' => '📘',
        'novels' => '📕',
        'essays' => '📝',
        'journalism' => '📰',
        'newsletters' => '📧',
        
        // Travel & Places
        'travel' => '✈️',
        'traveling' => '✈️',
        'travelling' => '✈️',
        'backpacking' => '🎒',
        'adventure' => '🧭',
        'exploration' => '🗺️',
        'cities' => '🌆',
        'nature' => '🌿',
        'beach' => '🏖️',
        'mountains' => '🏔️',
        'camping' => '⛺',
        'road trips' => '🚗',
        
        // Science & Learning
        'science' => '🔬',
        'physics' => '⚛️',
        'astronomy' => '🔭',
        'space' => '🚀',
        'biology' => '🧬',
        'chemistry' => '🧪',
        'psychology' => '🧠',
        'philosophy' => '💭',
        'mathematics' => '➗',
        'math' => '➗',
        'statistics' => '📊',
        'research' => '🔍',
        'education' => '🎓',
        'learning' => '📚',
        
        // Hobbies & Lifestyle
        'gaming' => '🎮',
        'video games' => '🎮',
        'board games' => '🎲',
        'chess' => '♟️',
        'poker' => '🃏',
        'puzzles' => '🧩',
        'gardening' => '🌱',
        'plants' => '🪴',
        'pets' => '🐕',
        'dogs' => '🐕',
        'cats' => '🐈',
        'fitness' => '💪',
        'gym' => '🏋️',
        'meditation' => '🧘',
        'mindfulness' => '🧘',
        'wellness' => '🌸',
        'self-improvement' => '📈',
        'productivity' => '⚡',
        
        // Entertainment
        'podcasts' => '🎙️',
        'comedy' => '😄',
        'stand-up' => '🎤',
        'tv shows' => '📺',
        'streaming' => '📺',
        'anime' => '🇯🇵',
        'manga' => '📚',
        'comics' => '💬',
        
        // Other
        'sustainability' => '♻️',
        'environment' => '🌍',
        'climate' => '🌡️',
        'social impact' => '💚',
        'charity' => '❤️',
        'volunteering' => '🤝',
        'community' => '👥',
        'networking' => '🔗',
        'leadership' => '👑',
        'mentoring' => '🎓',
        'parenting' => '👨‍👩‍👧',
        'family' => '👨‍👩‍👧',
        'fashion' => '👗',
        'style' => '👔',
        'minimalism' => '◻️',
        'interior design' => '🛋️',
        'home' => '🏠',
        'real estate' => '🏘️',
    );
    
    // Try exact match first
    if (isset($icons[$interest_lower])) {
        return $icons[$interest_lower];
    }
    
    // Try partial match
    foreach ($icons as $keyword => $icon) {
        if (strpos($interest_lower, $keyword) !== false || strpos($keyword, $interest_lower) !== false) {
            return $icon;
        }
    }
    
    // Default icon
    return '✨';
}

/**
 * Get initials from name
 */
function kunaal_get_initials() {
    $first = get_theme_mod('kunaal_author_first_name', 'K');
    $last = get_theme_mod('kunaal_author_last_name', 'W');
    return strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
}
