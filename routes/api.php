<?php

use App\Http\Controllers\Api\PyRunnerJobController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PyRunner job queue — called by PyRunner scripts, never by browsers.
| Every endpoint checks the PYRUNNER_WORKER_TOKEN bearer token itself.
|--------------------------------------------------------------------------
*/
Route::prefix('pyrunner/jobs')->group(function () {
    Route::post('/claim', [PyRunnerJobController::class, 'claim'])->name('pyrunner.jobs.claim');
    Route::post('/{uuid}/complete', [PyRunnerJobController::class, 'complete'])->name('pyrunner.jobs.complete');
    Route::post('/{uuid}/fail', [PyRunnerJobController::class, 'fail'])->name('pyrunner.jobs.fail');
});
