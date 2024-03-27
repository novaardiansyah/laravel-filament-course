<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\City;
use Filament\Infolists\Components\Section AS InfolistSection;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
  protected static ?string $model = Employee::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationGroup = 'Employee Manangement';
  protected static ?string $recordTitleAttribute = 'first_name';

  public static function getGlobalSearchResultTitle(Model $record): string
  {
    return $record->first_name . ' ' . $record->last_name;
  }

  public static function getGloballySearchableAttributes(): array
  {
    return [ 'first_name', 'last_name' ];
  }

  public static function getGlobalSearchResultDetails(Model $record): array
  {
    return [ 
      'Country' => $record->country->name,
    ];
  }

  public static function getGlobalSearchEloquentQuery(): Builder
  {
    return parent::getGlobalSearchEloquentQuery()->with(['country']);
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Section::make('Address')
          ->description('Please enter the complete address for the employee.')
          ->schema([
            Select::make('country_id')
              ->relationship(name: 'country', titleAttribute: 'name')
              ->searchable()
              ->preload()
              ->live()
              ->required()
              ->afterStateUpdated(function (Set $set) {
                $set('state_id', null);
                $set('city_id', null);
              }),
            Select::make('state_id')
              ->label('State')
              ->options(fn(Get $get): Collection => State::where('country_id', $get('country_id'))->get()->pluck('name', 'id'))
              ->searchable()
              ->required()
              ->live()
              ->afterStateUpdated(function (Set $set) {
                $set('city_id', null);
              }),
            Select::make('city_id')
              ->label('City')
              ->options(fn(Get $get): Collection => City::where('state_id', $get('state_id'))->get()->pluck('name', 'id'))
              ->searchable()
              ->required(),
            Select::make('department_id')
              ->relationship(name: 'department', titleAttribute: 'name')
              ->searchable()
              ->preload()
              ->required(),
          ])
          ->columns(2),

        Section::make('User Name')
          ->description('Put the user name details in.')
          ->schema([
            TextInput::make('first_name')
              ->required()
              ->maxLength(255),
            TextInput::make('last_name')
              ->required()
              ->maxLength(255),
            TextInput::make('middle_name')
              ->required()
              ->maxLength(255),
          ])
          ->columns(3),

        Section::make('User Address')
          ->description('Put the user address details in.')
          ->schema([
            TextInput::make('address')
              ->required()
              ->maxLength(255),
            TextInput::make('zip_code')
              ->required()
              ->maxLength(255),
          ])
          ->columns(2),

        Section::make('Dates')
          ->description('Please enter the dates for the employee.')
          ->schema([
            DatePicker::make('date_of_birth')
              ->displayFormat('Y/m/d')
              ->native(false)
              ->required(),
            DatePicker::make('date_of_hired')
              ->displayFormat('Y/m/d')
              ->native(false)
              ->required(),
          ])
          ->columns(2)
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('first_name')
          ->searchable(),
        TextColumn::make('last_name')
          ->searchable(),
        TextColumn::make('middle_name')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('department.name')
          ->searchable()
          ->sortable(),
        TextColumn::make('zip_code')
          ->searchable(),
        TextColumn::make('city.name')
          ->searchable()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('state.name')
          ->searchable()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('country.name')
          ->searchable()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('address')
          ->searchable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('date_of_birth')
          ->date()
          ->sortable(),
        TextColumn::make('date_of_hired')
          ->date()
          ->sortable(),
        TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
      ])
      ->filters([
        SelectFilter::make('department')
          ->searchable()
          ->preload()
          ->label('Filter by Department')
          ->indicator('Department')
          ->relationship(name: 'department', titleAttribute: 'name'),
        Filter::make('created_at')
          ->form([
            DatePicker::make('created_from')
              ->placeholder(Date('Y-m-d', strtotime('-1 year')))
              ->native(false),
            DatePicker::make('created_until')
              ->placeholder(Date('Y-m-d', strtotime('+1 day')))
              ->native(false)
          ])
          ->query(function (Builder $query, array $data): Builder {
            return $query
              ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date)
              )
              ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date)
              );
          })
          ->indicateUsing(function (array $data): array {
            $indicators = [];
            if ($data['created_from'] ?? null) {
              $indicators['created_form'] = 'Created from ' . Carbon::parse($data['created_from'])->format('F d, Y');
            }
            if ($data['created_until'] ?? null) {
              $indicators['created_until'] = 'Created until ' . Carbon::parse($data['created_until'])->format('F d, Y');
            }
            return $indicators;
          })
          ->columnSpan(2)
          ->columns(2)
        ], layout: FiltersLayout::Modal)
      ->filtersFormColumns(3)
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make()
          // ->successNotificationTitle('Employee deleted successfully')
          ->successNotification(
            Notification::make()
            ->success()
            ->title('Empleyee deleted.')
            ->body('The employee was deleted successfully')
          )
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        InfolistSection::make('Detail Employee')
          ->description('Complete employee details')
          ->schema([
            TextEntry::make('first_name'),
            TextEntry::make('middle_name'),
            TextEntry::make('last_name'),
            TextEntry::make('department.name')
              ->label('Department'),
            TextEntry::make('date_of_birth')
              ->label('Date of Birth'),
            TextEntry::make('date_of_hired')
              ->label('Date of Hired'),
            TextEntry::make('created_at')
              ->label('Entry At'),
            TextEntry::make('updated_at')
              ->label('Last Updated'),
          ])
          ->columns(3),

        InfolistSection::make('Detail Address')
          ->description('Complete address details of the employee concerned.')
          ->schema([
            TextEntry::make('city.name')
              ->label('City Name'),
            TextEntry::make('state.name')
              ->label('State Name'),
            TextEntry::make('country.name')
              ->label('Country Name'),
            TextEntry::make('address'),
            TextEntry::make('zip_code')
          ])
          ->columns(3)
      ]);
  }
  
  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index'  => Pages\ListEmployees::route('/'),
      'create' => Pages\CreateEmployee::route('/create'),
      'edit'   => Pages\EditEmployee::route('/{record}/edit'),
    ];
  }
}
