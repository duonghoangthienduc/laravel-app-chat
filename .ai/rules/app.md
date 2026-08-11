---
paths:
  - 'app/**'
---

# App

## Domain features live in app/Modules/, not app/
New domain/business features are built as self-contained nwidart/laravel-modules modules under app/Modules/{Name}/ (own app/, config/, database/, resources/, routes/, tests/, module.json, and a {Name}ServiceProvider extends ModuleServiceProvider). The root app/ tree is reserved for cross-cutting/auth/account scaffolding (Fortify actions, base User model, global middleware, Livewire settings pages).
