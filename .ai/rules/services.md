---
paths:
  - 'app/Modules/*/app/Services/**'
---

# Services

## Service layer between Controllers and Repositories
Services are plain constructor/method-injected classes (not Action-style handle()/execute()) that sit between Controllers/Livewire and Repositories, holding orchestration logic. Controllers and Livewire components call Services, never Repositories or Eloquent models directly.
