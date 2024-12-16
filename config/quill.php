<?php

// config/quill.php
return [
    /**
     * Specifies whether to use Quill's default image handling behavior.
     * Set to true to enable it, false to disable.
     * If disabled, ensure 'image' is NOT included in Quill's configuration
     * in resources/js/Components/QuillEditor.vue.
     */
    'use_image_handler' => true, // Set to false to disable the image handler in Quill
];
