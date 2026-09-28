<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'sort_order',
        'status',
    ];

    /**
     * Get default localization parameters mapping supported shorthands (:name, :site_name, etc.)
     */
    public static function getDefaultParams(): array
    {
        return [
            'name' => getSetting('name') ?? config('app.name', 'Foyana'),
            'site_name' => getSetting('name') ?? config('app.name', 'Foyana'),
            'currency' => getSetting('currency_symbol', '$'),
            'currency_symbol' => getSetting('currency_symbol', '$'),
            'email' => getSetting('contact_email', 'support@foyana.com'),
            'support_email' => getSetting('contact_email', 'support@foyana.com'),
            'url' => config('app.url', url('/')),
        ];
    }

    /**
     * Parse placeholders (e.g. :name, :currency) with fallback for unsupported shorthands
     */
    public static function parseShorthands(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $params = static::getDefaultParams();

        // Use Laravel's native translation engine to replace supported :key shorthands
        $parsed = __($text, $params);

        // Fallback parser: If any unsupported :unsupported_key placeholders remain, handle them gracefully
        $parsed = preg_replace_callback('/(?<!\\\\):([a-zA-Z0-9_]+)/', function ($matches) use ($params) {
            $key = $matches[1];
            if (array_key_exists($key, $params)) {
                return $params[$key];
            }
            // Fallback for unsupported shorthands: convert to clean text
            return ucwords(str_replace(['_', '-'], ' ', $key));
        }, $parsed);

        return $parsed;
    }

    /**
     * Accessor for parsed question
     */
    public function getParsedQuestionAttribute(): string
    {
        return static::parseShorthands($this->question);
    }

    /**
     * Accessor for parsed answer
     */
    public function getParsedAnswerAttribute(): string
    {
        return static::parseShorthands($this->answer);
    }
}
