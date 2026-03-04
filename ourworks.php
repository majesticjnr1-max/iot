 <?php
include 'components/header.php';
include 'components/nav.php';

// Include necessary classes
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/models/our_work.mod.php';
require_once __DIR__ . '/classes/views/OurWorkView.php';

// Initialize database connection and model
$pdo = DB::getConnection();
$workModel = new OurWork($pdo);

// Create view instance
$view = new OurWorkView();

// Retrieve all works from model
$allWorks = $workModel->getAll();

// Get structured data from view
$viewData = $view->getListData($allWorks);
?>
 
 <section class="section-padding">
              <div class="text-center mb-50">
                  <h2 class="section-title">Our Works</h2>
                  <p class="section-sub">We are paving the way for a new era of growth and prosperity on the continent, by providing training, mentorship, and support to young Africans in STEM fields. IoT Network Hub is empowering the next generation of leaders to drive innovation and change, with a focus on inclusivity, innovation, integrity, creativity, and teamwork, IoT Network Hub is at the forefront of a movement to transform Africa into a hub of technological progress and economic growth.</p>
              </div>


            <div class="portfolio-container text-center">
                <ul class="portfolio-filter brand-filter">
                    <li class="active waves-effect waves-light" data-group="all">All</li>
                    <li class=" waves-effect waves-light" data-group="websites">IoT Meetup</li>
                    <li class=" waves-effect waves-light" data-group="branding">Outreach</li>
                    <li class=" waves-effect waves-light" data-group="marketing">Exhibitions</li>
                    <li class=" waves-effect waves-light" data-group="photography">R&D</li>
                    <li class=" waves-effect waves-light" data-group="photography">Training</li>
                    <li class=" waves-effect waves-light" data-group="photography">IoT Brand</li>
                </ul>


                <div class="portfolio portfolio-masonry col-3 mtb-50">
                  <!-- add "gutter" class for add spacing -->

                    <?php if (!empty($viewData['works'])): ?>
                        <?php foreach ($viewData['works'] as $work): ?>
                            <div class="portfolio-item" data-groups='["all"]'>
                                <div class="portfolio-wrapper">
                                    <div class="thumb">
                                        <div class="bg-overlay brand-overlay"></div>
                                        <img src="<?php echo htmlspecialchars($work['photo_url'] ?? 'assets/img/portfolio/default.jpg'); ?>" alt="<?php echo htmlspecialchars($work['title'] ?? 'Work'); ?>">
                                        <div class="portfolio-intro">
                                            <div class="action-btn">
                                                <a href="<?php echo htmlspecialchars($work['photo_url'] ?? '#'); ?>" class="tt-lightbox" title="<?php echo htmlspecialchars($work['title'] ?? ''); ?>"><i class="fa fa-search"></i></a>
                                            </div>
                                            <h2><a href="#"><?php echo htmlspecialchars($work['work_title'] ?? 'Untitled'); ?></a></h2>
                                            <?php if (!empty($work['category'])): ?>
                                                <p><a href="#"><?php echo htmlspecialchars($work['category']); ?></a></p>
                                            <?php endif; ?>
                                        </div>
                                    </div><!-- thumb -->
                                </div><!-- /.portfolio-wrapper -->
                            </div><!-- /.portfolio-item -->
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center">No works available at this time.</p>
                        </div>
                    <?php endif; ?>

                </div><!-- /.portfolio -->

                <div class="load-more-button text-center">
                  <a class="waves-effect waves-light btn btn-large pink"> <i class="fa fa-spinner left"></i> Load More</a>
                </div>

            </div><!-- portfolio-container -->
        </section>

        <?php
include 'opinions.php';
include 'count.php';
include 'partners.php';
include 'components/footer.php';

?>