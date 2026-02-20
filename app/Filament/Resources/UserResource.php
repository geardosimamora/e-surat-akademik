<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder; // <-- Tambahkan ini

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Pengguna';
    
    protected static ?string $pluralModelLabel = 'Pengguna';
    
    protected static ?string $modelLabel = 'Pengguna';
    
    protected static ?string $navigationGroup = 'Pengaturan';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pribadi')
                    ->description('Data diri pengguna')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap')
                            ->autofocus(),
                            
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM (Nomor Induk Mahasiswa)')
                            ->maxLength(255)
                            ->placeholder('Contoh: 20210001')
                            ->unique(ignoreRecord: true)
                            ->helperText('NIM hanya diperlukan untuk role Mahasiswa'),
                            
                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('contoh@email.com')
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\Select::make('role')
                            ->label('Hak Akses')
                            ->options([
                                'admin' => 'Admin',
                                'student' => 'Mahasiswa',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Pilih role yang sesuai untuk pengguna'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Keamanan Akun')
                    ->description('Password untuk login pengguna')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (Page $livewire) => $livewire instanceof CreateRecord)
                            ->maxLength(255)
                            ->placeholder(fn (Page $livewire) => $livewire instanceof CreateRecord ? 'Masukkan password' : 'Kosongkan jika tidak ingin mengubah')
                            ->helperText(fn (Page $livewire) => $livewire instanceof CreateRecord 
                                ? 'Password minimal 8 karakter' 
                                : 'Kosongkan jika tidak ingin mengubah password'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (User $record): string => $record->email ?? ''),
                    
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('-'),
                    
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Email berhasil disalin')
                    ->copyMessageDuration(1500),
                    
                Tables\Columns\TextColumn::make('role')
                    ->label('Hak Akses')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'student' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'student' => 'Mahasiswa',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'admin' => 'heroicon-o-shield-check',
                        'student' => 'heroicon-o-academic-cap',
                        default => 'heroicon-o-user',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Filter Hak Akses')
                    ->options([
                        'admin' => 'Admin',
                        'student' => 'Mahasiswa',
                    ])
                    ->multiple()
                    ->placeholder('Semua Role'),
                    
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->placeholder('Semua Pengguna')
                    ->trueLabel('Email Terverifikasi')
                    ->falseLabel('Email Belum Terverifikasi')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat Detail')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('Detail Pengguna')
                        ->modalWidth('lg')
                        ->slideOver()
                        ->mutateRecordDataUsing(function (array $data): array {
                            return $data;
                        }),
                        
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->modalHeading('Edit Pengguna')
                        ->modalWidth('lg')
                        ->slideOver(),
                        
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->icon('heroicon-o-trash')
                        ->modalHeading('Hapus Pengguna')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->successNotificationTitle('Pengguna berhasil dihapus'),
                ])
                ->label('Aksi')
                ->icon('heroicon-o-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Pengguna Terpilih')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pengguna yang dipilih? Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pengguna terpilih berhasil dihapus'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Pengguna')
                    ->icon('heroicon-o-plus')
                    ->modalHeading('Tambah Pengguna Baru')
                    ->modalWidth('lg'),
            ])
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Silakan tambah pengguna baru untuk memulai.')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('60s');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'nim'];
    }
    
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Nama' => $record->name,
            'Email' => $record->email,
            'NIM' => $record->nim ?? '-',
            'Role' => $record->role === 'admin' ? 'Admin' : 'Mahasiswa',
        ];
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'Pengaturan';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 1;
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}