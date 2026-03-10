<?php
include 'components/header.php';
include 'components/nav.php';

// Include necessary classes
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/models/our_project.mod.php';

// Initialize database connection and model
$pdo = DB::getConnection();
$projectModel = new OurProject($pdo);

// Retrieve all projects from model
$allProjects = $projectModel->getAll();
?>

<section id="features" class="section-padding">
    <div class="container">

        <div class="text-center mb-80">
            <h2 class="section-title">Our Projects</h2>
            <p class="section-sub">On our quest to exploring emerging and exponential technologies to impact 1 billion young Africans, working with our partners we have initiated a number of innovative projects and campaigns to develop an enabling environment for everyone.</p>
        </div>

        <div class="row">
            <?php foreach ($allProjects as $project): ?>
            <div class="col-md-3 col-sm-6 mb-30">
                <div class="featured-item seo-service">
                    <div class="icon">
                        <img class="img-responsive" src="<?php echo htmlspecialchars($project['photo']); ?>" alt="">
                    </div>
                    <div class="desc">
                        <h2><?php echo htmlspecialchars($project['project_name']); ?></h2>
                        <p><?php echo htmlspecialchars($project['project_description']); ?></p>
                        <div class="bg-overlay"></div>
                        <p><a class="learn-more" href="#">Learn More <i class="fa fa-long-arrow-right"></i></a></p>
                    </div>
                </div><!-- /.featured-item -->
            </div><!-- /.col-md-3 -->
            <?php endforeach; ?>
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>