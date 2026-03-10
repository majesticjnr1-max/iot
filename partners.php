<?php
include 'components/header.php';
include 'components/nav.php';

// Include necessary classes
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/models/partners.mod.php';
require_once __DIR__ . '/classes/views/PartnerView.php';

// Initialize database connection and model
$pdo = DB::getConnection();
$partnerModel = new Partner($pdo);

// Create view instance
$view = new PartnerView();

// Retrieve all partners from model
$allPartners = $partnerModel->getAll();

// Get structured data from view
$viewData = $view->getListData($allPartners);
?>

<section class="section-padding">
    <div class="container">

        <div class="text-center mb-80">
            <h2 class="section-title text-uppercase">AWESOME PARTNERS</h2>
            <p class="section-sub">Through strategic partnerships and collaboration with governments, private sector organizations, and communities, IoT Network Hub fosters an innovative culture and leverages emerging technologies to address Africa's wicked problems, driving economic growth, social progress, and environmental sustainability across the continent.</p>
        </div>

        <div class="clients-grid gutter">
            <div class="row">
                <?php if (!empty($viewData['partners'])): ?>
                    <?php foreach ($viewData['partners'] as $partner): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="border-box">
                                <a href="<?php echo htmlspecialchars($partner['partner_website'] ?? '#'); ?>">
                                    <?php 
                                        $logoSrc = $partner['partner_logo'] ?? 'assets/img/client-logo/default.png';
                                        // If path starts with /, prepend /iot/ since app is at /iot/
                                        if (!empty($logoSrc) && strpos($logoSrc, '/') === 0 && strpos($logoSrc, '/iot/') !== 0 && strpos($logoSrc, 'data:') !== 0) {
                                            $logoSrc = '/iot' . $logoSrc;
                                        }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($logoSrc); ?>" alt="<?php echo htmlspecialchars($partner['partner_name'] ?? 'Partner'); ?>">
                                </a>
                            </div><!-- /.border-box -->
                        </div><!-- /.col-md-3 -->
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">No partners available at this time.</p>
                    </div>
                <?php endif; ?>
            </div><!-- /.row -->
        </div><!-- /.clients-grid -->

    </div><!-- /.container -->
</section>