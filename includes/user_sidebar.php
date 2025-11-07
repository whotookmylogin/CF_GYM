<?php
/**
 * User Dashboard Sidebar Component
 * Reusable sidebar navigation for user dashboard
 *
 * Required variables:
 * - $translations: Array of translated strings
 * - $business_name: Business name from config
 * - $current_page: String to determine active menu item (optional)
 */

// Determine the base path for links based on current location
$path_depth = substr_count($_SERVER['PHP_SELF'], '/') - substr_count(__DIR__, '/') - 2;
$base_path = str_repeat('../', $path_depth);
?>

<div class="col-sm-2 sidenav hidden-xs text-center">
    <h2><img src="<?php echo $base_path; ?>assets/img/brand/logo.png" width="105px" alt="Logo"></h2>
    <p class="lead mb-4 fs-4"><?php echo htmlspecialchars($business_name); ?></p>
    <ul class="nav nav-pills nav-stacked">
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'dashboard') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>dashboard/">
                <i class="bi bi-house"></i> <?php echo $translations["mainpage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'stats') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>dashboard/stats/">
                <i class="bi bi-graph-up"></i> <?php echo $translations["statspage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'profile') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>dashboard/profile/">
                <i class="bi bi-person-badge"></i> <?php echo $translations["profilepage"]; ?>
            </a>
        </li>
        <li class="sidebar-item <?php echo (isset($current_page) && $current_page === 'invoices') ? 'active' : ''; ?>">
            <a class="sidebar-link" href="<?php echo $base_path; ?>dashboard/invoices/">
                <i class="bi bi-receipt"></i> <?php echo $translations["invoicepage"]; ?>
            </a>
        </li>
    </ul><br>
</div>
