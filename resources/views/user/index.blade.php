@extends('layouts.app')

@section('content')
    <style>
        #table-6 {

            width: 100%;
        }

        #table-6 thead th {
            background-color: rgb(128, 102, 160);
            color: #fff;
            border-bottom-width: 0;
        }

        /* Column Style */
        #table-6 td {
            color: #000;
        }

        /* Heading and Column Style */
        #table-6 tr, #table-6 th {
            border-width: 1px;
            border-style: solid;
            border-color: rgb(128, 102, 160);
        }

        /* Padding and font style */
        #table-6 td, #table-6 th {
            padding: 5px 10px;
            font-weight: bold;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">user list</div>
                    <div class="card-body">
                        <table id="table-6"> <!-- Replace "table-1" with any of the design numbers -->
                            <thead>
                            <th width="10%">编号</th>
                            <th width="20%">姓名</th>
                            <th width="25%">邮箱</th>
                            <th width="25%">创建时间</th>
                            <th width="35%">操作</th>
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td><a href="{{ url("user/info/$user->id") }}">查看</a> <a
                                                href="{{ url("user/edit/$user->id") }}">编辑</a> <a
                                                href="{{ url("user/delete/$user->id") }}">删除</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$users -> links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        public function () {
            
        }
    </script>
@endsection

