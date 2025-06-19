{{-- resources/views/admin/books/partials/form.blade.php --}}
@if ($errors->any())
    <div class="mb-4">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="mb-4">
        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" id="title" value="{{ old('title', $book->title) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
    </div>

    <div class="mb-4">
        <label for="author" class="block text-sm font-medium text-gray-700">Author</label>
        <input type="text" name="author" id="author" value="{{ old('author', $book->author) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
    </div>

    <div class="mb-4">
        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>{{ old('description', $book->description) }}</textarea>
    </div>

    <div class="mb-4">
        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-4">
        <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Image</label>
        <input type="file" name="cover_image" id="cover_image" class="mt-1 block w-full">
        @if ($book->cover_image)
            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="mt-2 h-24">
        @endif
    </div>

    <div class="mb-4">
        <label for="file_path" class="block text-sm font-medium text-gray-700">Book File (PDF)</label>
        <input type="file" name="file_path" id="file_path" class="mt-1 block w-full">
    </div>

    <div class="flex items-center justify-end mt-4">
        <a href="{{ route('admin.books.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            {{ $book->exists ? 'Update' : 'Create' }} Book
        </button>
    </div>
</form>
