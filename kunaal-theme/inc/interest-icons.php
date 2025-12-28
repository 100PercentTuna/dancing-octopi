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
if (!function_exists('kunaal_get_interest_icon')) :
function kunaal_get_interest_icon($interest): string {
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
        'dim sum' => '🥟',
        'dumplings' => '🥟',
        'curry' => '🍛',
        'indian food' => '🍛',
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
        'f1' => '🏎️',
        'formula 1' => '🏎️',
        'racing' => '🏎️',
        
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
        'design' => '🎨',
        'ux' => '✏️',
        'product' => '📦',
        'strategy' => '🎯',
        'consulting' => '📋',
        
        // History & Politics
        'history' => '📜',
        'ww2' => '⚔️',
        'ww2 history' => '⚔️',
        'world war 2' => '⚔️',
        'ww1' => '⚔️',
        'military history' => '⚔️',
        'ancient history' => '🏛️',
        'geopolitics' => '🌍',
        'politics' => '🏛️',
        'international relations' => '🌐',
        'diplomacy' => '🤝',
        'elections' => '🗳️',
        
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
        'family' => '👨‍👩‍👧',
        'fashion' => '👗',
        'style' => '👔',
        'minimalism' => '◻️',
        'interior design' => '🛋️',
        'home' => '🏠',
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
endif;

// kunaal_get_initials() is now defined in inc/helpers.php
// This duplicate has been removed to prevent conflicts
