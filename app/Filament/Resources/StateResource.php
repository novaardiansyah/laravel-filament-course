<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section AS InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StateResource extends Resource
{
  protected static ?string $model = State::class;

  protected static ?string $navigationIcon = 'heroicon-o-building-library';
  protected static ?string $navigationGroup = 'System Manangement';
  protected static ?string $modelLabel = 'States';
  protected static ?string $navigationLabel = 'State';
  protected static ?int $navigationSort = 2;

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('country_id')
          ->relationship(name: 'country', titleAttribute: 'name')
          ->searchable()
          ->preload()
          ->required(),
        Forms\Components\TextInput::make('name') 
          ->required()
          ->maxLength(255),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        Tables\Columns\TextColumn::make('name')
          ->label('State Name')
          ->searchable()
          ->sortable(),
        Tables\Columns\TextColumn::make('country.name')
          ->numeric()
          ->sortable()
          ->searchable(),
        Tables\Columns\TextColumn::make('created_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: true),
        Tables\Columns\TextColumn::make('updated_at')
          ->dateTime()
          ->sortable()
          ->toggleable(isToggledHiddenByDefault: false),
      ])
      ->defaultSort('name', 'asc')
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
        InfolistSection::make('State Info')
          ->schema([
            TextEntry::make('name')
              ->label('State Name'),
            TextEntry::make('country.name')
              ->label('Country Name'),
            TextEntry::make('updated_at')
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
      'index' => Pages\ListStates::route('/'),
      'create' => Pages\CreateState::route('/create'),
      'edit' => Pages\EditState::route('/{record}/edit'),
    ];
  }
}
