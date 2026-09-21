<?php

require_once __DIR__ . '/db.php';


/**
 * Track website visitor.
 *
 * Uses an anonymous random cookie instead of storing IP addresses.
 */
function track_visitor(string $page = 'home'): void
{
    try {

        /*
         * Get existing visitor token.
         */
        $token = $_COOKIE['portfolio_visitor'] ?? '';


        /*
         * Validate token.
         *
         * Expected format:
         * 64 hexadecimal characters.
         */
        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {

            $token = bin2hex(random_bytes(32));

            /*
             * Keep visitor cookie for 1 year.
             */
            setcookie(
                'portfolio_visitor',
                $token,
                [
                    'expires' => time() + (365 * 24 * 60 * 60),
                    'path' => '/',
                    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }


        $pdo = db();


        /*
         * Add/update unique visitor.
         */
        $stmt = $pdo->prepare(
            "INSERT INTO visitors
                (visitor_token, first_seen, last_seen)
             VALUES
                (?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
                last_seen = NOW()"
        );

        $stmt->execute([$token]);


        /*
         * Record page view.
         */
        $stmt = $pdo->prepare(
            "INSERT INTO visitor_logs
                (visitor_token, page, visited_at)
             VALUES
                (?, ?, NOW())"
        );

        $stmt->execute([
            $token,
            $page
        ]);

    } catch (Throwable $e) {

        /*
         * Visitor statistics should never break
         * the public website.
         *
         * Do nothing if tracking fails.
         */
    }
}