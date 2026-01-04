<!-- Modificação para resources/views/post-form.blade.php -->

@props(['tags', 'hideTags' => false, 'defaultTag' => null, 'selectedTags' => [], 'memberLabs' => []])

@php
    $tagColors = config('tags.colors', []);
    $resolvedDefaultTag = $defaultTag;
@endphp

<div x-data="postForm()" class="mb-10">
    @if (isset($post))
        <!-- Reply form remains unchanged -->
        <form action="{{ route('posts-portal.reply', $post) }}" method="POST">
            @csrf
            <textarea name="content" rows="2" class="w-full border border-gray-200 rounded-lg p-3 focus:ring-1 focus:ring-blue-400 focus:border-blue-400 transition-all duration-200" placeholder="Reply to this post"></textarea>
            <button type="submit" class="mt-3 px-4 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-full text-sm shadow-sm transition-all duration-200">Reply</button>
        </form>
    @else
        <form action="{{ route('posts-portal.store') }}" method="POST" x-ref="postForm" enctype="multipart/form-data">
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

            @unless($hideTags)
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Tags (até 3)</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            @if($tag != 'all')
                                <button type="button"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold border transition"
                                    :class="isTagSelected('{{ $tag }}') ? 'bg-gray-300 text-gray-900 border-gray-400' : '{{ $tagColors[$tag] ?? 'bg-gray-200 text-gray-700 border-gray-200' }}'"
                                    @click.prevent="toggleTag('{{ $tag }}')">
                                    #{{ $tag }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <template x-for="(tag, index) in selectedTags" :key="index">
                            <span class="py-0.5 px-2.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700 flex items-center gap-1">
                                <span x-text="'#' + tag"></span>
                                <button type="button" class="text-indigo-700" @click="removeTag(tag)">×</button>
                            </span>
                        </template>
                    </div>
                </div>

                <!-- First tag (always required) -->
                <input type="hidden" name="tag" x-bind:value="selectedTags[0] || ''">

                <!-- Additional tags -->
                <template x-for="(tag, index) in selectedTags.slice(1)" :key="index">
                    <input type="hidden" name="additional_tags[]" :value="tag">
                </template>

                <!-- Pergunta sobre publicação do laboratório -->
                <div x-show="selectedTags.includes('publicação')" class="mt-6 p-4 bg-gray-50 rounded-lg shadow-sm">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Esta publicação pertence ao seu laboratório? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="is_lab_publication"
                                value="1"
                                x-model="labPublication"
                                class="mr-2"
                                required
                            >
                            <span class="text-sm text-gray-700">Sim</span>
                        </label>
                        <label class="flex items-center">
                            <input
                                type="radio"
                                name="is_lab_publication"
                                value="0"
                                x-model="labPublication"
                                class="mr-2"
                                required
                            >
                            <span class="text-sm text-gray-700">Não</span>
                        </label>
                    </div>

                    <div x-show="selectedTags.includes('publicação') && labPublication === '1'" class="mt-3 space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Selecione o laboratório
                        </label>
                        <select
                            name="lab_id"
                            x-model="selectedLabId"
                            class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-1 focus:ring-indigo-400 focus:border-indigo-400"
                        >
                            <option value="">Selecione um laboratório</option>
                            <template x-for="lab in memberLabs" :key="lab.id">
                                <option :value="lab.id" x-text="lab.name"></option>
                            </template>
                        </select>
                        <p x-show="memberLabs.length === 0" class="text-sm text-gray-500">
                            Você ainda não faz parte de nenhum laboratório. Selecione "Não" para continuar.
                        </p>
                        <p x-show="showLabWarning" class="text-sm text-red-600">
                            Selecione um laboratório ou escolha "Não".
                        </p>
                    </div>
                    <div x-show="selectedTags.includes('publicação') && labPublication === null" class="mt-2 text-sm text-red-600">
                        Por favor, selecione uma opção.
                    </div>
                </div>
            @else
                <input type="hidden" name="tag" value="{{ $resolvedDefaultTag }}">
            @endunless

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
        selectedTags: @json($selectedTags ?? []),
        quill: null,
        tagColors: @json($tagColors), // Use the colors from the config
        memberLabs: @json($memberLabs),
        labPublication: null,
        selectedLabId: '',
        showLabWarning: false,

        // Initialize
        init() {
            // Initialize Quill after Alpine component is mounted
            this.$nextTick(() => {
                this.initQuill();
                this.resetLabSelectionWhenNeeded();
                this.$watch('selectedTags', (tags) => {
                    if (!tags.includes('publicação')) {
                        this.labPublication = null;
                        this.selectedLabId = '';
                        this.showLabWarning = false;
                    }
                });
            });
        },

        resetLabSelectionWhenNeeded() {
            this.$watch('labPublication', (value) => {
                if (value !== '1') {
                    this.selectedLabId = '';
                    this.showLabWarning = false;
                }
            });
        },

        // Initialize Quill editor
        initQuill() {
            if (typeof Quill === 'undefined') {
                console.error('Quill is not loaded');
                return;
            }

            // Use the global function to initialize Quill with upload support
            this.quill = window.initQuillWithImageUpload('#quill-editor', '#quill-toolbar', {
                placeholder: 'Escreva seu post...',
                modules: {
                    toolbar: '#quill-toolbar',
                    imageUploader: {
                        url: '{{ route('upload.image') }}',
                        csrfToken: '{{ csrf_token() }}'
                    }
                }
            });

            // Add a handler for image uploads
            if (this.quill) {
                const quillInstance = this.quill;

                // When an image is uploaded, make sure the form knows content has changed
                this.quill.root.addEventListener('input', function() {
                    console.log('Quill content changed');
                });
            }
        },

        // Submit form with Quill content
        submitForm() {
            console.log('Submit form triggered');

            if (!this.quill) {
                console.error('Quill editor not initialized');
                return;
            }

            // Check if a tag is selected
            if (this.selectedTags.length === 0) {
                alert('Por favor, selecione pelo menos uma tag para seu post.');
                return;
            }

            if (this.selectedTags.includes('publicação')) {
                if (this.labPublication === null) {
                    this.showLabWarning = true;
                    return;
                }
                if (this.labPublication === '1') {
                    if (this.memberLabs.length === 0 || !this.selectedLabId) {
                        this.showLabWarning = true;
                        return;
                    }
                }
            }

            try {
                // Get content from Quill and set it to hidden input
                const content = this.quill.root.innerHTML;
                console.log('Quill content retrieved:', content.substring(0, 100) + '...');

                // Set the content to the hidden input
                this.$refs.contentInput.value = content;

                console.log('Content set to hidden input, submitting form');

                // Submit the form
                this.$refs.postForm.submit();
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Ocorreu um erro ao enviar o post. Por favor, tente novamente.');
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
