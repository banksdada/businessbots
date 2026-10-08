<?php

// config/portal.php
// Client advice portal: clients submit a business problem, a PyRunner script
// drafts advice with AI, and the owner reviews it before the client sees it.

return [

    // Who gets "draft ready" and "request failed" emails.
    'owner_email' => env('PORTAL_OWNER_EMAIL', env('MAIL_FROM_ADDRESS')),

    // AutomationJob type the PyRunner script claims.
    'job_type' => 'client_advice',

    // A job still "processing" after this many minutes is marked failed, so a
    // crashed script never leaves a client's request waiting forever.
    'stuck_after_minutes' => (int) env('PORTAL_STUCK_AFTER_MINUTES', 30),

];
