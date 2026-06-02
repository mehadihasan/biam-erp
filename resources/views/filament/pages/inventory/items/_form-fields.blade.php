@php
    $selectedCategoryId = old('inventory_category_id', $item?->inventory_category_id);
    $selectedUnitId = old('inventory_unit_id', $item?->inventory_unit_id ?? $units->first()?->id);
    $isActive = old('is_active', $item?->is_active ?? true);
@endphp

<div class="inventory-item-form__grid">
    <label class="inventory-item-form__field">
        <span>Item Name <span class="inventory-item-form__required">*</span></span>
        <input name="name" type="text" value="{{ old('name', $item?->name) }}" class="inventory-item-form__control">
        @error('name') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Category <span class="inventory-item-form__required">*</span></span>
        <select name="inventory_category_id" class="inventory-item-form__control">
            <option value="">Select category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $selectedCategoryId === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('inventory_category_id') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Unit <span class="inventory-item-form__required">*</span></span>
        <select name="inventory_unit_id" class="inventory-item-form__control">
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected((string) $selectedUnitId === (string) $unit->id)>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('inventory_unit_id') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Unit Cost (BDT)</span>
        <input name="unit_cost" type="number" min="0" step="0.01" value="{{ old('unit_cost', $item?->unit_cost ?? 0) }}" class="inventory-item-form__control">
        @error('unit_cost') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Location / Shelf</span>
        <input name="location" type="text" value="{{ old('location', $item?->location) }}" placeholder="e.g. Shelf A-1" class="inventory-item-form__control">
        @error('location') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Min Threshold <span class="inventory-item-form__required">*</span></span>
        <input name="min_threshold" type="number" min="0" step="1" value="{{ old('min_threshold', $item?->min_threshold ?? 5) }}" class="inventory-item-form__control">
        @error('min_threshold') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field">
        <span>Initial Stock Quantity</span>
        <input name="initial_stock_quantity" type="number" min="0" step="1" value="{{ old('initial_stock_quantity', $item?->initial_stock_quantity ?? 0) }}" class="inventory-item-form__control">
        @error('initial_stock_quantity') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__field inventory-item-form__field--wide">
        <span>Description</span>
        <textarea name="description" rows="4" class="inventory-item-form__control">{{ old('description', $item?->description) }}</textarea>
        @error('description') <span class="inventory-item-form__error">{{ $message }}</span> @enderror
    </label>

    <label class="inventory-item-form__check">
        <input name="is_active" type="hidden" value="0">
        <input name="is_active" type="checkbox" value="1" @checked((bool) $isActive)>
        <span>Active</span>
    </label>
</div>
