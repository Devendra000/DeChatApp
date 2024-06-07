<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .message-box {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .user {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .messages {
            margin-bottom: 10px;
        }

        .received,
        .sent {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 5px 0;
        }

        .received {
            justify-content: flex-start;
        }

        .sent {
            justify-content: flex-end;
        }

        .received .col,
        .sent .col {
            margin: 0 5px;
        }

        .received .col img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }

        .received .col p,
        .sent .col p {
            background-color: #e1ffc7;
            padding: 10px;
            border-radius: 10px;
            max-width: 300px;
        }

        .sent .col p {
            background-color: #c7e1ff;
        }

        .write-message {
            display: flex;
            justify-content: space-between;
        }

        .write-message form {
            display: flex;
            width: 100%;
        }

        .write-message input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }

        .write-message input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #4caf50;
            color: #fff;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
    </style>

    <script src='https://js.pusher.com/7.2/pusher.min.js'></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="message-box">
        <div class="user">User Name</div>
        <div class="messages">
            @include('messages.receive',['message' => 'First received message'])
            @include('messages.sent',['message' => 'First sent message'])
        </div>
        <div class="write-message">
            <form>
                <input type="text" name="message" id="message" placeholder="Type your message here">
                <input type="submit" value="Send">
            </form>
        </div>
    </div>

    <script>
        const pusher = new Pusher("{{config('broadcasting.connections.pusher.key')}}", {
            cluster: 'mt1'
        });

        console.log('pusher:',pusher);
        console.log('pusher:',pusher.sessionID);
        const channel = pusher.subscribe('public');

        channel.bind('chat', function(data) {
            $.post("{{route('messages.receive')}}", {
                    _token: "{{csrf_token()}}",
                    message: data.message
                })
                .done(function(res) {
                    console.log('received:', res);
                    $('.messages > .message').last().after(res);
                    $(document).scrollTop($(document).height());
                });
        });

        $('form').submit(function(event) {
            event.preventDefault();
            $.ajax({
                url: "{{route('messages.send')}}",
                method: 'POST',
                headers: {
                    'X-Socket-Id': pusher.connection.socket_id,
                },
                data: ({
                    _token: "{{csrf_token()}}",
                    message: $('form #message').val()
                })
            }).done(function(res) {
                console.log('sent:', res);
                $('.messages > .message').last().after(res);
                $('form #message').val('');
                $(document).scrollTop($(document).height());
            });
        });
    </script>
</body>

</html>