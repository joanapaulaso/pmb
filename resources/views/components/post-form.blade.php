<!-- Modificação para resources/views/post-form.blade.php -->

@props(['tags'])

@php
    $tagColors = config('tags.colors');
@endphp

<div x-data="postForm()" class="mb-10">
    @if (isset($post))
        <!-- Reply form remains unchanged -->
        <form action="{{ route('posts.reply', $post) }}" method="POST">
            @csrf
            <textarea name="content" rows="2" class="w-full border border-gray-200 rounded-lg p-3 focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200" placeholder="Reply to this post"></textarea>
            <button type="submit" class="mt-3 px-4 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-full text-sm shadow-sm transition-all duration-200">Reply</button>
        </form>
    @else
        <form action="{{ route('posts.store') }}" method="POST" x-ref="postForm">
            @csrf

            <!-- Hidden field to store Quill content -->
            <input type="hidden" name="content" x-ref="contentInput">

            <!-- Quill container -->
            <div class="mb-3">
                <!-- Quill toolbar -->
                <div id="quill-toolbar">
                    <span class="ql-formats">
                        <select class="ql-font">
                            <option value="sans-serif" selected>Sans Serif</option>
                            <option value="serif">Serif</option>
                            <option value="monospace">Monospace</option>
                        </select>
                        <select class="ql-size">
                            <option value="small">Small</option>
                            <option selected>Normal</option>
                            <option value="large">Large</option>
                            <option value="huge">Huge</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold"></button>
                        <button class="ql-italic"></button>
                        <button class="ql-underline"></button>
                        <button class="ql-strike"></button>
                    </span>
                    <span class="ql-formats">
                        <select class="ql-color"></select>
                        <select class="ql-background"></select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered"></button>
                        <button class="ql-list" value="bullet"></button>
                        <button class="ql-indent" value="-1"></button>
                        <button class="ql-indent" value="+1"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link"></button>
                        <button class="ql-image"></button>
                    </span>
                </div>

                <!-- Quill editor container -->
                <div id="quill-editor" class="border border-gray-200 rounded-lg focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200" style="min-height: 100px;"></div>
            </div>

            <div class="mb-3 relative">
                <div class="flex items-center mb-2">
                    <div class="ml-2 flex flex-wrap gap-1">
                        <template x-for="(tag, index) in selectedTags" :key="index">
                            <span
                                class="py-0.5 px-2.5 text-xs font-medium rounded-full cursor-pointer transition-all duration-200 flex items-center gap-1"
                                :class="tagColors[tag] || 'bg-gray-200 text-gray-700'"
                                @click="removeTag(tag)"
                            >
                                <span x-text="'#' + tag"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        </template>
                    </div>
                </div>

                <div
                    @click="toggle()"
                    class="w-full border-0 rounded-full py-2 px-3 cursor-pointer bg-white flex items-center justify-between transition-all duration-200"
                    :class="{ 'border-0': open }"
                >
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="mr-4 fill-indigo-600 size-4">
                            <path fill-rule="evenodd" d="M4.5 2A2.5 2.5 0 0 0 2 4.5v2.879a2.5 2.5 0 0 0 .732 1.767l4.5 4.5a2.5 2.5 0 0 0 3.536 0l2.878-2.878a2.5 2.5 0 0 0 0-3.536l-4.5-4.5A2.5 2.5 0 0 0 7.38 2H4.5ZM5 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                        </svg>
                        <span
                            x-text="selectedTags.length > 0 ? 'Selecionou ' + selectedTags.length + ' tag(s)' : 'Selecione tags para sua postagem (até 3)'"
                            :class="selectedTags.length > 0 ? 'text-indigo-600 font-semibold' : 'text-indigo-600 font-semibold'"
                            class="text-sm"
                        ></span>
                    </div>
                    <svg
                        :class="{'rotate-180': open}"
                        class="w-4 h-4 text-gray-400 transform transition-transform duration-300"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    @click.away="close()"
                    class="absolute mt-1 w-full z-10 py-2"
                >
                    <div class="flex flex-wrap gap-1.5 px-2">
                        @foreach($tags as $tag)
                            @if($tag != 'all')
                                <div
                                    @click="toggleTag('{{ $tag }}')"
                                    class="py-1 px-3 gap-2 cursor-pointer rounded-full text-xs font-medium transition-all duration-200 hover:scale-105 {{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700' }}"
                                    x-bind:class="{'ring-2 ring-offset-2': isTagSelected('{{ $tag }}')}"
                                >
                                    #{{ $tag }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- First tag (always required) -->
            <input type="hidden" name="tag" x-bind:value="selectedTags[0] || ''">

            <!-- Additional tags -->
            <template x-for="(tag, index) in selectedTags.slice(1)" :key="index">
                <input type="hidden" name="additional_tags[]" :value="tag">
            </template>

            <button
                type="submit"
                @click.prevent="submitForm"
                class="px-4 py-1.5 mt-8 bg-blue-500 hover:bg-blue-600 text-white rounded text-sm shadow-sm transition-all duration-200"
            >
                Post
            </button>
        </form>
    @endif
</div>

<script>
    function postForm() {
        return {
            open: false,
            selectedTags: [],
            quill: null,
            tagColors: @json($tagColors), // Use the colors from the config

            // Initialize
            init() {
                // Initialize Quill after Alpine component is mounted
                this.$nextTick(() => {
                    this.initQuill();
                });
            },

            // Initialize Quill editor
            initQuill() {
                if (typeof Quill === 'undefined') {
                    console.error('Quill is not loaded');
                    return;
                }

                this.quill = new Quill('#quill-editor', {
                    modules: {
                        toolbar: '#quill-toolbar'
                    },
                    placeholder: 'Write your post',
                    theme: 'snow'
                });
            },

            // Submit form with Quill content
            submitForm() {
                if (this.quill) {
                    // Get content from Quill and set it to hidden input
                    const content = this.quill.root.innerHTML;
                    this.$refs.contentInput.value = content;

                    // Submit the form
                    this.$refs.postForm.submit();
                }
            },

            // Tag functions
            toggle() {
                this.open = !this.open;
            },
            close() {
                this.open = false;
            },
            toggleTag(tag) {
                if (this.isTagSelected(tag)) {
                    this.removeTag(tag);
                } else {
                    this.addTag(tag);
                }
            },
            addTag(tag) {
                if (this.selectedTags.length < 3 && !this.selectedTags.includes(tag)) {
                    this.selectedTags.push(tag);
                }
            },
            removeTag(tag) {
                this.selectedTags = this.selectedTags.filter(t => t !== tag);
            },
            isTagSelected(tag) {
                return this.selectedTags.includes(tag);
            }
        }
    }
</script>
