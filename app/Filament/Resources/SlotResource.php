<?php

// app/Filament/Resources/SlotResource.php
namespace App\Filament\Resources;

// app/Filament/Resources/SlotResource.php
use Livewire\Livewire;

use App\Filament\Resources\SlotResource\Pages;
use App\Filament\Resources\SlotResource\RelationManagers;

use Filament\Forms\Components\Actions\Action;

use Filament\Notifications\Notification;

use App\Models\Slot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlotResource extends Resource
{
    protected static ?string $model = Slot::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Créneaux Horaires';
    protected static ?string $modelLabel = 'Créneau';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->label('Date'),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->label('Heure de début'),
                Forms\Components\TimePicker::make('end_time')
                    ->required()
                    ->label('Heure de fin')
                    ->rules(['after:start_time']),
                Forms\Components\Select::make('duration')
                    ->label('Durée des créneaux')
                    ->options([
                        30 => '30 minutes',
                        60 => '1 heure',
                        120 => '2 heures',
                    ])
                    ->default(60)
                    ->required(),
                Forms\Components\TimePicker::make('pause_start')
                    ->label('Début de pause')
                    ->nullable(),
                Forms\Components\TimePicker::make('pause_end')
                    ->label('Fin de pause')
                    ->nullable()
                    ->rules(['after:pause_start']),
                Forms\Components\Actions::make([
                    Action::make('generateSlots')
                        ->label('Générer les créneaux horaires')
                        ->action(function ($state) {
                            $date = $state['date'];
                            $start = Carbon::parse($state['start_time']);
                            $end = Carbon::parse($state['end_time']);
                            $duration = $state['duration'];
                            $pauseStart = $state['pause_start'] ? Carbon::parse($state['pause_start']) : null;
                            $pauseEnd = $state['pause_end'] ? Carbon::parse($state['pause_end']) : null;
    
                            // Vérifier les chevauchements
                            $overlappingSlots = Slot::where('date', $date)
                                ->where(function ($query) use ($start, $end) {
                                    $query->whereBetween('start_time', [$start, $end])
                                        ->orWhereBetween('end_time', [$start, $end]);
                                })
                                ->exists();
    
                            if ($overlappingSlots) {
                                Notification::make()
                                    ->title('Erreur')
                                    ->body('La plage horaire chevauche un créneau existant.')
                                    ->danger()
                                    ->send();
                                return;
                            }
    
                            // Générer les créneaux
                            $current = $start->copy();
                            while ($current->addMinutes($duration) <= $end) {
                                $slotStart = $current->copy()->subMinutes($duration);
                                $slotEnd = $current;
    
                                // Ignorer la pause
                                if ($pauseStart && $pauseEnd && $slotStart->lt($pauseEnd) && $slotEnd->gt($pauseStart)) {
                                    continue;
                                }
    
                                Slot::create([
                                    'date' => $date,
                                    'start_time' => $slotStart,
                                    'end_time' => $slotEnd,
                                    'max_reservations' => 50 // Valeur par défaut
                                ]);
                            }
    
                            Notification::make()
                                ->title('Succès')
                                ->body('Créneaux générés avec succès !')
                                ->success()
                                ->send();
                        })
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->label('Date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->time('H:i')
                    ->label('Début'),
                Tables\Columns\TextColumn::make('end_time')
                    ->time('H:i')
                    ->label('Fin'),
                Tables\Columns\TextColumn::make('reservations_count')
                    ->label('Réservations')
                    ->counts('reservations'),
            ])
            ->filters([
                // Filtres optionnels
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlots::route('/'),
            'create' => Pages\CreateSlot::route('/create'),
            'edit' => Pages\EditSlot::route('/{record}/edit'),
        ];
    }
}