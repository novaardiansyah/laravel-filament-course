<?php

namespace App\Filament\Resources\CountryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
  protected static string $relationship = 'employees';

  public function form(Form $form): Form
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
              ->required(),
            Select::make('state_id')
              ->relationship(name: 'state', titleAttribute: 'name')
              ->searchable()
              // ->preload()
              ->required(),
            Select::make('city_id')
              ->relationship(name: 'city', titleAttribute: 'name')
              ->searchable()
              // ->preload()
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

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('first_name')
      ->columns([
        Tables\Columns\TextColumn::make('first_name'),
        Tables\Columns\TextColumn::make('last_name'),
        Tables\Columns\TextColumn::make('date_of_birth')
          ->date(),
        Tables\Columns\TextColumn::make('date_of_hired')
          ->date(),
      ])
      ->filters([
        //
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
        Tables\Actions\DeleteAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }
}
