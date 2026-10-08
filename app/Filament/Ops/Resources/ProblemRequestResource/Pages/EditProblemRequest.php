<?php

namespace App\Filament\Ops\Resources\ProblemRequestResource\Pages;

use App\Filament\Ops\Resources\ProblemRequestResource;
use App\Models\ProblemRequest;
use App\Services\Advice\AdviceRequestService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProblemRequest extends EditRecord
{
    protected static string $resource = ProblemRequestResource::class;

    protected static ?string $title = 'Review request';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve and send')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn () => $this->record->status === ProblemRequest::STATUS_PENDING_REVIEW)
                ->requiresConfirmation()
                ->modalDescription('The client will be emailed and can read this report straight away.')
                ->action(function (AdviceRequestService $advice) {
                    $data = $this->form->getState();

                    $advice->approve($this->record, $data['draft_report']);

                    Notification::make()->title('Report sent to the client')->success()->send();

                    $this->redirect(ProblemRequestResource::getUrl('index'));
                }),

            Action::make('retry')
                ->label('Retry AI draft')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn () => $this->record->status === ProblemRequest::STATUS_FAILED)
                ->requiresConfirmation()
                ->action(function (AdviceRequestService $advice) {
                    $advice->retry($this->record);

                    Notification::make()->title('Sent back to the AI')->success()->send();

                    $this->refreshFormData(['draft_report']);
                }),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()->label('Save draft');
    }

    /** Only the draft is editable here; status changes go through the actions above. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return ['draft_report' => $data['draft_report'] ?? $this->record->draft_report];
    }
}
