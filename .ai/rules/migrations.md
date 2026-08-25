---
paths:
  - 'app/Modules/*/database/migrations/**'
---

# Migrations

## No DB-level FK constraints on relation columns
Relation columns (conversation_id, user_id, sender_id) are plain uuid()/integer() columns without ->constrained() or ->foreign()->references(). Referential integrity is enforced only at the Eloquent layer, not the database. Confirmed module-wide: Log's activity_day migration also uses a plain integer user_id with no FK.
