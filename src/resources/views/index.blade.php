@extends('layouts.app')
@section('title', 'Todo')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection
@section('message')
<div class="message__container">
        @if($errors->any())
            <div class="message__ng">
                <ul>
                    @foreach($errors->all() as $error)
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
    <div class="container-title">新規作成</div>
    <form class="form" action="/todos" method="post">
        @csrf
        <div class="form__group">
            <span class="form__group--todo"><input type="text" name="content" value="{{ old('content') }}"></span>
            <span class="form__group--category">
                <select name="category_id" value="{{ old('category_id') }}">
                    <option value="">カテゴリを選択</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </span>
            <span class="form__group--priority">
                <select name="priority" value="{{ old('priority') }}">
                    <option value="">優先度を選択</option>
                    <option value="low">低</option>
                    <option value="medium">中</option>
                    <option value="high">高</option>
                </select>
            </span>
            <span class="form__group--due_date">
                <input type="date" name="due_date" value="{{ old('due_date') }}">
            </span>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">作成</button>
        </div>
    </form>
</div>
@endsection

@section('todo_search')
<div class="todo__search">
    <div class="container-title">Todo検索</div>
    <form class="form" action="/todos/search" method="get">
        <div class="form__group">
            <span class="form__group--todo"><input type="text" name="keyword" value="{{ old('keyword') }}"></span>
            <span class="form__group--category">
                <select name="category_id" value="{{ old('category_id') }}">
                    <option value="">カテゴリを選択</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </span>
            <span class="form__group--priority">
                <select name="priority" value="{{ old('priority') }}">
                    <option value="">優先度を選択</option>
                    <option value="low">低</option>
                    <option value="medium">中</option>
                    <option value="high">高</option>
                </select>
            </span>
            <span class="form__group--due_date">
                <input type="date" name="due_date" value="{{ old('due_date') }}">
            </span>
            <span class="form__group--is_completed">
                <select name="is_completed" value="{{ old('is_completed') }}">
                    <option value="">未/済を選択</option>
                    <option value="1">完了</option>
                    <option value="0">未完了</option>
                </select>
            </span>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">検索</button>
        </div>
    </form>
</div>
@endsection

@section('content_edit')
<div class="todo__container">
    <p class="todo__container--count">{{ $todos_count }}件</p>
    <table class="todo__table">
        <colgroup>
            <col class="table__col--is_completed">
            <col class="table__col--todo">
            <col class="table__col--category">
            <col class="table__col--priority">
            <col class="table__col--due_date">
            <col class="table__col--update">
            <col class="table__col--delete">
        </colgroup>
        <tr class="todo__table--row">
            <th class="todo__table--header">未/済</th>
            <th class="todo__table--header">Todo</th>
            <th class="todo__table--header">カテゴリ</th>
            <th class="todo__table--header">優先度</th>
            <th class="todo__table--header">期限日</th>
            <th class="todo__table--header"></th>
            <th class="todo__table--header"></th>
        </tr>
            @foreach ($todos as $todo)
                @php
                    $dueDate = $todo['due_date'] ? \Carbon\Carbon::parse($todo['due_date']) : null;
                    $isCompleted = $todo['is_completed'] ? 1 : 0;
                    $today = \Carbon\Carbon::today();
                    $rowClass = '';
                    if ($dueDate) {
                        if ($dueDate->isPast() && !$dueDate->isToday() && $isCompleted == 0) {
                            $rowClass = 'todo__table--row-overdue';
                        } elseif ($dueDate->isToday() && $isCompleted == 0) {
                            $rowClass = 'todo__table--row-today';
                        }
                    }
                @endphp
                <tr class="todo__table--row {{ $rowClass }}">
                    <form class="update-form" action="/todos/update" method="post">
                    @method('PATCH')
                    @csrf
                        <td class="todo__table--cell">
                                <div class="update-form__item">
                                    <input type="hidden" name="is_completed" value="0">
                                    <input type="checkbox" name="is_completed" value="1" {{ $todo['is_completed'] == 1 ? 'checked' : '' }}>
                                </div>
                        </td>
                        <td class="todo__table--cell">
                            <div class="update-form__item">
                                <input class="form__item--todo" type="text" name="content" value="{{ $todo['content'] }}">
                            </div>
                        <td class="todo__table--cell">
                            <div class="update-form__item">
                                <select name="category_id">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $todo['category_id'] == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="todo__table--cell">
                            <div class="update-form__item">
                                <select name="priority">
                                    <option value="low" {{ $todo['priority'] == 'low' ? 'selected' : '' }}>低</option>
                                    <option value="medium" {{ $todo['priority'] == 'medium' ? 'selected' : '' }}>中</option>
                                    <option value="high" {{ $todo['priority'] == 'high' ? 'selected' : '' }}>高</option>
                                </select>
                            </div>
                        </td>
                        <td class="todo__table--cell">
                            <div class="update-form__item">
                                <input type="date" name="due_date" value="{{ $todo['due_date'] }}">
                            </div>
                        </td>
                        <td class="todo__table--cell">
                            <div class="form__button">
                                <input type="hidden" name="id" value="{{ $todo['id'] }}">
                                <button type="submit" class="update-form__button-submit">更新</button>
                            </div>
                        </td>
                    </form>
                    <form class="delete-form" action="/todos/delete" method="post">
                    @method('DELETE')
                    @csrf
                        <td class="todo__table--cell">
                            <div class="form__button">
                                <input type="hidden" name="id" value="{{ $todo['id'] }}">
                                <button type="submit" class="delete-form__button-submit">削除</button>
                            </div>
                        </td>
                    </form>
                </tr>
        @endforeach
    </table>
</div>
{{ $todos->links() }}
@endsection