<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Business';

    /** Admins only see leads for the business they're scoped to — enforced globally, not per-query here. See AppServiceProvider tenant scoping note in SETUP-NOTES.md. */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->maxLength(255),
            TextInput::make('phone')->tel()->required(),
            Select::make('intent')->options([
                'inquiry' => 'Inquiry',
                'schedule' => 'Schedule',
                'complaint' => 'Complaint',
                'other' => 'Other',
            ]),
            Select::make('status')->options([
                'new' => 'New',
                'followup' => 'Needs follow-up',
                'closed' => 'Closed',
            ]),
            Textarea::make('message')->disabled()->columnSpanFull(),
            Toggle::make('opted_out')
                ->label('Opted out of messages')
                ->helperText('Set automatically when the lead opts out. Editable here only for manual correction.'),
            DateTimePicker::make('scheduled_visit_at')->label('Scheduled visit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->default('Unknown'),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('message')->limit(50)->searchable(),
                BadgeColumn::make('intent')->colors([
                    'info' => 'inquiry',
                    'success' => 'schedule',
                    'danger' => 'complaint',
                    'gray' => 'other',
                ]),
                IconColumn::make('opted_out')
                    ->label('Opted out')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('gray'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'new',
                    'warning' => 'followup',
                    'success' => 'closed',
                ]),
                TextColumn::make('created_at')->dateTime('j M, H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('intent')->options([
                    'inquiry' => 'Inquiry',
                    'schedule' => 'Schedule',
                    'complaint' => 'Complaint',
                ]),
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'followup' => 'Needs follow-up',
                    'closed' => 'Closed',
                ]),
                TernaryFilter::make('opted_out')->label('Opted out'),
                Filter::make('needs_human')
                    ->label('Needs human attention')
                    ->query(fn ($query) => $query->where('status', '!=', 'closed')),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('call')
                        ->icon('heroicon-o-phone')
                        ->url(fn (Lead $record) => "tel:{$record->phone}"),
                    Action::make('markClosed')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Lead $record) => $record->update(['status' => 'closed']))
                        ->visible(fn (Lead $record) => $record->status !== 'closed'),
                    Action::make('markOptedOut')
                        ->label('Mark opted out')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Lead $record) {
                            Lead::where('business_id', $record->business_id)
                                ->where('phone', $record->phone)
                                ->update(['opted_out' => true, 'opted_out_at' => now()]);
                        })
                        ->visible(fn (Lead $record) => ! $record->opted_out),
                ]),
            ])
            ->bulkActions([
                BulkAction::make('markClosed')
                    ->label('Mark as closed')
                    ->action(fn ($records) => $records->each->update(['status' => 'closed'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
