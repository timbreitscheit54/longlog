<?php

use common\helpers\enum\ApiVersion;

return [
    'adminEmail' => 'admin@example.com',
    // All supported API versions
    'supportedApiVersions' => ApiVersion::getKeys(),
    'latestApiVersion' => ApiVersion::LATEST,
];
