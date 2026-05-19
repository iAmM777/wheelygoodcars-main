<?php

namespace App\Livewire;

use App\Models\Car;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class CarCatalog extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * @var array<int>
     */
    public array $selectedTags = [];

    protected string $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedTags' => ['except' => []],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedTags(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'selectedTags']);
        $this->resetPage();
    }

    public function render(): View
    {
        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        $cars = Car::query()
            ->with('tags')
            ->whereNull('sold_at')
            ->when($this->search !== '', function (Builder $query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('brand', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('license_plate', 'like', $search);
                });
            })
            ->when($this->selectedTags !== [], function (Builder $query): void {
                $query->whereHas('tags', function (Builder $tagQuery): void {
                    $tagQuery->whereIn('tags.id', $this->selectedTags);
                }, '=', count($this->selectedTags));
            })
            ->latest()
            ->paginate(12);

        $featuredCarIds = Car::query()
            ->with('tags')
            ->whereNull('sold_at')
            ->when($this->search !== '', function (Builder $query): void {
                $search = '%' . trim($this->search) . '%';

                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('brand', 'like', $search)
                        ->orWhere('model', 'like', $search)
                        ->orWhere('license_plate', 'like', $search);
                });
            })
            ->when($this->selectedTags !== [], function (Builder $query): void {
                $query->whereHas('tags', function (Builder $tagQuery): void {
                    $tagQuery->whereIn('tags.id', $this->selectedTags);
                }, '=', count($this->selectedTags));
            })
            ->pluck('id')
            ->shuffle()
            ->take(max(1, min(6, $cars->total())))
            ->all();

        return view('livewire.car-catalog', [
            'cars' => $cars,
            'tags' => $tags,
            'featuredCarIds' => $featuredCarIds,
        ]);
    }
}
