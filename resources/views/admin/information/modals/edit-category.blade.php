<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">

            <form action="{{ route('admin.information.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-3" value="{{ $category->name }}" required>

                    <input type="color" name="color" value="{{ $category->color }}">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>