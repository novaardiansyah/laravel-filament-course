<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use Filament\Infolists\Components\Section AS InfolistSection;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
  protected static ?string $model = Employee::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';
  protected static ?string $navigationGroup = 'Employee Manangement';

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Section::make('Address')
          ->description('Please enter the complete address for the employee.')
          ->schema([
            Forms\Components\Select::make('country_id')
              ->relationship(name: 'country', titleAttribute: 'name')
              ->searchable()
              ->preload()
              ->required(),
            Forms\Components\Select::make('state_id')
              ->relationship(name: 'state', titleAttribute: 'name')
              ->searchable()
              // ->preload()
              ->required(),
            Forms\Components\Select::make('city_id')
              ->relationship(name: 'city', titleAttribute: 'name')
              ->searchable()
              // ->preload()
              ->required(),
            Forms\Components\Select::make('department_id')
              ->relationship(name: 'department', titleAttribute: 'name')
              ->searchable()
              ->preload()
              ->required(),
          ])->columns(2),
        Forms\Components\Section::make('User Name')
          ->description('Put the user name details in.')
          ->schema([
            Forms\Components\TextInput::make('first_name')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('last_name')
              ->required()
              ->maxLength(255),
            Forms\Components\TextInput::make('middle_name')
              ->required()
              ->maxLength(255),
          ])->columns(3),
        Forms\Components\Section::make('User Address')
            ->schema([
              Forms\Components\TextInput::make('address')
                ->required()
                ->maxLength(255),
              Forms\Components\TextInput::make('zip_code')
                ->required()
                ->maxLength(255),
            ])->columns(2),
        Forms\Components\Section::make('Dates')
          ->schema([
            Forms\Components\DatePicker::make('date_of_birth')
              ->displayFormat('Y/m/d')
              ->native(false)
              ->required(),
            Forms\Components\DatePicker::make('date_of_hired')
              ->displayFormat('Y/m/d')
              ->native(false)
              ->required(),
          ])->columns(2)
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
        //
      ])
      ->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\EditAction::make(),
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
