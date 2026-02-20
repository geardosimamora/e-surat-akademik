<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LetterResource\Pages;
use App\Models\Letter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
// BARIS PENTING DI BAWAH INI UNTUK MEMPERBAIKI ERROR BUILDER:
use Illuminate\Database\Eloquent\Builder;

class LetterResource extends Resource
{
    protected static ?string $model = Letter::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $modelLabel = 'Pengajuan Surat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECTION 1: INFORMASI PEMOHON (SNAPSHOT)
                Forms\Components\Section::make('Informasi Pemohon')
                    ->description('Data mahasiswa saat pengajuan surat dibuat.')
                    ->schema([
                        Forms\Components\Placeholder::make('nama_mahasiswa')
                            ->label('Nama Lengkap')
                            ->content(fn ($record) => $record?->user_snapshot['name'] ?? $record?->user->name ?? 'Tidak Terdeteksi'),
                            
                        Forms\Components\Placeholder::make('nim_mahasiswa')
                            ->label('NIM')
                            ->content(fn ($record) => $record?->user_snapshot['nim'] ?? $record?->user->nim ?? 'Tidak Terdeteksi'),
                    ])->columns(2),

                // SECTION 2: AREA KERJA ADMIN TU
                Forms\Components\Section::make('Pemrosesan Surat')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Pengajuan')
                            ->options([
                                'pending' => 'Menunggu Diproses',
                                'processing' => 'Sedang Diproses',
                                'approved' => 'Selesai & Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->required()
                            ->live() 
                            ->native(false),

                        Forms\Components\TextInput::make('letter_number')
                            ->label('Nomor Surat')
                            ->maxLength(255)
                            ->placeholder('Contoh: 001/UNIMAL/SI/II/2026')
                            ->visible(fn (Get $get) => $get('status') === 'approved')
                            ->required(fn (Get $get) => $get('status') === 'approved'),

                        Forms\Components\KeyValue::make('additional_data')
                            ->label('Data Tambahan (Alasan / Detail Instansi)')
                            ->disabled() // Admin hanya bisa membaca, tidak mengubah alasan mahasiswa
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('rejection_note')
                            ->label('Alasan Penolakan')
                            ->placeholder('Sebutkan alasan penolakan secara jelas...')
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('status') === 'rejected')
                            ->required(fn (Get $get) => $get('status') === 'rejected')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_snapshot.name')
                    ->label('Pemohon')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('user_snapshot->name', 'like', "%{$search}%");
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('letterType.name')
                    ->label('Jenis Surat')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('letter_number')
                    ->label('No. Surat')
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Proses'),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Letter $record): string => route('verification.show', ['token' => $record->id]))
                    ->openUrlInNewTab()
                    ->visible(fn (Letter $record): bool => $record->status === 'approved' && !empty($record->letter_number)),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLetters::route('/'),
            'edit' => Pages\EditLetter::route('/{record}/edit'),
        ];
    }
}
