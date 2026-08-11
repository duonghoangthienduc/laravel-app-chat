---
paths:
  - 'app/Modules/*/app/Repositories/**'
---

# Repositories

## Repository per model, extending AbstractRepository
Each Eloquent model gets a Repository extending App\Core\Repositories\AbstractRepository and implementing a {Model}RepositoryInterface (extends App\Core\Contracts\RepositoryInterface) declared in the module's Interfaces/ folder. Custom queries live here, not in Services or Controllers.
