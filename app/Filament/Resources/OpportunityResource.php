<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportunityResource\Pages;
use App\Filament\Resources\OpportunityResource\RelationManagers\ActividadesRelationManager;
use App\Filament\Resources\OpportunityResource\Schemas\OpportunityForm;
use App\Filament\Resources\OpportunityResource\Tables\OpportunitiesTable;
use App\Filament\Traits\AuthorizedResource;
use App\Models\Opportunity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OpportunityResource extends Resource
{
    use AuthorizedResource;

    protected static ?string $model = Opportunity::class;

    protected static ?string $navigationLabel = 'Oportunidades';

    protected static ?string $modelLabel = 'Oportunidad';

    protected static ?string $pluralModelLabel = 'Oportunidades';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?int $navigationSort = 3;

    protected static function getPermissionKey(): string
    {
        return 'oportunidades';
    }

    public static function form(Schema $schema): Schema
    {
        return OpportunityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpportunitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ActividadesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpportunities::route('/'),
            'create' => Pages\CreateOpportunity::route('/create'),
            'edit' => Pages\EditOpportunity::route('/{record}/edit'),
            'view' => Pages\ViewOpportunity::route('/{record}'),
            'kanban' => Pages\KanbanOpportunities::route('/kanban'),
        ];
    }
}
