<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Safety net for the transactional mail queue (order invoices are queued on
// the database connection by SendOrderInvoiceMail). Without a dedicated
// `php artisan queue:work` process those jobs sit in the `jobs` table forever
// and no order email is ever sent. Draining the queue every minute with
// --stop-when-empty works on dev machines and small hosts that have no
// supervisor; it is harmless next to a real worker because
// withoutOverlapping() keeps the two from running simultaneously.
// The scheduler itself must run: `php artisan schedule:work` locally, the
// usual cron `schedule:run` entry in production.
Schedule::command('queue:work', ['--stop-when-empty' => true])
    ->everyMinute()
    ->withoutOverlapping();
