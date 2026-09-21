<?php

require_once __DIR__ . '/../auth.php';

require_admin();

$pdo = db();

/*
|--------------------------------------------------------------------------
| PROJECT STATISTICS
|--------------------------------------------------------------------------
*/

$totalProjects = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM projects
    ")
    ->fetchColumn();

$publishedProjects = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM projects
        WHERE published = 1
    ")
    ->fetchColumn();


/*
|--------------------------------------------------------------------------
| ASSET STATISTICS
|--------------------------------------------------------------------------
*/

$totalAssets = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM assets
    ")
    ->fetchColumn();

$publishedAssets = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM assets
        WHERE published = 1
    ")
    ->fetchColumn();


/*
|--------------------------------------------------------------------------
| USER STATISTICS
|--------------------------------------------------------------------------
*/

$userCount = (int) $pdo
    ->query("
        SELECT COUNT(*)
        FROM users
    ")
    ->fetchColumn();


/*
|--------------------------------------------------------------------------
| VISITOR STATISTICS
|--------------------------------------------------------------------------
*/

$totalVisitors = 0;
$todayVisitors = 0;
$totalViews = 0;
$todayViews = 0;

try {

    $totalVisitors = (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM visitors
        ")
        ->fetchColumn();

    $todayVisitors = (int) $pdo
        ->query("
            SELECT COUNT(DISTINCT visitor_token)
            FROM visitor_logs
            WHERE DATE(visited_at) = CURDATE()
        ")
        ->fetchColumn();

    $totalViews = (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM visitor_logs
        ")
        ->fetchColumn();

    $todayViews = (int) $pdo
        ->query("
            SELECT COUNT(*)
            FROM visitor_logs
            WHERE DATE(visited_at) = CURDATE()
        ")
        ->fetchColumn();

} catch (Throwable $e) {

    /*
     * Kalau tabel visitor belum ada,
     * dashboard tetap bisa dibuka.
     */

    $totalVisitors = 0;
    $todayVisitors = 0;
    $totalViews = 0;
    $todayViews = 0;
}


/*
|--------------------------------------------------------------------------
| VISITOR CHART - LAST 7 DAYS
|--------------------------------------------------------------------------
*/

$visitorChart = [];

try {

    $stmt = $pdo->query("
        SELECT
            DATE(visited_at) AS visit_date,
            COUNT(DISTINCT visitor_token) AS visitors,
            COUNT(*) AS views
        FROM visitor_logs
        WHERE visited_at >= DATE_SUB(
            CURDATE(),
            INTERVAL 6 DAY
        )
        GROUP BY DATE(visited_at)
        ORDER BY visit_date ASC
    ");

    $chartRows = $stmt->fetchAll();

    foreach ($chartRows as $row) {

        $visitorChart[$row['visit_date']] = [
            'visitors' => (int) $row['visitors'],
            'views' => (int) $row['views']
        ];
    }

} catch (Throwable $e) {

    $visitorChart = [];
}


/*
|--------------------------------------------------------------------------
| BUILD 7 DAYS DATA
|--------------------------------------------------------------------------
*/

$chart = [];

for ($i = 6; $i >= 0; $i--) {

    $date = date(
        'Y-m-d',
        strtotime("-{$i} days")
    );

    $chart[] = [

        'date' => $date,

        'label' => date(
            'd M',
            strtotime($date)
        ),

        'visitors' =>
            $visitorChart[$date]['visitors']
            ?? 0,

        'views' =>
            $visitorChart[$date]['views']
            ?? 0
    ];
}


/*
|--------------------------------------------------------------------------
| FIND MAX CHART VALUE
|--------------------------------------------------------------------------
*/

$maxVisitors = 1;

foreach ($chart as $day) {

    if ($day['visitors'] > $maxVisitors) {

        $maxVisitors = $day['visitors'];
    }
}


/*
|--------------------------------------------------------------------------
| PROJECT LIST
|--------------------------------------------------------------------------
*/

$projects = $pdo
    ->query("
        SELECT *
        FROM projects
        ORDER BY created_at DESC
        LIMIT 10
    ")
    ->fetchAll();


/*
|--------------------------------------------------------------------------
| ASSET LIST
|--------------------------------------------------------------------------
*/

$assets = $pdo
    ->query("
        SELECT *
        FROM assets
        ORDER BY created_at DESC
        LIMIT 10
    ")
    ->fetchAll();


/*
|--------------------------------------------------------------------------
| FLASH MESSAGE
|--------------------------------------------------------------------------
*/

$flash = get_flash();

?>

<!doctype html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Portfolio Admin
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/admin.css?v=20260921"
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

            <a href="../index.php">
                View Site
            </a>

            <a href="settings.php">
                Settings
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

<main class="admin-main">

    <div class="admin-container">


        <?php if ($flash): ?>

            <div class="admin-flash">

                <?= e(
                    $flash['message']
                    ?? ''
                ) ?>

            </div>

        <?php endif; ?>


        <!-- =================================================
             DASHBOARD HEADER
        ================================================== -->

        <section class="dashboard-header">

            <div>

                <p class="admin-eyebrow">
                    CONTROL PANEL
                </p>

                <h1>
                    Dashboard
                </h1>

                <p class="dashboard-subtitle">

                    Manage your portfolio,
                    projects, assets and
                    website statistics.

                </p>

            </div>


            <div class="dashboard-actions">

                <a
                    class="admin-btn admin-btn-primary"
                    href="project_form.php"
                >
                    + Project
                </a>


                <a
                    class="admin-btn"
                    href="asset_form.php"
                >
                    + Asset
                </a>

            </div>

        </section>


        <!-- =================================================
             STATISTICS
        ================================================== -->

        <section class="stats-grid">


            <!-- PROJECTS -->

            <article class="stat-card">

                <div class="stat-icon">
                    🎮
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Total Projects
                    </span>

                    <strong class="stat-number">
                        <?= $totalProjects ?>
                    </strong>

                    <span class="stat-info">
                        <?= $publishedProjects ?>
                        published
                    </span>

                </div>

            </article>


            <!-- ASSETS -->

            <article class="stat-card">

                <div class="stat-icon">
                    🧩
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Total Assets
                    </span>

                    <strong class="stat-number">
                        <?= $totalAssets ?>
                    </strong>

                    <span class="stat-info">
                        <?= $publishedAssets ?>
                        published
                    </span>

                </div>

            </article>


            <!-- USERS -->

            <article class="stat-card">

                <div class="stat-icon">
                    👤
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Registered Users
                    </span>

                    <strong class="stat-number">
                        <?= $userCount ?>
                    </strong>

                    <span class="stat-info">
                        registered accounts
                    </span>

                </div>

            </article>


            <!-- VISITORS -->

            <article class="stat-card stat-highlight">

                <div class="stat-icon">
                    👁
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Total Visitors
                    </span>

                    <strong class="stat-number">
                        <?= $totalVisitors ?>
                    </strong>

                    <span class="stat-info">
                        unique visitors
                    </span>

                </div>

            </article>


            <!-- TODAY -->

            <article class="stat-card">

                <div class="stat-icon">
                    📈
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Visitors Today
                    </span>

                    <strong class="stat-number">
                        <?= $todayVisitors ?>
                    </strong>

                    <span class="stat-info">
                        unique today
                    </span>

                </div>

            </article>


            <!-- PAGE VIEWS -->

            <article class="stat-card">

                <div class="stat-icon">
                    📊
                </div>

                <div class="stat-content">

                    <span class="stat-label">
                        Page Views
                    </span>

                    <strong class="stat-number">
                        <?= $totalViews ?>
                    </strong>

                    <span class="stat-info">
                        <?= $todayViews ?>
                        views today
                    </span>

                </div>

            </article>


        </section>


        <!-- =================================================
             VISITOR ANALYTICS
        ================================================== -->

        <section class="dashboard-panel analytics-panel">

            <div class="panel-header">

                <div>

                    <p class="admin-eyebrow">
                        ANALYTICS
                    </p>

                    <h2>
                        Website Visitors
                    </h2>

                </div>


                <div class="analytics-summary">

                    <div>

                        <span>
                            Today
                        </span>

                        <strong>
                            <?= $todayVisitors ?>
                        </strong>

                    </div>


                    <div>

                        <span>
                            Views
                        </span>

                        <strong>
                            <?= $todayViews ?>
                        </strong>

                    </div>

                </div>

            </div>


            <div class="chart-title">

                <span>
                    Visitors — Last 7 Days
                </span>

                <span class="chart-legend">

                    <i></i>

                    Unique Visitors

                </span>

            </div>


            <div class="chart">

                <?php foreach ($chart as $day): ?>


                    <?php

                    $height = 0;

                    if ($day['visitors'] > 0) {

                        $height =
                            (
                                $day['visitors']
                                /
                                $maxVisitors
                            ) * 100;
                    }

                    ?>


                    <div class="chart-column">


                        <div class="chart-value">

                            <?= $day['visitors'] ?>

                        </div>


                        <div class="chart-bar-wrapper">

                            <div
                                class="chart-bar"
                                style="height: <?= max(
                                    8,
                                    $height
                                ) ?>%;"
                                title="<?= $day['visitors'] ?> visitors / <?= $day['views'] ?> views"
                            ></div>

                        </div>


                        <div class="chart-label">

                            <?= e(
                                $day['label']
                            ) ?>

                        </div>


                    </div>


                <?php endforeach; ?>

            </div>

        </section>


        <!-- =================================================
             QUICK ACTIONS
        ================================================== -->

        <section class="quick-actions">


            <a
                class="quick-card"
                href="project_form.php"
            >

                <span class="quick-icon">
                    🎮
                </span>


                <span class="quick-text">

                    <strong>
                        Add Project
                    </strong>

                    <small>
                        Create a new game project
                    </small>

                </span>


                <span class="quick-arrow">
                    →
                </span>

            </a>


            <a
                class="quick-card"
                href="asset_form.php"
            >

                <span class="quick-icon">
                    🧩
                </span>


                <span class="quick-text">

                    <strong>
                        Add Asset
                    </strong>

                    <small>
                        Upload a game asset
                    </small>

                </span>


                <span class="quick-arrow">
                    →
                </span>

            </a>


            <a
                class="quick-card"
                href="settings.php"
            >

                <span class="quick-icon">
                    ⚙
                </span>


                <span class="quick-text">

                    <strong>
                        Website Settings
                    </strong>

                    <small>
                        Edit portfolio information
                    </small>

                </span>


                <span class="quick-arrow">
                    →
                </span>

            </a>


        </section>


        <!-- =================================================
             PROJECTS
        ================================================== -->

        <section class="dashboard-panel">

            <div class="panel-header">

                <div>

                    <p class="admin-eyebrow">
                        CONTENT
                    </p>

                    <h2>
                        Projects
                    </h2>

                </div>


                <a
                    class="panel-link"
                    href="project_form.php"
                >
                    + Add Project
                </a>

            </div>


            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Title
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Published
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $projects
                            as $p
                        ): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= e(
                                            $p['title']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= e(
                                        $p['category']
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $p['published']
                                    ): ?>

                                        <span
                                            class="status status-success"
                                        >
                                            Published
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="status status-muted"
                                        >
                                            Draft
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="project_form.php?id=<?= (int) $p['id'] ?>"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            class="danger-link"
                                            href="project_delete.php?id=<?= (int) $p['id'] ?>"
                                            onclick="return confirm('Delete this project?')"
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (!$projects): ?>

                            <tr>

                                <td
                                    colspan="4"
                                    class="empty-state"
                                >
                                    No projects yet.
                                </td>

                            </tr>

                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </section>


        <!-- =================================================
             ASSETS
        ================================================== -->

        <section class="dashboard-panel">

            <div class="panel-header">

                <div>

                    <p class="admin-eyebrow">
                        LIBRARY
                    </p>

                    <h2>
                        Assets
                    </h2>

                </div>


                <a
                    class="panel-link"
                    href="asset_form.php"
                >
                    + Add Asset
                </a>

            </div>


            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Title
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Published
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $assets
                            as $a
                        ): ?>

                            <tr>

                                <td>

                                    <strong>

                                        <?= e(
                                            $a['title']
                                        ) ?>

                                    </strong>

                                </td>


                                <td>

                                    <?= e(
                                        $a['category']
                                    ) ?>

                                </td>


                                <td>

                                    <?php if (
                                        $a['published']
                                    ): ?>

                                        <span
                                            class="status status-success"
                                        >
                                            Published
                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="status status-muted"
                                        >
                                            Draft
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="asset_form.php?id=<?= (int) $a['id'] ?>"
                                        >
                                            Edit
                                        </a>


                                        <a
                                            class="danger-link"
                                            href="asset_delete.php?id=<?= (int) $a['id'] ?>"
                                            onclick="return confirm('Delete this asset?')"
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (!$assets): ?>

                            <tr>

                                <td
                                    colspan="4"
                                    class="empty-state"
                                >
                                    No assets yet.
                                </td>

                            </tr>

                        <?php endif; ?>


                    </tbody>

                </table>

            </div>

        </section>


    </div>

</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="admin-footer">

    <div class="admin-container">

        Portfolio Admin Panel

        <span>
            • <?= date('Y') ?>
        </span>

    </div>

</footer>


</body>

</html>