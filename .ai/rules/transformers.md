---
paths:
  - 'app/Modules/*/app/Transformers/**'
---

# Transformers

## API resources live in Transformers/, not Http/Resources/
JsonResource classes are placed in the module's Transformers/ folder (e.g. Modules\Chat\Transformers\MessageResource), not the conventional Http/Resources/ path.
