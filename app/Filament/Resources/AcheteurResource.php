<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AcheteurResource\Pages;


class AcheteurResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Tous les acheteurs';
    protected static ?string $modelLabel = 'Acheteur';
    protected static ?string $pluralModelLabel = 'Liste de tous les acheteurs';
    protected static ?string $navigationGroup = 'Acheteurs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom complet')
                    ->required(),
                    
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                    
                Forms\Components\TextInput::make('phone')
                    ->label('Téléphone')
                    ->tel()
                    ->nullable(),
                    
                Forms\Components\Hidden::make('role')
                    ->default('buyer'),
                    
                Forms\Components\Select::make('association_id')
                    ->label('Association')
                    ->relationship(
                        name: 'association',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('role', 'association')
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Compte actif')
                    ->default(true),
                    
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->dehydrated(fn(?string $state): bool => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('association.name')
                    ->label('Association')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('association')
                    ->relationship('association', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->where('role', 'buyer'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcheteurs::route('/'),
            'create' => Pages\CreateAcheteur::route('/create'),
            'edit' => Pages\EditAcheteur::route('/{record}/edit'),
        ];
    }
}