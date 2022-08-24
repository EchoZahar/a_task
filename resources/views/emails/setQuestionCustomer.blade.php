<!DOCTYPE html>
<html>
<head>
    <title>Question for you request #{{ $customerRequest->id }}</title>
</head>
<body>
<h1>Customer name: {{ $customerRequest->name }}</h1>
<p>Your write:</p>
<p>{{ $customerRequest->message }}</p>
<p>Comment for you request</p>
<p>{{ $customerRequest->comment }}</p>
<p>Thank you</p>
</body>
</html>
