<?php
include 'components/header.php';
include 'components/nav.php';

// Include necessary classes
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/models/team.mod.php';
require_once __DIR__ . '/classes/views/TeamView.php';

// Initialize database connection and model
$pdo = DB::getConnection();
$teamModel = new Team($pdo);

// Create view instance
$view = new TeamView();

// Retrieve all team members from model
$allTeam = $teamModel->getAll();

// Get structured data from view
$viewData = $view->getListData($allTeam);
?>

<section class="section-padding">
            <div class="container">

              <div class="text-center mb-80">
                  <h2 class="section-title text-uppercase">Our Team</h2>
                  <p class="section-sub">We are a diverse team of passionate individuals at IoT Network Hub, united by our shared vision to leverage emerging technologies and empower young Africans to shape a brighter future for the continent.</p>
              </div>

              <div class="row">
                <?php if (!empty($viewData['teams'])): ?>
                    <?php foreach ($viewData['teams'] as $member): ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="team-wrapper">
                                <div class="team-img">
                                    <?php 
                                        $photoSrc = $member['photo'] ?? 'assets/img/team/default.jpg';
                                        // If path starts with /, prepend /iot/ since app is at /iot/
                                        if (!empty($photoSrc) && strpos($photoSrc, '/') === 0 && strpos($photoSrc, '/iot/') !== 0 && strpos($photoSrc, 'data:') !== 0) {
                                            $photoSrc = '/iot' . $photoSrc;
                                        }
                                    ?>
                                    <a href="#"><img src="<?php echo htmlspecialchars($photoSrc); ?>" class="img-responsive" alt="<?php echo htmlspecialchars($member['name'] ?? 'Team Member'); ?>"></a>
                                </div><!-- /.team-img -->

                                <div class="team-title">
                                    <h3><a href="#"><?php echo htmlspecialchars(strtoupper($member['name'] ?? '')); ?></a></h3>
                                    <span><?php echo htmlspecialchars($member['position'] ?? ''); ?></span>
                                    <?php if (!empty($member['bio'])): ?>
                                        <p><?php echo htmlspecialchars($member['bio']); ?></p>
                                    <?php endif; ?>
                                </div><!-- /.team-title -->

                                <ul class="team-social-links list-inline text-center">
                                    <?php if (!empty($member['facebook'])): ?>
                                        <li><a href="<?php echo htmlspecialchars($member['facebook']); ?>"><i class="fa fa-facebook"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($member['twitter'])): ?>
                                        <li><a href="<?php echo htmlspecialchars($member['twitter']); ?>"><i class="fa fa-twitter"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($member['linkedin'])): ?>
                                        <li><a href="<?php echo htmlspecialchars($member['linkedin']); ?>"><i class="fa fa-linkedin"></i></a></li>
                                    <?php endif; ?>
                                    <?php if (!empty($member['instagram'])): ?>
                                        <li><a href="<?php echo htmlspecialchars($member['instagram']); ?>"><i class="fa fa-instagram"></i></a></li>
                                    <?php endif; ?>
                                </ul>

                            </div><!-- /.team-wrapper -->
                        </div><!-- /.col-md-4 -->
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">No team members available at this time.</p>
                    </div>
                <?php endif; ?>
              </div><!-- /.row -->
            </div><!-- /.container -->
        </section>

        <?php
      include 'speaker.php';  
include 'components/footer.php';

?>