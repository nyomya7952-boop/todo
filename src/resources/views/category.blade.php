@extends('layouts.app')
@section('title', 'Category')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/category.css') }}">
@endsection
@section('message')
<div class="message__container">
        @if($errors->has('name'))
            <div class="message__ng">
                <ul>
                    @foreach($errors->get('name') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @elseif(isset($message) && $message)
            <div class="message__ok">{{ $message }}</div>
        @endif
</div>
@endsection

@section('content_create')
<div class="form__container">
    <form class="form" action="/categories" method="post">
        @csrf
        <div class="form__group">
            <input type="text" name="name" value="{{ old('name') }}">
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">作成</button>
        </div>
    </form>
</div>
@endsection

@section('content_edit')
<div class="category__container">
    <table class="category__table">
        <colgroup>
            <col class="table__col--category">
            <col class="table__col--update">
            <col class="table__col--delete">
        </colgroup>
        <tr class="category__table--row">
            <th class="category__table--header">Category</th>
            <th class="category__table--header"></th>
            <th class="category__table--header"></th>
        </tr>
            @foreach ($categories as $category)
                <tr class="category__table--row">
                    <form class="update-form" action="/categories/update" method="post">
                    @method('PATCH')
                    @csrf
                        <td class="category__table--cell">
                            <div class="update-form__item">
                                <input class="form__item--category" type="text" name="name" value="{{ $category['name'] }}">
                            </div>
                        <td class="category__table--cell">
                            <div class="form__button">
                                <input type="hidden" name="id" value="{{ $category['id'] }}">
                                <button type="submit" class="update-form__button-submit">更新</button>
                            </div>
                        </td>
                    </form>
                    <form class="delete-form" action="/categories/delete" method="post">
                        @method('DELETE')
                        @csrf
                        <td class="category__table--cell">
                            <div class="form__button">
                                <input type="hidden" name="id" value="{{ $category['id'] }}">
                                <button type="submit" class="delete-form__button-submit">削除</button>
                            </div>
                        </td>
                    </form>
                </tr>
        @endforeach
    </table>
</div>
{{ $categories->links() }}
@endsection