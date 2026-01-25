 <?php
include 'components/header.php';
include 'components/nav.php';
?>
 
 <section id="contact" class="section-padding contact-form-wrapper">
          
          <div class="container">
            <div class="row">

                <div class="col-md-8">
                    <form name="contact-form" id="contactForm" class="clearfix" action="sendemail.php" method="POST">

                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-field">
                            <input type="text" name="name" class="validate" id="name">
                            <label for="name">Name</label>
                          </div>

                        </div><!-- /.col-md-6 -->

                        <div class="col-md-6">
                          <div class="input-field">
                            <label class="sr-only" for="email">Email</label>
                            <input id="email" type="email" name="email" class="validate" >
                            <label for="email" data-error="wrong" data-success="right">Email</label>
                          </div>
                        </div><!-- /.col-md-6 -->
                      </div><!-- /.row -->

                      <div class="row">
                        <div class="col-md-6">
                          <div class="input-field">
                            <input id="phone" type="tel" name="phone" class="validate" >
                            <label for="phone">Phone Number</label>
                          </div>
                        </div><!-- /.col-md-6 -->

                        <div class="col-md-6">
                          <div class="input-field">
                            <input id="website" type="text" name="website" class="validate" >
                            <label for="website">Your Website</label>
                          </div>
                        </div><!-- /.col-md-6 -->
                      </div><!-- /.row -->

                      <div class="input-field">
                        <textarea name="message" id="message" class="materialize-textarea" ></textarea>
                        <label for="message">Message</label>
                      </div>

                      <button type="submit" name="submit" class="waves-effect waves-light btn pink right mt-30">Send Message</button>
                    </form>
                </div><!-- /.col-md-8 -->

                <div class="col-md-4 contact-info">

                    <address>
                      <i class="material-icons brand-color">&#xE8B4;</i>
                      <div class="address">
                        15th Sun Street <br>
                        RingRoad-Circle

                        <hr>

                        <p>GA 0481145,P.O. BOX 6994<br>
                        Accra North,Ghana-West Africa.</p>
                      </div>

                      <i class="material-icons brand-color">&#xE61C;</i>
                      <div class="phone">
                        <p>Tel: +233 548-889-998<br>
                        Phone: +233 548-889-998</p>
                      </div>

                      <i class="material-icons brand-color">&#xE0B7;</i>
                      <div class="mail">
                        <p><a href="mailto:#">hello@iotnetworkhub.org</a><br>
                        <a href="#">www.iotnetworkhub.org</a></p>
                      </div>
                    </address>
                </div><!-- /.col-md-4 -->
            </div><!-- /.row -->
          </div>
        </section>
        <?php
include 'components/footer.php';

?>