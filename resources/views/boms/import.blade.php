<form action="{{ route('bom.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="bom_file">
    <button type="submit">Upload</button>
</form>
