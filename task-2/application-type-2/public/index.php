<?php

/**
 * This file is part of the bitrix24-php-sdk package.
 *
 * © Maksim Mesilov <mesilov.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the MIT-LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Throwable;

require_once dirname(__DIR__). '/vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://8957ea811b2dc3b847c9e7d80316b08a@o4508060222291968.ingest.de.sentry.io/4508060226420816' ,
    // Specify a fixed sample rate
    'traces_sample_rate' => 1.0,
    // Set a sampling rate for profiling - this is relative to traces_sample_rate
    'profiles_sample_rate' => 1.0,
]);

try {
    $incomingRequest = Request::createFromGlobals();
    Application::getLog()->debug(
        'index.init',
        ['request' => $incomingRequest->request->all(), 'query' => $incomingRequest->query->all()]
    );
} catch (Throwable $exception) {
    \Sentry\captureException($exception);
}
?>
    <pre>
    Application is worked, auth tokens from bitrix24:
    <?= print_r($_REQUEST, true) ?>
</pre>
<?php

//  try work with app

