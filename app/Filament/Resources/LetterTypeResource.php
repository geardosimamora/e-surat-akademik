<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LetterTypeResource\Pages;
use App\Models\LetterType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LetterTypeResource extends Resource
{
    protected static ?string $model = LetterType::class;

    // Ganti icon di menu navigasi sebelah kiri
    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    // Kelompokkan menu
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Jenis Surat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jenis Surat')
                    ->description('Atur kode dan template untuk jenis surat baru.')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Surat')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Contoh: SK-AKTIF'),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Surat')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Surat Keterangan Aktif Kuliah'),
                            
                        Forms\Components\TextInput::make('template_view')
                            ->label('Path Template (Blade)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: letters.active_student')
                            ->helperText('Pastikan file blade.php tersedia di resources/views/letters/'),
                            
                        Forms\Components\Toggle::make('requires_approval')
                            ->label('Butuh Persetujuan Kaprodi?')
                            ->default(true)
                            ->helperText('Jika aktif, surat harus di-approve sebelum bisa di-generate PDF.'),
                    ])->columns(2), // Membuat layout 2 kolom agar rapi
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Surat')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('template_view')
                    ->label('Template')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('requires_approval')
                    ->label('Butuh Approval')
                    ->boolean(),
            ])
            ->filters([
                // Nanti kita bisa tambah filter di sini
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLetterTypes::route('/'),
            'create' => Pages\CreateLetterType::route('/create'),
            'edit' => Pages\EditLetterType::route('/{record}/edit'),
        ];
    }
}