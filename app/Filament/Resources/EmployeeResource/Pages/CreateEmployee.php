<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
  protected static string $resource = EmployeeResource::class;

  // protected function getCreatedNotificationTitle(): ?string
  // {
  //   return 'Empleyee created successfully';
  // }

  protected function getCreatedNotification(): ?Notification
  {
    return Notification::make()
      ->success()
      ->title('Empleyee created.')
      ->body('The employee was created successfully');
  }
}
