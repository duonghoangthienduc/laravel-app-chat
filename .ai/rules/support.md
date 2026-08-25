---
paths:
  - 'app/Support/**'
---

# Support

## Cross-cutting UI extension points use a Registry + Facade in app/Support/{Feature}/
Pluggable UI regions (nav items, dashboard widgets) follow one shape: a `{Feature}Registry` class in `app/Support/{Feature}/` holding an array with a `register(...)` method, a `Facades/{Feature}.php` Facade over it, bound as a singleton in `AppServiceProvider::register()` and aliased in `AppServiceProvider::boot()`. Modules push into the registry from their own `{Module}ServiceProvider::boot()` (see `Modules\Log\Providers\LogServiceProvider` calling `Dashboard::register(view: ..., data: Closure, priority: ...)`), and the host Blade view iterates the registry's collection to render (see `resources/views/dashboard.blade.php` looping `Dashboard::widgets()`). Existing instances: `Navigation` (nav children) and `Dashboard` (dashboard widgets). Follow this same pattern for new pluggable regions instead of ad hoc module-to-module coupling.
