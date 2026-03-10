<?php
include 'components/header.php';
include 'components/nav.php';

require_once 'classes/views/testimonies.view.php';
$testimonies = getTestimoniesData();
?>
<section class="section-padding">
  <div class="container">
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        <div class="thumb-carousel circle-thumb text-center">
          <ul class="slides">
            <?php foreach ($testimonies as $t): ?>
            <li data-thumb="<?php echo htmlspecialchars($t['avatar']); ?>">
              <div class="icon">
                <img src="<?php echo htmlspecialchars($t['avatar']); ?>" alt="Customer Thumb" class="opinion-thumb" style="border-radius: 50%;">
              </div>
              <div class="content">
                <p><?php echo htmlspecialchars($t['message']); ?></p>
                <div class="testimonial-meta brand-color">
                  <?php echo htmlspecialchars($t['name']); ?>,
                  <span><?php echo htmlspecialchars($t['position']); ?></span>
                </div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div><!-- /.row -->
  </div><!-- /.container -->
</section>
<style>
.opinion-thumb {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  object-fit: cover;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border: 2px solid #eee;
  background: #fff;
  display: inline-block;
}
</style>