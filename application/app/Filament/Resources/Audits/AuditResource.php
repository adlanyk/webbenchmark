<?php

namespace App\Filament\Resources\Audits;

use App\Filament\Resources\Audits\Pages\CreateAudit;
use App\Filament\Resources\Audits\Pages\EditAudit;
use App\Filament\Resources\Audits\Pages\ListAudits;
use App\Filament\Resources\Audits\Schemas\AuditForm;
use App\Filament\Resources\Audits\Tables\AuditsTable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Audit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Inspeksi 5R (Audit)';

    protected static ?string $modelLabel = 'Inspeksi 5R';

    public static function form(Schema $schema): Schema
    {
        return AuditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    $user = auth()->user();

    // Jika user memiliki role verifikator atau super_admin, tampilkan SEMUA lokasi
    if ($user && $user->hasAnyRole(['verifikator', 'super_admin'])) {
        return $query;
    }

    // Jika bukan verifikator dan terikat ke department tertentu, batasi hanya milik department tersebut
    if ($user && $user->department_id) {
        $query->whereHas('location', function (Builder $q) use ($user) {
            $q->where('department_id', $user->department_id);
        });
    }

    return $query;
}

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'create' => CreateAudit::route('/create'),
            'edit' => EditAudit::route('/{record}/edit'),
        ];
    }
}