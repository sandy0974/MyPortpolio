<?php

require_once __DIR__ . '/../auth.php';
require_admin();

$pdo = db();

$success = '';
$error = '';

/*
|--------------------------------------------------------------------------
| LOAD SETTINGS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT *
    FROM settings
    WHERE id = 1
    LIMIT 1
");

$settings = $stmt->fetch();

if (!$settings) {
    $settings = [
        'id' => 1,
        'site_title' => 'My Game Portfolio',
        'tagline' => 'Game Developer • 3D Artist • Programmer',
        'about' => 'Welcome to my portfolio.',
        'github_url' => '',
        'itch_url' => '',
        'email' => ''
    ];
}

/*
|--------------------------------------------------------------------------
| SAVE SETTINGS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $siteTitle = trim($_POST['site_title'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $about = trim($_POST['about'] ?? '');
    $githubUrl = trim($_POST['github_url'] ?? '');
    $itchUrl = trim($_POST['itch_url'] ?? '');
    $email = trim($_POST['email'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($siteTitle === '') {

        $error = 'Site title cannot be empty.';

    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK IF SETTINGS ROW EXISTS
            |--------------------------------------------------------------------------
            */

            $check = $pdo->prepare("
                SELECT id
                FROM settings
                WHERE id = 1
                LIMIT 1
            ");

            $check->execute();

            $exists = $check->fetchColumn();

            /*
            |--------------------------------------------------------------------------
            | UPDATE
            |--------------------------------------------------------------------------
            */

            if ($exists) {

                $stmt = $pdo->prepare("
                    UPDATE settings
                    SET
                        site_title = ?,
                        tagline = ?,
                        about = ?,
                        github_url = ?,
                        itch_url = ?,
                        email = ?
                    WHERE id = 1
                ");

                $stmt->execute([
                    $siteTitle,
                    $tagline,
                    $about,
                    $githubUrl,
                    $itchUrl,
                    $email
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | INSERT IF ROW DOES NOT EXIST
            |--------------------------------------------------------------------------
            */

            else {

                $stmt = $pdo->prepare("
                    INSERT INTO settings (
                        id,
                        site_title,
                        tagline,
                        about,
                        github_url,
                        itch_url,
                        email
                    )
                    VALUES (
                        1,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ");

                $stmt->execute([
                    $siteTitle,
                    $tagline,
                    $about,
                    $githubUrl,
                    $itchUrl,
                    $email
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE LOCAL DATA
            |--------------------------------------------------------------------------
            */

            $settings['site_title'] = $siteTitle;
            $settings['tagline'] = $tagline;
            $settings['about'] = $about;
            $settings['github_url'] = $githubUrl;
            $settings['itch_url'] = $itchUrl;
            $settings['email'] = $email;

            $success = 'Settings saved successfully.';

        } catch (Throwable $e) {

            $error = 'Failed to save settings. Please try again.';

        }
    }
}

?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Site Settings</title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=20260921-settings"
    >

</head>

<body>

<!-- =====================================================
     ADMIN NAVIGATION
===================================================== -->

<header class="admin-nav">

    <div class="admin-container admin-nav-inner">

        <a
            class="admin-brand"
            href="index.php"
        >
            Portfolio Admin
        </a>

        <nav class="admin-nav-links">

            <a href="index.php">
                Dashboard
            </a>

            <a href="../index.php">
                View Site
            </a>

            <a href="logout.php">
                Logout
            </a>

        </nav>

    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="admin-main settings-page">

    <div class="admin-container">

        <!-- PAGE HEADER -->

        <section class="settings-header">

            <div>

                <p class="admin-eyebrow">
                    WEBSITE CONFIGURATION
                </p>

                <h1>
                    Site Settings
                </h1>

                <p class="settings-subtitle">
                    Manage your portfolio title, description,
                    social links and contact information.
                </p>

            </div>

        </section>


        <!-- =================================================
             MESSAGES
        ================================================= -->

        <?php if ($success): ?>

            <div class="settings-alert settings-success">

                <span class="settings-alert-icon">
                    ✓
                </span>

                <div>
                    <strong>
                        Saved
                    </strong>

                    <p>
                        <?= e($success) ?>
                    </p>
                </div>

            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="settings-alert settings-error">

                <span class="settings-alert-icon">
                    !
                </span>

                <div>
                    <strong>
                        Error
                    </strong>

                    <p>
                        <?= e($error) ?>
                    </p>
                </div>

            </div>

        <?php endif; ?>


        <!-- =================================================
             SETTINGS FORM
        ================================================= -->

        <form
            method="post"
            action=""
            class="settings-form"
        >

            <!-- =============================================
                 GENERAL
            ============================================== -->

            <section class="settings-card">

                <div class="settings-card-header">

                    <div class="settings-card-icon">
                        ◈
                    </div>

                    <div>

                        <p class="settings-card-label">
                            GENERAL
                        </p>

                        <h2>
                            Website Information
                        </h2>

                        <p>
                            Basic information displayed throughout
                            your portfolio.
                        </p>

                    </div>

                </div>


                <div class="settings-fields">

                    <!-- SITE TITLE -->

                    <div class="settings-field">

                        <label for="site_title">
                            Site Title
                        </label>

                        <span class="settings-help">
                            The name shown in your website header
                            and browser title.
                        </span>

                        <input
                            type="text"
                            id="site_title"
                            name="site_title"
                            value="<?= e($settings['site_title'] ?? '') ?>"
                            maxlength="180"
                            placeholder="My Game Portfolio"
                            required
                        >

                    </div>


                    <!-- TAGLINE -->

                    <div class="settings-field">

                        <label for="tagline">
                            Tagline
                        </label>

                        <span class="settings-help">
                            A short description of what you do.
                        </span>

                        <input
                            type="text"
                            id="tagline"
                            name="tagline"
                            value="<?= e($settings['tagline'] ?? '') ?>"
                            maxlength="255"
                            placeholder="Game Developer • 3D Artist • Programmer"
                        >

                    </div>


                    <!-- ABOUT -->

                    <div class="settings-field">

                        <label for="about">
                            About
                        </label>

                        <span class="settings-help">
                            Your main portfolio introduction.
                        </span>

                        <textarea
                            id="about"
                            name="about"
                            rows="7"
                            placeholder="Write something about yourself, your skills and your work..."
                        ><?= e($settings['about'] ?? '') ?></textarea>

                        <div class="settings-counter">

                            <span>
                                This text is displayed on the
                                homepage and About section.
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 SOCIAL
            ============================================== -->

            <section class="settings-card">

                <div class="settings-card-header">

                    <div class="settings-card-icon">
                        ↗
                    </div>

                    <div>

                        <p class="settings-card-label">
                            SOCIAL
                        </p>

                        <h2>
                            Social Links
                        </h2>

                        <p>
                            Connect your portfolio to your
                            development profiles.
                        </p>

                    </div>

                </div>


                <div class="settings-fields">

                    <!-- GITHUB -->

                    <div class="settings-field">

                        <label for="github_url">
                            GitHub URL
                        </label>

                        <span class="settings-help">
                            Your GitHub profile or repository URL.
                        </span>

                        <div class="settings-input-group">

                            <span class="settings-input-prefix">
                                github.com
                            </span>

                            <input
                                type="url"
                                id="github_url"
                                name="github_url"
                                value="<?= e($settings['github_url'] ?? '') ?>"
                                maxlength="500"
                                placeholder="https://github.com/username"
                            >

                        </div>

                    </div>


                    <!-- ITCH.IO -->

                    <div class="settings-field">

                        <label for="itch_url">
                            itch.io URL
                        </label>

                        <span class="settings-help">
                            Your itch.io developer page.
                        </span>

                        <div class="settings-input-group">

                            <span class="settings-input-prefix">
                                itch.io
                            </span>

                            <input
                                type="url"
                                id="itch_url"
                                name="itch_url"
                                value="<?= e($settings['itch_url'] ?? '') ?>"
                                maxlength="500"
                                placeholder="https://username.itch.io"
                            >

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 CONTACT
            ============================================== -->

            <section class="settings-card">

                <div class="settings-card-header">

                    <div class="settings-card-icon">
                        @
                    </div>

                    <div>

                        <p class="settings-card-label">
                            CONTACT
                        </p>

                        <h2>
                            Contact Information
                        </h2>

                        <p>
                            Contact information displayed on
                            your public portfolio.
                        </p>

                    </div>

                </div>


                <div class="settings-fields">

                    <!-- EMAIL -->

                    <div class="settings-field">

                        <label for="email">
                            Email
                        </label>

                        <span class="settings-help">
                            Visitors can use this address to
                            contact you.
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= e($settings['email'] ?? '') ?>"
                            maxlength="180"
                            placeholder="you@example.com"
                        >

                    </div>

                </div>

            </section>


            <!-- =============================================
                 SAVE BAR
            ============================================== -->

            <div class="settings-save-bar">

                <div class="settings-save-info">

                    <span class="settings-save-dot"></span>

                    <span>
                        Changes are saved to your portfolio database.
                    </span>

                </div>

                <button
                    type="submit"
                    class="settings-save-button"
                >
                    <span>
                        Save Settings
                    </span>

                    <span class="settings-save-arrow">
                        →
                    </span>

                </button>

            </div>

        </form>

    </div>

</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="admin-footer">

    <div class="admin-container">

        Portfolio Admin Panel
        <span>• <?= date('Y') ?></span>

    </div>

</footer>


</body>
</html>