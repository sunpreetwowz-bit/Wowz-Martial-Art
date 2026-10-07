<x-admin-layout title="Gallery">
    <x-slot name="header">Gallery</x-slot>
    <div class="grid gap-6 lg:grid-cols-2">
        <x-admin.card>
            <x-slot:header><h2 class="text-sm font-semibold">Add category</h2></x-slot:header>
            <form method="POST" action="{{ route('admin.gallery.categories.store') }}" class="space-y-3">
                @csrf
                <x-admin.input label="Name" name="name" required />
                <x-admin.input label="Description" name="description" type="textarea" />
                <x-admin.input label="Display order" name="display_order" type="number" value="0" />
                <x-admin.input label="Status" name="status" type="select" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </x-admin.input>
                <x-admin.button type="submit">Create category</x-admin.button>
            </form>
            <ul class="mt-6 space-y-2 text-sm">
                @foreach ($categories as $category)
                    <li class="flex items-center justify-between border-b border-slate-100 py-2">
                        <span>{{ $category->name }} <span class="text-slate-400">({{ $category->images_count }})</span></span>
                        <form method="POST" action="{{ route('admin.gallery.categories.destroy', $category) }}">@csrf @method('DELETE')
                            <button class="text-red-700">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </x-admin.card>

        <x-admin.card>
            <x-slot:header><h2 class="text-sm font-semibold">Upload image</h2></x-slot:header>
            <form method="POST" action="{{ route('admin.gallery.images.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <x-admin.input label="Category" name="gallery_category_id" type="select">
                    <option value="">None</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-admin.input>
                <x-admin.input label="Title" name="title" />
                <x-admin.input label="Caption" name="caption" />
                <x-admin.input label="Image" name="image" type="file" accept="image/*" required />
                <x-admin.input label="Display order" name="display_order" type="number" value="0" />
                <x-admin.input label="Status" name="status" type="select" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </x-admin.input>
                <x-admin.button type="submit">Upload</x-admin.button>
            </form>
        </x-admin.card>
    </div>

    <x-admin.card class="mt-6" :padding="false">
        <x-admin.table>
            <x-slot:head>
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3 text-right">Actions</th>
            </x-slot:head>
            @foreach ($images as $image)
                <tr>
                    <td class="px-4 py-3"><img src="{{ asset('storage/'.$image->image_path) }}" class="h-12 w-16 object-cover" alt=""></td>
                    <td class="px-4 py-3">{{ $image->title ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $image->category?->name ?: '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" action="{{ route('admin.gallery.images.destroy', $image) }}">@csrf @method('DELETE')
                            <x-admin.button type="submit" variant="danger">Delete</x-admin.button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </x-admin.table>
        <x-admin.pagination :paginator="$images" />
    </x-admin.card>
</x-admin-layout>
