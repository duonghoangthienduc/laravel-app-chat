---
paths:
  - 'app/Modules/*/app/Http/**'
---

# Http

## Inline abort_if authorization, no Policies
Access control (e.g. "is this user a participant in this conversation") is enforced with inline abort_if(!$model->relation->contains(...), 403) checks in controller methods, not Policy classes or Gates.
