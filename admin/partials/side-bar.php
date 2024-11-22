<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary vh-100">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Company name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <?php 
                    $currentPage = basename($_SERVER['PHP_SELF']);
                    $dashboardActive = ($currentPage == 'dashboard.php') ? 'disabled' : '';
                    ?>
                    <a class="nav-link d-flex align-items-center gap-2 <?= $dashboardActive ?>" href="../dashboard.php" <?= ($dashboardActive == 'disabled') ? 'aria-disabled="true" tabindex="-1"' : '' ?> 
                    <?= ($dashboardActive == 'disabled') ? 'style="pointer-events: none;"' : '' ?>>
                        <i class="fa-solid fa-gauge fa-fw me-2"></i>
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <?php 
                    $studentsActive = in_array($currentPage, ['register.php', 'edit.php', 'delete.php']) ? 'disabled' : '';
                    ?>
                    <a class="nav-link d-flex align-items-center gap-2 <?= $studentsActive ?>" href="student/register.php" 
                    <?= $studentsActive ? 'aria-disabled="true" tabindex="-1" style="pointer-events: none;"' : '' ?>>
                        <i class="fa-solid fa-user fa-fw me-2"></i>
                        Students
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="subject/add.php">
                        <i class="fa-solid fa-book fa-fw me-2"></i>
                        Subjects
                    </a>
                </li>

            </ul>
            <hr class="my-3">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" href="logout.php">
                        <i class="fa-solid fa-right-to-bracket fa-fw me-2"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
