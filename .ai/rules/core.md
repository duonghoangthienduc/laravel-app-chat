---
paths:
  - 'app/Core/**'
---

# Core

## Shared repository base contract
App\Core\Contracts\RepositoryInterface + App\Core\Repositories\AbstractRepository define the base CRUD contract (find/findOrFail/create/update/delete) that module repositories extend and implement. Add new base repository behavior here, not per-module.
