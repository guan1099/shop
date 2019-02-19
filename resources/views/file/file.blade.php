@extends('layouts.bst');
@section('content')
    <form action="/file/upload" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <input type="file" name="pdf" >
        <input type="submit" value="上传">
    </form>
@endsection