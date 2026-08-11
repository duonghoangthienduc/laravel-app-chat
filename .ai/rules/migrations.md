---
paths:
  - 'app/Modules/Chat/database/migrations/**'
---

# Migrations

## No DB-level FK constraints on relation columns
Relation columns (conversation_id, user_id, sender_id) are plain uuid()/integer() columns without ->constrained() or ->foreign()->references(). Referential integrity is enforced only at the Eloquent layer, not the database.
