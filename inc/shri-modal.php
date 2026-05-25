<?php

?>

<template id="modalContactInfo">

</template>

<div class="shri--site-modal" id="site-modal">
    <div class="modal__overlay"></div>
    <div class="container">
        <div class="modalContainer">
            <div class="modalBackground__container"></div>
            <div class="grid">
                <button class="close_modal"><span></span><span></span></button>
                <div class="modalContent">
                    <h2 class="m0">Contact us</h2>
                    <div class="contactUs__details grid">
                        <div class="grid">
                            <div class="title">Contact Details</div>
                            <div class="content">
                                <ul class="unstyledList grid">
                                    <?php foreach ( contact_phones() as $index => $phone ) : ?>
                                        <li>
                                            <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>"
                                            aria-label="<?php echo $index === 0
                                                ? esc_attr( 'Call ' . $phone )
                                                : esc_attr( 'Call alternate number ' . $phone ); ?>"
                                            >
                                                <?php echo esc_html( $phone ); ?>
                                            </a>

                                            <?php if ( ! $loop_last = ( $index === count( contact_phones() ) - 1 ) ) : ?>
                                                <br>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php foreach ( contact_emails() as $email ) : ?>
                                        <li>
                                            <a href="mailto:<?php echo esc_attr( $email ); ?>"
                                                aria-label="<?php echo esc_attr( 'Send email to ' . $email ); ?>"
                                            >
                                                <?php echo esc_html( $email ); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <a href="<?php echo contact_whatsapp_url(); ?>" target="_blank" class="modal_btn">Send WhatsApp</a>
                            </div>
                        </div>
                        <div class="grid">
                            <div class="title">Our Location</div>
                            <div class="content">
                                <address><?php echo contact_address(); ?></address>
                                <a href="<?php echo contact_maps_url(); ?>" target="_blank" class="modal_btn">Google Directions</a>
                            </div>
                        </div>
                        <div class="grid">
                            <div class="title">Social</div>
                            <div class="content">
                                    <?php
                                        wp_nav_menu([
                                            'theme_location' => 'social-menu',
                                            'container'      => false,
                                            'menu_class'     => 'unstyledList modal_social_menu',
                                            'menu_id'        => 'modal_social_menu',
                                        ]);
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modalFooter">
                    <div class="grid">
                        <div class="shri_text_sm">&copy; Shri Shanti Engineering</div>
                        <div class="shri_text_sm"><a href="">Privacy Policy</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>