<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Filament\Resources\BusinessResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditBusiness extends EditRecord
{
    protected static string $resource = BusinessResource::class;

    /**
     * No {record} route param (see BusinessResource::getPages) — this page
     * always edits the currently selected tenant, since a business only ever
     * manages its own record, never anyone else's.
     */
    public function getRecord(): \Illuminate\Database\Eloquent\Model
    {
        return Filament::getTenant();
    }

    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        return Filament::getTenant();
    }
}
