<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_user_id'])) {
    header('Location: ../admin-login.php');
    exit();
}

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../classes/db.php';
require_once __DIR__ . '/../classes/models/count.mod.php';
require_once __DIR__ . '/../classes/models/users.mod.php';
require_once __DIR__ . '/../classes/models/team.mod.php';
require_once __DIR__ . '/../classes/models/our_work.mod.php';
require_once __DIR__ . '/../classes/models/our_project.mod.php';
require_once __DIR__ . '/../classes/models/partners.mod.php';
require_once __DIR__ . '/../classes/models/roles.mod.php';
require_once __DIR__ . '/../classes/models/privileges.mod.php';

try {
    $pdo = DB::getConnection();
    
    // Get statistics
    $count = new Count($pdo);
    $user = new User($pdo);
    $team = new Team($pdo);
    $work = new OurWork($pdo);
    $project = new OurProject($pdo);
    $partner = new Partner($pdo);
    
    $countData = $count->getAll();
    $users = $user->getAll();
    $teamMembers = $team->getAll();
    $works = $work->getAll();
    $projects = $project->getAll();
    $partners = $partner->getAll();
    
    // Extract count data
    $countInfo = $countData[0] ?? [];
    $totalUsers = count($users);
    $totalTeam = count($teamMembers);
    $totalWorks = count($works);
    $totalProjects = count($projects);
    $totalPartners = count($partners);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IoT Portal</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #48bb78;
            --danger: #f56565;
            --warning: #ed8936;
            --info: #4299e1;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-header i {
            font-size: 24px;
            margin-right: 10px;
        }
        
        .sidebar-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            padding-left: 20px;
        }
        
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        .sidebar-menu i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 30px;
        }
        
        .top-navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .logout-btn {
            padding: 8px 15px;
            background-color: #f56565;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #e53e3e;
            text-decoration: none;
            color: white;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid var(--color);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card.users { --color: var(--primary); }
        .stat-card.team { --color: var(--success); }
        .stat-card.works { --color: var(--warning); }
        .stat-card.projects { --color: var(--info); }
        .stat-card.partners { --color: var(--danger); }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--color);
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-icon {
            font-size: 32px;
            color: var(--color);
            opacity: 0.2;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .section-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .action-btn {
            padding: 15px;
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            display: block;
        }
        
        .action-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        
        .action-btn i {
            display: block;
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1001;
            }
            .sidebar.open {
                transform: translateX(0);
            }

            /* overlay when sidebar open */
            .sidebar-backdrop {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.4);
                z-index: 1000;
            }
            .sidebar-backdrop.hidden { display: none; }

            .main-content {
                margin-left: 0;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            /* show toggle button in navbar */
            .menu-toggle {
                display: inline-block;
                font-size: 24px;
                cursor: pointer;
                margin-right: 15px;
                color: #333;
            }
        }
        
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar backdrop -->
        <div id="sidebarBackdrop" class="sidebar-backdrop hidden" onclick="toggleSidebar()"></div>
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-sliders-h"></i>
                <h3>IoT Admin</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="team.php"><i class="fas fa-users-circle"></i> Team Members</a></li>
                <li><a href="works.php"><i class="fas fa-briefcase"></i> Our Works</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <span class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></span>
            <h2 style="margin: 0; color: #333;">Dashboard</h2>
                <div class="user-info">
                    <div>
                        <p style="margin: 0; color: #666; font-size: 12px;">Welcome back</p>
                        <p style="margin: 0; font-weight: 600; color: #333;"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?></div>
                    <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <h3 class="section-title"><i class="fas fa-chart-pie"></i> Overview</h3>
            <div class="dashboard-grid">
                <div class="stat-card users">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number"><?php echo $totalUsers; ?></div>
                </div>
                
                <div class="stat-card team">
                    <div class="stat-icon"><i class="fas fa-users-circle"></i></div>
                    <div class="stat-label">Team Members</div>
                    <div class="stat-number"><?php echo $totalTeam; ?></div>
                </div>
                
                <div class="stat-card works">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-label">Our Works</div>
                    <div class="stat-number"><?php echo $totalWorks; ?></div>
                </div>
                
                <div class="stat-card projects">
                    <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="stat-label">Projects</div>
                    <div class="stat-number"><?php echo $totalProjects; ?></div>
                </div>
                
                <div class="stat-card partners">
                    <div class="stat-icon"><i class="fas fa-handshake"></i></div>
                    <div class="stat-label">Partners</div>
                    <div class="stat-number"><?php echo $totalPartners; ?></div>
                </div>
                <!-- count metrics cards -->
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="stat-label">Project Run</div>
                    <div class="stat-number"><?php echo htmlspecialchars($countInfo['count_project'] ?? 0); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-heartbeat"></i></div>
                    <div class="stat-label">Impact</div>
                    <div class="stat-number"><?php echo htmlspecialchars($countInfo['count_impact'] ?? 0); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Members</div>
                    <div class="stat-number"><?php echo htmlspecialchars($countInfo['count_member'] ?? 0); ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-label">Trainees</div>
                    <div class="stat-number"><?php echo htmlspecialchars($countInfo['count_trainees'] ?? 0); ?></div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <h3 class="section-title"><i class="fas fa-magic"></i> Quick Actions</h3>
            <div class="quick-actions">
                <a href="users.php?action=create" class="action-btn">
                    <i class="fas fa-user-plus"></i>
                    <strong>Add User</strong>
                </a>
                <a href="team.php?action=create" class="action-btn">
                    <i class="fas fa-user-plus"></i>
                    <strong>Add Team Member</strong>
                </a>
                <a href="works.php?action=create" class="action-btn">
                    <i class="fas fa-plus"></i>
                    <strong>Add Work</strong>
                </a>
                <a href="projects.php?action=create" class="action-btn">
                    <i class="fas fa-plus"></i>
                    <strong>Add Project</strong>
                </a>
                <a href="partners.php?action=create" class="action-btn">
                    <i class="fas fa-handshake"></i>
                    <strong>Add Partner</strong>
                </a>
                <a href="statistics.php?action=counts" class="action-btn">
                    <i class="fas fa-plus"></i>
                    <strong>Add Counts</strong>
                </a>
                <a href="statistics.php" class="action-btn">
                    <i class="fas fa-chart-bar"></i>
                    <strong>View Statistics</strong>
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            var sidebar = document.querySelector('.sidebar');
            var backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                backdrop.classList.add('hidden');
            } else {
                sidebar.classList.add('open');
                backdrop.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
