@extends('template.app')

@section('content')
    <style>
        .chat-container {
            height: 60vh;
            overflow-y: auto;
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background-color: #f9f9f9;
        }

        .message-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: fadeIn 0.5s ease-in-out;
            width: 50%;
        }

        .my-message-row {
            justify-content: flex-end;
        }

        .other-message-row {
            justify-content: flex-start;
        }

        .user-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #555;
            flex-shrink: 0;
            margin-top: 10px;
        }

        .my-message-row .user-icon {
            order: 2;
        }

        .message-content {
            display: flex;
            flex-direction: column;
            max-width: 70%;
        }

        .my-message-row .message-content {
            align-items: flex-end;
        }

        .message-bubble {
            padding: 10px 15px;
            border-radius: 20px;
            word-wrap: break-word;
            line-height: 1.4;
            font-size: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-username {
            font-size: 12px;
            color: #777;
            margin-bottom: 5px;
        }

        .my-message {
            background-color: #4e73df;
            color: white;
            border-bottom-right-radius: 5px;
        }

        .other-message {
            background-color: #ffffff;
            color: #333;
            border-bottom-left-radius: 5px;
        }

        .message-time {
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }

        .other-message .message-time {
            text-align: left;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f0f0f0;
            padding: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <div class="content">
        <div class="panel-header bg-primary-gradient"
            style="background: linear-gradient(45deg, #01c293, #01b48b) !important;">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Dashboard</h2>
                        <h5 class="text-white op-7 mb-2">History Chat</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner mt--5">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">History Chat</h4>
                        </div>
                        <div class="card-body">
                            {{-- Formulir untuk Filter ID --}}
                            <form action="{{ route('history-chat.index') }}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="from_id">From</label>
                                            <select name="from_id" id="from_id" class="form-control select2">
                                                <option value="">---Choose Person---</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ request('from_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="to_id">To</label>
                                            <select name="to_id" id="to_id" class="form-control select2">
                                                <option value="">---Choose Person ---</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ request('to_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            @if (request('from_id') && request('to_id'))
                                <div class="chat-container">
                                    @php
                                        $fromId = request('from_id');
                                        $toId = request('to_id');
                                        $fromUser = $users->firstWhere('id', $fromId);
                                        $toUser = $users->firstWhere('id', $toId);
                                    @endphp
                                    @forelse ($historyChat as $item)
                                        @if ($item->from_id == $fromId)
                                            {{-- Pesan yang dikirim oleh pengguna "From" --}}
                                            <div class="message-row other-message-row">
                                                <div class="user-icon">
                                                    {{ substr($fromUser->name, 4, 1) }}
                                                </div>
                                                <div class="message-content">
                                                    <div class="message-username">{{ $fromUser->name }}</div>
                                                    <div class="message-bubble other-message">
                                                        {{ $item->body }}
                                                    </div>
                                                    <div class="message-time">
                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Pesan yang dikirim oleh pengguna "To" --}}
                                            <div class="message-row my-message-row">
                                                <div class="user-icon">
                                                    {{ substr($toUser->name, 4, 1) }}
                                                </div>
                                                <div class="message-content">
                                                    <div class="message-username">{{ $toUser->name }}</div>
                                                    <div class="message-bubble my-message">
                                                        {{ $item->body }}
                                                    </div>
                                                    <div class="message-time">
                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="text-center text-muted m-auto">There is no chat history.</div>
                                    @endforelse
                                </div>
                            @else
                                <div class="text-center text-muted mt-5">
                                    <h3>Please select person first</h3>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
