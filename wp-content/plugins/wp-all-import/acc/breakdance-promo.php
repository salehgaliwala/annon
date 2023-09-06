<?php

namespace WPAI\Breakdance;

function echoPromoCard() {
    $promoCardStyles = file_get_contents(__DIR__ . "/promo-card-styles.css");
    ?>
    <style>
        <?php echo $promoCardStyles; ?>
    </style>
    <div class='wpai-breakdance-promo-card'>
        
        <div class='promo-breakdance-logo-wrapper'>
            <a href='https://breakdance.com/' target='_blank'><img class='promo-breakdance-logo' width='120' src='<?php echo WP_ALL_IMPORT_ROOT_URL . '/acc/breakdance-logo-black.png'; ?>' /></a>
        </div>

        <div class='promo-heading'>The Ultimate Visual Website Builder for WordPress</div>
        <div class='promo-paragraphs'>
            <p>
                Brand new from the WP All Import team!
            </p> 
            
            <p>
                We've been helping agencies and freelancers build better websites for the last 15 years.
            </p>
            <p>
                After using everything from Visual Composer to Elementor to Oxygen, we decided to create Breakdance - the one visual website builder that can rule them all.
            </p>
        </div>

        <a class='promo-button-learn-more' target='_blank' href='https://breakdance.com/'>Learn More & Try For Free<span>(new tab)</span></a>
        
        <div class='promo-testimonials'>
            <p>
                "With the superb toolset this will be a <span>serious go-to for developers and agencies</span>."<br />
                <b>Kevin Nicholson</b>
            </p>
            <p>
                "I love the idea that <span>Breakdance replaces so many plugins</span>, avoiding Frankenstein WordPress installations."<br />
                <b>Max Ziebell</b>
            </p>
            <p>
                "It's a great balance between the <span>simplicity of Elementor</span> for non-developers with the <span>power features of Oxygen</span>."<br />
                <b>Nick Flowers</b>
            </p>
        </div>

    </div>
    <?php
}
