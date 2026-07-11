<?php

namespace App\Filament\Resources\Submissions;

use App\Filament\Resources\Submissions\Pages\CreateSubmission;
use App\Filament\Resources\Submissions\Pages\EditSubmission;
use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Filament\Resources\Submissions\Schemas\SubmissionForm;
use App\Filament\Resources\Submissions\Schemas\SubmissionInfolist;
use App\Filament\Resources\Submissions\Tables\SubmissionsTable;
use App\Filament\Resources\Submissions\Pages;
use App\Models\Submission;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Pengajuan';

    protected static ?string $pluralModelLabel = 'Pengajuan';

    protected static ?string $modelLabel = 'Pengajuan';

    protected static ?int $navigationSort = 1;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute = 'submission_number';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return SubmissionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SubmissionInfolist::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return SubmissionsTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [
            'index'  => ListSubmissions::route('/'),
            'create' => CreateSubmission::route('/create'),
            'view' => Pages\ViewSubmission::route('/{record}'),
            'edit'   => EditSubmission::route('/{record}/edit'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) Submission::query()
            ->whereNotIn('status', [
                Submission::STATUS_PAID,
                Submission::STATUS_REJECTED,
            ])
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Submission::query()
            ->whereNotIn('status', [
                Submission::STATUS_PAID,
                Submission::STATUS_REJECTED,
            ])
            ->count();

        if ($count > 10) {
            return 'danger';
        }

        if ($count > 0) {
            return 'warning';
        }

        return 'success';
    }

    /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    */

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole([
            'Admin',
            'Staff',
        ]);
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function canRestoreAny(): bool
    {
        return false;
    }
}