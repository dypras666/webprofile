<?php

namespace App\Helpers;

class CommentHelper
{
    /**
     * List of bad words to block or flag.
     * Add more words as needed.
     */
    protected static $badWords = [
        'judi',
        'slot',
        'gacor',
        'togel',
        'poker',
        'casino',
        'bet',
        'sex',
        'porn',
        'bokep',
        'telanjang',
        'bugil',
        'anjing',
        'babi',
        'bangsat',
        'kontol',
        'memek',
        'jembut',
        'ngentot',
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'sara',
        'kafir',
        'bunuh',
        'bom',
        'teroris'
    ];

    /**
     * Check if the content contains any bad words.
     *
     * @param string $content
     * @return string|false
     */
    public static function containsBadWords($content)
    {
        $content = strtolower($content);

        // 1. Check exact matches first (fastest)
        foreach (self::$badWords as $word) {
            if (strpos($content, $word) !== false) {
                return $word;
            }
        }

        // 2. Normalize content for leetspeak and symbols
        // Replace common leetspeak substitutions
        $replacements = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '6' => 'g',
            '7' => 't',
            '8' => 'b',
            '9' => 'g',
            '@' => 'a',
            '$' => 's',
            '!' => 'i',
            '(' => 'c',
            '+' => 't',
            '|' => 'i',
            '[' => 'c'
        ];

        $normalized = strtr($content, $replacements);

        // Remove non-alphabetic characters
        $cleaned = preg_replace('/[^a-z]/', '', $normalized);

        // Check against bad words again with cleaned content
        foreach (self::$badWords as $word) {
            if (strpos($cleaned, $word) !== false) {
                return $word;
            }
        }

        return false;
    }

    /**
     * Check if the comment is potential spam based on heuristics.
     * e.g., contains too many links.
     *
     * @param string $content
     * @return bool
     */
    public static function isSpam($content)
    {
        // Check for too many links (e.g., more than 2)
        $linkCount = preg_match_all('/http|www\.|t\.co|\.com|\.net|\.org/i', $content);
        if ($linkCount > 2) {
            return true;
        }

        // Check for repetitive characters (e.g., "haaaaallo") - simplified
        if (preg_match('/(.)\1{4,}/', $content)) {
            return true;
        }

        return false;
    }
}
