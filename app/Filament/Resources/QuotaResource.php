<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotaResource\Pages;
use App\Models\Quota;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class QuotaResource extends Resource
{
    protected static ?string $model = Quota::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Association')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('grand')
                    ->label('Grand')
                    ->numeric()
                    ->default(0), // Valeur par défaut 0
                Forms\Components\TextInput::make('moyen')
                    ->label('Moyen')
                    ->numeric()
                    ->default(0), // Valeur par défaut 0
                Forms\Components\TextInput::make('petit')
                    ->label('Petit')
                    ->numeric()
                    ->default(0), // Valeur par défaut 0
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Association'),
                Tables\Columns\TextColumn::make('grand')
                    ->label('Grand')
                    ->formatStateUsing(fn ($state) => $state ?? 0), // Affiche 0 si NULL
                Tables\Columns\TextColumn::make('moyen')
                    ->label('Moyen')
                    ->formatStateUsing(fn ($state) => $state ?? 0), // Affiche 0 si NULL
                Tables\Columns\TextColumn::make('petit')
                    ->label('Petit')
                    ->formatStateUsing(fn ($state) => $state ?? 0), // Affiche 0 si NULL
            ])
            ->filters([
                // Ajouter des filtres si nécessaire
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotas::route('/'),
            'create' => Pages\CreateQuota::route('/create'),
            'edit' => Pages\EditQuota::route('/{record}/edit'),
        ];
    }
}