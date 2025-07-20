<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class DetectLazyLoading extends Command
{
    /** @var string */
    protected $signature = 'detect:lazy-loading
                            {--records=5 : Number of records to be checked from each model}';

    /** @var string */
    protected $description = 'Scans all models and detects any possible lazy loading.';

    /** @var array<string,array<int,string>> */
    protected array $violations = [];

    public function handle(): int
    {
        $this->info('🔍 Start Lazy Loading Scan…');

        // 🔐 Temporarily disable Lazy Loading
        Model::preventLazyLoading();
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $this->violations[get_class($model)][] = $relation;
        });

        // ✨ Collect all class names inside app/Models
        foreach ($this->getModelClasses() as $class) {
            $this->scanModel($class, (int) $this->option('records'));
        }

        // 📝 Show results
        if (empty($this->violations)) {
            $this->info('✅ No Lazy Loading Violations 🎉');
            return self::FAILURE;
        }

        $rows = [];
        foreach ($this->violations as $model => $relations) {
            foreach (array_unique($relations) as $relation) {
                $rows[] = [$model, $relation];
            }
        }

        $this->table(['Model', 'Relation'], $rows);

        $this->warn('⚠️ Make sure to use eager loading (with) or modify the code to avoid these violations.');
        return self::SUCCESS;
    }


    protected function getModelClasses(): array
    {
        $modelsPath = app_path('Models');
        if (!File::isDirectory($modelsPath)) {
            return [];
        }

        return collect(File::allFiles($modelsPath))
            ->filter(fn($file) => Str::endsWith($file, '.php'))
            ->map(function ($file) {
                $relativePath = str_replace(app_path() . '/', '', $file->getRealPath());
                $class = 'App\\' . str_replace(['/', '.php'], ['\\', ''], $relativePath);
                return class_exists($class) &&
                    is_subclass_of($class, Model::class) ? $class : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function scanModel(string $class, int $take = 5): void
    {
        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $class;

        $relationships = $this->getRelationshipMethods($model);

        $model::take($take)->get()->each(function (Model $instance) use ($relationships) {
            foreach ($relationships as $method) {
                // Attempt to access the relationship → The violation will be automatically recorded if it occurs
                try {
                    $instance->getRelationValue($method);
                } catch (\Throwable) {
                    // We ignore any other exceptions (such as no data)
                }
            }
        });
    }


    protected function getRelationshipMethods(Model $model): array
    {
        return collect((new ReflectionClass($model))->getMethods())
            ->filter(fn($m) => $m->class === get_class($model)
                && $m->getNumberOfParameters() === 0
                && $m->getReturnType() instanceof \ReflectionNamedType
                && is_subclass_of($m->getReturnType()->getName(), Relation::class))

            ->map(fn($m) => $m->getName())
            ->all();
    }
}
