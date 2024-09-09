
<div class="container">
    <h1>Edit Diskon</h1>
    
    <!-- Menampilkan pesan sukses jika ada -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulir untuk mengedit diskon -->
    <form action="{{ route('admin.discounts.update', $discount) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="code">Kode Diskon</label>
            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $discount->code) }}" required>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Deskripsi</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $discount->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="percentage">Persentase Diskon</label>
            <input type="number" name="percentage" id="percentage" class="form-control @error('percentage') is-invalid @enderror" value="{{ old('percentage', $discount->percentage) }}" min="0" max="100" step="0.01" required>
            @error('percentage')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="active">Status</label>
            <select name="active" id="active" class="form-control @error('active') is-invalid @enderror" required>
                <option value="1" {{ old('active', $discount->active) == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ old('active', $discount->active) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('active')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan Diskon</button>
        <a href="{{ route('admin.discounts.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

