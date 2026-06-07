@php
    $selectedCategoryId = old('category_id', $supplier?->category_id);
    $categoryOptions = $categories
        ->map(fn ($category): array => [
            'id' => (string) $category->id,
            'name' => $category->name,
        ])
        ->values();
    $isActive = old('is_active', $supplier?->is_active ?? true);
@endphp

<div class="supplier-form__grid">
    <label class="supplier-form__field">
        <span>Company Name <span class="supplier-form__required">*</span></span>
        <input name="company_name" type="text" value="{{ old('company_name', $supplier?->company_name) }}" class="supplier-form__control">
        @error('company_name') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Contact Person</span>
        <input name="contact_person" type="text" value="{{ old('contact_person', $supplier?->contact_person) }}" class="supplier-form__control">
        @error('contact_person') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Phone</span>
        <input name="phone" type="text" value="{{ old('phone', $supplier?->phone) }}" class="supplier-form__control">
        @error('phone') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field">
        <span>Email</span>
        <input name="email" type="email" value="{{ old('email', $supplier?->email) }}" class="supplier-form__control">
        @error('email') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label
        class="supplier-form__field supplier-form__combo"
        x-data="{
            open: false,
            query: '',
            selectedId: @js((string) $selectedCategoryId),
            selectedName: '',
            options: @js($categoryOptions),
            init() {
                const selected = this.options.find((option) => option.id === this.selectedId);

                if (selected) {
                    this.selectedName = selected.name;
                    this.query = selected.name;
                }
            },
            get filteredOptions() {
                const term = this.query.trim().toLowerCase();

                if (term === '' || this.query === this.selectedName) {
                    return this.options;
                }

                return this.options.filter((option) => option.name.toLowerCase().includes(term));
            },
            search() {
                this.selectedId = '';
                this.selectedName = '';
                this.open = true;
            },
            select(option) {
                this.selectedId = option.id;
                this.selectedName = option.name;
                this.query = option.name;
                this.open = false;
            },
            close() {
                window.setTimeout(() => {
                    this.open = false;

                    if (this.selectedName !== '') {
                        this.query = this.selectedName;
                    }
                }, 120);
            },
        }"
    >
        <span>Category <span class="supplier-form__required">*</span></span>
        <input name="category_id" type="hidden" x-model="selectedId">

        <div class="supplier-form__combo-wrap">
            <input
                type="search"
                x-model="query"
                x-on:focus="open = true"
                x-on:input="search()"
                x-on:blur="close()"
                x-on:keydown.escape.prevent="open = false"
                x-on:keydown.enter.prevent="if (filteredOptions.length > 0) select(filteredOptions[0])"
                placeholder="Select Category"
                class="supplier-form__control supplier-form__combo-input"
                autocomplete="off"
                role="combobox"
                aria-autocomplete="list"
                x-bind:aria-expanded="open.toString()"
            >

            <div x-cloak x-show="open" class="supplier-form__combo-menu">
                <template x-for="option in filteredOptions" :key="option.id">
                    <button
                        type="button"
                        x-on:mousedown.prevent="select(option)"
                        class="supplier-form__combo-option"
                        :class="{ 'supplier-form__combo-option--selected': selectedId === option.id }"
                    >
                        <span x-text="option.name"></span>
                    </button>
                </template>

                <div x-show="filteredOptions.length === 0" class="supplier-form__combo-empty">
                    No categories found.
                </div>
            </div>
        </div>

        @error('category_id') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__field supplier-form__field--wide">
        <span>Address</span>
        <textarea name="address" rows="3" class="supplier-form__control">{{ old('address', $supplier?->address) }}</textarea>
        @error('address') <span class="supplier-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="supplier-form__check">
        <input name="is_active" type="hidden" value="0">
        <input name="is_active" type="checkbox" value="1" @checked((bool) $isActive)>
        <span>Active</span>
    </label>
</div>
