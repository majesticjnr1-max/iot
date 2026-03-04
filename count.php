 <?php
session_start();
include 'components/header.php';
include 'components/nav.php';

// include necessary classes for counts
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/models/count.mod.php';
require_once __DIR__ . '/classes/views/CountView.php';

// initialize database and model
$pdo = DB::getConnection();
$countModel = new Count($pdo);

// fetch all count records
$allCounts = $countModel->getAll();

// create view and get structured data
$view = new CountView();
$viewData = $view->getListData($allCounts);

// determine stats (use first record or defaults)
$stats = [
    'count_project' => 0,
    'count_impact' => 0,
    'count_member' => 0,
    'count_trainees' => 0
];
if (!empty($viewData['counts'][0])) {
    $stats = $viewData['counts'][0];
}
?>

 <section class="counter-section facts-two banner-10 parallax-bg bg-fixed overlay light-9" data-stellar-background-ratio="0.5">
              <div class="container">

                  <div class="row text-center">
                      <div class="col-sm-3 counter-wrap">
                        <i class="material-icons brand-color">&#xE90F;</i>
                        <span class="timer"><?php echo htmlspecialchars($stats['count_project'] ?? 0); ?></span>
                        <span class="count-description">PROJECT RUN</span>
                      </div> <!-- /.col-sm-3 -->

                      <div class="col-sm-3 counter-wrap">
                        <i class="material-icons brand-color">&#xE863;</i>
                        <span class="timer"><?php echo htmlspecialchars($stats['count_impact'] ?? 0); ?></span>
                        <span class="count-description">IMPACT</span>
                      </div><!-- /.col-sm-3 -->

                      <div class="col-sm-3 counter-wrap">
                        <i class="material-icons brand-color">&#xE8F8;</i>
                        <span class="timer"><?php echo htmlspecialchars($stats['count_member'] ?? 0); ?></span>
                        <span class="count-description">MEMBERS</span>
                      </div><!-- /.col-sm-3 -->

                      <div class="col-sm-3 counter-wrap">
                        <i class="material-icons brand-color">&#xE87E;</i>
                        <span class="timer"><?php echo htmlspecialchars($stats['count_trainees'] ?? 0); ?></span>
                        <span class="count-description">PEOPLE TRAINED</span>
                      </div><!-- /.col-sm-3 -->
                  </div>
              </div><!-- /.container -->
        </section>

<?php if (isset($_SESSION['admin_user_id'])): ?>
    <div class="text-center mt-4">
        <a href="admin/statistics.php" class="btn btn-primary">Add Counts</a>
    </div>
<?php endif; ?>