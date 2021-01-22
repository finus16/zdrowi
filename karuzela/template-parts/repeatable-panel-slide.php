
<tr class="panel <?php echo ( $hidden_panel ? 'hidden' : '' ); ?>">

<td class="sort-handle">
    <i class="icon sss-fa sss-fa-arrows"></i>
</td>

<td class="image-container">
    <?php $this->create_justart_form_control( 'justart_slider_slide_image', $this->repeatable_fieldset_settings ); ?>
</td>

<td class="width-65 no-padding">
    
    <!-- Tabs -->
    <div class="otb-tabs-container">
        <ul class="tabs">
            <li><a data-tab="content" class="active"><?php esc_html_e( 'Content', 'super-simple-slider' ); ?></a></li>
            <li><a data-tab="seo">SEO</a></li>
        </ul>

        <!-- Content -->
        <div class="tab-content content active">
            <?php $this->create_justart_form_control( 'justart_slider_slide_name', $this->repeatable_fieldset_settings ); ?>
            <?php $this->create_justart_form_control( 'justart_slider_slide_title', $this->repeatable_fieldset_settings ); ?>
        </div>
        
        <!-- SEO -->
        <div class="tab-content seo">
            <?php $this->create_justart_form_control( 'super_simple_slider_slide_image_alt', $this->repeatable_fieldset_settings ); ?>
            <?php $this->create_justart_form_control( 'super_simple_slider_slide_image_title', $this->repeatable_fieldset_settings ); ?>
        </div>
        
    </div>
</td>

<td class="remove-repeatable-panel">
    <a href="#" class="icon" title="Delete this slide">
        <i class="sss-fa sss-fa-times"></i>
    </a>
</td>

</tr>