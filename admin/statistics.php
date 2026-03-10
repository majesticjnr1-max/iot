<?php
session_start();

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

$pdo = DB::getConnection();

// Get all data models
$count = new Count($pdo);
$user = new User($pdo);
$team = new Team($pdo);
$work = new OurWork($pdo);
$project = new OurProject($pdo);
$partner = new Partner($pdo);

// Fetch all data
$countData = $count->getAll();
$users = $user->getAll();
$teamMembers = $team->getAll();
$works = $work->getAll();
$projects = $project->getAll();
$partners = $partner->getAll();

// Extract statistics
$countInfo = $countData[0] ?? [];
$message = '';
$error = '';

// handle form submission for count metrics
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_counts'])) {
    $impact = intval($_POST['impact'] ?? 0);
    $project = intval($_POST['project'] ?? 0);
    $member = intval($_POST['member'] ?? 0);
    $trainees = intval($_POST['trainees'] ?? 0);

    if (!empty($countData) && isset($countData[0]['id'])) {
        // update existing record
        if ($count->update($countData[0]['id'], $impact, $project, $member, $trainees)) {
            $message = 'Counts updated successfully.';
        } else {
            $error = 'Failed to update counts.';
        }
    } else {
        // create new record
        if ($count->addCount($impact, $project, $member, $trainees)) {
            $message = 'Counts added successfully.';
        } else {
            $error = 'Failed to add counts.';
        }
    }

    // refresh data
    $countData = $count->getAll();
    $countInfo = $countData[0] ?? [];
}

$stats = [
    'users' => count($users),
    'team' => count($teamMembers),
    'works' => count($works),
    'projects' => count($projects),
    'partners' => count($partners),
];

// Additional count statistics (align with model columns)
$statsCount = [
    'project_run' => $countInfo['count_project'] ?? 0,
    'impact' => $countInfo['count_impact'] ?? 0,
    'members' => $countInfo['count_member'] ?? 0,
    'trainees' => $countInfo['count_trainees'] ?? 0,
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Admin Portal</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/img/icons/IoT.png" type="image/x-icon">
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
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--color);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.users { --color: var(--primary); }
        .stat-card.team { --color: var(--success); }
        .stat-card.works { --color: var(--warning); }
        .stat-card.projects { --color: var(--info); }
        .stat-card.partners { --color: var(--danger); }
        
        .stat-number {
            font-size: 40px;
            font-weight: 700;
            color: var(--color);
            margin: 15px 0 10px 0;
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
        }
        
        .content-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }
        
        .detail-list {
            list-style: none;
            padding: 0;
        }
        
        .detail-list li {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .detail-list li:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #666;
            font-size: 14px;
        }
        
        .detail-value {
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-sliders-h"></i>
                <h3>IoT Admin</h3>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="team.php"><i class="fas fa-users-circle"></i> Team Members</a></li>
                <li><a href="works.php"><i class="fas fa-briefcase"></i> Our Works</a></li>
                <li><a href="projects.php"><i class="fas fa-project-diagram"></i> Projects</a></li>
                <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
                <li><a href="roles.php"><i class="fas fa-user-tag"></i> Roles</a></li>
                <li><a href="privileges.php"><i class="fas fa-shield-alt"></i> Privileges</a></li>
                <li><a href="statistics.php" class="active"><i class="fas fa-chart-bar"></i> Statistics</a></li>
                <li><a href="testimonies.php"><i class="fas fa-comment-dots"></i> Testimonies</a></li>
                <li style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">
                    <a href="../admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </aside>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Navbar -->
            <div class="top-navbar">
                <h2 style="margin: 0; color: #333;">Statistics</h2>
                <a href="../admin-logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
            
            <!-- Overview Statistics -->
            <h3 class="section-title"><i class="fas fa-chart-pie"></i> Content Overview</h3>
            <div class="stats-grid">
                <div class="stat-card users">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-number"><?php echo $stats['users']; ?></div>
                </div>
                
                <div class="stat-card team">
                    <div class="stat-icon"><i class="fas fa-users-circle"></i></div>
                    <div class="stat-label">Team Members</div>
                    <div class="stat-number"><?php echo $stats['team']; ?></div>
                </div>
                
                <div class="stat-card works">
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                    <div class="stat-label">Our Works</div>
                    <div class="stat-number"><?php echo $stats['works']; ?></div>
                </div>
                
                <div class="stat-card projects">
                    <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="stat-label">Projects</div>
                    <div class="stat-number"><?php echo $stats['projects']; ?></div>
                </div>
                
                <div class="stat-card partners">
                    <div class="stat-icon"><i class="fas fa-handshake"></i></div>
                    <div class="stat-label">Partners</div>
                    <div class="stat-number"><?php echo $stats['partners']; ?></div>
                </div>
            </div>
            
            <!-- Detailed Statistics & Counter Edit Form -->
            <div class="content-card">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Portal Statistics</h3>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" class="row g-3 mb-4">
                    <input type="hidden" name="update_counts" value="1">
                    <div class="col-md-3">
                        <label class="form-label">Project Run</label>
                        <input type="number" name="project" class="form-control" value="<?php echo htmlspecialchars($countInfo['count_project'] ?? 0); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Impact</label>
                        <input type="number" name="impact" class="form-control" value="<?php echo htmlspecialchars($countInfo['count_impact'] ?? 0); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Members</label>
                        <input type="number" name="member" class="form-control" value="<?php echo htmlspecialchars($countInfo['count_member'] ?? 0); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trainees</label>
                        <input type="number" name="trainees" class="form-control" value="<?php echo htmlspecialchars($countInfo['count_trainees'] ?? 0); ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Counters</button>
                    </div>
                </form>

                <ul class="detail-list">
                    <li>
                        <span class="detail-label"><i class="fas fa-project-diagram"></i> Project Run</span>
                        <span class="detail-value"><?php echo htmlspecialchars($statsCount['project_run'] ?? 0); ?></span>
                    </li>
                    <li>
                        <span class="detail-label"><i class="fas fa-heartbeat"></i> Impact</span>
                        <span class="detail-value"><?php echo htmlspecialchars($statsCount['impact'] ?? 0); ?></span>
                    </li>
                    <li>
                        <span class="detail-label"><i class="fas fa-users"></i> Members</span>
                        <span class="detail-value"><?php echo htmlspecialchars($statsCount['members'] ?? 0); ?></span>
                    </li>
                    <li>
                        <span class="detail-label"><i class="fas fa-user-graduate"></i> Trainees</span>
                        <span class="detail-value"><?php echo htmlspecialchars($statsCount['trainees'] ?? 0); ?></span>
                    </li>
                </ul>
            </div>
            
            <!-- Additional Statistics -->
            <div class="stats-grid">
                <div class="content-card">
                    <h3 class="section-title"><i class="fas fa-list"></i> Users Statistics</h3>
                    <ul class="detail-list">
                        <li>
                            <span class="detail-label">Total Users</span>
                            <span class="detail-value"><?php echo count($users); ?></span>
                        </li>
                        <li>
                            <span class="detail-label">Admin Users</span>
                            <span class="detail-value"><?php echo count(array_filter($users, fn($u) => $u['role_id'] == 1)); ?></span>
                        </li>
                    </ul>
                </div>
                
                <div class="content-card">
                    <h3 class="section-title"><i class="fas fa-users"></i> Team Statistics</h3>
                    <ul class="detail-list">
                        <li>
                            <span class="detail-label">Total Members</span>
                            <span class="detail-value"><?php echo count($teamMembers); ?></span>
                        </li>
                        <li>
                            <span class="detail-label">Positions Available</span>
                            <span class="detail-value"><?php echo count(array_unique(array_column($teamMembers, 'position'))); ?></span>
                        </li>
                    </ul>
                </div>
                
                <div class="content-card">
                    <h3 class="section-title"><i class="fas fa-briefcase"></i> Works Statistics</h3>
                    <ul class="detail-list">
                        <li>
                            <span class="detail-label">Total Works</span>
                            <span class="detail-value"><?php echo count($works); ?></span>
                        </li>
                        <li>
                            <span class="detail-label">With Photos</span>
                            <span class="detail-value"><?php echo count(array_filter($works, fn($w) => !empty($w['photo']))); ?></span>
                        </li>
                    </ul>
                </div>
                
                <div class="content-card">
                    <h3 class="section-title"><i class="fas fa-handshake"></i> Partners Statistics</h3>
                    <ul class="detail-list">
                        <li>
                            <span class="detail-label">Total Partners</span>
                            <span class="detail-value"><?php echo count($partners); ?></span>
                        </li>
                        <li>
                            <span class="detail-label">Active Partners</span>
                            <span class="detail-value"><?php echo count($partners); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
