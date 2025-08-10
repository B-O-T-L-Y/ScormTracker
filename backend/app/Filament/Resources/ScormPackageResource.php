<?php

namespace App\Filament\Resources;

use App\Domain\Scorm\Models\ScormPackage;
use App\Filament\Resources\ScormPackageResource\Pages;
use App\Filament\Resources\ScormPackageResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ScormPackageResource extends Resource
{
    protected static ?string $model = ScormPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('file')
                    ->required()
                    ->label('SCORM ZIP')
                    ->disk(config('scorm.scorm_disk'))
                    ->directory('uploads')
                    ->acceptedFileTypes(['application/zip'])
                    ->multiple(false)
                    ->dehydrated(false)
                    ->required(fn($record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('path')->label('Extracted path'),
                TextColumn::make('created_at')->dateTime(),

                TextColumn::make('user_viewed_count')
                    ->label('Views')
                    ->sortable()
                    ->getStateUsing(fn(ScormPackage $record) => $record->stats->firstWhere('user_id', Auth::id())?->views_count ?? 0),

                TextColumn::make('user_last_viewed_at')
                    ->label('Last viewed')
                    ->getStateUsing(fn(ScormPackage $record) => optional($record->stats->firstWhere('user_id', Auth::id()))?->last_viewed_at?->diffForHumans())
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScormPackages::route('/'),
            'create' => Pages\CreateScormPackage::route('/create'),
            'edit' => Pages\EditScormPackage::route('/{record}/edit'),
        ];
    }
}
