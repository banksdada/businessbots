<?php

// config/features.php
// Switches for whole product areas. Turning one off hides its pages, menu
// links and scheduled jobs, but keeps its code and database tables so it can
// come back later with a .env change.

return [

    // WhatsApp replies, channel connections, scheduled social posting and the
    // leads page. Paused while the client advice portal is the focus.
    'social' => (bool) env('FEATURE_SOCIAL', false),

];
