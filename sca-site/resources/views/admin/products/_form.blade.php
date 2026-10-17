<div class="ad-field">
    <label class="ad-label" for="f-category">Catégorie</label>
    <select id="f-category" name="category_id" required>
        <option value="">— Choisir —</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    @error('category_id') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field">
    <label class="ad-label" for="f-price">Prix (MAD)</label>
    <input id="f-price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price ?? '') }}" required>
    @error('price') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <label class="ad-label" for="f-name">Nom du produit</label>
    <input id="f-name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
    @error('name') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <label class="ad-label" for="f-desc">Description</label>
    <textarea id="f-desc" name="description" rows="5">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <span class="ad-label">Photos</span>
    <label class="ad-drop">
        <x-ad-icon name="upload" :size="24" />
        <span><b>Choisir des images</b> — vous pouvez en sélectionner plusieurs</span>
        <input type="file" name="images[]" accept="image/*" multiple data-preview="ad-previews">
    </label>
    <div class="ad-previews" id="ad-previews"></div>
    @error('images.*') <p class="ad-error">{{ $message }}</p> @enderror
</div>

<div class="ad-field full">
    <label class="ad-check">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true))>
        <span><b>Produit visible pour les clients</b><small>Décochez pour le masquer du catalogue.</small></span>
    </label>
</div>
