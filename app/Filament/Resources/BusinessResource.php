<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Models\Business;
use Filament\Forms\Form;
use Filament\Forms\Components\{TextInput, Textarea, Select, Toggle, Section, Repeater, Placeholder};
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, IconColumn, BadgeColumn};
use Filament\Tables\Filters\TernaryFilter;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Business';

    // BusinessResource IS the tenant model, not a child of it — Filament's
    // automatic ->where('business_id', ...) scoping (used on LeadResource etc.)
    // doesn't apply here. Instead, restrict every query to the single business
    // currently selected in the tenant switcher.
    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereKey(\Filament\Facades\Filament::getTenant()?->id);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Business details')
                ->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('location')->required()->maxLength(255),
                    Textarea::make('description')->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Onboarding complete')
                        ->helperText('Flips automatically when the onboarding wizard finishes — edit manually only for support cases.'),
                ]),

            Section::make('Vertical')
                ->relationship('businessVertical')
                ->schema([
                    Select::make('vertical_type')
                        ->label('Industry')
                        ->options([
                            'care' => 'Care & Support Services',
                            'cleaning' => 'Cleaning & Decluttering',
                            'real_estate' => 'Property & Real Estate',
                            'fitness' => 'Fitness & Wellness',
                            'trades' => 'Trades & Services',
                            'beauty' => 'Beauty & Salon',
                            'legal' => 'Legal Services',
                            'automotive' => 'Automotive & Mechanics',
                        ])
                        ->required(),
                ])
                ->columnSpanFull(),

            Section::make('Connected channels')
                ->schema([
                    Placeholder::make('channels_note')
                        ->label('')
                        ->content('Channel connections are managed by the business owner via OAuth (Settings → Connect). This admin view is read-only — see the Channels tab below the form.')
                        ->columnSpanFull(),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('location')->searchable(),
                TextColumn::make('businessVertical.vertical_type')->label('Vertical')->badge(),
                IconColumn::make('is_active')->label('Onboarded')->boolean(),
                TextColumn::make('leads_count')->label('Leads')->counts('leads'),
                // Per-row query is fine at admin-panel scale; revisit with withCount() if this list grows past ~500 rows.
                TextColumn::make('connected_channels_count')
                    ->label('Channels connected')
                    ->getStateUsing(fn (Business $record) => $record->channelSettings()->where('is_connected', true)->count()),
                TextColumn::make('created_at')->dateTime('j M Y')->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Onboarded'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditBusiness::route('/'),
        ];
    }
}
