<li class="glide__slide">
    <div class="card my-3">
        <div class="row no-gutters">
            <div class="col-md-3">
                <img src="<?php echo esc_url( $slide_image[0] ); ?>" width="<?php echo esc_attr( $slide_image[1] ); ?>" height="<?php echo esc_attr( $slide_image[2] ); ?>" class="card-img" alt="Slider image" />
            </div>
            <div class="col-md-9 d-flex align-items-center">
                <div class="card-body">
                    <p class="card-title"><?php echo $title ?></p>
                    <p class="card-text"><?php echo $description ?></p>
                </div>
            </div>
        </div>
    </div>
</li>