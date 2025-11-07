<?php
/**
 * Admin Sidebar Component
 * Reusable sidebar navigation for admin panel
 *
 * Required variables:
 * - $translations: Array of translated strings
 * - $business_name: Business name from config
 * - $version: Application version
 * - $is_boss: Integer (1 if user is boss, 0 otherwise)
 * - $is_new_version_available: Boolean indicating if update is available
 * - $current_page: String to determine active menu item (optional)
 */

// Determine the base path for links based on current location
$path_depth = substr_count($_SERVER['PHP_SELF'], '/') - substr_count(__DIR__, '/') - 2;
$base_path = str_repeat('../', $path_depth);
?>

<div class="col-sm-2 sidenav hidden-xs text-center">
    <h2><img src="<?php echo $base_path; ?>assets/img/logo.png" width="105px" alt="Logo"></h2>
    <p class="lead mb-4 fs-4"><?php echo htmlspecialchars($business_name); ?> - <?php echo htmlspecialchars($version); ?></p>
    <ul class="nav nav-pills nav-stacked">
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'dashboard') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/dashboard/">
                <i class="bi bi-speedometer"></i> <?php echo $translations["mainpage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'users') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/users/">
                <i class="bi bi-people"></i> <?php echo $translations["users"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'statistics') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/statistics/">
                <i class="bi bi-bar-chart"></i> <?php echo $translations["statspage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'sell') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/sell/">
                <i class="bi bi-shop"></i> <?php echo $translations["sellpage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'invoices') ? 'active' : ''; ?>">
            <a href="<?php echo $base_path; ?>admin/invoices/" class="sidebar-link">
                <i class="bi bi-receipt"></i> <?php echo $translations["invoicepage"]; ?>
            </a>
        </li>
        <?php if (isset($is_boss) && $is_boss === 1): ?>
            <li class="sidebar-header">
                <?php echo $translations["settings"]; ?>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'mainsettings') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/mainsettings/">
                    <i class="bi bi-gear"></i>
                    <span><?php echo $translations["businesspage"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'workers') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/workers/">
                    <i class="bi bi-people"></i>
                    <span><?php echo $translations["workers"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'packages') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/packages/">
                    <i class="bi bi-box-seam"></i>
                    <span><?php echo $translations["packagepage"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'hours') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/hours/">
                    <i class="bi bi-clock"></i>
                    <span><?php echo $translations["openhourspage"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'smtp') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/smtp/">
                    <i class="bi bi-envelope-at"></i>
                    <span><?php echo $translations["mailpage"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'chroom') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/chroom/">
                    <i class="bi bi-duffle"></i>
                    <span><?php echo $translations["chroompage"]; ?></span>
                </a>
            </li>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'rule') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/boss/rule/">
                    <i class="bi bi-file-ruled"></i>
                    <span><?php echo $translations["rulepage"]; ?></span>
                </a>
            </li>
        <?php endif; ?>
        <li class="sidebar-header">
            <?php echo $translations["shopcategory"]; ?>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'tickets') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/shop/tickets/">
                <i class="bi bi-ticket"></i>
                <span><?php echo $translations["ticketspage"]; ?></span>
            </a>
        </li>
        <li class="sidebar-header">
            <?php echo $translations["trainersclass"]; ?>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'timetable') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/trainers/timetable/">
                <i class="bi bi-calendar-event"></i>
                <span><?php echo $translations["timetable"]; ?></span>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'trainers') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/trainers/personal/">
                <i class="bi bi-award"></i>
                <span><?php echo $translations["trainers"]; ?></span>
            </a>
        </li>
        <li class="sidebar-header"><?php echo $translations["other-header"]; ?></li>
        <?php if (isset($is_boss) && $is_boss === 1): ?>
            <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'updater') ? 'active' : ''; ?>">
                <a class="sidebar-link" href="<?php echo $base_path; ?>admin/updater/">
                    <i class="bi bi-cloud-download"></i>
                    <span><?php echo $translations["updatepage"]; ?></span>
                    <?php if (isset($is_new_version_available) && $is_new_version_available): ?>
                        <span class="sidebar-badge badge">
                            <i class="bi bi-exclamation-circle"></i>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'log') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>admin/log/">
                <i class="bi bi-clock-history"></i>
                <span><?php echo $translations["logpage"]; ?></span>
            </a>
        </li>
    </ul><br>
</div>
