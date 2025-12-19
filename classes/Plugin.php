<?php

declare(strict_types=1);

namespace Castlegate\MailHeaders;

final class Plugin
{
    /**
     * Initialization
     *
     * @return void
     */
    public static function init(): void
    {
        $plugin = new self();

        add_filter('wp_mail', [$plugin, 'editWpMailArgs']);
        add_action('admin_notices', [$plugin, 'printAdminNotice']);
    }

    /**
     * Edit wp_mail arguments
     *
     * @param array $args
     * @return array
     */
    public function editWpMailArgs(array $args): array
    {
        $from_address = self::getFromAddress();
        $reply_to_address = self::getReplyToAddress();

        if (!$from_address || !$reply_to_address) {
            return $args;
        }

        // Sanitize the header parameters to a standard format
        $headers = self::sanitizeHeaders($args['headers'] ?? []);

        // Set the From header
        $from_headers = preg_grep('/^from:/i', $headers);

        if ($from_headers) {
            $headers = array_diff($headers, $from_headers);
        }

        $headers[] = 'From: ' . $from_address;

        // Set the Reply-To header if it is not already set
        $reply_to_headers = preg_grep('/^reply-to:/i', $headers);

        if (!$reply_to_headers) {
            $headers[] = 'Reply-To: ' . $reply_to_address;
        }

        $args['headers'] = $headers;

        return $args;
    }

    /**
     * Print admin notices
     *
     * @return void
     */
    public function printAdminNotice(): void
    {
        if (!self::hasConstants()) {
            include CGIT_WP_MAIL_HEADERS_PLUGIN_DIR . '/views/missing-constants.php';
            return;
        }

        if (!self::hasValidConstants()) {
            include CGIT_WP_MAIL_HEADERS_PLUGIN_DIR . '/views/invalid-constants.php';
            return;
        }
    }

    /**
     * Required constants exist?
     *
     * @return bool
     */
    private static function hasConstants(): bool
    {
        return defined('CGIT_WP_MAIL_HEADERS_FROM') &&
            defined('CGIT_WP_MAIL_HEADERS_REPLY_TO');
    }

    /**
     * Required constants exist and have valid values?
     *
     * @return bool
     */
    private static function hasValidConstants(): bool
    {
        return self::hasConstants() &&
            is_string(CGIT_WP_MAIL_HEADERS_FROM) &&
            is_string(CGIT_WP_MAIL_HEADERS_REPLY_TO) &&
            self::isValidEmail(CGIT_WP_MAIL_HEADERS_FROM) &&
            self::isValidEmail(CGIT_WP_MAIL_HEADERS_REPLY_TO);
    }

    /**
     * Return From address
     *
     * @return string|null
     */
    private static function getFromAddress(): ?string
    {
        if (static::hasValidConstants()) {
            return CGIT_WP_MAIL_HEADERS_FROM;
        }

        return null;
    }

    /**
     * Return Reply-To address
     *
     * @return string|null
     */
    private static function getReplyToAddress(): ?string
    {
        if (static::hasValidConstants()) {
            return CGIT_WP_MAIL_HEADERS_REPLY_TO;
        }

        return null;
    }

    /**
     * Check if an email address is valid
     *
     * Returns true if the string is a valid email address or is a valid name
     * with an email address in angle brackets. Returns false for any other
     * string value.
     *
     * Example: "Caz Legate <caz.legate@castlegateit.co.uk>".
     *
     * @param string $email
     * @return bool
     */
    private static function isValidEmail(string $email): bool
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        if (preg_match('/^.+<(.+)>$/', $email, $matches) && isset($matches[1])) {
            return self::isValidEmail($matches[1]);
        }

        return false;
    }

    /**
     * Sanitize headers from wp_mail arguments
     *
     * @param mixed $headers
     * @return array
     */
    private static function sanitizeHeaders($headers): array
    {
        if (is_string($headers)) {
            $headers = preg_split('/[\n\r]+/', $headers);
        } elseif (!is_array($headers)) {
            return [];
        }

        $headers = array_filter($headers, 'is_string');
        $headers = array_map('trim', $headers);
        $headers = array_filter($headers);

        return $headers;
    }
}
