<?php

require_once __DIR__ . '/db.php';


/**
 * Track anonymous website visitor.
 *
 * Visitor tracking must NEVER break
 * the public website.
 *
 * No IP address is stored.
 */
function track_visitor(string $page = 'home'): void
{
    try {

        // =================================================
        // GET EXISTING COOKIE
        // =================================================

        $token = $_COOKIE['portfolio_visitor'] ?? '';


        // =================================================
        // VALIDATE / CREATE VISITOR TOKEN
        // =================================================

        if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {

            try {

                $token = bin2hex(
                    random_bytes(32)
                );

            } catch (Throwable $e) {

                // Cannot generate token.
                // Skip tracking.

                return;
            }


            // =================================================
            // SET COOKIE
            // =================================================

            if (!headers_sent()) {

                setcookie(
                    'portfolio_visitor',
                    $token,
                    [
                        'expires' => time() + (
                            365 * 24 * 60 * 60
                        ),
                        'path' => '/',
                        'secure' =>
                            !empty($_SERVER['HTTPS'])
                            &&
                            $_SERVER['HTTPS'] !== 'off',
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]
                );
            }
        }


        // =================================================
        // DATABASE CONNECTION
        // =================================================

        $pdo = db();


        if (!$pdo) {
            return;
        }


        // =================================================
        // REGISTER / UPDATE VISITOR
        // =================================================

        $stmt = $pdo->prepare(
            "INSERT INTO visitors
            (
                visitor_token,
                first_seen,
                last_seen
            )
            VALUES
            (
                ?,
                NOW(),
                NOW()
            )
            ON DUPLICATE KEY UPDATE
                last_seen = NOW()"
        );


        $stmt->execute([
            $token
        ]);


        // =================================================
        // REGISTER PAGE VIEW
        // =================================================

        $stmt = $pdo->prepare(
            "INSERT INTO visitor_logs
            (
                visitor_token,
                page,
                visited_at
            )
            VALUES
            (
                ?,
                ?,
                NOW()
            )"
        );


        $stmt->execute([
            $token,
            $page
        ]);


    } catch (Throwable $e) {

        /*
         * IMPORTANT
         *
         * If visitor tracking fails:
         *
         * - database error
         * - table doesn't exist
         * - duplicate key problem
         * - cookie problem
         * - SQL error
         *
         * The website must continue normally.
         */

        return;
    }
}