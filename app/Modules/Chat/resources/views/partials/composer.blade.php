{{-- Input --}}
<div style="border-top:1px solid #27272a; padding:12px 16px;">
    <div style="width:100%; margin-left:auto; margin-right:auto;">
        <div x-show="pendingMedia.length > 0" class="mb-2 flex flex-wrap gap-2" style="position:relative;">
            <template x-for="media in pendingMedia" :key="media.id">
                <div style="position:relative; width:64px; height:64px;">
                    <img :src="media.url" style="width:100%; height:100%; object-fit:cover; border-radius:10px;" :style="isSendingMedia ? 'opacity:0.4;' : ''"/>
                    <button type="button" @click="removePendingMedia(media.id)" x-show="!isSendingMedia" style="position:absolute; top:-6px; right:-6px; width:18px; height:18px; border-radius:9999px; background:#18181b; border:1px solid rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; color:#fff; cursor:pointer;">
                        <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </template>
            <div x-show="isSendingMedia" style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,.3); border-radius:10px;">
                <svg width="20" height="20" class="animate-spin" style="color:#818cf8;" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.2" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path style="opacity:.9" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </div>
        </div>
        <form @submit.prevent="send()" class="flex items-end gap-2 rounded-3xl border border-zinc-800 bg-zinc-900/80 px-3 py-2">
            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept="image/*" multiple class="hidden"/>
            <button type="button" x-show="mediaEnabled" @click="openFilePicker()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-zinc-400 transition hover:bg-zinc-800 hover:text-white">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </button>
            <textarea x-model="draft" @input="notifyTyping()" @keydown.enter.prevent="handleEnter($event)" @paste="handlePaste($event)" rows="1" placeholder="{{ __('Type a message here') }}" class="max-h-32 flex-1 resize-none bg-transparent py-1.5 text-[15px] text-white outline-none placeholder:text-zinc-600"></textarea>
            <button type="submit" :disabled="(!draft.trim() && pendingMedia.length === 0) || isSendingMedia" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-500 text-white transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-40">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.77 59.77 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.874L5.999 12Zm0 0h7.5"/>
                </svg>
            </button>
        </form>
    </div>
</div>