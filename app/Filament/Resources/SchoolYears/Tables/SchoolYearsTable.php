<?php

namespace App\Filament\Resources\SchoolYears\Tables;

use App\Models\SchoolYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->rowIndex(),
                TextColumn::make('year_name')
                    ->label('Tahun Ajaran')
                    ->sortable()
                    ->weight('bold')
                    ->size('lg')
                    ->searchable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->beforeStateUpdated(function ($record, $state) {
                        // Jika akan diaktifkan dan sudah ada yang aktif
                        if ($state) {
                            $activeYear = SchoolYear::where('is_active', true)
                                ->where('id', '!=', $record->id)
                                ->first();

                            if ($activeYear) {
                                Notification::make()
                                    ->warning()
                                    ->title('Perubahan Tahun Aktif')
                                    ->body("Tahun ajaran {$activeYear->year_name} saat ini aktif. Mengaktifkan {$record->year_name} akan menonaktifkan tahun ajaran sebelumnya.")
                                    ->persistent()
                                    ->send();
                            }
                        }
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            Notification::make()
                                ->success()
                                ->title('Tahun Ajaran Diaktifkan')
                                ->body("Tahun ajaran {$record->year_name} sekarang aktif.")
                                ->send();
                        }
                    }),
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
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Tidak Aktif'
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
