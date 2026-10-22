<?php

namespace App\Filament\Resources\Bimrevo\Contacts\Pages;

use App\Filament\Resources\Bimrevo\Contacts\ContactResource;
use App\Models\Bimrevo\Contact;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    // Opening a message marks it as read.
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->getRecord()->markAsRead();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('markUnread')
                ->label('Mark as unread')
                ->icon('heroicon-o-envelope')
                ->color('gray')
                ->action(function (Contact $record) {
                    $record->markAsUnread();

                    $this->redirect(ContactResource::getUrl('index'));
                }),
        ];
    }
}
