<?php

namespace App\Filament\Ops\Resources;

use App\Filament\Ops\Resources\ProblemRequestResource\Pages;
use App\Models\ProblemRequest;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProblemRequestResource extends Resource
{
    protected static ?string $model = ProblemRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Advice requests';
    protected static ?string $modelLabel = 'advice request';

    public static function getNavigationBadge(): ?string
    {
        $count = ProblemRequest::where('status', ProblemRequest::STATUS_PENDING_REVIEW)->count();

        return $count ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        $answer = fn (string $label, callable $value) => Placeholder::make($label)
            ->content(fn (?ProblemRequest $record) => $record ? new HtmlString(nl2br(e($value($record) ?: '—'))) : '—');

        return $form->schema([
            Section::make('What the client told us')
                ->columns(3)
                ->schema([
                    $answer('Organisation', fn ($r) => $r->business->name . ' (' . \App\Livewire\Onboarding\VerticalStep::labelFor($r->business->verticalType()) . ')'),
                    $answer('Client', fn ($r) => $r->user->name . ' · ' . $r->user->email),
                    $answer('Status', fn ($r) => $r->statusLabel()),
                    $answer('Title', fn ($r) => $r->title)->columnSpanFull(),
                    $answer('Problem', fn ($r) => $r->description)->columnSpanFull(),
                    $answer('Already tried', fn ($r) => $r->already_tried)->columnSpanFull(),
                    $answer('Tools used now', fn ($r) => $r->current_tools),
                    $answer('Staff', fn ($r) => $r->staff_count),
                    $answer('Urgency / wanted', fn ($r) => (ProblemRequest::URGENCIES[$r->urgency] ?? $r->urgency) . ' · ' . (ProblemRequest::HELP_OPTIONS[$r->help_wanted] ?? $r->help_wanted)),
                ]),

            Section::make('Error')
                ->visible(fn (?ProblemRequest $record) => $record?->status === ProblemRequest::STATUS_FAILED)
                ->schema([
                    $answer('What went wrong', fn ($r) => $r->error_message),
                ]),

            Section::make('Report')
                ->description('Edit the AI draft, then press Approve and send. The client only sees the report after you approve it.')
                ->schema([
                    MarkdownEditor::make('draft_report')
                        ->label('Draft')
                        ->required()
                        ->disabled(fn (?ProblemRequest $record) => $record?->status === ProblemRequest::STATUS_PROCESSING)
                        ->helperText(fn (?ProblemRequest $record) => $record?->status === ProblemRequest::STATUS_PROCESSING
                            ? 'The AI is still writing this draft.'
                            : null),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->limit(60)->weight('semibold'),
                TextColumn::make('business.name')->label('Organisation')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state, ProblemRequest $record) => $record->statusLabel())
                    ->color(fn (string $state) => match ($state) {
                        ProblemRequest::STATUS_PENDING_REVIEW => 'warning',
                        ProblemRequest::STATUS_APPROVED => 'success',
                        ProblemRequest::STATUS_FAILED => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('urgency')
                    ->formatStateUsing(fn (string $state) => ProblemRequest::URGENCIES[$state] ?? $state),
                TextColumn::make('created_at')->label('Sent')->dateTime('j M Y, H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    ProblemRequest::STATUS_PENDING_REVIEW => 'Pending review',
                    ProblemRequest::STATUS_PROCESSING => 'AI drafting',
                    ProblemRequest::STATUS_FAILED => 'Failed',
                    ProblemRequest::STATUS_APPROVED => 'Approved',
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('business'))
            ->recordUrl(fn (ProblemRequest $record) => static::getUrl('edit', ['record' => $record]));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProblemRequests::route('/'),
            'edit' => Pages\EditProblemRequest::route('/{record}/edit'),
        ];
    }
}
