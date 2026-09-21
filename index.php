<?php

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';


// =====================================================
// WEBSITE DATA
// =====================================================

$settings = db()->query(
    "SELECT * FROM settings WHERE id = 1"
)->fetch() ?: [];


$projects = db()->query(
    "SELECT * FROM projects
     WHERE published = 1
     ORDER BY featured DESC, created_at DESC"
)->fetchAll();


$assets = db()->query(
    "SELECT * FROM assets
     WHERE published = 1
     ORDER BY created_at DESC"
)->fetchAll();


$isAdmin = is_admin_logged_in();

$loggedUser = current_user();


// =====================================================
// HELPER DATA
// =====================================================

$siteTitle = $settings['site_title'] ?? SITE_NAME;

$tagline = $settings['tagline']
    ?? 'Game Developer • 3D Artist • Programmer';

$about = $settings['about'] ?? '';

$githubUrl = $settings['github_url'] ?? '';

$itchUrl = $settings['itch_url'] ?? '';

$email = $settings['email'] ?? '';

?>
<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title><?= e($siteTitle) ?></title>


    <!-- WEBSITE CSS -->

    <link
        rel="stylesheet"
        href="assets/css/style.css?v=5"
    >


    <!-- GOOGLE MODEL VIEWER -->

    <script
        type="module"
        src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"
    ></script>

</head>


<body>


<!-- =====================================================
     NAVIGATION
===================================================== -->

<header class="nav">

    <div class="container nav-inner">


        <!-- BRAND -->

        <a
            class="brand"
            href="#home"
        >
            <?= e($siteTitle) ?>
        </a>


        <!-- MOBILE MENU BUTTON -->

        <button
            class="nav-toggle"
            type="button"
            aria-label="Toggle navigation"
            aria-expanded="false"
        >
            ☰
        </button>


        <!-- NAVIGATION MENU -->

        <div class="nav-menu">

            <nav
                class="nav-links"
                aria-label="Main navigation"
            >

                <a href="#home">
                    Home
                </a>

                <a href="#projects">
                    Projects
                </a>

                <a href="#models">
                    3D Models
                </a>

                <a href="#assets">
                    Assets
                </a>

                <a href="#about">
                    About
                </a>

            </nav>


            <!-- USER ACTIONS -->

            <div class="nav-actions">

                <?php if ($isAdmin): ?>

                    <a
                        class="btn btn-small"
                        href="admin/index.php"
                    >
                        Dashboard
                    </a>

                    <a
                        class="btn btn-small btn-ghost"
                        href="admin/logout.php"
                    >
                        Logout
                    </a>


                <?php elseif ($loggedUser): ?>

                    <a
                        class="btn btn-small"
                        href="profile.php"
                    >
                        Profile
                    </a>

                    <a
                        class="btn btn-small btn-ghost"
                        href="logout.php"
                    >
                        Logout
                    </a>


                <?php else: ?>

                    <a
                        class="btn btn-small btn-primary"
                        href="login.php"
                    >
                        Login
                    </a>

                <?php endif; ?>

            </div>

        </div>

    </div>

</header>



<!-- =====================================================
     MAIN
===================================================== -->

<main>


<!-- =====================================================
     HERO
===================================================== -->

<section
    id="home"
    class="hero"
>

    <div class="container">

        <p class="eyebrow">
            HELLO MY NAME IS SANDY PURNAMA
        </p>


        <h1>
            <?= e($tagline) ?>
        </h1>


        <p class="lead">
            <?= e($about) ?>
        </p>


        <div class="actions">

            <a
                class="btn primary"
                href="#projects"
            >
                View Projects
            </a>


            <?php if (!empty($githubUrl)): ?>

                <a
                    class="btn"
                    href="<?= e($githubUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    GitHub
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>



<!-- =====================================================
     PROJECTS
===================================================== -->

<section
    id="projects"
    class="section"
>

    <div class="container">


        <div class="section-head">

            <div>

                <p class="eyebrow">
                    WORK
                </p>

                <h2>
                    Projects
                </h2>

            </div>

        </div>


        <div class="grid">


            <?php foreach ($projects as $p): ?>

                <article class="card">


                    <!-- PROJECT IMAGE -->

                    <?php if (!empty($p['thumbnail'])): ?>

                        <img
                            src="<?= e(asset_path($p['thumbnail'])) ?>"
                            alt="<?= e($p['title']) ?>"
                            loading="lazy"
                        >

                    <?php else: ?>

                        <div class="placeholder">
                            PROJECT
                        </div>

                    <?php endif; ?>


                    <!-- PROJECT CONTENT -->

                    <div class="card-body">

                        <span class="tag">
                            <?= e($p['category']) ?>
                        </span>


                        <h3>
                            <?= e($p['title']) ?>
                        </h3>


                        <p>
                            <?= e($p['description']) ?>
                        </p>


                        <div class="card-links">


                            <?php if (!empty($p['project_url'])): ?>

                                <a
                                    href="<?= e($p['project_url']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Project ↗
                                </a>

                            <?php endif; ?>


                            <?php if (!empty($p['github_url'])): ?>

                                <a
                                    href="<?= e($p['github_url']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Source ↗
                                </a>

                            <?php endif; ?>


                        </div>

                    </div>

                </article>

            <?php endforeach; ?>


            <?php if (!$projects): ?>

                <p class="muted">
                    No projects published yet.
                </p>

            <?php endif; ?>


        </div>

    </div>

</section>



<!-- =====================================================
     3D MODELS
===================================================== -->

<section
    id="models"
    class="section alt"
>

    <div class="container">


        <div class="section-head">

            <div>

                <p class="eyebrow">
                    3D
                </p>

                <h2>
                    3D Models
                </h2>

            </div>

        </div>


        <div class="model-grid">


            <?php foreach ($projects as $p): ?>


                <?php

                if (empty($p['model_url'])) {
                    continue;
                }

                $modelFormat = strtolower(
                    $p['model_format'] ?? ''
                );

                ?>


                <article class="model-card">


                    <?php if (
                        $modelFormat === 'glb'
                        ||
                        $modelFormat === 'gltf'
                    ): ?>

                        <model-viewer
                            src="<?= e(asset_path($p['model_url'])) ?>"
                            camera-controls
                            auto-rotate
                            shadow-intensity="1"
                            loading="lazy"
                        ></model-viewer>


                    <?php else: ?>

                        <div class="model-fallback">

                            3D file:

                            <?= e(
                                strtoupper($modelFormat)
                            ) ?>

                        </div>

                    <?php endif; ?>


                    <div class="card-body">

                        <span class="tag">
                            3D MODEL
                        </span>


                        <h3>
                            <?= e($p['title']) ?>
                        </h3>


                        <p>
                            <?= e($p['description']) ?>
                        </p>

                    </div>

                </article>

            <?php endforeach; ?>


        </div>

    </div>

</section>



<!-- =====================================================
     GAME ASSETS
===================================================== -->

<section
    id="assets"
    class="section"
>

    <div class="container">


        <div class="section-head">

            <div>

                <p class="eyebrow">
                    LIBRARY
                </p>

                <h2>
                    Game Assets
                </h2>

            </div>

        </div>


        <div class="grid">


            <?php foreach ($assets as $a): ?>

                <article class="card">


                    <!-- ASSET IMAGE -->

                    <?php if (!empty($a['thumbnail'])): ?>

                        <img
                            src="<?= e(asset_path($a['thumbnail'])) ?>"
                            alt="<?= e($a['title']) ?>"
                            loading="lazy"
                        >

                    <?php else: ?>

                        <div class="placeholder">
                            ASSET
                        </div>

                    <?php endif; ?>


                    <!-- ASSET CONTENT -->

                    <div class="card-body">

                        <span class="tag">
                            <?= e($a['category']) ?>
                        </span>


                        <h3>
                            <?= e($a['title']) ?>
                        </h3>


                        <p>
                            <?= e($a['description']) ?>
                        </p>


                        <div class="card-links">


                            <?php if (!empty($a['file_url'])): ?>

                                <a
                                    href="<?= e(asset_path($a['file_url'])) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Download ↗
                                </a>

                            <?php endif; ?>


                            <?php if (!empty($a['external_url'])): ?>

                                <a
                                    href="<?= e($a['external_url']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    Details ↗
                                </a>

                            <?php endif; ?>


                        </div>

                    </div>

                </article>

            <?php endforeach; ?>


            <?php if (!$assets): ?>

                <p class="muted">
                    No assets published yet.
                </p>

            <?php endif; ?>


        </div>

    </div>

</section>



<!-- =====================================================
     ABOUT
===================================================== -->

<section
    id="about"
    class="section alt"
>

    <div class="container narrow">


        <p class="eyebrow">
            ABOUT
        </p>


        <h2>
            About Me
        </h2>


        <p>
            <?= nl2br(e($about)) ?>
        </p>


        <div class="socials">


            <?php if (!empty($githubUrl)): ?>

                <a
                    href="<?= e($githubUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    GitHub
                </a>

            <?php endif; ?>


            <?php if (!empty($itchUrl)): ?>

                <a
                    href="<?= e($itchUrl) ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    itch.io
                </a>

            <?php endif; ?>


            <?php if (!empty($email)): ?>

                <a
                    href="mailto:<?= e($email) ?>"
                >
                    Email
                </a>

            <?php endif; ?>


        </div>

    </div>

</section>


</main>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer>

    <div class="container">

        © <?= date('Y') ?>

        <?= e($siteTitle) ?>

    </div>

</footer>



<!-- =====================================================
     MOBILE MENU JAVASCRIPT
===================================================== -->

<script>

const toggle = document.querySelector('.nav-toggle');

const menu = document.querySelector('.nav-menu');


if (toggle && menu) {

    toggle.addEventListener(
        'click',
        function () {

            const expanded =
                toggle.getAttribute('aria-expanded') === 'true';


            toggle.setAttribute(
                'aria-expanded',
                String(!expanded)
            );


            menu.classList.toggle('is-open');

        }
    );


    menu
        .querySelectorAll('a')
        .forEach(function (link) {

            link.addEventListener(
                'click',
                function () {

                    if (window.innerWidth <= 768) {

                        menu.classList.remove('is-open');

                        toggle.setAttribute(
                            'aria-expanded',
                            'false'
                        );

                    }

                }
            );

        });

}

</script>


</body>

</html>