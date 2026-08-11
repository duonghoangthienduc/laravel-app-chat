---
paths:
  - 'app/Modules/Chat/resources/**'
---

# Resources

## Chat inbox UI is Alpine.js + JSON API, not Livewire
The live chat inbox (conversation list + message thread) is a hand-rolled Alpine.js component (resources/assets/js/app.js, x-data="chatInbox(...)") that calls the versioned JSON API (/api/v1/chat/...) via fetch with Sanctum SPA cookies, and subscribes to Echo/Reverb private channels (conversation.{id}) for push updates. It is not a Livewire component. Reserve Livewire for simpler, non-realtime screens in this module (e.g. FindUsers user search).
