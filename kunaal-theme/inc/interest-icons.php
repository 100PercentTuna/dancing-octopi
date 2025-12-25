<?php
/**
 * Interest Icons Mapping
 * 
 * Maps common interests to emojis for the About page interests cloud.
 * 
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get emoji for an interest
 */
function kunaal_get_interest_icon($interest) {
    $interest_lower = strtolower(trim($interest));
    
    $icons = array(
        // Food & Drink
        'ramen' => '🍜',
        'noodles' => '🍜',
        'tacos' => '🌮',
        'pizza' => '🍕',
        'sushi' => '🍣',
        'coffee' => '☕',
        'tea' => '🍵',
        'wine' => '🍷',
        'beer' => '🍺',
        'whiskey' => '🥃',
        'cocktails' => '🍸',
        'cooking' => '👨‍🍳',
        'baking' => '🥐',
        'barbecue' => '🍖',
        'bbq' => '🍖',
        'burgers' => '🍔',
        'ice cream' => '🍦',
        'chocolate' => '🍫',
        'cheese' => '🧀',
        'pasta' => '🍝',
        'steak' => '🥩',
        'seafood' => '🦐',
        'dim sum' => '🥟',
        'dumplings' => '🥟',
        'curry' => '🍛',
        'pho' => '🍲',
        'brunch' => '🥞',
        'breakfast' => '🍳',
        
        // Sports
        'football' => '🏈',
        'american football' => '🏈',
        'college football' => '🏈',
        'soccer' => '⚽',
        'basketball' => '🏀',
        'baseball' => '⚾',
        'tennis' => '🎾',
        'golf' => '⛳',
        'hockey' => '🏒',
        'skiing' => '⛷️',
        'snowboarding' => '🏂',
        'surfing' => '🏄',
        'swimming' => '🏊',
        'running' => '🏃',
        'cycling' => '🚴',
        'biking' => '🚴',
        'hiking' => '🥾',
        'climbing' => '🧗',
        'yoga' => '🧘',
        'gym' => '💪',
        'fitness' => '💪',
        'boxing' => '🥊',
        'martial arts' => '🥋',
        'cricket' => '🏏',
        'rugby' => '🏉',
        'volleyball' => '🏐',
        'table tennis' => '🏓',
        'badminton' => '🏸',
        'f1' => '🏎️',
        'formula 1' => '🏎️',
        'racing' => '🏎️',
        'motorsport' => '🏎️',
        
        // History & Politics
        'history' => '📜',
        'ww2' => '⚔️',
        'world war 2' => '⚔️',
        'wwii' => '⚔️',
        'world war ii' => '⚔️',
        'ww1' => '⚔️',
        'ancient history' => '🏛️',
        'medieval' => '🏰',
        'geopolitics' => '🌍',
        'politics' => '🏛️',
        'elections' => '🗳️',
        'democracy' => '🗽',
        'diplomacy' => '🤝',
        'international relations' => '🌐',
        'cold war' => '❄️',
        'military history' => '🎖️',
        
        // Technology
        'coding' => '💻',
        'programming' => '💻',
        'software' => '💻',
        'ai' => '🤖',
        'artificial intelligence' => '🤖',
        'machine learning' => '🧠',
        'data science' => '📊',
        'startups' => '🚀',
        'entrepreneurship' => '🚀',
        'crypto' => '₿',
        'blockchain' => '⛓️',
        'cybersecurity' => '🔐',
        'gaming' => '🎮',
        'video games' => '🎮',
        'vr' => '🥽',
        'virtual reality' => '🥽',
        'robotics' => '🤖',
        'space' => '🚀',
        'spacex' => '🚀',
        'nasa' => '🛸',
        'tech' => '💻',
        'gadgets' => '📱',
        'apple' => '🍎',
        'android' => '🤖',
        
        // Arts & Culture
        'music' => '🎵',
        'jazz' => '🎷',
        'rock' => '🎸',
        'classical music' => '🎻',
        'hip hop' => '🎤',
        'rap' => '🎤',
        'edm' => '🎧',
        'electronic music' => '🎧',
        'concerts' => '🎤',
        'art' => '🎨',
        'painting' => '🖼️',
        'photography' => '📷',
        'film' => '🎬',
        'movies' => '🎬',
        'cinema' => '🎬',
        'theater' => '🎭',
        'theatre' => '🎭',
        'design' => '🎨',
        'architecture' => '🏗️',
        'fashion' => '👗',
        'dance' => '💃',
        'ballet' => '🩰',
        'poetry' => '📝',
        'literature' => '📚',
        'writing' => '✍️',
        'reading' => '📖',
        'books' => '📚',
        'anime' => '🎌',
        'manga' => '📔',
        'comics' => '📰',
        'museums' => '🏛️',
        
        // Science & Learning
        'science' => '🔬',
        'physics' => '⚛️',
        'chemistry' => '🧪',
        'biology' => '🧬',
        'astronomy' => '🔭',
        'mathematics' => '🔢',
        'math' => '🔢',
        'economics' => '📈',
        'psychology' => '🧠',
        'philosophy' => '🤔',
        'neuroscience' => '🧠',
        'medicine' => '⚕️',
        'health' => '❤️',
        'climate' => '🌡️',
        'environment' => '🌱',
        'sustainability' => '♻️',
        'renewable energy' => '☀️',
        
        // Travel & Places
        'travel' => '✈️',
        'adventure' => '🏔️',
        'backpacking' => '🎒',
        'road trips' => '🚗',
        'camping' => '🏕️',
        'beaches' => '🏖️',
        'mountains' => '🏔️',
        'cities' => '🌆',
        'nature' => '🌲',
        'wildlife' => '🦁',
        'safari' => '🦒',
        'scuba diving' => '🤿',
        'snorkeling' => '🤿',
        
        // Business & Work
        'business' => '💼',
        'strategy' => '♟️',
        'consulting' => '📊',
        'finance' => '💰',
        'investing' => '📈',
        'stocks' => '📈',
        'real estate' => '🏠',
        'marketing' => '📢',
        'leadership' => '👔',
        'management' => '📋',
        'productivity' => '⚡',
        'public speaking' => '🎤',
        
        // Lifestyle
        'meditation' => '🧘',
        'mindfulness' => '🧘',
        'wellness' => '🌿',
        'self improvement' => '📈',
        'minimalism' => '◻️',
        'organization' => '📂',
        'journaling' => '📓',
        'podcasts' => '🎙️',
        'documentaries' => '🎥',
        'news' => '📰',
        
        // Hobbies
        'gardening' => '🌻',
        'plants' => '🌱',
        'pets' => '🐕',
        'dogs' => '🐕',
        'cats' => '🐈',
        'chess' => '♟️',
        'board games' => '🎲',
        'puzzles' => '🧩',
        'crafts' => '🎨',
        'woodworking' => '🪵',
        'diy' => '🔧',
        'cars' => '🚗',
        'motorcycles' => '🏍️',
        'watches' => '⌚',
        'sneakers' => '👟',
        'vintage' => '📻',
        'collecting' => '🏆',
        'lego' => '🧱',
        
        // Social
        'family' => '👨‍👩‍👧‍👦',
        'friends' => '👫',
        'community' => '🤝',
        'volunteering' => '🤲',
        'mentoring' => '🎓',
        'teaching' => '👨‍🏫',
        'learning' => '📚',
        'languages' => '🗣️',
        
        // Abstract
        'innovation' => '💡',
        'creativity' => '✨',
        'ideas' => '💡',
        'thinking' => '🤔',
        'systems thinking' => '🔄',
        'behavioral economics' => '🧠',
        'data visualization' => '📊',
        'storytelling' => '📖',
        'communication' => '💬',
        'debate' => '⚖️',
        'analysis' => '🔍',
        'research' => '🔬',
        'problem solving' => '🧩',
        
        // Misc
        'coffee shops' => '☕',
        'libraries' => '📚',
        'cozy' => '🛋️',
        'rain' => '🌧️',
        'sunsets' => '🌅',
        'night owl' => '🦉',
        'early bird' => '🌅',
    );
    
    // Check for exact match
    if (isset($icons[$interest_lower])) {
        return $icons[$interest_lower];
    }
    
    // Check for partial match
    foreach ($icons as $key => $emoji) {
        if (strpos($interest_lower, $key) !== false || strpos($key, $interest_lower) !== false) {
            return $emoji;
        }
    }
    
    // Default fallback
    return '✨';
}

